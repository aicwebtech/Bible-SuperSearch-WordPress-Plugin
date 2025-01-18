  // import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'
  // import { createVuetify } from 'https://cdn.jsdelivr.net/npm/vuetify@3.7.6/dist/vuetify.min.js'

const { createApp } = Vue
const { createVuetify } = Vuetify

  const vuetify = createVuetify();

  const app = createApp({
    template: `{{message}}`,
    data() {
      return {
        message: 'Hello Vue!'
      }
    }
  });

  app.use(vuetify).mount('#bss_config_app');