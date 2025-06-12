const tpl = `
    <table>
        <thead>
            <tr>
                <th>Language</th>
                <th>Defaults</th>
                <th>Bibles {{modelValue}} </th>
            </tr>
        </thead>
        <tbody>
            <Selector 
                v-for='lang in enabledLanguages' 
                :language='lang' 
                v-model='modelValue[lang.value]' 
            />
        </tbody>
    </table>
`;

import Selector from './LanguageBibleSelector.vue.js';

export default {
    inject: ['bootstrap', 'enabledLanguages'],
    props: ['modelValue'],
    emits: ['update:modelValue'],
    template: tpl,
    components: {
        Selector
    },
    data() {
        return {
            modelTest: {},
            valueChanged: false,
            oldValue: null,
            newValue: null,
            changeSuccess: false,
            loading: false,
            successIcon: 'mdi-check'
        }
    },
    methods: {
        updateModelValue(event) {
            this.valueChanged = true;
            this.changeSuccess = false;
            this.oldValue = this.modelValue;
            this.newValue = event;
        },
    }
}
