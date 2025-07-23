
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default; // safer with `.default`
window.swal = require("sweetalert2");

// for localization
// Only proceed if Vue is defined
if (window.Vue) {
    Vue.prototype.trans = function (string, args = {}) {
        let value = _.get(window.i18n, string, string); // fallback to string itself if not found
        _.eachRight(args, (paramVal, paramKey) => {
            value = _.replace(value, paramKey, paramVal);
        });
        return value;
    };
}

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
import VuePaginate from 'vue-paginate';
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/default.css';
import PrettyCheckbox from 'pretty-checkbox-vue';

import 'quill/dist/quill.core.css' // import styles
import 'quill/dist/quill.snow.css' // for snow theme
import 'quill/dist/quill.bubble.css' // for bubble theme

Vue.use(vueDebounce);
Vue.use(Vue2Filters);
Vue.use(BootstrapVue);
Vue.use(ToggleButton);
Vue.use(VueMoment);
Vue.use(VuePaginate);
Vue.use(PrettyCheckbox);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('VueSlider', VueSlider);
Vue.component('example', require('./components/Example.vue').default);
Vue.component('take-survey', require('./components/TakeSurvey.vue').default);
Vue.component('svea-checkout', require('./frontend/course/svea-checkout.vue').default);
Vue.component('publishing-list', require('./frontend/components/publishing-list.vue').default);
Vue.component('publishing-service-checkout', require('./frontend/publishing-service/checkout.vue').default);
Vue.component('service-calculator', require('./frontend/publishing-service/service-calculator.vue').default);
Vue.component('publishing-order', require('./frontend/publishing-service/publishing-order.vue').default);
Vue.component('course-checkout', require('./frontend/course/checkout.vue').default);
Vue.component('gift-course-checkout', require('./frontend/gift/course-checkout.vue').default);
Vue.component('gift-shop-manuscript-checkout', require('./frontend/gift/shop-manuscript-checkout.vue').default);
Vue.component('shop-manuscript-checkout', require('./frontend/shop-manuscript/checkout.vue').default);
Vue.component('course-upgrade', require('./frontend/upgrade/course.vue').default);
Vue.component('manuscript-upgrade', require('./frontend/upgrade/manuscript.vue').default);
Vue.component('assignment-upgrade', require('./frontend/upgrade/assignment.vue').default);
Vue.component('coaching-time-checkout', require('./frontend/coaching-time/checkout.vue').default);
Vue.component('order-history', require('./frontend/components/order-history.vue').default);
Vue.component('time-register', require('./backend/TimeRegister.vue').default);
Vue.component('project', require('./backend/project/list.vue').default);
Vue.component('project-details', require('./backend/project/details.vue').default);
Vue.component('project-whole-book', require('./backend/project/whole-book.vue').default);
Vue.component('project-books', require('./backend/project/books.vue').default);
Vue.component('project-tasks', require('./backend/project/tasks.vue').default);
Vue.component('project-time-register', require('./backend/project/time-register.vue').default);
Vue.component('project-notes', require('./backend/project/notes.vue').default);
Vue.component('publishing-services', require('./backend/publishing-package/services.vue').default);
Vue.component('assemble-book', require('./backend/assemble-books/list.vue').default);
Vue.component('service-order-calculator', require('./frontend/services/calculator.vue').default);
Vue.component('group-assignment', require('./frontend/components/group-assignment.vue').default);
Vue.component('editor-project-time-register', require('./editor/project/time-register.vue').default);
Vue.component('chat', require('./frontend/chat/index.vue').default);
Vue.component('v-select', vSelect);
Vue.use(VueQuillEditor);

new Vue({
    el: '#app-container'
});
