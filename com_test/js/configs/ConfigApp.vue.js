const { createApp } = Vue
const { createVuetify } = Vuetify

import ConfigTabs from './source/ConfigTabs.vue.js';

const vuetify = createVuetify();

const app = createApp({
    template: `
        <ConfigTabs></ConfigTabs>
        `,
    components: {
        ConfigTabs
    },
});

app.use(vuetify)
    .provide('bootstrap', bootstrap)
    .mount('#bss_config_app');