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
                        :ref="(el) => storeRef('minWidth', n, el)"
                    />
                </td>
                <td>
                    <v-text-field read-only :value='calcMax(n)' />
                </td>
                <td>
                    <v-text-field 
                        v-model='modelValue[idx(n)].maxBibles' 
                        :rules="[ required, value => checkMaxBibles(value, n) ]"  
                        :ref="(el) => storeRef('maxBibles', n, el)"
                        @update:modelValue='updateModelValue("maxBibles", n, $event)'
                    />
                </td>
                <td>
                    <v-text-field 
                        v-model='modelValue[idx(n)].minBibles' 
                        :rules="[ required, value => checkMinBibles(value, n) ]"  
                        :ref="(el) => storeRef('minBibles', n, el)"
                        @update:modelValue='updateModelValue("minBibles", n, $event)'
                    />
                </td>
                <td>
                    <v-text-field 
                        v-model='modelValue[idx(n)].startBibles' 
                        :rules="[ required, value => checkStartBibles(value, n) ]"  
                        :ref="(el) => storeRef('startBibles', n, el)"
                        @update:modelValue='updateModelValue("startBibles", n, $event)'
                    />
                </td>
            </tr>
        </tbody>

    </table>

    <v-btn v-if='count < max' @click='count ++'>Add</v-btn>
    <v-btn v-if='count > min' @click='count --'>Remove</v-btn>
`;

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
            formValid: false,
            formRefs: {},
            validating: {},
            validatingDefault: {
                minWidth: false,
                maxBibles: false,
                minBibles: false,
                startBibles: false,
            },
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

    mounted() {
        console.log('formRefs', this.formRefs);
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
        storeRef(key, n, el) {
            if(!el) return;

            this.formRefs[key] = this.formRefs[key] || [];
            this.formRefs[key][n - 1] = el;
        },  
        required(value) {
            return value ? true : 'Required';
        },
        checkMinWidth(value, n) {
            var val = parseInt(value, 10);

            if(n == 1) {
                return value == '0' ? true : 'Value must be 0';
            }

            if(val < 1) {
                return 'Must be greater than 0';
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
        },
        checkMaxBibles(value, n) {
            var val = parseInt(value, 10);

            if(val < 1) {
                return 'Must be greater than 0';
            }

            var above = this.modelValue[ n - 2 ]?.maxBibles || null;
            var below = this.modelValue[ n ]?.maxBibles || null;
            var min = this.modelValue[ n - 1 ]?.minBibles || null;
            var start = this.modelValue[ n - 1 ]?.startBibles || null;

            if(above && val < above) {
                return 'Must be greater than or equal to above ' + above;
            }            

            if(below && val > below) {
                return 'Must be less than or equal to below ' + below;
            }

            if(min && val < min) {
                return 'Must be greater than or equal to min Bibles ' + min;
            }

            if(start && val < start) {
                return 'Must be greater than or equal to start Bibles ' + start;
            }

            // this.resetValidating('maxBibles');
            this.validateField('minBibles', n);
            this.validateField('startBibles', n);

            return true;
        },
        checkMinBibles(value, n) {
            var val = parseInt(value, 10);

            if(val < 1) {
                return 'Must be greater than 0';
            }

            var above = this.modelValue[ n - 2 ]?.minBibles || null;
            var below = this.modelValue[ n ]?.minBibles || null;
            var max = this.modelValue[ n - 1 ]?.maxBibles || null;
            var start = this.modelValue[ n - 1 ]?.startBibles || null;

            if(above && val < above) {
                return 'Must be greater than or equal to above ' + above;
            }            

            if(below && val > below) {
                return 'Must be less than or equal to below ' + below;
            }

            if(max && val > max) {
                return 'Must be less than or equal to max Bibles ' + max;
            }

            if(start && val > start) {
                return 'Must be less than or equal to start Bibles ' + start;
            }

            // this.resetValidating('minBibles');
            this.validateField('maxBibles', n);
            this.validateField('startBibles', n);

            return true;
        },
        checkStartBibles(value, n) {
            var val = parseInt(value, 10);

            if(val < 1) {
                return 'Must be greater than 0';
            }

            var above = this.modelValue[ n - 2 ]?.startBibles || null;
            var below = this.modelValue[ n ]?.startBibles || null;
            var min = this.modelValue[ n - 1 ]?.minBibles || null;
            var max = this.modelValue[ n - 1 ]?.maxBibles || null;

            if(above && val < above) {
                return 'Must be greater than or equal to above ' + above;
            }            

            if(below && val > below) {
                return 'Must be less than or equal to below ' + below;
            }

            if(min && val < min) {
                return 'Must be greater than or equal to min Bibles ' + min;
            }

            if(max && val > max) {
                return 'Must be less than or equal to max Bibles ' + max;
            }

            // this.resetValidating('minBibles');
            this.validateField('minBibles', n);
            this.validateField('maxBibles', n);

            return true;
        },

        resetValidating(key) {
            this.validating = this.validatingDefault;

            if(key) {
                this.validating[key] = true;
            }
        },

        updateModelValue(key, n, event) {
                console.log('updateModelValue', key, n); 
                // var mv = this.modelValue;

                // mv[n - 1][key] = event;

                // this.$emit('update:modelValue', mv);

            this.resetValidating(key);
        },

        validateField(key, n) {
            if(this.validating[key]) {
                console.log('already validating', key);
                return;
            }

            this.validating[key] = true;
            
            var el = this.formRefs[key][n - 1];

            if(!el) {
                console.log('no element', key, n);
                return;
            }

            el.validate();
        }

    }
}
