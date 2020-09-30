<?php

defined( 'ABSPATH' ) or die; // exit if accessed directly


class BibleSuperSearch_Widget extends WP_Widget {
    
    public function __construct() {
 
        parent::__construct(
            'biblesupersearch_widget',  // Base ID
            'Bible SuperSearch',   // Name
            [
                'description' => __('Small Bible search form that links back to your main Bible SuperSearch page.'),
            ]
        );
 
        add_action( 'widgets_init', function() {
            register_widget( 'BibleSuperSearch_Widget' );
        });
    }
 
    public $args = array(
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
    );
 
    public function widget( $args, $instance ) {
        global $BibleSuperSearch_Options;

        $options        = $BibleSuperSearch_Options->getOPtions();
        $form_action    = get_permalink($instance['landing_page']);
        $query_vars     = array_key_exists('biblesupersearch', $_REQUEST) ? $_REQUEST['biblesupersearch'] : [];
        $selected_bible = array_key_exists('bible', $query_vars) ? $query_vars['bible'] : NULL;
        $bible_list = [];

        if(!$selected_bible) {
            $selected_bible = $options['defaultBible'];
        }

        $request_style = $instance['bible_list_display'] != 'none' ? 'width: 100%' : 'width: 80%; float:left';

        if($instance['bible_list_grouping'] == 'global') {
            $instance['bible_list_grouping'] = $options['bibleGrouping'];
        }

        if($instance['bible_list_display'] != 'none') {
            $group = NULL;

            foreach($BibleSuperSearch_Options->getEnabledBibles() as $bible) {
                $b = [
                    'module' => $bible['module'],
                ];

                switch($instance['bible_list_display']) {
                    case 'full_name':
                        $b['name'] = $bible['name'];
                        break;
                    case 'short_name':
                    default:
                        $b['name'] = $bible['shortname'];
                }

                switch($instance['bible_list_grouping']) {
                    case 'language': // Language: Endonym
                        $b['group_value'] = $bible['lang_short'];
                        $n = $bible['lang_native'] ?: $bible['lang']; // Fall back to English name if needed
                        $b['group_name'] = $n . ' - (' . strtoupper($bible['lang_short']) . ')';
                        break;                
                    case 'language_and_english': // Language: Both Endonym and English name
                        $b['group_value'] = $bible['lang_short'];
                        // If no Endonym, only display English name once
                        $n = ($bible['lang_native'] && $bible['lang_native'] != $bible['lang']) ? $bible['lang_native'] . ' / ' . $bible['lang'] : $bible['lang'];
                        $b['group_name'] = $n . ' - (' . strtoupper($bible['lang_short']) . ')';
                        break;
                    case 'language_english': // Language: English name
                        $b['group_value'] = $bible['lang_short'];
                        $b['group_name'] = $bible['lang'] . ' - (' . strtoupper($bible['lang_short']) . ')';
                        break;
                    default:
                        $b['group_value'] = NULL;
                        $b['group_name']  = NULL;
                        $b['name'] .= ' (' . $bible['lang'] . ')';
                }


                $bible_list[] = $b;
            }
        }

        // echo('<pre>');
        // print_r($bible_list);
        // echo('</pre>');

        echo $args['before_widget'];
 
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        ?>
            <form action='<?php echo $form_action ?>' method='POST'>
                <input name='biblesupersearch[request]' style='<?php echo $request_style ?>' placeholder= 'Verse(s) or Keyword(s)'/>

                <?php if($instance['bible_list_display'] != 'none'): ?>
                    <br />
                    <select name='biblesupersearch[bible]' style='width: 80%; float: left'>
                        <?php foreach($bible_list as $bible): ?>
                            <?php 
                                if($bible['group_value'] && $bible['group_value'] != $group):
                                    if($group !== NULL) echo '</optgroup>';
                                    $group = $bible['group_value'];
                            ?> 
                                <optgroup label='<?php echo $bible['group_name'] ?>' >
                            <?php endif; ?>

                            <?php
                                $selected = ($bible['module'] == $selected_bible) ? "selected='selected'" : '';
                                $display  = $bible['name'];
                            ?>

                            <option value='<?php echo $bible['module'] ?>' <?php echo $selected; ?> ><?php echo $display ?></option>
                        <?php endforeach; ?>
                        <?php if($group): ?></optgroup><?php endif; ?>
                    </select>
                <?php endif; ?>

                <input type='submit' value='Go' style='float:right' />
                <div style='clear:both'></div>
            </form>
        <?php

        echo $args['after_widget'];
    }
 
