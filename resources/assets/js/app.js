
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

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

Vue.use(vueDebounce);
Vue.use(Vue2Filters);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('take-survey', require('./components/TakeSurvey.vue'));
Vue.component('course-checkout', require('./frontend/course/checkout.vue'));

new Vue({
    el: '#app-container'
});
