const tpl = `
    <tr class="language-bible-selector">
        <td>{{ language.label }}</td>
        <td>
            <v-switch v-model="enabled" density='compact' />
        </td>
        <td>
            model {{ modelValue }}
        

        </td>
    </tr>
`;

            // <VSelectOrdered
            //     :items="enabledBibles"
            //     v-model="modelValue"
            //     @update:modelValue="onSelect"
            //     v-if="enabled"
            //     llabel="name"
            //     lvalue="id"
            // />

import VSelectOrdered from '../VSelectOrdered.vue.js';

export default {
    name: 'LanguageBibleSelector',
    props: {
        modelValue: {
            // type: [String, Number, null],
            required: true
        },
        language: {
            type: Object,
            required: true
        }
    },
    emits: ['update:modelValue'],
    inject: ['enabledBibles'],
    template: tpl,
    components: {
        VSelectOrdered
    },
    watch: {
        modelValue(newValue, oldValue) {
            this.enabled = newValue !== null && newValue !== undefined;
        }
    },
    data() {
        return {
            enabled: false,
            bibles: [
                { id: 'kjv', name: 'King James Version' },
                { id: 'niv', name: 'New International Version' },
                { id: 'esv', name: 'English Standard Version' }
                // Add more bibles as needed
            ]
        };
    },
    methods: {
        onSelect(value) {
            this.$emit('update:modelValue', value);
        }
    }
};
