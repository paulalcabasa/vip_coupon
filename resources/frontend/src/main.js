/* eslint-disable */ 
import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import ApiService from "./common/api.service";
import JwtService from "./common/jwt.service";
import LocalStorageService from "./common/localstorageservice";
//import MockService from "./common/mock/mock.service";
import { VERIFY_AUTH, REFRESH_TOKEN } from "./store/auth.module";
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
import VueSweetalert2 from 'vue-sweetalert2';
import VueProgressBar from 'vue-progressbar';
import axios from 'axios';
Vue.use(BlockUI);
Vue.use(VueSweetalert2);

Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '2px'
});


// Add a request interceptor
axios.interceptors.request.use(function (config) {
  const token = JwtService.getToken();
  config.headers.Authorization = "Bearer " + token;
  return config;
}, function (error) {
  // Do something with request error
  return Promise.reject(error);
});

axios.interceptors.response.use(function (response) {
  // Any status code that lie within the range of 2xx cause this function to trigger
  // Do something with response data
  return response;
}, function (error) {
  // Any status codes that falls outside the range of 2xx cause this function to trigger
  // Do something with response error

  if (error.response.status === 401) {
    Promise.all([store.dispatch(REFRESH_TOKEN)]).then(res => {
      error.config['headers']['Authorization'] = "Bearer " + JwtService.getToken();
      return axios(error.config);
    });
  }  
  
  
    return Promise.reject(error);
});


 
// API service init
ApiService.init();


// Ensure we checked auth before each page load.
router.beforeEach((to, from, next) => {
  
  Promise.all([store.dispatch(VERIFY_AUTH)]).then(res => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
      if(!store.getters.isAuthenticated){
        next({ name: 'login' });
      }
      else {
        next()
      }
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
