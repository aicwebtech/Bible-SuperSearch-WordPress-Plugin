// Tab Item that displays configs
import ApiUrl from './components/ApiUrl.vue.js';
import SelectGroup from './components/VSelectGroup.vue.js';
import SelectOrdered from './components/VSelectOrdered.vue.js';
import BibleLimitsByWidth from './components/ParLim.vue.js';
import Rules from './components/FormRules.vue.js';

var components = {
    ApiUrl,
    SelectGroup,
    SelectOrdered,
    BibleLimitsByWidth
};

const tpl_ = `
    <v-row
        v-for='config in tab.options'
    >   
        <v-col 
            v-if='op(config).label_cols !== 0 && configIf(config)' 
            :cols='op(config).label_cols || 2' 
            :class='op(config).type == "section" ? "text-left mt-3" : "text-right mt-3"'
        >
            <b>{{op(config).label}}</b>
        </v-col>
        <v-col :cols='op(config).comp_cols || 5' v-if='configIf(config)'> 
            <component 
                :is='formComponent(config)' 
                v-model='options[config]'
                v-bind='configProps(config)'
            ></component>

            <v-sheet v-if='!hasSubLabel(config) || op(config).sublabel' v-html="op(config).desc" class='mt-1'></v-sheet>
        </v-col>
        <v-col v-if='debug && configIf(config)' cols='4'>{{options[config]}}</v-col>
    </v-row>
`;

const tpl = `
    <template v-for='config in tab.options'>
        <v-sheet 
            v-if='configIf(config)' 
            class='mb-4'
        >
            <div 
                v-if='op(config).label_cols !== 0 && configIf(config)' 
                class='d-inline-block float-left w-25 ma-3'
                :class="{'text-left': op(config).type == 'section', 'text-right': op(config).type != 'section'}"
                style='max-width: 250px;'
                :style="{'max-width': op(config).label_width || '250px'}"
            >
                <b>{{op(config).label}}</b>
            </div>
            <div 
                class='d-inline-block float-left w-50'
                ssstyle='max-width: {{op(config).comp_width || 500px}};'
                :style="{'max-width': op(config).comp_width || '500px'}"
            >
                <component 
                    :is='formComponent(config)' 
                    v-model='options[config]'
                    v-bind='configProps(config)'
                ></component>

                <v-sheet v-if='!hasSubLabel(config) || op(config).sublabel' v-html="op(config).desc" class='mt-1'></v-sheet>
            </div>
            <div v-if='debug && configIf(config)' class='d-inline-block float-left w-25'>{{options[config]}}</div>
            <div style='clear: both;'></div>
        </v-sheet>
    </template>
`;

export default {
    inject: ['bootstrap'],
    template: tpl,
    props: {
        tab: {
            type: Object,
            required: true
        },            
        options: {
            type: Object,
            required: true
        },               
    },
    components: components,
    data() {
        return {
            debug: false,
            rules: Rules
        }
    },
    computed: {
        optionProps() {
            return this.bootstrap.option_props[this.tab.id];
        }
    },
    methods: {
        op(config) {
            return this.optionProps[config] || null;
        },
        formComponent(config) {
            var prop = this.op(config);

            if(prop.v_component) {
                // console.log(this.vm.$options);
                
                if(prop.v_component[0] != 'v' && !components[prop.v_component]) {
                    alert('Component missing ' + prop.v_component);
                }

                return prop.v_component;
            }

            var map = {
                select: 'v-select',
                checkbox: 'v-switch',
                integer: 'v-text-field',
                section: 'v-header',
                textarea: 'v-textarea'
            };

            var c = map[prop.type] || 'v-text-field';

            return c;
        },
        configProps(config) {
            var prop = this.op(config),
                bind = prop.vue_props || {},
                t = this;

            if(prop.v_no_attr) {
                return null;
            }

            // bind.label = bind.label || prop.label;
            // bind.hint = bind.hint || prop.desc; // needs to allow HTML

            if(this.hasSubLabel(config)) {
                bind.label = prop.sublabel || prop.desc;
            } 

            // For selects / autocompletes
            bind.items = bind.items || prop.items;

            if(bind.items && typeof bind.items == 'string') {
                bind.items = this.bootstrap.statics[bind.items] || [];
            }

            bind['item-title'] = 'label';
            bind['item-value'] = 'value';
            // bind['item-props'] = 'itemProps';

            bind['item-props'] = function(item) {
                var props = item.itemProps || {};
                props.density = 'compact';
                // props.variant = 'outlined';
                return props;
            }

            bind.variant = 'outlined';
            bind.density = 'compact';
            bind.multiple = prop.multiple || false;
            bind['hide-details'] = 'auto';

            if(Array.isArray(prop.rules)) {                
                bind['rules'] = [];

                prop.rules.forEach(function(r) {
                    if(typeof t.rules[r] == 'function') {
                        bind['rules'].push(v => t.rules[r](v, prop));
                    } 
                });
            }



            // For checkboxes / switches
            // bind['true-value'] = '1';
            // bind['false-value'] = '0';
            bind['color'] = 'primary';

            return bind;
        },
        configIf(config) {
            var prop = this.op(config);

            if(prop.type == 'hidden' || prop.hidden) {
                return false;
            }

            // Special cases

            if(config === 'formatButtonsToggle') {
                return this.options.formatButtons !== 'none';
            }

            if(config === 'strongsDialogSearchLink') {
                return this.options.strongsOpenClick !== 'none';
            }

            // End special cases


            if(prop.if_conditions) {
                var ifAnd = prop.if_conditions.split(',');
                
                for(var i = 0; i < ifAnd.length; i++) {
                    var vv = ifAnd[i].split('|');

                    if(vv[1] == 'false') {
                        if(this.options[vv[0]]) {
                            return false;
                        }
                    } else {
                        if(!this.options[vv[0]]) {
                            return false; 
                        }
                    }
                }

                return true;
            }

            return true;
        },
        hasSubLabel(config) {
            var comp = this.formComponent(config);
            return (comp == 'v-switch' || comp == 'v-checkbox');
        },
        descBr(config) {
            var comp = this.formComponent(config);
            return !(comp == 'v-switch' || comp == 'v-checkbox');
        }
    }
}