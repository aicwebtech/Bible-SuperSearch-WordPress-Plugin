
const tpl = `
    <v-select>
        <template v-slot:item="{ props, item }">
            <v-list-subheader v-if="item.props.role == 'header'">
                {{ item.props.title }}
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
        // 'modelValue'
        // 'items'
    ],
    // emits: ['update:modelValue'],
    template: tpl,
    components: {

    },
    data() {
        return {

        }
    },
    methods: {

    }
}