    public function form( $instance ) {
        global $BibleSuperSearch_Options;
        $landing_page = array_key_exists('landing_page', $instance) ? (int) $instance['landing_page'] : 0;
        $options = $BibleSuperSearch_Options->getOptions();

        $defaults = [
            'bible_list_display'    => 'none',
            'bible_list_grouping'   => 'global',
        ];

        foreach($defaults as $key => $def) {
            if(!array_key_exists($key, $instance) || !$instance[$key]) {
                $instance[$key] = $def;
            }
        }

        if(!$landing_page) {
            $lp = $BibleSuperSearch_Options->getLandingPage();
            $landing_page = (int) $lp['ID'];
        }

        $landing_page_options = $BibleSuperSearch_Options->getLandingPageOptions(TRUE, $landing_page, FALSE);

        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        $show_bible_list = ! empty( $instance['show_bible_list'] ) ? $instance['show_bible_list'] : 0;

        $sbl_sel1 = ($show_bible_list)  ? "checked='checked'" : '';
        $sbl_sel0 = (!$show_bible_list) ? "checked='checked'" : '';

        $sbl_field_id = $this->get_field_id( 'show_bible_list' );
        $sbl_field_name = $this->get_field_name( 'show_bible_list' );

        // Todo
        // Bible List Display: Select NONE, Full Name, Short Name
        // Bible List Grouping: Select Global Default, <dynamic list>
        // Placeholder: Text
        // CSS classes

        $bible_list_display_options = [
            'none' => 'None',
            'full_name' => 'Full Name',
            'short_name' => 'Short Name'
        ]; 

        $bible_list_grouping_options = $BibleSuperSearch_Options->getSelectorOptions('bibleGrouping');

        $bible_list_grouping_global = [ 'global' => 'Global (' . $bible_list_grouping_options[ $options['bibleGrouping'] ] .')' ];
        $bible_list_grouping_options = $bible_list_grouping_global + $bible_list_grouping_options;

        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $sbl_field_id ); ?>"><?php echo esc_html__( 'Show Bible List:', 'text_domain' ); ?></label>
            <label for="<?php echo esc_attr( $sbl_field_id ); ?>_1"><?php echo esc_html__( 'Yes', 'text_domain' ); ?></label>
            <input type='radio' id="<?php echo esc_attr( $sbl_field_id ); ?>_1" name="<?php echo esc_attr( $sbl_field_name ); ?>" value='1' <?php echo $sbl_sel1 ?> />    

            <label for="<?php echo esc_attr( $sbl_field_id ); ?>_0"><?php echo esc_html__( 'No', 'text_domain' ); ?></label>
            <input type='radio' id="<?php echo esc_attr( $sbl_field_id ); ?>_0" name="<?php echo esc_attr( $sbl_field_name ); ?>" value='0' <?php echo $sbl_sel0 ?> />
        </p>

        <?php if(!$BibleSuperSearch_Options->hasLandingPageOptions()): ?> 
            <p style='color: red'>
                ERROR: No landing page options found. &nbsp;The widget will not work without a landing page. &nbsp;Please create a landing page by adding the [biblesupersearch] shortcode to a page or post.
            </p>
        <?php endif; ?>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'landing_page' ) ); ?>"><?php echo esc_html__( 'Landing Page:', 'text_domain' ); ?></label>
            <select name="<?php echo esc_attr( $this->get_field_name( 'landing_page' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'landing_page' ) ); ?>" >
                <?php echo $landing_page_options; ?>
            </select>
        </p>        

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'bible_list_display' ) ); ?>"><?php echo esc_html__( 'Bible List Display:', 'text_domain' ); ?></label>
            <select name="<?php echo esc_attr( $this->get_field_name( 'bible_list_display' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'bible_list_display' ) ); ?>" >
                <?php foreach ($bible_list_display_options as $value => $option): ?>
                    <option value='<?php echo $value ?>' <?php if($value == $instance['bible_list_display']): echo "selected='selected'"; endif; ?> ><?php echo $option ?></option>
                <?php endforeach; ?>
            </select>
        </p>        
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'bible_list_grouping' ) ); ?>"><?php echo esc_html__( 'Bible List Grouping:', 'text_domain' ); ?></label>
            <select name="<?php echo esc_attr( $this->get_field_name( 'bible_list_grouping' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'bible_list_grouping' ) ); ?>" >
                <?php foreach ($bible_list_grouping_options as $value => $option): ?>
                    <option value='<?php echo $value ?>' <?php if($value == $instance['bible_list_grouping']): echo "selected='selected'"; endif; ?> ><?php echo $option ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
 
    }
 
    public function update( $new_instance, $old_instance ) {
 
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['show_bible_list'] = ( !empty( $new_instance['show_bible_list'] ) ) ? $new_instance['show_bible_list'] : '0';
        $instance['landing_page'] = ( !empty( $new_instance['landing_page'] ) ) ? $new_instance['landing_page'] : '0';
        $instance['bible_list_display'] = ( !empty( $new_instance['bible_list_display'] ) ) ? $new_instance['bible_list_display'] : 'none';
        $instance['bible_list_grouping'] = ( !empty( $new_instance['bible_list_grouping'] ) ) ? $new_instance['bible_list_grouping'] : 'global';
        
        return $instance;
    }
}

$BibleSuperSearch_Widget = new BibleSuperSearch_Widget();
