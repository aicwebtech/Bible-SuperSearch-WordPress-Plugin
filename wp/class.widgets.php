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

        $form_action    = get_permalink($instance['landing_page']);
        $query_vars     = array_key_exists('biblesupersearch', $_REQUEST) ? $_REQUEST['biblesupersearch'] : [];
        $selected_bible = array_key_exists('bible', $query_vars) ? $query_vars['bible'] : NULL;

        if(!$selected_bible) {
            $options = $BibleSuperSearch_Options->getOPtions();
            $selected_bible = $options['defaultBible'];
        }

        $request_style = $instance['show_bible_list'] ? 'width: 100%' : 'width: 80%; float:left';

        echo $args['before_widget'];
 
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        ?>
            <form action='<?php echo $form_action ?>' method='POST'>
                <input name='biblesupersearch[request]' style='<?php echo $request_style ?>' placeholder= 'Verse(s) or Keyword(s)'/>

                <?php if($instance['show_bible_list']): ?>
                    <br />
                    <select name='biblesupersearch[bible]' style='width: 80%; float: left'>
                        <?php foreach($BibleSuperSearch_Options->getEnabledBibles() as $bible): ?>
                            
                            <?php
                                $selected = ($bible['module'] == $selected_bible) ? "selected='selected'" : '';
                                $display  = '(' . $bible['lang'] . ') ' . $bible['shortname'];
                            ?>

                            <option value='<?php echo $bible['module'] ?>' <?php echo $selected; ?> ><?php echo $display ?></option>
                        <?php endforeach; ?>
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
        <?php
 
    }
 
    public function update( $new_instance, $old_instance ) {
 
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['show_bible_list'] = ( !empty( $new_instance['show_bible_list'] ) ) ? $new_instance['show_bible_list'] : '0';
        $instance['landing_page'] = ( !empty( $new_instance['landing_page'] ) ) ? $new_instance['landing_page'] : '0';
        
        return $instance;
    }
}

$BibleSuperSearch_Widget = new BibleSuperSearch_Widget();
