// Tab Item that displays configs
import ApiUrl from './components/ApiUrl.vue.js';
import SelectGroup from './components/VSelectGroup.vue.js';
import SelectOrdered from './components/VSelectOrdered.vue.js';
import BibleLimitsByWidth from './components/ParLim.vue.js';

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
        <v-col cols='2'><b>{{op(config).label}}</b></v-col>
        <v-col cols='6'> 
            <component 
                :is='formComponent(config)' 
                v-model='options[config]'
                v-bind='configProps(config)'
                v-if='configIf(config)' 
            ></component>

            <v-sheet v-if='!descAsLabel(config)' v-html="op(config).desc" class='mt-1'></v-sheet>
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
            debug: true
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
                bind = prop.vue_props || {};

            if(prop.v_no_attr) {
                return null;
            }

            // bind.label = bind.label || prop.label;
            // bind.hint = bind.hint || prop.desc; // needs to allow HTML

            if(this.descAsLabel(config)) {
                bind.label = prop.desc;
            } 

            // For selects / autocompletes
            bind.items = bind.items || prop.items;
                console.log(bind.items, typeof bind.items);

            if(bind.items && typeof bind.items == 'string') {
                bind.items = this.bootstrap.statics[bind.items] || [];
            }

            console.log(bind.items);


            bind['item-title'] = 'label';
            bind['item-value'] = 'value';
            bind['item-props'] = 'itemProps';
            bind.variant = 'outlined';
            bind.density = 'compact';
            bind.multiple = prop.multiple || false;
            bind['hide-details'] = true;

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

            return true;

            // return false;
            // return true;

            // ideally, we'd be able to hand it an expression to evaluate
            // not sure this is even possible.
            return prop['v-if'] ? "`" + prop['v-if'] + "`" : "`true`";
        },
        descAsLabel(config) {
            var comp = this.formComponent(config);
            return (comp == 'v-switch' || comp == 'v-checkbox');
        },

        descBr(config) {
            var comp = this.formComponent(config);
            return !(comp == 'v-switch' || comp == 'v-checkbox');
        }
    }
}