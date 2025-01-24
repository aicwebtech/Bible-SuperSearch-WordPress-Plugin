
const tpl = `
    <v-select 
        v-for='n in count' 
        v-model='modelValue[idx(n)]' 
        v-bind='$attrs'
        :multiple='false' 
        @update:modelValue='updateModelValue(n, $event)'
    >
        
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
    props: ['modelValue'],
    emits: ['update:modelValue'],
    template: tpl,
    data() {
        return {
            min: 2,
            max: 10,
            count: 4
        }
    },
    watch: {
        count(is, was) {
            if(is < this.modelValue.length) {
                var nmv = this.modelValue.slice(0, is);
                this.$emit('update:modelValue', nmv);
            }
        }
    },

    methods: {
        idx: (num) => num - 1,
        updateModelValue(n, event) {
            console.log('updateModelValue', n, event); 
            var mv = this.modelValue;


        },
    }
}

