/* eslint-disable */ 
import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import ApiService from "./common/api.service";
//import MockService from "./common/mock/mock.service";
import { VERIFY_AUTH } from "./store/auth.module";
Vue.config.productionTip = false;
// Global 3rd party plugins
import "bootstrap";
import "popper.js";
import "tooltip.js";
import "perfect-scrollbar";
// Vue 3rd party plugins
import i18n from "./common/plugins/vue-i18n";
import vuetify from "./common/plugins/vuetify";
import "./common/plugins/bootstrap-vue";
import "./common/plugins/perfect-scrollbar";
import "./common/plugins/highlight-js";
import "@babel/polyfill";
import "@mdi/font/css/materialdesignicons.css";
import BlockUI from 'vue-blockui';
Vue.use(BlockUI);



// API service init
ApiService.init();

// Remove this to disable mock API
//MockService.init();
const DEFAULT_TITLE = 'VIP Coupon';

// Ensure we checked auth before each page load.
router.beforeEach((to, from, next) => {
  Promise.all([store.dispatch(VERIFY_AUTH)]).then(res => {

    Vue.nextTick(() => {
      document.title = to.meta.title || DEFAULT_TITLE;
    });

    if (to.matched.some(record => record.meta.requiresAuth) && !store.getters.isAuthenticated) {
      next({ name: "login" })
    }
    else {
      next()
    }
  });

  // Scroll page to top on every route change
  setTimeout(() => {
    window.scrollTo(0, 0);
  }, 100);
});

new Vue({
  router,
  store,
  i18n,
  vuetify,
  render: h => h(App)
}).$mount("#app");
