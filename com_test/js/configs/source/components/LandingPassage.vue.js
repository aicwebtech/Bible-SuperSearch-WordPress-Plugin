const tpl = `
    <template v-if="true">
        <v-textarea
            :modelValue="modelValue"
            @update:modelValue='updateModelValue'
            :disabled='loading'
            v-bind="$attrs"
        >
        </v-textarea>
        <v-btn @click='populate' size="small" :disabled='loading || $attrs.disabled'>Populate with the Gospel</v-btn>
    </template>
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
            this.$emit('update:modelValue', event);
        },
        populate() {
            this.updateModelValue([
                'Romans 3:10, 23',
                '6:23',
                '5:8',
                '10:9, 13',
                'John 3:16',
                'John 14:6',
                'Acts 4:12',
                'Ephesians 2:8, 9',
            ].join('; '));
        }

    }
}
