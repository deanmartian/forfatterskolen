
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.swal = require("sweetalert2");

// for localization
Vue.prototype.trans = string => _.get(window.i18n, string);

Vue.prototype.trans = (string, args) => {
    let value = _.get(window.i18n, string);
    _.eachRight(args, (paramVal, paramKey) => {
        value = _.replace(value, paramKey, paramVal);
    });
    return value;
};

import vueDebounce from 'vue-debounce'
import toasted from './toasted'
import Vue2Filters from 'vue2-filters'
import './global'
import BootstrapVue from 'bootstrap-vue'
import ToggleButton from 'vue-js-toggle-button'
import VueMoment from 'vue-moment'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import VueQuillEditor from 'vue-quill-editor'

import 'quill/dist/quill.core.css' // import styles
import 'quill/dist/quill.snow.css' // for snow theme
import 'quill/dist/quill.bubble.css' // for bubble theme

Vue.use(vueDebounce);
Vue.use(Vue2Filters);
Vue.use(BootstrapVue);
Vue.use(ToggleButton);
Vue.use(VueMoment)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('take-survey', require('./components/TakeSurvey.vue'));
Vue.component('svea-checkout', require('./frontend/course/svea-checkout.vue'));
Vue.component('course-checkout', require('./frontend/course/checkout.vue'));
Vue.component('gift-course-checkout', require('./frontend/gift/course-checkout.vue'));
Vue.component('gift-shop-manuscript-checkout', require('./frontend/gift/shop-manuscript-checkout.vue'));
Vue.component('shop-manuscript-checkout', require('./frontend/shop-manuscript/checkout.vue'));
Vue.component('course-upgrade', require('./frontend/upgrade/course.vue'));
Vue.component('manuscript-upgrade', require('./frontend/upgrade/manuscript.vue'));
Vue.component('assignment-upgrade', require('./frontend/upgrade/assignment.vue'));
Vue.component('coaching-time-checkout', require('./frontend/coaching-time/checkout.vue'));
Vue.component('order-history', require('./frontend/components/order-history.vue'));
Vue.component('time-register', require('./backend/TimeRegister.vue'));
Vue.component('project', require('./backend/project/list.vue'));
Vue.component('project-details', require('./backend/project/details.vue'));
Vue.component('v-select', vSelect);
Vue.use(VueQuillEditor);

new Vue({
    el: '#app-container'
});
