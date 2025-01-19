// Tab Item that displays configs

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
        <v-col v-if='false' cols='4' v-html="op(config).desc"></v-col>
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
    components: {


    },
    data() {
        return {

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

            // bind.label = bind.label || prop.label;
            // bind.hint = bind.hint || prop.desc; // needs to allow HTML

            if(this.descAsLabel(config)) {
                bind.label = prop.desc;
            } 

            // For selects / autocompletes
            bind.items = bind.items || prop.items;
            bind['item-title'] = 'label';
            bind['item-value'] = 'value';
            bind.variant = 'outlined';
            bind.density = 'compact';
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