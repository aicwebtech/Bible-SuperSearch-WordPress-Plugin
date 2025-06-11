const tpl = `
    <table>
        <thead>
            <tr>
                <th>Language</th>
                <th>Defaults</th>
                <th>Bibles</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for='lang in bootstrap.statics.languages'>
                <td>{{ lang.name }}</td>
                <td>
                    <v-select
                        v-model='modelValue[lang.id]'
                        :items='bootstrap.defaultBibles'
                        :rules="[ value => !!value || 'Please select a default Bible' ]"
                        :label='lang.name + " default Bible"'
                        :disabled='loading'
                        @update:modelValue='updateModelValue'
                    ></v-select>
                </td>
                <td>
                    <v-text-field
                        v-model='modelValue[lang.id + "_bibles"]'
                        type='number'
                        :label='lang.name + " Bibles"'
                        :rules="[ value => !!value || 'Please enter the number of Bibles' ]"
                        :disabled='loading'
                        @update:modelValue='updateModelValue'
                    ></v-text-field>
                </td>
            </tr>
        </tbody>
    </table>
`;

export default {
    inject: ['bootstrap'],
    props: ['modelValue'],
    emits: ['update:modelValue'],
    template: tpl,
    data() {
        return {
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
