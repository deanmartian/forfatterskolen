<template>
    <div class="card main-card">
        <div id="scrollhere"></div>
        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="'Til betaling'" :backButtonText="trans('site.paginate.previous')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="" ref="wizard">

            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list" :before-change="validateOrder">
                <div class="row">
                    <div class="col-md-6">
                        <div class="gray-box">
                            <div class="row">
                                <div class="col-md-8">
                                    <h1>
                                        {{ shopManuscript.title }}
                                    </h1>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="global-price">
                                        {{ shopManuscript.max_words }} {{ trans('site.learner.words-text') }}
                                    </h3>
                                </div>
                            </div>

                            <h3 class="mt-3 font-weight-bold">
                                {{ trans('site.front.our-course.show.package-details-text') }}:
                            </h3>

                            <p v-html="shopManuscript.description" class="mt-2">
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="mb-0">
                                {{ trans('site.front.form.upload-manuscript') }}
                            </label>

                            <!-- 'application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,' 
                            + 'application/pdf, application/vnd.oasis.opendocument.text' -->

                            <div v-if="orderForm.temp_file" class="temp-file-container">
                                {{ orderForm.temp_file.original_name }}
                                <button @click="removeFile">x</button>
                            </div>

                            <FileUpload
                            :accept="documentAcceptTypes"
                            @fileSelected="handleFileSelected('manuscript', $event)" v-else/>
                            <p v-if="isConvertingManuscript" class="text-info mt-2">
                                {{ conversionMessage }}
                            </p>
                            <input type="hidden" name="manuscript">

                            <div class="custom-checkbox mt-4">
                                <input type="checkbox" name="send_to_email" id="send_to_email"
                                        v-model="orderForm.send_to_email">
                                <label for="send_to_email" class="control-label">
                                    {{ trans('site.front.form.send-to-email') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-500">
                                {{ trans('site.front.genre') }}
                            </label>
                            <select class="form-control" name="genre" v-model="orderForm.genre" @change="genreChanged()">
                                <option value="" disabled="disabled" selected
                                        v-html="trans('site.free-text-evaluation.choose-genre')">
                                </option>

                                <option :value="type.id" v-for="type in assignmentTypes" v-text="type.name" 
                                    :key="'assignment-type-' + type.id">
                                </option>
                            </select>
                        </div> <!-- end genre -->

                        <div class="form-group">
                            <label class="mb-0">
                                {{ trans('site.front.form.synopsis-optional') }}
                            </label>
                            <FileUpload
                            :accept="documentAcceptTypes"
                            @fileSelected="handleFileSelected('synopsis', $event)"/>
                            <input type="hidden" name="synopsis">
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.front.form.coaching-time-later-in-manus') }}</label>
                            <toggle-button :labels="{checked: trans('site.front.yes'), unchecked: trans('site.front.no')}"
                                            :width="60" :height="30" :font-size="12"
                                            :color="{checked:'#5F0000', unchecked:'#CCCCCC'}" class="mt-2 ml-2"
                                            v-model="orderForm.coaching_time_later">
                            </toggle-button>
                        </div>

                        <div class="form-group">
                            <label for="">
                                {{ trans('site.front.form.manuscript-description') }}
                            </label>
                            <textarea name="description" id="" cols="30" rows="7" class="form-control"
                                        v-model="orderForm.description"></textarea>
                        </div>

                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <td>{{ trans('site.front.price') }}:</td>
                                <td class="text-right" style="width: 150px">
                                    {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>

                            <tr v-if="orderForm.totalDiscount > 0">
                                <td>{{ trans('site.front.discount') }}:</td>
                                <td class="text-right">
                                    {{ orderForm.totalDiscount | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>

                            <tr v-if="orderForm.has_vat">
                                <td>Mva 25%:</td>
                                <td class="text-right">
                                    {{ orderForm.additional | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>

                            <tr>
                                <td>{{ trans('site.front.total') }}:</td>
                                <td class="text-right" style="width: 150px">
                                    {{ totalPrice | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </tab-content>

            <tab-content :title="trans('site.front.form.user-information')" icon="fas fa-id-card"
                         :before-change="validateForm" style="min-height: 300px">

                <template v-if="!currentUser">

                    <button class="btn btn-default" @click="toggleNewCustomer()" v-if="isNewCustomer"
                            style="margin-bottom: 10px">
                        {{ trans('site.back') }}
                    </button>

                    <form @submit.prevent="handleLogin($event)" v-if="!isNewCustomer" class="second-col mb-4">

                        <p class="text-center" v-html="trans('site.login-or-register-below')">
                        </p>

                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3">
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control no-border-left"
                                           :placeholder="trans('site.front.form.email')" v-model="loginForm.email">
                                </div>

                                <div class="form-group">
                                    <input type="password" name="password" :placeholder="trans('site.front.form.password')"
                                           class="form-control no-border-left" v-model="loginForm.password">
                                </div>

                                <div class="form-group clearfix">
                                    <a href="/auth/login?t=passwordreset" class="no-underline pull-left">
                                        {{ trans('site.front.login.password-reset') }}
                                    </a>
                                    <a href="/auth/login?t=password-change" class="no-underline pull-right">
                                        {{ trans('site.front.login.change-password') }}
                                    </a>
                                </div>

                                <button type="submit" class="btn btn-dark site-btn btn-block"
                                        :disabled="isLoginDisabled">
                                    <i class="fas fa-spinner fa-spin" v-if="isLoginDisabled"></i>
                                    {{ loginText }}
                                </button>

                                <a :href="'/auth/login/facebook'" class="btn site-btn btn-block fb-link">
                                    {{ trans('site.front.form.login-with-facebook') }}
                                </a>

                                <a :href="'/auth/login/google'" class="btn site-btn btn-block google-link">
                                    {{ trans('site.front.form.login-with-google') }}
                                </a>

                                <button class="btn btn-dark-red site-btn btn-block" type="button" @click="toggleNewCustomer()">
                                    {{ trans('site.front.login.register') }}
                                </button>
                            </div> <!-- end col-sm-6 -->

                        </div><!-- end row -->

                        <div class="row">
                            <div class="col-sm-12">
                            <span class="text-danger invalid-credentials" v-if="invalidCred">
                                <i class="fas fa-exclamation-circle"></i>
                                <span v-html="errorMsg"></span>
                            </span>
                            </div>
                        </div>
                    </form> <!-- end login form -->

                </template> <!-- end not logged in user -->

                <template v-if="currentUser || isNewCustomer">
                    <wizard-button  v-if="wizardProps.activeTabIndex > 0"  @click.native="wizardProps.prevTab();" 
                        class="back-btn">
                        {{ trans('site.back') }}
                    </wizard-button>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="gray-box">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h1>
                                            {{ shopManuscript.title }}
                                        </h1>
                                    </div>
                                    <div class="col-md-4">
                                        <h3 class="global-price">
                                            {{ shopManuscript.max_words }} {{ trans('site.learner.words-text') }}
                                        </h3>
                                    </div>
                                </div>

                                <h3 class="mt-3 font-weight-bold">
                                    {{ trans('site.front.our-course.show.package-details-text') }}:
                                </h3>

                                <p v-html="shopManuscript.description" class="mt-2">
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="control-label">
                                    {{ trans('site.front.form.email') }}
                                </label>
                                <input type="email" id="email" class="form-control" name="email" required
                                    v-model="orderForm.email"
                                    :disabled="currentUser"
                                    :placeholder="trans('site.front.form.email')">
                            </div> <!-- end email form-group -->

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="first_name" class="control-label">
                                        {{ trans('site.first-name') }}
                                    </label>
                                    <input type="text" id="first_name" class="form-control" name="first_name" required
                                        v-model="orderForm.first_name"
                                        :disabled="currentUser"
                                        :placeholder="trans('site.first-name')">
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="control-label">
                                        {{ trans('site.last-name') }}
                                    </label>
                                    <input type="text" id="last_name" class="form-control" name="last_name" required
                                        v-model="orderForm.last_name"
                                        :disabled="currentUser"
                                        :placeholder="trans('site.last-name')">
                                </div>
                            </div> <!-- end first and last name -->

                            <div class="form-group">
                                <label for="street" class="control-label">
                                    {{ trans('site.front.form.street') }}
                                </label>
                                <input type="text" id="street" class="form-control" name="street" required
                                    v-model="orderForm.street"
                                    :placeholder="trans('site.checkout.street')">
                            </div> <!-- end street -->

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="zip" class="control-label">{{ trans('site.front.form.zip') }}</label>
                                    <input type="text" id="zip" class="form-control" name="zip" required
                                        v-model="orderForm.zip" :placeholder="trans('site.checkout.zip')">
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="control-label">{{ trans('site.front.form.city') }}</label>
                                    <input type="text" id="city" class="form-control" name="city" required
                                        v-model="orderForm.city" :placeholder="trans('site.checkout.city')">
                                </div>
                            </div> <!-- end zip, city -->

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="phone" class="control-label">
                                        {{ trans('site.front.form.phone-number') }}
                                    </label>
                                    <input type="text" id="phone" class="form-control" name="phone" required
                                        v-model="orderForm.phone" :placeholder="trans('site.checkout.phone')">
                                </div>

                                <div class="col-md-6" v-if="!currentUser">
                                    <label for="password" class="control-label">
                                        {{ trans('site.front.form.create-password') }}
                                    </label>
                                    <input type="password" id="password" class="form-control"
                                        name="password" required :placeholder="trans('site.front.form.create-password')"
                                        v-model="orderForm.password">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

            </tab-content>

            <tab-content :title="trans('site.checkout.payment-details')" icon="fas fa-credit-card"
                         :before-change="validateForm">
                <wizard-button class="back-btn" v-if="wizardProps.activeTabIndex > 0"  @click.native="wizardProps.prevTab();">
                    {{ trans('site.back') }}
                </wizard-button>

                <div id="checkout-display"></div>
            </tab-content>

            <template slot="footer" slot-scope="props">
                <div class="wizard-footer-left">
                    <!-- <wizard-button  v-if="props.activeTabIndex > 0 && !props.isLastStep"
                                    @click.native="props.prevTab(); scrollTop()"
                                    :style="props.fillButtonStyle">
                        {{ trans('site.back') }}
                    </wizard-button> -->
                </div>
                <div class="wizard-footer-right">
                    <template v-if="userHasPaidCourse">
                        <template v-if="props.activeTabIndex === 0">
                            <button type="button" class="vipps-btn" slot="custom-buttons-right" @click="vippsCheckout();"
                                    :disabled="isLoading">
                                <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i>
                                <span>Hurtigutsjekk med</span>
                                <img src="/images-new/vipps.png" class="inline" alt="vipps-buy-button"
                                    :style="isLoading ? 'opacity: .8;' : ''">
                            </button>
                        </template>

                        <template v-if="!currentUser || (currentUser && currentUser.could_buy_course)">
                            <wizard-button v-if="!props.isLastStep" @click.native="handleNextTab(props)"
                            class="wizard-footer-right"
                            :class="{'w-100': props.activeTabIndex === 1 }"
                                        :style="props.fillButtonStyle" :disabled="(!currentUser && !isNewCustomer
                                        && props.activeTabIndex > 0) || isLoadingSubmit || isConvertingManuscript">
                                <i class="fa fa-pulse fa-spinner" v-if="isLoadingSubmit"></i> Til betaling
                            </wizard-button>

                            <!-- v-else before -->
                            <wizard-button v-if="props.isLastStep && !isSveaPayment" @click.native="props.nextTab()" 
                                class="wizard-footer-right finish-button"
                                        :style="props.fillButtonStyle" :disabled="isLoading && props.isLastStep">
                                <i class="fa fa-pulse fa-spinner" v-if="isLoading && props.isLastStep"></i>
                                {{props.isLastStep ? trans('site.front.buy')
                                : trans('site.learner.next-text')}}</wizard-button>
                        </template>

                        <template v-if="props.activeTabIndex === 0">
                            <br>
                            <span class="d-block mt-3">
                                {{ trans('site.front.checkout.note') }}
                            </span>
                        </template>
                    </template>
                    <template v-else>
                        <wizard-button v-if="!props.isLastStep" @click.native="props.nextTab(); scrollTop()"
                            class="wizard-footer-right w-100" :style="props.fillButtonStyle"
                            :disabled="isConvertingManuscript">
                                Bestill
                        </wizard-button>
                    </template>

                </div>
            </template> <!-- end buttons slot -->

            <button slot="finish" class="d-none">{{ trans('site.checkout.finish') }}</button>

        </form-wizard>
    </div>
</template>

<script>
import mammoth from 'mammoth/mammoth.browser';
import FileUpload from '../../components/FileUpload.vue';
    export default {

        props: {
            user: Object,
            shopManuscript: Object,
            assignmentTypes: Array,
            userHasPaidCourse: Boolean,
            origPrice: [Number, String],
            tempFile: Object,
        },

        data() {
            const initialBasePrice = parseFloat(this.origPrice || this.shopManuscript.full_payment_price) || 0;
            const hasPaidCourseInitial = typeof this.userHasPaidCourse === 'boolean'
                ? this.userHasPaidCourse
                : null;
            const applyVatInitially = hasPaidCourseInitial === false;

            return {
                currentUser: this.user,
                orderForm: {
                    email: '',
                    first_name: '',
                    last_name: '',
                    street: '',
                    zip: '',
                    city: '',
                    phone: '',
                    password: '',
                    package_id: 0,
                    price: initialBasePrice,//this.shopManuscript.full_payment_price,
                    payment_plan_id: 8,
                    payment_mode_id: 3,
                    mobile_number: "",
                    totalDiscount: 0,
                    send_to_email: false,
                    genre: '',
                    description: '',
                    coaching_time_later: false,
                    word_count: null,
                    item_type: 2,
                    shop_manuscript_id: this.shopManuscript.id,
                    has_vat: applyVatInitially,
                    //is_pay_later: !this.userHasPaidCourse,
                    additional: applyVatInitially ? (initialBasePrice * .25) : 0,
                    excess_words_amount: 0,
                    temp_file: this.tempFile
                },
                currencyOptions: {
                    thousandsSeparator: '.',
                    decimalSeparator: ',',
                    spaceBetweenAmountAndSymbol: true
                },
                loginForm: {
                    email: '',
                    password: ''
                },
                initialBasePrice,
                originalPrice: initialBasePrice,
                isSveaPayment: true,
                invalidCred: false,
                isLoginDisabled: false,
                loginText: i18n.site.front.form.login,
                isNewCustomer: false,
                manuscriptName: i18n.site['learner.files-text'],
                synopsisName: i18n.site['learner.files-text'],
                hasPaidCourse: hasPaidCourseInitial,
                isLoading: false,
                isLoadingSubmit: false,
                isConvertingManuscript: false,
                conversionMessage: 'Konverterer dokumentet… Vennligst vent.',
                wizardProps: {},
                requestUrl: '/shop-manuscript/'+this.shopManuscript.id,
                documentAcceptTypes: [
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/pdf',
                    'application/vnd.oasis.opendocument.text',
                    'application/vnd.apple.pages',
                    'application/x-iwork-pages-sffpages',
                    '.doc',
                    '.docx',
                    '.pdf',
                    '.odt',
                    '.pages'
                ].join(',')
            }
        },

        computed: {
            totalPrice() {
                const price = parseFloat(this.orderForm.price) || 0;
                const discount = parseFloat(this.orderForm.totalDiscount) || 0;
                const additional = parseFloat(this.orderForm.additional) || 0;

                return price - discount + additional;
            }
        },

        components: {
            FileUpload,
        },

        methods: {

            getCurrentUser() {
                axios.get('/current-user').then(response => {

                    this.currentUser = response.data;
                })
            },

            loadOptions() {
                this.orderForm.email = this.currentUser ? this.currentUser.email : '';
                this.orderForm.first_name = this.currentUser ? this.currentUser.first_name : '';
                this.orderForm.last_name = this.currentUser ? this.currentUser.last_name : '';
                this.orderForm.street = this.currentUser && this.currentUser.address ? this.currentUser.address.street
                    : '';
                this.orderForm.zip = this.currentUser && this.currentUser.address ? this.currentUser.address.zip : '';
                this.orderForm.city = this.currentUser && this.currentUser.address ? this.currentUser.address.city : '';
                this.orderForm.phone = this.currentUser && this.currentUser.address ? this.currentUser.address.phone : '';
            },

            handleLogin(event) {
                this.isLoginDisabled = true;
                this.removeValidationError();

                axios.post('/auth/checkout/login', this.loginForm).then(response => {

                    window.location.href = window.location.pathname;

                }).catch(error => {
                    this.loginText = i18n.site.front.form.login;
                    this.isLoginDisabled = false;
                    if (error.response.status === 401) {
                        $('.validation-err').remove();
                        this.invalidCred = true;
                        this.errorMsg = error.response.data.error;
                    }

                    if (error.response.status === 422) {
                        const err_data = error.response.data;
                        $.each(err_data,function(k, v){
                            let element = $("[name="+k+"]");

                            // append error message after the element
                            element.after("<small class='text-danger validation-err'>" +
                                "<i class='fas fa-exclamation-circle'></i> " +
                                "<span>" + v+"</span></small>");
                        });
                    }
                });
            },

            validateOrder() {

                this.removeValidationError();
                this.isLoadingSubmit = true;
                return new Promise((resolve, reject) => {
                    let formData = new FormData();
                    $.each(this.orderForm, function(k, v) {
                        formData.append(k, v);
                    });
                    // Add your form data here if needed

                    axios.post(this.requestUrl + '/checkout/validate-order', formData)
                        .then(response => {
                            this.orderForm.excess_words_amount = response.data.excess_words_amount;
                            this.orderForm.price = response.data.price;
                            this.orderForm.price = parseFloat(this.orderForm.price) + response.data.excess_words_amount;
                            this.genreChanged(); // re-calculate for genres

                            // Delay and then resolve
                            setTimeout(() => {
                                this.scrollTop(); // Call scrollTop after the delay
                                this.isLoadingSubmit = false;
                                resolve(true);
                            }, 1500); // 1-second delay
                        })
                        .catch(error => {
                            this.processError(error);
                            this.scrollTop(); // Call scrollTop after the delay
                            this.isLoadingSubmit = false;
                            reject(false); // Reject to prevent tab change on error
                        });
                });

                /* this.removeValidationError();

                let formData = new FormData();
                $.each(this.orderForm, function(k, v) {
                    formData.append(k, v);
                });

                return axios.post(this.requestUrl+'/checkout/validate-order', formData).then(response => {
                    console.log(response);
                    this.orderForm.excess_words_amount = response.data.excess_words_amount;
                    this.orderForm.price = response.data.price;
                    this.orderForm.price = parseFloat(this.orderForm.price) + response.data.excess_words_amount;
                    console.log(this.orderForm.price);
                    
                    // Return a promise that resolves after a delay
                    return new Promise((resolve) => {
                        setTimeout(() => resolve(false), 3000);
                    });
                }).catch(error => {
                    this.processError(error);
                }); */
            },

            validateForm() {

                this.removeValidationError();

                let formData = new FormData();
                this.orderForm.payment_mode_id = 3; // Faktura
                $.each(this.orderForm, function(k, v) {
                    formData.append(k, v);
                });

                return axios.post(this.requestUrl+'/checkout/validate-form', formData).then(response => {
                    this.removeValidationError();
                    this.getCurrentUser();

                    if (response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    }

                    $("#checkout-display").html(response.data);
                    return true;

                }).catch(error => {

                    this.processError(error);

                });
            },

            vippsCheckout() {
                this.removeValidationError();

                let formData = new FormData();
                this.orderForm.payment_mode_id = 5; // Vipps
                $.each(this.orderForm, function(k, v) {
                    formData.append(k, v);
                });

                this.isLoading = true;
                console.log("vipps checkout here");
                return axios.post(this.requestUrl+'/checkout/vipps', formData).then(response => {
                    console.log(response);

                    if (response.data.redirect_link) {
                        window.location.href = response.data.redirect_link;
                        return;
                    }

                    this.isLoading = false;
                }).catch(error => {
                    this.processError(error);
                    this.isLoading = false;
                });
            },

            prevTab() {
                console.log("adfafd");
            },

            toggleNewCustomer() {
                this.isNewCustomer = !this.isNewCustomer;
            },

            handleNextTab(props) {
                // Call the next tab method
                props.nextTab();

                // Prevent scrollTop on the first page
                if (props.activeTabIndex !== 0) {
                    this.scrollTop();
                }
            },

            scrollTop() {
                jQuery([document.documentElement, document.body]).animate({
                    scrollTop: $("#scrollhere").offset().top
                }, 1000);
            },

            onManuscriptChange(e) {
                let files = e.target.files;

                if (!files.length)
                {
                    this.manuscriptName = i18n.site['learner.files-text'];
                    this.orderForm.manuscript = [];
                    return;
                }

                this.manuscriptName = files[0].name;
                this.orderForm.manuscript = files[0];

                $(".validation-err").remove();
            },

            onSynopsisChange(e) {
                let files = e.target.files;

                if (!files.length)
                {
                    this.synopsisName = i18n.site['learner.files-text'];
                    this.orderForm.synopsis = [];
                    return;
                }

                this.synopsisName = files[0].name;
                this.orderForm.synopsis = files[0];

                $(".validation-err").remove();
            },

            checkHasPaidCourse() {
                axios.get('/has-paid-course/').then(response => {
                    const resolvedValue = response && response.data;
                    this.hasPaidCourse = resolvedValue === true || resolvedValue === 1 || resolvedValue === '1';
                    this.updatePriceTotals();
                })
            },

            genreChanged() {
                this.updatePriceTotals();
            },

            updatePriceTotals() {
                let basePrice = parseFloat(this.originalPrice);

                if (!Number.isFinite(basePrice)) {
                    basePrice = parseFloat(this.shopManuscript.full_payment_price) || 0;
                }

                const excessAmount = parseFloat(this.orderForm.excess_words_amount) || 0;
                const genreId = parseInt(this.orderForm.genre, 10);

                let price = basePrice + excessAmount;
                const hasPaidCourse = this.hasPaidCourse === true;
                const appliesVat = this.hasPaidCourse === false;
                const totalDiscount = hasPaidCourse ? (basePrice * 0.05) : 0;

                if (genreId === 10) {
                    price += (price - totalDiscount) * 0.50;
                } else if (genreId === 17) {
                    price += (price - totalDiscount) * 0.30;
                }

                //this.orderForm.totalDiscount = totalDiscount;
                this.orderForm.price = price;
                this.orderForm.has_vat = appliesVat;
                this.orderForm.additional = appliesVat ? price * 0.25 : 0;
            },

            async handleFileSelected(type, file) {
                if (type === 'synopsis') {
                    this.orderForm.synopsis = file;
                } else {
                    if (!file) {
                        return;
                    }

                    let manuscriptFile = file;
                    let extension = this.getFileExtension(manuscriptFile);
                    let conversionStarted = false;

                    if (extension !== 'docx') {
                        this.isConvertingManuscript = true;
                        this.conversionMessage = 'Konverterer dokumentet… Vennligst vent.';
                        conversionStarted = true;
                        try {
                            manuscriptFile = await this.convertFileToDocx(file);
                            extension = this.getFileExtension(manuscriptFile);
                        } catch (error) {
                            this.orderForm.temp_file = null;
                            this.orderForm.manuscript = null;
                            this.orderForm.word_count = null;
                            this.isConvertingManuscript = false;
                            this.conversionMessage = 'Konverterer dokumentet… Vennligst vent.';

                            if (error && error.response) {
                                this.processError(error);
                            } else {
                                this.$toasted.global.showErrorMsg({
                                    message: 'Kunne ikke konvertere filen. Prøv igjen.'
                                });
                            }

                            return;
                        }
                    }

                    this.orderForm.manuscript = manuscriptFile;
                    let wordCount = null;

                    if (extension === 'docx') {
                        try {
                            wordCount = await this.extractWordCountWithMammoth(manuscriptFile);
                        } catch (error) {
                            console.error('Mammoth word count failed, falling back to server', error);
                        }
                    }

                    this.orderForm.word_count = wordCount;
                    this.orderForm.temp_file = {
                        original_name: manuscriptFile.name,
                        word_count: wordCount,
                    };

                    try {
                        await this.uploadManuscriptTemp(manuscriptFile, wordCount);
                        await this.computeManuscriptPrice();
                    } catch (error) {
                        // Errors are handled in uploadManuscriptTemp/computeManuscriptPrice
                    } finally {
                        if (conversionStarted) {
                            this.isConvertingManuscript = false;
                            this.conversionMessage = 'Konverterer dokumentet… Vennligst vent.';
                        }
                    }
                }
            },

            async computeManuscriptPrice() {
                let formData = new FormData();
                $.each(this.orderForm, function(k, v) {
                    formData.append(k, v);
                });

                formData.append('is_manuscript_only', true);

                try {
                    const response = await axios.post(this.requestUrl+'/checkout/validate-order', formData);
                    this.orderForm.excess_words_amount = parseFloat(response.data.excess_words_amount) || 0;
                    const computedPrice = parseFloat(response.data.price);
                    if (Number.isFinite(computedPrice)) {
                        this.originalPrice = computedPrice;
                    }
                    this.updatePriceTotals();
                } catch (error) {
                    this.processError(error);
                    throw error;
                }
            },

            removeFile() {
                const data = {
                    key: 'temp_uploaded_file'
                }
                axios.get('/forget-session-key/temp_uploaded_file').then(response => {
                    this.orderForm.temp_file = null;
                    this.originalPrice = this.initialBasePrice;
                    this.orderForm.manuscript = null;
                    this.orderForm.word_count = null;
                    this.orderForm.excess_words_amount = 0;
                    this.updatePriceTotals();
                    this.isConvertingManuscript = false;
                    this.conversionMessage = 'Konverterer dokumentet… Vennligst vent.';
                });
            },

            getFileExtension(file) {
                if (!file || !file.name) {
                    return '';
                }

                const parts = file.name.split('.');
                return parts.length > 1 ? parts.pop().toLowerCase() : '';
            },

            async convertFileToDocx(file) {
                const formData = new FormData();
                formData.append('document', file);

                try {
                    const response = await axios.post('/documents/convert-to-docx', formData, {
                        responseType: 'blob',
                    });

                    const contentDisposition = response.headers ? response.headers['content-disposition'] : null;
                    const fallbackName = this.createDocxFileName(file && file.name ? file.name : null);
                    const filename = this.extractFilenameFromContentDisposition(contentDisposition) || fallbackName;
                    const mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    const responseBlob = response.data;
                    const docxBlob = responseBlob instanceof Blob
                        ? responseBlob
                        : new Blob(responseBlob ? [responseBlob] : [], { type: mimeType });

                    return new File([docxBlob], filename, { type: mimeType, lastModified: Date.now() });
                } catch (error) {
                    if (error && error.response && error.response.data instanceof Blob) {
                        try {
                            const parsed = await this.parseErrorBlob(error.response.data);
                            if (parsed) {
                                error.response.data = parsed;
                            }
                        } catch (parseError) {
                            console.error('Failed to parse conversion error response', parseError);
                        }
                    }

                    if (!error.response || !error.response.data) {
                        error.response = error.response || {};
                        error.response.data = {
                            errors: {
                                manuscript: ['Kunne ikke konvertere filen. Prøv igjen.']
                            },
                            message: 'Kunne ikke konvertere filen. Prøv igjen.'
                        };
                    }

                    throw error;
                }
            },

            createDocxFileName(originalName) {
                if (!originalName || typeof originalName !== 'string') {
                    return 'document.docx';
                }

                const dotIndex = originalName.lastIndexOf('.');

                if (dotIndex <= 0) {
                    return originalName.toLowerCase().endsWith('.docx')
                        ? originalName
                        : `${originalName}.docx`;
                }

                const baseName = originalName.substring(0, dotIndex);
                const extension = originalName.substring(dotIndex + 1).toLowerCase();

                if (extension === 'docx') {
                    return originalName;
                }

                return `${baseName}.docx`;
            },

            extractFilenameFromContentDisposition(header) {
                if (!header || typeof header !== 'string') {
                    return null;
                }

                const utf8Match = header.match(/filename\*=UTF-8''([^;]+)/i);
                if (utf8Match && utf8Match[1]) {
                    try {
                        return decodeURIComponent(utf8Match[1]);
                    } catch (error) {
                        console.error('Failed to decode UTF-8 filename', error);
                    }
                }

                const quotedMatch = header.match(/filename="?([^";]+)"?/i);
                if (quotedMatch && quotedMatch[1]) {
                    return quotedMatch[1];
                }

                return null;
            },

            async parseErrorBlob(blob) {
                if (!blob || typeof blob.text !== 'function') {
                    return null;
                }

                const text = await blob.text();

                if (!text) {
                    return null;
                }

                try {
                    return JSON.parse(text);
                } catch (error) {
                    return { message: text };
                }
            },

            extractWordCountWithMammoth(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();

                    reader.onload = async (event) => {
                        try {
                            const arrayBuffer = event.target.result;
                            const result = await mammoth.extractRawText({ arrayBuffer });
                            resolve(this.countWords(result.value));
                        } catch (error) {
                            reject(error);
                        }
                    };

                    reader.onerror = (error) => {
                        reject(error);
                    };

                    reader.readAsArrayBuffer(file);
                });
            },

            countWords(text) {
                if (!text) {
                    return 0;
                }

                const matches = text.trim().match(/\S+/g);
                return matches ? matches.length : 0;
            },

            async uploadManuscriptTemp(file, wordCount = null) {
                const formData = new FormData();
                formData.append('manuscript', file);

                if (Number.isFinite(wordCount) && wordCount > 0) {
                    formData.append('word_count', wordCount);
                }

                try {
                    const response = await axios.post('/shop-manuscript/store-temp-upload', formData);
                    const { word_count: resolvedWordCount, price } = response.data;

                    this.orderForm.temp_file = {
                        original_name: file.name,
                        word_count: resolvedWordCount,
                    };

                    this.orderForm.word_count = resolvedWordCount;

                    const resolvedPrice = parseFloat(price);
                    if (Number.isFinite(resolvedPrice)) {
                        this.originalPrice = resolvedPrice;
                    }

                    this.removeValidationError();

                    return response.data;
                } catch (error) {
                    this.processError(error);
                    this.orderForm.temp_file = null;
                    throw error;
                }
            }
        },

        mounted() {
            this.wizardProps = this.$refs.wizard;
            this.loadOptions();
            this.checkHasPaidCourse();

            if (this.tempFile) {
                const tempPrice = parseFloat(this.tempFile.price);
                if (Number.isFinite(tempPrice)) {
                    this.originalPrice = tempPrice;
                }
                this.orderForm.excess_words_amount = parseFloat(this.tempFile.excess_words_amount) || 0;
                this.orderForm.word_count = this.tempFile.word_count || null;
            }

            this.updatePriceTotals();
        }

    }
</script>

<style>
    .custom-checkbox>[type=checkbox]:checked+label:before, .custom-checkbox>[type=checkbox]:not(:checked)+label:before {
        border: 1px solid;
    }

    .vipps-btn {
        border: none;
        color: #fff;
        background-color: #fe5b24;
        font-weight: 600;
        margin-right: 10px;
        padding: 0.5180469716em 1.41575em;
        position: relative;
    }

    .vipps-btn img.inline {
        height: 2ex;
        display: inline;
        vertical-align: text-bottom;
    }

    .temp-file-container {
        border-radius: 4px;
        background-color: #f8f8ff;
        font-family: Inter;
        font-weight: 700;
        min-height: 50px;
        padding: 0;
        border: 2px dashed rgb(56, 78, 183, 30%);
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 20px;
        font-size: 15px;
        padding: 5px;
    }

    .temp-file-container button {
        background: #f00;
        border:none;
        border-radius: 3px;
        color: white;
        padding: 2px 7px;
    }

    .temp-file-container button:hover {
        opacity: .6;
    }

</style>