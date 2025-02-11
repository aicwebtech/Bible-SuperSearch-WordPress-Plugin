import ConfigTabItem from './ConfigTabItem.vue.js';

const tpl = `
    <v-form v-model='formValid' @submit.prevent @submit='submit'>
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
                        <v-btn class="mt-2" type="submit" :color="formValidFalse ? 'error' : 'primary'">Submit</v-btn>
                    </v-sheet>    
                    <br />
                    <ConfigTabItem :tab='tab' :options='options'></ConfigTabItem>
                    <br />
                    <v-sheet class='text-center'>
                        <v-btn class="mt-2" type="submit">Submit</v-btn>
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
        // if(this.options.newConfigSave == 'false' && this.options.parallelBibleLimitByWidth.length > 0) {
        //     this.options.parallelBibleLimitByWidthEnable = true;
        // }
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
        submit() {
            console.log('submit');

            var formData = this.options
            formData.tab = 'all';
            formData.newConfigSave = true;
            
            console.log(formData);

            axios({
                method: 'post',
                url: this.bootstrap.configUrl,
                data: formData
            }).then((response) => {
                console.log('response', response);
                this.formValid = true;
            }).catch((error) => {
                console.log('error', error);
                this.formValid = false;
            });
        }
    }
}