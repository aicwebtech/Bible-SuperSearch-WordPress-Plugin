const tpl = `
    <tr class="language-bible-selector border-0">
        <td class='w-30 pa-3'>
            <span class='position-sticky' style='top: 10px'>    
                {{ language.label }}
            </span>
        </td>
        <td class='pl-3 w-10'>
            <v-switch 
                v-model="enabled" 
                density='compact' 
                hide-details
                color="primary"
            />
        </td>
        <td class='w-60'>
            <VSelectOrdered
                v-bind="$attrs"
                :items="enabledBibles"
                v-model="modelValue"
                density="compact"
                @update:modelValue="onSelect"
                v-if="enabled"
                item-title="label"
                item-value="value"
                :item-props="item => ({...item.itemProps, ...{ density: 'compact'}})"
            />

            <span v-else class='pa-3' @click='enabled = true'>
                (Global Default Bibles)
            </span>
        </td>
    </tr>
`;

import VSelectOrdered from '../VSelectOrdered.vue.js';

export default {
    name: 'LanguageBibleSelector',
    props: {
        modelValue: {
            type: Array,
            default: () => [],
            required: true
        },
        language: {
            type: Object,
            required: true
        }
    },
    emits: ['update:modelValue'],
    inject: ['enabledBibles', 'bootstrap'],
    template: tpl,
    components: {
        VSelectOrdered
    },
    watch: {
        modelValue(newValue, oldValue) {
            this.enabled = newValue !== null && newValue !== undefined && newValue.length > 0;
        },
        enabled(newValue, oldValue) {
            if (!newValue) {
                this.$emit('update:modelValue', []);
            } else if (this.valueCache !== null) {
                this.$emit('update:modelValue', this.valueCache);
            }
        }
    },
    data() {
        return {
            enabled: false,
            valueCache: null
        };
    },
    methods: {
        onSelect(value) {
            console.log('LBS Selected value:', value);
            this.$emit('update:modelValue', value);
            this.valueCache = value;
        }
    }
};
