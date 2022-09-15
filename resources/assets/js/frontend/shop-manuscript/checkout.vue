<template>
    <div class="card">
        <div id="scrollhere"></div>
        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="'Til betaling'" :backButtonText="trans('site.paginate.previous')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="">

            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list" :before-change="validateOrder">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td width="45%">
                            <div class="media-body">
                                <h1 class="media-heading font-quicksand">
                                    {{ shopManuscript.title }}
                                </h1>

                                <h3 class="mt-3 font-weight-bold">
                                    {{ trans('site.front.our-course.show.package-details-text') }}:
                                </h3>

                                <p v-html="shopManuscript.description" class="mt-2">
                                </p>

                                <h3 class="mt-3 font-weight-bold">
                                    {{ trans('site.learner.max-number-of-words-text') }}:
                                </h3>
                                <p>
                                    {{ shopManuscript.max_words }} {{ trans('site.learner.words-text') }}
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label>
                                    {{ trans('site.front.form.upload-manuscript') }}
                                </label>
                                <div class="custom-file">
                                    <input type="file" name="manuscript" class="form-control"
                                           @change="onManuscriptChange"
                                           id="manuscript"
                                           accept="application/msword,
application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                                </div>

                                <div class="custom-checkbox mt-4">
                                    <input type="checkbox" name="send_to_email" id="send_to_email"
                                           v-model="orderForm.send_to_email">
                                    <label for="send_to_email" class="control-label">
                                        {{ trans('site.front.form.send-to-email') }}
                                    </label>
                                </div>
                            </div> <!-- end manuscript -->

                            <div class="form-group">
                                <label class="font-weight-500">
                                    {{ trans('site.front.genre') }}
                                </label>
                                <select class="form-control" name="genre" v-model="orderForm.genre" @change="genreChanged()">
                                    <option value="" disabled="disabled" selected
                                            v-html="trans('site.free-text-evaluation.choose-genre')">
                                    </option>

                                    <option :value="type.id" v-for="type in assignmentTypes" v-text="type.name">
                                    </option>
                                </select>
                            </div> <!-- end genre -->

                            <div class="form-group">
                                <label>
                                    {{ trans('site.front.form.synopsis-optional') }}
                                </label>
                                <div class="custom-file d-block">
                                    <input type="file" name="synopsis" class="form-control"
                                           @change="onSynopsisChange"
                                           id="synopsis"
                                           accept="application/msword,
application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ trans('site.front.form.coaching-time-later-in-manus') }}</label>
                                <toggle-button :labels="{checked: trans('site.front.yes'), unchecked: trans('site.front.no')}"
                                               :width="60" :height="30" :font-size="12"
                                               :color="{checked:'#337ab7', unchecked:'#354350'}" class="mt-2 ml-2"
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
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td class="text-right h3">{{ trans('site.front.price') }}:</td>
                        <td class="text-right h3 text-red" style="width: 150px">
                            {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
                        </td>
                    </tr>

                    <tr v-if="orderForm.totalDiscount">
                        <td class="text-right h3">{{ trans('site.front.discount') }}:</td>
                        <td class="text-right h3 text-red">
                            {{ orderForm.totalDiscount | currency('Kr', 2, currencyOptions) }}
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
                        < {{ trans('site.back') }}
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
                    <template v-if="props.activeTabIndex === 0">
                        <span style="margin-right: 10px">
                            {{ trans('site.front.checkout.note') }}
                        </span>

                        <button type="button" class="vipps-btn" slot="custom-buttons-right" @click="vippsCheckout();"
                                :disabled="isLoading">
                            <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i>
                            <span>Hurtigutsjekk med</span>
                            <img src="/images-new/vipps.png" class="inline" alt="vipps-buy-button"
                                 :style="isLoading ? 'opacity: .8;' : ''">
                        </button>
                    </template>

                    <template v-if="!currentUser || (currentUser && currentUser.could_buy_course)">
                        <wizard-button v-if="!props.isLastStep" @click.native="props.nextTab(); scrollTop()" class="wizard-footer-right"
                                       :style="props.fillButtonStyle" :disabled="!currentUser && !isNewCustomer && props.activeTabIndex > 0">
                            Til betaling
                        </wizard-button>

                        <!-- v-else before -->
                        <wizard-button v-if="props.isLastStep && !isSveaPayment" @click.native="props.nextTab()" class="wizard-footer-right finish-button"
                                       :style="props.fillButtonStyle" :disabled="isLoading && props.isLastStep">
                            <i class="fa fa-pulse fa-spinner" v-if="isLoading && props.isLastStep"></i>
                            {{props.isLastStep ? trans('site.front.buy')
                            : trans('site.learner.next-text')}}</wizard-button>
                    </template>
                </div>
            </template> <!-- end buttons slot -->

            <button slot="finish" class="d-none">{{ trans('site.checkout.finish') }}</button>

        </form-wizard>
    </div>
</template>

<script>
    export default {

        props: {
            user: Object,
            shopManuscript: Object,
            assignmentTypes: Array
        },

        data() {
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
                    price: this.shopManuscript.full_payment_price,
                    payment_plan_id: 8,
                    payment_mode_id: 3,
                    mobile_number: "",
                    totalDiscount: 0,
                    send_to_email: false,
                    genre: '',
                    description: '',
                    coaching_time_later: false,
                    item_type: 2,
                    shop_manuscript_id: this.shopManuscript.id,
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
                isSveaPayment: true,
                invalidCred: false,
                isLoginDisabled: false,
                loginText: i18n.site.front.form.login,
                isNewCustomer: false,
                manuscriptName: i18n.site['learner.files-text'],
                synopsisName: i18n.site['learner.files-text'],
                hasPaidCourse: false,
                isLoading: false,
                requestUrl: '/shop-manuscript/'+this.shopManuscript.id
            }
        },

        computed: {
            totalPrice() {
                return parseFloat(this.orderForm.price) - this.orderForm.totalDiscount;
            }
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

                let formData = new FormData();
                $.each(this.orderForm, function(k, v) {
                    formData.append(k, v);
                });

                return axios.post(this.requestUrl+'/checkout/validate-order', formData).then(response => {
                    return true;
                }).catch(error => {
                    this.processError(error);
                });
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
                    this.hasPaidCourse = response.data;
                    this.genreChanged();
                })
            },

            genreChanged() {
                let shopManuscript = this.shopManuscript;
                let totalDiscount = this.hasPaidCourse ? (shopManuscript.full_payment_price * 0.05) : 0;
                let price = parseFloat(this.shopManuscript.full_payment_price);

                if (this.orderForm.genre === 10) {
                    price = price + ((price - totalDiscount) * .50);
                }

                this.orderForm.totalDiscount = totalDiscount;
                this.orderForm.price = price;
            }
        },

        mounted() {
            this.loadOptions();
            this.checkHasPaidCourse();
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

</style>