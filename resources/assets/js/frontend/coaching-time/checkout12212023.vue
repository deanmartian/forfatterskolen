<template>
    <div class="card">
        <div id="scrollhere"></div>
        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="trans('site.paginate.next')" :backButtonText="trans('site.paginate.previous')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="">
            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list" :before-change="validateOrder">

                <h2 v-html="title">
                </h2>

                <div class="form-group">
                    <div id="manuscript-file">
                        <label class="control-label">
                            {{ trans('site.front.form.upload-manuscript') }}
                        </label>
                        <input type="file" ref="file" class="hidden"
                               @change="onManuscriptChange"
                               id="manuscript"
                               accept="application/msword,
application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                        <input type="text" readonly class="form-control"
                               :placeholder="manuscriptName" name="manuscript"
                               @click="$refs.file.click()">
                    </div>

                    <button class="btn btn-default mt-4 btn-common-padding" @click="clearManuscript()"
                            v-if="orderForm.manuscript">
                        {{ trans('site.front.cancel') }}
                    </button>
                </div>

                <div class="form-group">
                    {{ trans('site.front.coaching-timer.note') }}
                </div>

                <!-- <div class="row mb-4">
                    <div class="col-sm-4" style="" v-for="i in 3">
                        <label>
                            {{ trans('site.front.coaching-timer.desired-date') }}
                        </label>
                        <input type="datetime-local" class="form-control p-1"
                               v-model="orderForm.suggested_date[i - 1]" required>
                    </div>
                    <div class="col-sm-12">
                        <input type="hidden" name="suggested_date">
                    </div>
                </div> -->

                <div class="form-group">
                    <label for="">
                        {{ trans('site.front.coaching-timer.help-with-text') }}
                    </label>
                    <textarea name="help_with" id="" cols="30" rows="10" class="form-control" v-model="orderForm.help_with"></textarea>
                </div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td class="text-right h3">{{ trans('site.front.price') }}:</td>
                        <td class="text-right h3 text-red" style="width: 150px">
                            {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
                        </td>
                    </tr>

                    <tr v-if="orderForm.additional_price">
                        <td class="text-right h3">{{ trans('site.add-on-price') }}:</td>
                        <td class="text-right h3 text-red" style="width: 150px">
                            {{ orderForm.additional_price | currency('Kr', 2, currencyOptions) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="text-right h3">{{ trans('site.front.total') }}:</td>
                        <td class="text-right h3 text-red" style="width: 150px">
                            {{ totalPrice | currency('Kr', 2, currencyOptions) }}
                        </td>
                    </tr>
                    </tbody>
                </table>

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
                </template>

            </tab-content>

            <tab-content :title="trans('site.checkout.payment-details')" icon="fas fa-credit-card"
                         :before-change="validateForm">
                <div id="checkout-display"></div>
            </tab-content>

            <template slot="footer" slot-scope="props">
                <div class="wizard-footer-left">
                    <wizard-button  v-if="props.activeTabIndex > 0 && !props.isLastStep"
                                    @click.native="props.prevTab(); scrollTop()"
                                    :style="props.fillButtonStyle">
                        {{ trans('site.back') }}
                    </wizard-button>
                </div>
                <div class="wizard-footer-right">
                    <span v-if="props.activeTabIndex === 0" style="margin-right: 10px">
                        {{ trans('site.front.checkout.note') }}
                    </span>

                    <wizard-button v-if="!props.isLastStep" @click.native="props.nextTab(); scrollTop()" class="wizard-footer-right"
                                   :style="props.fillButtonStyle" :disabled="!currentUser && !isNewCustomer && props.activeTabIndex > 0">
                        {{ trans('site.learner.next-text') }}
                    </wizard-button>

                    <!-- v-else before -->
                    <wizard-button v-if="props.isLastStep" @click.native="props.nextTab()" class="wizard-footer-right finish-button"
                                   :style="props.fillButtonStyle" :disabled="isLoading && props.isLastStep">
                        <i class="fa fa-pulse fa-spinner" v-if="isLoading && props.isLastStep"></i>
                        {{props.isLastStep ? trans('site.front.buy')
                        : trans('site.learner.next-text')}}</wizard-button>
                </div>
            </template> <!-- end buttons slot -->

            <button slot="finish" class="d-none">{{ trans('site.checkout.finish') }}</button>
        </form-wizard>
    </div>
</template>

<script>
    export default {

        props: ['price', 'title', 'plan_id', 'user'],

        data() {
            return {
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
                    price: this.price,
                    payment_plan_id: 8,
                    payment_mode_id: 3,
                    mobile_number: "",
                    totalDiscount: 0,
                    item_id: this.plan_id,
                    manuscript: null,
                    fileName: '',
                    fileLocation: '',
                    additional_price: 0,
                    suggested_date: [],
                    help_with: ''
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
                manuscriptName: i18n.site.front.form['select-document-to-upload'],
                currentUser: this.user,
                isNewCustomer: false,
                invalidCred: false,
                isLoginDisabled: false,
                loginText: i18n.site.front.form.login,
                isLoading: false,
                requestUrl: '/coaching-time'
            }
        },

        computed: {
            totalPrice() {
                return this.orderForm.price + this.orderForm.additional_price;
            }
        },

        methods: {
            onManuscriptChange(e) {
                let files = e.target.files;

                if (!files.length)
                {
                    this.manuscriptName = i18n.site.front.form['select-document-to-upload'];
                    this.orderForm.manuscript = null;
                    this.orderForm.fileName = '';
                    this.orderForm.fileLocation = '';
                    return;
                }

                this.manuscriptName = files[0].name;
                this.orderForm.manuscript = files[0];
                this.checkAdditionalPrice();

                $(".validation-err").remove();
            },

            clearManuscript() {
                this.manuscriptName = i18n.site.front.form['select-document-to-upload'];
                this.orderForm.manuscript = null;
                this.orderForm.fileName = '';
                this.orderForm.fileLocation = '';
                this.orderForm.additional_price = 0;
                this.$refs.file.value = null;
            },

            checkAdditionalPrice() {

                this.removeValidationError();

                let self = this;
                let formData = new FormData();
                formData.append('manuscript', self.orderForm.manuscript);

                axios.post(self.requestUrl + '/calculate', formData).then(response => {

                    let data = response.data;
                    self.orderForm.fileName = data.file_name;
                    self.orderForm.fileLocation = data.file_location;
                    self.orderForm.additional_price = data.additional_price;

                }).catch(error => {

                    this.clearManuscript();
                    this.processError(error);

                });
            },

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

            toggleNewCustomer() {
                this.isNewCustomer = !this.isNewCustomer;
            },

            scrollTop() {
                jQuery([document.documentElement, document.body]).animate({
                    scrollTop: $("#scrollhere").offset().top
                }, 1000);
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
                /* if (this.orderForm.suggested_date.length < 3) {
                    this.$toasted.global.showErrorMsg({
                        message : 'Error on the form'
                    });

                    let element = $("[name=suggested_date]");
                    element.after("<small class='text-danger validation-err'>" +
                        "<i class='fas fa-exclamation-circle'></i> " +
                        "<span> Please select a date </span></small>");
                    return false;
                } */

                return true;
            },

            validateForm() {
                this.removeValidationError();

                let formData = new FormData();
                $.each(this.orderForm, function(k, v) {
                    formData.append(k, v);
                });

                return axios.post(this.requestUrl+'/validate-form', formData).then(response => {
                    this.removeValidationError();
                    this.getCurrentUser();
                    $("#checkout-display").html(response.data);
                    return true;

                }).catch(error => {

                    this.processError(error);

                });
            }
        },

        mounted() {
            this.loadOptions();
        }

    }
</script>