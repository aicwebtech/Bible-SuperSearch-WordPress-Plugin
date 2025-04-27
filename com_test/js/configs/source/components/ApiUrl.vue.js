
const tpl = `
    <v-text-field
        :modelValue="modelValue"
        @update:modelValue='updateModelValue'
        @blur='inputBlur'
        :disabled='loading'
    >
        <template v-if='changeSuccess' v-slot:prepend-inner>
            <v-icon color='success' icon='mdi-check'></v-icon>
        </template>
    </v-text-field>
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
        },
        inputBlur(event) {
            console.log('API URL blurred');
            var t = this,
                nv = this.newValue,
                ov = this.oldValue;
                nv = nv.replace(/\/+$/, ''); // strip trailing /

                // Force HTTPS if using main API
                if(nv === 'http://api.biblesupersearch.com') {
                    nv = 'https://api.biblesupersearch.com';
                }

            if(this.valueChanged) {
                this.valueChanged = false;

                if(nv) {
                    t.$emit('update:modelValue', nv); // provisionally set to new value?
                    t.loading = true;

                    axios.request({
                        url: nv + '/api/version',
                        method: 'POST',
                        headers: {
                            // 'X-Requested-With': 'XMLHttpRequest', // causes CORS errors?
                            // 'Content-Type': 'application/json' // causes CORS errors?
                        },
                        withCredentials: false
                    }).then(function(response) {
                        t.loading = false;

                        var data = response.data,
                            valid = true,
                            version = false;

                        if(!data.results || !data.results.name || !data.results.version || data.error_level != 0 || !Array.isArray(data.errors)) {
                            valid = false;
                        } else {
                            version = parseFloat(data.results.version);

                            // Perhaps there is a better way
                            if(version >= 5 && data.results.hash != 'fd9f996adfe0beb419a5a40b2adaf573baf55464f7c2c9101b4d7ce6e42310cf') {
                                valid = false;
                            }
                        }

                        if(!valid) {
                            alert('Error:\nURL \'' + nv + '\' does not appear to be an instance of \nthe Bible SuperSearch API, reverting to original.');
                            t.$emit('update:modelValue', ov);
                        } else {
                            t.changeSuccess = true;
                            t.$emit('update:modelValue', nv);
                        }

                    }).catch(function(error) {
                        t.loading = false;
                        alert('Error:\nCannot load URL \'' + nv + '\',\nreverting to original.');
                        t.$emit('update:modelValue', ov);
                    });
                } else {
                    t.$emit('update:modelValue', 'https://api.biblesupersearch.com');
                }
            }
        }
    }
}
