
const tpl = `
    <v-select 
        :modelValue="modelValue" 
        @update:modelValue="$emit('update:modelValue', $event)" 
        multiple
        :items="items"
    >
        <template v-slot:prepend-item>
            <v-list-item
                title="Select All"
                @click="toggle"
            >
                <template v-slot:prepend>
                <v-checkbox-btn
                    :indeterminate="someItemsSelected && !allItemsSelected"
                    :model-value="allItemsSelected"
                ></v-checkbox-btn>
                </template>
            </v-list-item>

            <v-divider class="mt-2"></v-divider>
        </template>
    
        <template v-slot:item="{ props, item }">
            <hr v-if="item.props.role == 'header'" />
            <v-list-subheader v-if="item.props.role == 'header'">
                {{ item.props.title }}
                <span v-if='false' @click="toggleGroup(item.raw.group)">Toggle</span>
            </v-list-subheader>
            <v-divider v-else-if="item.props.role == 'divider'" />
            <v-list-item v-else v-bind="props">
                <template v-slot:prepend='{ isSelected }'>
                    <v-checkbox-btn 
                        v-if='!item.props.disabled'
                        :key='item.value'
                        :model-value='isSelected'
                        :ripple="false"
                        :tabindex="-1"
                    />
                </template>
            </v-list-item>
        </template>
    </v-select>
`;

/*
            {{data}}
            <v-list-subheader v-if="data.header">
                {{ data.props.header }}
            </v-list-subheader>
            <v-divider v-else-if="data.divider" />

        <template #item="{ data }">
            <v-list-item v-bind="data"></v-list-item>
        </template>

        <template v-slot:item="{ props, item }">
            <v-list-item v-bind="props"></v-list-item>
        </template>

*/

export default {
    inject: ['bootstrap'],
    props: [
        'modelValue',
        'items'
    ],
    emits: ['update:modelValue'],
    template: tpl,
    components: {

    },
    data() {
        return {
            selectAll: false,
        }
    },
    computed: {
        allItemsSelected() {
            return this.modelValue.length === this.valueItems.length;
        },
        someItemsSelected() {
            return this.modelValue.length > 0;
        },
        valueItems() {
            return this.items.filter(item => !!item.value);
        }
    },
    methods: {
        toggle() {
            if(this.allItemsSelected) {
                this.$emit('update:modelValue', []);
            } else {
                this.$emit('update:modelValue', this.valueItems.map(item => item.value));
            }
        },
        // Experimental
        toggleGroup(group) {
            console.log('group', group, this.valueItems);
            
            var groupItems = this.valueItems.filter(i => i.group === group);



            var values = groupItems.map(i => i.value);

            console.log(groupItems, values);

            this.$emit('update:modelValue', [...this.modelValue, ...values]);


            
            // if(this.modelValue.includes(item.value)) {
            //     this.$emit('update:modelValue', this.modelValue.filter(i => i !== item.value));
            // } else {
            //     this.$emit('update:modelValue', [...this.modelValue, item.value]);
            // }
        }
    }
}

