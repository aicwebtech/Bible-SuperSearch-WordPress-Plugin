import ConfigTabItem from './ConfigTabItem.vue.js';

const tpl = `
    <v-form v-model='formValid' @submit.prevent='submit'>
        <v-sheet v-if='formValid === false' class='text-center'>
            <v-alert type="error">Form is not valid</v-alert>
        </v-sheet>
    
        <v-tabs v-model="selectedTab">
            <v-tab 
                v-for='tab in tabList'
                :value='tab.id'
            >{{tab.name}}</v-tab>
        </v-tabs>

        <v-tabs-window v-model='selectedTab'>
            <v-tabs-window-item
                v-for='tab in tabList'
                :value='tab.id'
                :class='"pa-4 " + bootstrap.classes.tabs'
            >
                <v-sheet v-if='tab.type == "config"'>
                    <v-sheet class='text-center'>
                        <v-btn class="mt-2" type="submit" :color="formValidFalse ? 'error' : 'primary'">Save Changes</v-btn>
                    </v-sheet>    
                    <br /><br />
                    <ConfigTabItem :tab='tab' :options='options'></ConfigTabItem>
                    <br /><br />
                    <v-sheet class='text-center'>
                        <v-btn class="mt-2" type="submit" :color="formValidFalse ? 'error' : 'primary'">Save Changes</v-btn>
                    </v-sheet>
                </v-sheet>
                <v-sheet v-else>
                    Static tab: {{tab.name}}

                    {{options}}
                </v-sheet>
            </v-tabs-window-item>
        </v-tabs-window>
    </v-form>
    `;

export default {
    inject: ['bootstrap'],
    template: tpl,
    components: {
        ConfigTabItem
    },
    data() {
        return {
            formValid: false,
            selectedTab: 'general',
            options: this.bootstrap.options
        }
    },
    mounted() {
        if(!this.options._newConfigSave && this.options.parallelBibleLimitByWidth.length > 0) {
            this.options.parallelBibleLimitByWidthEnable = true;
        }
    },
    computed: {
        tabList() {
            var tabs = this.bootstrap.tabs;
            // tabs.push({name: 'API', id: 'api'}); // todo: add API / dashboard tab
            return tabs;
        },
        formValidFalse() {
            return this.formValid === false;
        }
    },
    methods: {
        async submit(e) {
            await e; // wait for the event to finish

            if(this.formValid !== true) {
                window.console && console.log('Form is not valid');
                return;
            }

            var formData = this.options
            formData._tab = 'all';
            formData._newConfigSave = true;

            if(formData.enableAllLanguages) {
                formData.languageList = [];
            }

            if(formData.enableAllBibles) {
                formData.enabledBibles = [];
            }
            
            this._submitHelper(formData);
        },
        tmpReset() {
            if(this.formValid !== true) {
                window.console && console.log('Form is not valid');
                return;
            }

            var formData = this.options
            formData._tab = 'all';
            formData._newConfigSave = false;

            if(formData.enableAllLanguages) {
                formData.languageList = [];
            }

            if(formData.enableAllBibles) {
                formData.enabledBibles = [];
            }
            
            this._submitHelper(formData);
        },
        _submitHelper(formData) {
            var headers = this.bootstrap.configHttpHeaders || {};

            axios({
                method: 'post',
                url: this.bootstrap.configUrl,
                data: formData,
                headers: headers
            }).then((response) => {
                if(response?.data?.refresh) {
                    alert('Settings saved, page will refresh');
                    this.refreshStatics();
                } else {
                    alert('Settings saved');
                }
            }).catch((error) => {
                window.console && console.log('error', error);
            });
        },
        refreshStatics() {
            location.reload();
        }
    }
}