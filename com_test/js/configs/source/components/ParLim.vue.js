const tpl = `
    <table>
        <thead>
            <tr>
                <th>Minimum Width (in pixels)</th>
                <th>Maximum Width (in pixels)</th>
                <th>Maximum Bibles</th>
                <th>Minimum Bibles</th>
                <th>Initial Number of Parallel Bibles</th>
            </tr>
            <tr>
                <td>Minimum page width.&nbsp; Must start with 0 and be in ascending order.</td>
                <td>Maximum page width.&nbsp; Automatically calculated.</td>
                <td>Maximum allowable parallel Bibles at this width.</td>
                <td>Minimum number of parallel Bible selectors displayed at this width.</td>
                <td>Number of parallel Bible selectors to iniitally display when the app loads.</td>
            </tr>
        </thead>

        <tbody>
            <tr v-for='n in count'>
                <td>
                    <v-text-field 
                        v-model='modelValue[idx(n)].minWidth' 
                        :rules="[required, value => checkMinWidth(value, n) ]"  
                        :read-only="n==1"
                    />
                </td>
                <td>
                    <v-text-field read-only :value='calcMax(n)' />
                </td>
                <td>
                    <v-text-field 
                        v-model='modelValue[idx(n)].maxBibles' 
                        :rules="[ required ]"  
                    />
                </td>
                <td>
                    <v-text-field v-model='modelValue[idx(n)].minBibles' />
                </td>
                <td>
                    <v-text-field v-model='modelValue[idx(n)].startBibles' />
                </td>
            </tr>
        </tbody>

    </table>

    <v-btn v-if='count < max' @click='count ++'>Add</v-btn>
    <v-btn v-if='count > min' @click='count --'>Remove</v-btn>
`;

// :todo this needs validation!!


export default {
    inject: ['bootstrap'],
    props: ['modelValue'],
    emits: ['update:modelValue'],
    template: tpl,
    data() {
        return {
            min: 1,
            max: 9999, // unlimited Bibles here (not in new UI)
            count: this.modelValue.length || 1,
            template: {
                minWidth: '',
                maxBibles: '',
                minBibles: '',
                startBibles: '',
            }
        }
    },
    watch: {
        count(is, was) {
            if(is < this.modelValue.length) {
                var nmv = this.modelValue.slice(0, is);
            } else {
                var nmv = this.modelValue;
                nmv.push(this.template);
            }
            
            this.$emit('update:modelValue', nmv);
        },
    },

    methods: {
        idx: (num) => num - 1,
        calcMax(num) {
            if(this.modelValue.length == num) {
                return '(infinite)';
            }

            // 'num' is the idx we need as we need to look forward one row
            return this.modelValue[num] && this.modelValue[num].minWidth ? this.modelValue[num].minWidth -1 : '';
        },
        required(value) {
            return value ? true : 'Required';
        },
        checkMinWidth(value, n) {
            console.log('checkMin', arguments);

            var val = parseInt(value, 10);

            if(n == 1) {
                return value == '0' ? true : 'Value must be 0';
            }

            var above = this.modelValue[ n - 2 ]?.minWidth || null;
            var below = this.modelValue[ n ]?.minWidth || null;

            if(above && val <= above) {
                return 'Must be greater than above ' + above;
            }            

            if(below && val >= below) {
                return 'Must be less than below ' + below;
            }

            // return  value > this.modelValue[ n - 2 ].minWidth ? true : 'Value must be greater than that in row above';

            return true;
        }
    }
}
