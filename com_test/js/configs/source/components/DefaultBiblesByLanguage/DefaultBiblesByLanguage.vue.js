const tpl = `
    <table>
        <thead>
            <tr>
                <th>Language</th>
                <th>Defaults</th>
                <th>
                    Bibles External:{{modelValue}} 
                    
                    <br>Internal: {{modelInternal}}
                </th>
            </tr>
        </thead>
        <tbody>
            <Selector 
                v-for='lang in enabledLanguages' 
                :language='lang' 
                v-model='modelInternal[lang.value]' 
                @update:modelValue="onUpdateValue"
            />
        </tbody>
    </table>
`;

import Selector from './LanguageBibleSelector.vue.js';

export default {
    inject: ['bootstrap', 'enabledLanguages', 'enabledBibles'],
    props: ['modelValue'],
    emits: ['update:modelValue'],
    template: tpl,
    components: {
        Selector
    },
    watch: {
        modelValue: {
            handler(newValue, oldValue) {
                // this.modelInternal = {};
                
                for (const idx in newValue) {
                    var lang = newValue[idx].lang || idx;
                    var def = newValue[idx].default || newValue[idx];

                    this.modelInternal[lang] = def || [];
                    
                    // this.modelInternal.push({
                    //     lang: lang,
                    //     bibles: newValue[lang] || []
                    // });
                }
            },
            immediate: true
        }
    },
    mounted() {
        for(const idx in this.bootstrap.statics.languages) {
            var lang = this.bootstrap.statics.languages[idx];
            
            // Initialize the modelInternal for each language
            if(!this.modelInternal[lang.value]) {
                this.modelInternal[lang.value] = [];
            }
        }
    },
    data() {
        return {
            modelInternal: {},
            valueChanged: false,
            oldValue: null,
            newValue: null,
            changeSuccess: false,
            loading: false,
            successIcon: 'mdi-check'
        }
    },
    methods: {
        onUpdateValue(event) {
            var modelClean = {};
            
            // Clean the modelInternal to only include languages with bibles
            for (const lang in this.modelInternal) {
                if (this.modelInternal[lang] && this.modelInternal[lang].length > 0) {
                    modelClean[lang] = this.modelInternal[lang];
                }
            }

            // Update the modelValue with the new value
            this.$emit('update:modelValue', modelClean);
        },
    }
}
