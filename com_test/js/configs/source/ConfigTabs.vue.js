import ConfigTabItem from './ConfigTabItem.vue.js';

const tpl = `
    <v-form v-model='formValid'>
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
                    <ConfigTabItem :tab='tab' :options='options'></ConfigTabItem>
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
    computed: {
        tabList() {
            var tabs = this.bootstrap.tabs;

            // tabs.push({name: 'Documentation', id: 'docs'});
            tabs.push({name: 'API', id: 'api'});

            return tabs;
        }
    }
}