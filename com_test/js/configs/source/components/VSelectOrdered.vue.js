
const tpl = `
    <v-select :bultiple='false' v-for='n in count' v-model='modelValue[idx(n)]' v-bind='$attrs'>
        
        <template v-slot:item="{ props, item }">
            <v-list-subheader v-if="item.props.role == 'header'">
                {{ item.props.title }}
            </v-list-subheader>
            <v-divider v-else-if="item.props.role == 'divider'" />
            <v-list-item v-else v-bind="props"></v-list-item>
        </template>
    </v-select>
    <v-btn v-if='count < max' @click='count ++'>Add</v-btn>
    <v-btn v-if='count > min' @click='count --'>Rem</v-btn>
`;


export default {
    inject: ['bootstrap'],
    props: [
        'modelValue'
        // 'items'
    ],
    // emits: ['update:modelValue'],
    template: tpl,
    components: {

    },
    data() {
        return {
            min: 2,
            max: 10,
            count: 4
        }
    },
    methods: {
        idx: (num) => num - 1,

    }
}

