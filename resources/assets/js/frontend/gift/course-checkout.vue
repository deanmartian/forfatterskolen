<template>
    <div class="card">
        <div id="scrollhere"></div>
        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="trans('site.paginate.next')" :backButtonText="trans('site.paginate.previous')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="" :startIndex="startIndex">
            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td width="25%" id="image-td">
                            <div class="media">
                                <a class="thumbnail mr-3" href="#">
                                    <img class="media-object w-100" :src="course.course_image"
                                         style="width: 200px; height: 200px;">
                                </a>
                            </div>
                        </td>
                        <td width="45%">
                            <div class="media-body">
                                <h1 class="media-heading font-quicksand">
                                    <a :href="'/course/'+course.id" class="text-red h1 font-weight-bold">
                                        {{ course.title }}
                                    </a>
                                </h1>

                                <h3 class="mt-3 font-weight-bold">
                                    {{ trans('site.front.our-course.show.package-details-text') }}:
                                </h3>

                                <p v-html="coursePackage.description_formatted" class="mt-2">
                                </p>

                                <!--<template
                                        v-if="coursePackage.has_coaching
                                        || (coursePackage.included_courses
                                        && coursePackage.included_courses.length)">

                                    <h5 class="mt-3" style="font-weight: 400">
                                        {{ trans('site.front.our-course.show.includes') }}: </h5> <br>

                                    <template v-if="coursePackage.included_courses
                                    && coursePackage.included_courses.length">

                                        <template v-for="included_course in coursePackage.included_courses">
                                            {{ included_course.included_package_course_title }}
                                            ({{ included_course.included_package_variation }}) <br>
                                        </template>

                                    </template>
                                </template>-->

                                <div class="mt-3">
                                    <h3 class="font-weight-bold">
                                        Rabattkupong:
                                    </h3>
                                    <input type="text" name="coupon" class="form-control w-50"
                                           v-model="orderForm.coupon"
                                           v-debounce:1s="checkDiscount" :debounce-events="'keyup'">
                                </div>
                            </div>
                        </td>
                        <td width="30%">

                            <h3>{{ trans('site.front.form.course-package') }}:</h3>
                            <div class="package-option custom-radio" v-for="(pkg, index) in packages"
                                 :key="pkg.id">
                                <input type="radio" name="package_id" :id="pkg.variation"
                                       v-model="orderForm.package_id"
                                       :value="pkg.id" @change="packageChanged">
                                <label :for="pkg.variation" v-text="pkg.variation" class="font-weight-normal"></label>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <div class="col-sm-6 text-center" v-for="giftCard in giftCards">
                                <label>
                                    <input type="radio" name="card" :value="giftCard.name" v-model="orderForm.gift_card"
                                           class="image-radio" @click="setGiftCard()">
                                    <img :src="giftCard.image">
                                    <b> {{ giftCard.label }} </b>
                                </label>
                            </div>
                        </td>
                        <td>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td class="text-right h3">{{ trans('site.front.price') }}:</td>
                        <td class="text-right h3 text-red">
                            {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
                        </td>
                    </tr>

                    <!--<tr v-if="studentDiscount">
                        <td></td>
                        <td class="text-right h3">Studentrabatt:</td>
                        <td class="text-right h3 text-red">
                            {{ studentDiscount | currency('Kr', 2, currencyOptions) }}
                        </td>
                    </tr>-->

                    <tr v-if="orderForm.totalDiscount">
                        <td></td>
                        <td class="text-right h3">{{ trans('site.front.discount') }}:</td>
                        <td class="text-right h3 text-red">
                            {{ orderForm.totalDiscount | currency('Kr', 2, currencyOptions) }}
                        </td>
                    </tr>

                    <tr v-if="isMonthly">
                        <td></td>
                        <td class="text-right h3">{{ trans('site.front.per-month') }}:</td>
                        <td class="text-right h3 text-red">
                            {{ monthlyPrice | currency('Kr', 2, currencyOptions) }}
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td class="text-right h3">{{ trans('site.front.total') }}:</td>
                        <td class="text-right h3 text-red">
                            {{ totalPrice | currency('Kr', 2, currencyOptions) }}
                        </td>
                    </tr>

                    </tbody>
                </table>
            </tab-content> <!-- end order details-->

            <tab-content :title="trans('site.front.form.user-information')" icon="fas fa-id-card"
                         :before-change="validateForm" style="min-height: 300px">

                <template v-if="!currentUser">

                    <!--<p class="text-center" v-if="!isAlreadyAMember && !isNewCustomer">
                        Are you <a href="javascript:void(0)" @click="isAlreadyAMember = true">already a member</a>?
                        or a <a href="javascript:void(0)" @click="isNewCustomer = true">new customer</a>
                    </p>-->

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

                                <a :href="'/auth/login/facebook?package=' + orderForm.package_id + '&c=' + orderForm.coupon
                                + '&si=1'" class="btn site-btn btn-block fb-link">
                                    {{ trans('site.front.form.login-with-facebook') }}
                                </a>

                                <a :href="'/auth/login/google?package=' + orderForm.package_id + '&c=' + orderForm.coupon
                                + '&si=1'" class="btn site-btn btn-block google-link">
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

                <div v-if="isSveaPayment" id="checkout-display"></div>

                <div v-else="">
                    <div class="col-sm-6">
                        <div>
                            {{ trans('site.front.form.payment-method') }}
                        </div>
                        <div class="panel-body px-0 pb-0">
                            <select class="form-control" name="payment_mode_id" data-size="15"
                                    v-model="orderForm.payment_mode_id" @change="paymentModeChanged()">
                                <option :value="paymentMode.id" v-for="paymentMode in paymentModes"
                                        v-html="paymentMode.mode"></option>
                            </select>
                        </div>

                        <h1 v-html="coursePackage.variation" style="margin-top: 30px" class="text-red">
                        </h1>
                    </div>

                    <div class="col-sm-6">
                        <div>
                            {{ trans('site.front.form.payment-plan') }}
                        </div>

                        <div class="panel-body px-0 pb-0">
                            <div class="row">
                                <div class="col-sm-12" id="paymentPlanContainer">
                                    <div class="payment-option custom-radio col-sm-6 px-0" v-for="paymentPlan in paymentPlans">
                                        <input type="radio" name="payment_plan_id" v-model="orderForm.payment_plan_id"
                                               :id="paymentPlan.plan" :disabled="disableOtherPlan && paymentPlan.id !== 8"
                                               :value="paymentPlan.id" @change="paymentPlanChanged(paymentPlan)">
                                        <label :for="paymentPlan.plan">{{ paymentPlan.plan }} </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 margin-top custom-checkbox">
                                <input type="checkbox" name="agree_terms" id="agree_terms" v-model="orderForm.agree_terms">
                                <label for="agree_terms">
                                    Jeg aksepterer
                                    <a href="javascript:void(0)" @click="$refs.termsModal.show()">kjøpsvilkårene</a>
                                </label>
                            </div>
                        </div>

                        <table class="table" style="margin-top: 15px">
                            <tbody>
                            <tr>
                                <td></td>
                                <td class="text-right h3">{{ trans('site.front.price') }}:</td>
                                <td class="text-right h3 text-red">
                                    {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>

                            <!--<tr v-if="studentDiscount">
                                <td></td>
                                <td class="text-right h3">Studentrabatt:</td>
                                <td class="text-right h3 text-red">
                                    {{ studentDiscount | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>-->

                            <tr v-if="orderForm.totalDiscount">
                                <td></td>
                                <td class="text-right h3">{{ trans('site.front.discount') }}:</td>
                                <td class="text-right h3 text-red">
                                    {{ orderForm.totalDiscount | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>

                            <tr v-if="isMonthly">
                                <td></td>
                                <td class="text-right h3">{{ trans('site.front.per-month') }}:</td>
                                <td class="text-right h3 text-red">
                                    {{ monthlyPrice | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td class="text-right h3">{{ trans('site.front.total') }}:</td>
                                <td class="text-right h3 text-red">
                                    {{ totalPrice | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div> <!-- end col-sm-6 -->
                </div> <!-- end div -->

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

                    <template v-if="!currentUser || (currentUser && currentUser.could_buy_course)">
                        <wizard-button v-if="!props.isLastStep" @click.native="props.nextTab(); scrollTop()" class="wizard-footer-right"
                                       :style="props.fillButtonStyle" :disabled="!currentUser && !isNewCustomer && props.activeTabIndex > 0">
                            {{ trans('site.learner.next-text') }}
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

        <b-modal
                ref="termsModal"
                :title="trans('site.terms')"
                size="lg"
                :hide-footer="true"
        >
            <div v-html="terms" class="card-body">
            </div>
        </b-modal>

    </div> <!-- end card -->
</template>

<style scoped="">
    .wizard-progress-with-circle {
        padding-left: 30px !important;
    }

    .wizard-btn {
        border-radius: 0 !important;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    #cardForm [type='submit'] {
        display: none;
    }

    .input-group, .site-btn-global {
        margin-top: 20px;
    }

    .second-col p {
        font-size: 19.8px;
        margin-top: 20px;
        color: #a2a2a2;
    }

    .second-col .form-group {
        margin-top: 3rem;
    }

    .second-col .form-control, .second-col .form-control:focus {
        border-style: none none solid none !important;
        border-radius: 0;
        font-size: 18px;
        text-align: center;
        border-width: 1.5px !important;
        box-shadow: none;
    }

    .second-col .form-control:focus {
        border-color: #354350 !important;
    }

    .second-col p, .second-col a.no-underline, .second-col .form-control::placeholder {
        color: #a2a2a2;
    }

    .second-col .btn {
        padding: 5px 0;
        font-size: 18px;
        white-space: normal;
    }

    .second-col .fb-link {
        background-color: #1b4c8f;
        color: #fff;
    }

    .second-col .google-link {
        background-color: #cb2f1e;
        color: #fff;
    }

    .second-col .btn-dark-red, .second-col .btn-outline-dark-red:hover {
        background: #C12938;
        color: #fff;
    }

    .btn-block+.btn-block {
        margin-top: .5rem;
    }

    .modal-backdrop {
        background-color: #0009;
    }

    .modal-dialog .modal-header .close {
        padding: 1rem 2rem;
        font-size: 40px;
    }

    .modal-dialog .modal-title {
        line-height: 1.6;
        font-size: 26px;
    }

    @media only screen and (max-width: 640px) {
        #image-td {
            display: none;
        }
    }
</style>

<script>
    import {FormWizard, TabContent} from 'vue-form-wizard'
    import 'vue-form-wizard/dist/vue-form-wizard.min.css'
    export default {

        props: {
            course: Object,
            packageId: {
                type: Number,
                required: true
            },
            passedCoupon: String,
            packages: Array,
            user: Object,
            startIndex: Number,
            giftCard: String,
            giftCards: Array
        },

        data() {
            return {
                currentUser: this.user,
                coursePackage: {},
                orderForm: {
                    email: '',
                    first_name: '',
                    last_name: '',
                    street: '',
                    zip: '',
                    city: '',
                    phone: '',
                    national_id: '',
                    password: '',
                    package_id: this.packageId,
                    price: 0,
                    payment_plan_id: 8,
                    coupon: this.passedCoupon,
                    payment_mode_id: 3,
                    mobile_number: "",
                    campaign_code: '',
                    campaign_months: 0,
                    campaign_initial_fee: 0,
                    campaign_admin_fee: 0,
                    totalDiscount: 0,
                    hasPaidCourse: false,
                    agree_terms: false,
                    gift_card: this.giftCard
                },
                singleCourseDiscount: 500,
                groupCourseDiscount: 1000,
                isMonthly: false,
                monthlyPrice: 0,
                monthlyPriceFormatted: 0,
                studentDiscount: 0,
                couponDiscount: 0,
                couponDiscountFormatted: 0,
                totalPrice: 0,
                totalPriceFormatted: 0,
                saleDiscount: 0,
                currencyOptions: {
                    thousandsSeparator: '.',
                    decimalSeparator: ',',
                    spaceBetweenAmountAndSymbol: true
                },
                loginForm: {
                    email: '',
                    password: ''
                },
                invalidCred: false,
                isLoginDisabled: false,
                loginText: i18n.site.front.form.login,
                isAlreadyAMember: false,
                isNewCustomer: false,
                paymentModes: [],
                paymentPlans: [],
                disableOtherPlan: false,
                terms: '',
                isSveaPayment: true,
                isLoading: false,
                requestUrl: '/gift/course/'+this.course.id
            }
        },

        computed: {
            coupon() {
                return this.orderForm.coupon;
            },

            notAllowedPaymentMode() {
                return this.orderForm.payment_mode_id > 1;
            },

            selectedPaymentPlan() {
                let scope = this;
                return scope.paymentPlans.filter(plan => {
                    return plan.id === scope.orderForm.payment_plan_id;
                })[0];
            }
        },

        methods: {
            checkHasPaidCourse() {
                axios.get('/has-paid-course/').then(response => {

                    this.orderForm.hasPaidCourse = response.data;

                    this.packageChanged();
                })
            },

            getCurrentUser() {
                axios.get('/current-user').then(response => {

                    this.currentUser = response.data;
                })
            },

            getPaymentModeOptions() {
                axios.get('/payment-modes').then(response => {
                    this.paymentModes = response.data;
                });
            },

            getPaymentPlanOptions() {
                axios.get('/payment-plan-options/' + this.orderForm.package_id).then(response => {
                    this.paymentPlans = response.data;
                });
            },

            getTerms() {
                axios.get('/terms/course-terms').then(response => {
                    this.terms = response.data;
                });
            },

            paymentModeChanged() {
                this.disableOtherPlan = false;
                if (this.orderForm.payment_mode_id !== 3) {
                    this.disableOtherPlan = true;
                    this.orderForm.payment_plan_id = 8;
                    this.paymentPlanChanged(this.selectedPaymentPlan);
                }
            },

            paymentPlanChanged(paymentPlan) {
                let field = 'full_payment_price';
                this.saleDiscount = this.coursePackage.sale_discount;
                this.isMonthly = false;
                if (paymentPlan.division > 1) {
                    field = 'months_' + paymentPlan.division + '_price';
                    this.isMonthly = true;
                    this.saleDiscount = 0;

                    if (this.coursePackage['months_' + paymentPlan.division + '_is_sale']) {
                        this.saleDiscount = this.coursePackage[field] - this.coursePackage['months_' + paymentPlan.division + '_sale_price'];
                    }
                }

                this.studentDiscount = this.coursePackage.has_student_discount
                    ? (this.course.type === 'Single' ? 500 : 1000)
                    : 0;

                if (this.orderForm.hasPaidCourse && (this.studentDiscount > this.couponDiscount)) {
                    this.couponDiscount = this.studentDiscount;
                }

                this.orderForm.totalDiscount = this.couponDiscount + this.saleDiscount;
                this.orderForm.price = this.coursePackage[field];
                this.totalPrice = this.coursePackage[field] - this.orderForm.totalDiscount;
                this.monthlyPrice = (this.totalPrice) / paymentPlan.division;
            },

            packageChanged() {
                const selectedPackageId = this.orderForm.package_id;
                const self = this;
                this.packages.forEach(function(pkg) {
                    if (pkg.id === selectedPackageId) {
                        self.coursePackage =  pkg;
                    }
                });
                this.paymentPlanChanged(this.selectedPaymentPlan);

            },

            checkDiscount(val, e) {

                this.couponDiscount = 0;
                //this.orderForm.payment_plan_id = 8;
                if (val) {
                    axios.get('/course/'+this.course.id+'/check_coupon_discount/'+val).then(response => {

                        this.couponDiscount = response.data.discountCoupon.discount;
                        this.packageChanged();

                    }).catch(error => {
                        this.packageChanged();
                        this.$toasted.global.showErrorMsg({
                            message : error.response.data.error_message
                        });

                    });
                } else {
                    this.packageChanged();
                }
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
                this.orderForm.national_id = this.currentUser && this.currentUser.address ? this.currentUser.address.national_id : '';
            },

            handleLogin(event) {
                this.isLoginDisabled = true;
                this.removeValidationError();
                this.loginForm.course_id = this.course.id;

                axios.post('/auth/checkout/login', this.loginForm).then(response => {

                    // uncomment these if message would be shown instead of window location
                    /*this.invalidCred = false;
                    this.loginText = i18n.site.front.form.login;
                    this.isLoginDisabled = false;
                    this.currentUser = response.data.user;
                    this.loadOptions();
                    this.checkHasPaidCourse();*/

                    if (response.data.user.course_link) {
                        window.location.href = response.data.user.course_link;
                    }

                    window.location.href = window.location.pathname + '?package=' + this.orderForm.package_id
                        + '&c=' + this.orderForm.coupon
                        + '&si=1'

                    /*this.$toasted.global.showSuccessMsg({
                        message : response.data.success
                    });*/

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

            validateForm() {
                return axios.post(this.requestUrl+'/checkout/validate-form', this.orderForm).then(response => {
                    this.removeValidationError();
                    this.checkHasPaidCourse();
                    this.getCurrentUser();
                    $("#checkout-display").html(response.data);

                    return true;

                }).catch(error => {

                    this.processError(error);

                });
            },

            processOrder() {

                this.isLoading = true;
                this.removeValidationError();
                axios.post(this.requestUrl + '/checkout/process-order', this.orderForm).then(response => {
                    if (response.data.redirect_link) {
                        window.location.href = response.data.redirect_link;
                    }
                    //this.isLoading = false;
                }).catch(error => {

                    this.isLoading = false;

                    if (error.response.data['agree_terms']) {
                        $("label[for=agree_terms]").after("<small class='text-danger validation-err d-block'>" +
                            "<i class='fas fa-exclamation-circle'></i> " +
                            "<span>" + error.response.data['agree_terms'][0]+"</span></small>");
                    } else {
                        this.processError(error);
                    }


                });
                return false;
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

            setGiftCard() {
                let self = this;
                setTimeout(function(){
                    axios.post('/set-gift-card', {card: self.orderForm.gift_card}).then(response => {

                    })
                }, 200);
            }

        },

        mounted() {
            this.checkHasPaidCourse();
            this.getPaymentModeOptions();
            this.getPaymentPlanOptions();
            this.getTerms();
            this.loadOptions();
            if (this.orderForm.coupon) {
                this.checkDiscount(this.orderForm.coupon);
            }

        }

    }
</script>