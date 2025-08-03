const tpl = `
    <v-select 
        :modelValue="modelValue" 
        @update:modelValue="$emit('update:modelValue', $event)" 
        multiple
        :items="items"
    >
        <template v-slot:prepend-item v-if="showSelectAll">
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
            <!--<v-list-item>
                <template v-slot:default="{ isSelected }">
                    <v-btn ddensity='compact' @click='selectAllItems'>Select All</v-btn>
                    <v-btn ddensity='compact' @click='selectNone'>Select None</v-btn>
                </template>
            </v-list-item>-->

            <v-divider class="mt-2"></v-divider>
        </template>
    
        <template v-slot:item="{ props, item }">
            <hr v-if="item.props.role == 'header'" />
            <v-list-subheader v-if="item.props.role == 'header'">
                <v-checkbox-btn 
                    :key='item.value'
                    :model-value='selectAllByGroup[item.raw.group]'
                    :ddindeterminate="selectAllByGroup[item.raw.group] !== true && selectAllByGroup[item.raw.group] !== false"
                    :ripple="false"
                    :tabindex="-1"
                    color="primary"
                    @click="toggleGroup(item.raw.group)"
                    class="d-inline"
                />
                
                <!--<v-icon size='x-small' @click="selectAllGroup(item.raw.group)" icon='mdi-select-all'></v-icon>
                <v-btn size='x-small' @click="selectNoneGroup(item.raw.group)" icon='mdi-select-remove'></v-btn>-->
                <!--<span v-if='false' @click="toggleGroup(item.raw.group)">Toggle</span>-->
                <b>{{ item.props.title }}</b>
            </v-list-subheader>
            <v-divider v-else-if="item.props.role == 'divider'" />
            <v-list-item v-else v-bind="props">
                <template v-slot:prepend='{ isSelected }'>
                    <v-checkbox-btn 
                        v-if='!item.props.disabled'
                        :key='item.value'
                        :model-value='isSelected'
                        :ripple="false"
                        color="primary"
                        :tabindex="-1"
                    />
                </template>
            </v-list-item>
        </template>
    </v-select>
`;


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
            showSelectAll: false, // todo make this a prop
            selectAllByGroup: {},
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
        },
        groups() {
            var groups = this.valueItems.map(i => i.group);
            return [...new Set(groups)];
        }
    },
    methods: {
        toggle() {
            if(this.allItemsSelected) {
                this.selectNone();
            } else {
                this.selectAllItems();
            }
        },
        selectAllItems() {
            var t = this;

            this.groups.forEach((group) => t.selectAllByGroup[group] = true);
            this.$emit('update:modelValue', this.valueItems.map(item => item.value));
        },
        selectNone() {
            var t = this;
            
            this.groups.forEach((group) => t.selectAllByGroup[group] = false);
            this.$emit('update:modelValue', []);
        },
        selectAllGroup(group) {
            var groupItems = this.valueItems.filter(i => i.group === group);
            var values = groupItems.map(i => i.value);

            var valuesWithDuplicates = [...this.modelValue, ...values];

            this.$emit('update:modelValue', [...new Set(valuesWithDuplicates)]);
            this.selectAllByGroup[group] = true;
        },
        selectNoneGroup(group) {
            var groupItems = this.valueItems.filter(i => i.group === group);
            var values = groupItems.map(i => i.value);

            this.$emit('update:modelValue', this.modelValue.filter(i => !values.includes(i)));
            this.selectAllByGroup[group] = false;
        },
        // Experimental
        toggleGroup(group) {
            if(this.selectAllByGroup[group]) {
                this.selectNoneGroup(group);
            } else {
                this.selectAllGroup(group);
            }
        }
    }
}

