const { createApp } = Vue
const { createVuetify } = Vuetify

import ConfigTabs from '../com_test/js/configs/source/ConfigTabs.vue.js';

const vuetify = createVuetify();

const app = createApp({
    template: `
        <ConfigTabs></ConfigTabs>
        `,
    components: {
        ConfigTabs
    },
});

biblesupersearch_config_bootstrap.configUrl = wpApiSettings.root + 'biblesupersearch/v1/config';
// biblesupersearch_config_bootstrap.configUrl = '/wp-admin/options.php';
axios.defaults.headers.common['X-WP-Nonce'] = wpApiSettings.nonce;

app.use(vuetify)
    .provide('bootstrap', biblesupersearch_config_bootstrap)
    .mount('#bss_config_app');