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

const tpl = `
    <v-row
        v-for='config in tab.options'
    >   
        <v-col v-if='op(config).label_cols !== 0' :cols='op(config).label_cols || 2'>
            <b>{{op(config).label}}</b>
        </v-col>
        <v-col :cols='op(config).comp_cols || 6'> 
            <component 
                :is='formComponent(config)' 
                v-model='options[config]'
                v-bind='configProps(config)'
                v-if='configIf(config)' 
            ></component>

            <v-sheet v-if='!hasSubLabel(config) || op(config).sublabel' v-html="op(config).desc" class='mt-1'></v-sheet>
        </v-col>
        <v-col v-if='debug' cols='4'>{{options[config]}}</v-col>
    </v-row>
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
            debug: true,
            rules: Rules
        }
    },
    computed: {
        optionProps() {
            return this.bootstrap.option_props[this.tab.id];
        },
        // options() {
        //     return this.bootstrap.options;
        // }
    },
    methods: {
        op(config) {
            return this.optionProps[config] || null;
        },
        formComponent(config) {
            var prop = this.op(config);

            if(prop.v_component) {
                if(!components[prop.v_component]) {
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
            bind['item-props'] = 'itemProps';
            bind.variant = 'outlined';
            bind.density = 'compact';
            bind.multiple = prop.multiple || false;
            bind['hide-details'] = 'auto';

            if(Array.isArray(prop.rules)) {                
                var rules = ['smited'];

                bind['rules'] = [];

                prop.rules.forEach(function(r) {
                    if(typeof t.rules[r] == 'function') {
                        bind['rules'].push(v => t.rules[r](v, prop));
                    } 
                    
                    // bind['rules'].push(value => t[r](value, prop.label));
                });
            }


            // bind['rules'] = [
            //     // (value) => this.smited(value, prop.label)
            //     this.smited
            // ];

            // For checkboxes / switches
            // bind['true-value'] = '1';
            // bind['false-value'] = '0';
            bind['color'] = 'primary';

            return bind;
        },
        configIf(config) {
            var prop = this.op(config);

            if(config == 'languageList') {
                return !this.options.enableAllLanguages;
            }

            if(config == 'enabledBibles') {
                return !this.options.enableAllBibles;
            }

            if(config == 'parallelBibleLimitByWidth' || config == 'parallelBibleStartSuperceedsDefaultBibles') {
                // :todo - update value of options parallelBibleLimitByWidthEnable
                // :todo - hide label of parallelBibleStartSuperceedsDefaultBibles

                // return this.options.parallelBibleLimitByWidthEnable; // || !!this.options.parallelBibleLimitByWidth;
            }

            return true;

            // return false;
            // return true;

            // ideally, we'd be able to hand it an expression to evaluate
            // not sure this is even possible.
            return prop['v-if'] ? "`" + prop['v-if'] + "`" : "`true`";
        },
        hasSubLabel(config) {
            var comp = this.formComponent(config);
            return (comp == 'v-switch' || comp == 'v-checkbox');
        },

        descBr(config) {
            var comp = this.formComponent(config);
            return !(comp == 'v-switch' || comp == 'v-checkbox');
        },

        // rules
        smited(value, prop) {
            return value ? true : prop.label + ' is required';
        }
    }
}