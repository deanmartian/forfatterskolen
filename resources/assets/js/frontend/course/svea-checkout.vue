<template>
    <div class="card">

        <!-- <a :href="'/course/' + course.id + '/fs_checkout'" class="btn site-btn-global"
           style="width: 200px; border-radius: 0;">
            FS Checkout
        </a> -->

        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="'Til betaling'" :backButtonText="trans('site.paginate.previous')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="" ref="wizard">
            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list">
                <div class="row">
                    <div class="col-md-6 course-details">
                        <CourseDetails :course="course" :coursePackage="coursePackage"></CourseDetails>
                    </div>

                    <div class="col-md-6 package-details">
                        <h3>{{ trans('site.front.form.course-package') }}:</h3>

                        <div :class="['package-option', orderForm.package_id === pkg.id ? 'active' : '']" 
                            v-for="pkg in packages" :key="pkg.id" 
                            @click="setSelectedPackage(pkg.id)">
                                {{ pkg.variation }}
                        </div>

                        <div class="discount-container">
                            <h3 class="font-weight-bold">
                                Rabattkupong:
                            </h3>
                            <input type="text" name="coupon" class="form-control"
                                    v-model="orderForm.coupon"
                                    v-debounce:1s="checkDiscount" :debounce-events="'keyup'">
                        </div>

                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td class="h3">{{ trans('site.front.price') }}:</td>
                                    <td class="text-right h3">
                                        {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
                                    </td>
                                </tr>

                                <tr v-if="studentDiscount">
                                    <td class="h3">Studentrabatt:</td>
                                    <td class="text-right h3">
                                        {{ studentDiscount | currency('Kr', 2, currencyOptions) }}
                                    </td>
                                </tr>

                                <tr v-if="totalDiscount">
                                    <td class="h3">{{ trans('site.front.discount') }}:</td>
                                    <td class="text-right h3">
                                        {{ totalDiscount | currency('Kr', 2, currencyOptions) }}
                                    </td>
                                </tr>

                                <tr v-if="isMonthly">
                                    <td class="h3">{{ trans('site.checkout.per-month') }}:</td>
                                    <td class="text-right h3">
                                        {{ monthlyPrice | currency('Kr', 2, currencyOptions) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="h3">{{ trans('site.front.total') }}:</td>
                                    <td class="text-right h3">
                                        {{ totalPrice | currency('Kr', 2, currencyOptions) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <wizard-button v-if="wizardProps.activeTabIndex === 0 && coursePackage.is_pay_later_allowed"
                        @click.native="payLaterClicked()"
                            class="wizard-footer-right w-100" :style="wizardProps.fillButtonStyle" style="margin-right: 10px">
                            Bestill kurs, betal senere
                        </wizard-button>

                        <wizard-button v-if="wizardProps.activeTabIndex === 0" @click="vippsCheckout()"
                            class="wizard-footer-right w-100 vipps-btn" :style="wizardProps.fillButtonStyle"
                            :disabled="isLoading">
                            <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i>
                            <span>Hurtigutsjekk med</span>
                            <img src="/images-new/vipps.png" class="inline" alt="vipps-buy-button"
                                :style="isLoading ? 'opacity: .8;' : ''">
                        </wizard-button>

                        <wizard-button v-if="!wizardProps.isLastStep" @click.native="nextTab()"
                            class="wizard-footer-right w-100" :style="wizardProps.fillButtonStyle">
                            {{ orderForm.is_pay_later && wizardProps.activeTabIndex != 0 
                            ? 'Bestill kurs, betal senere' :'Til betaling' }}
                        </wizard-button>

                    </div>
                </div>
            </tab-content> <!-- end order details-->

            <tab-content :title="trans('site.front.form.user-information')" icon="fas fa-id-card"
                         :before-change="validateForm">
                <wizard-button  v-if="wizardProps.activeTabIndex > 0"  @click.native="wizardProps.prevTab();" 
                    class="back-btn">
                    Back
                </wizard-button>

                <div class="row">
                    <div class="col-md-6 course-details">
                        <CourseDetails :course="course" :coursePackage="coursePackage"></CourseDetails>
                    </div>

                    <div class="col-md-6 package-details form-details">
                        <form @submit.prevent="handleLogin($event)" v-if="!currentUser" class="mb-4">
                            <div class="row">
                                <div class="col-sm-12 mb-1">
                                    <span>
                                        {{ trans('site.front.form.already-registered-text') }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <input type="text" name="email" :placeholder="trans('site.front.form.email')"
                                        class="form-control" v-model="loginForm.email" required>
                                </div>

                                <div class="form-group col-sm-12">
                                    <input type="password" name="login_password" :placeholder="trans('site.front.form.password')"
                                        class="form-control" v-model="loginForm.password" required>
                                </div>

                                <div class="form-group col-sm-6 mb-0">
                                    <p style="margin-top: 7px;">
                                        <a href="/auth/login?t=passwordreset" tabindex="-1" class="text-red">
                                            {{ trans('site.front.form.reset-password') }}?
                                        </a>
                                    </p>
                                </div>

                                <div class="form-group col-sm-6 mb-0 text-right">
                                    <button type="submit" class="btn site-btn-global login-btn"
                                            :disabled="isLoginDisabled">
                                        <i class="fas fa-spinner fa-spin" v-if="isLoginDisabled"></i>
                                        {{ loginText }}
                                    </button>
                                </div>
                            </div><!-- end row for login -->

                            <div class="row">
                                <div class="col-sm-12">
                                    <span class="text-danger invalid-credentials" v-if="invalidCred">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span v-html="errorMsg"></span>
                                    </span>
                                </div>
                            </div>
                        </form> <!-- end login form -->

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

                        <div class="form-group row">
                            <div class="row">
                                <div class="col-sm-12 margin-top custom-checkbox" style="padding-left:32px">
                                    <input type="checkbox" name="agree_terms" id="agree_terms" v-model="orderForm.terms">
                                    <label for="agree_terms"> Jeg aksepterer </label>
                                    <a href="/terms/course-terms" target="_new"> kjøpsvilkårene </a> <br>
                                    <input type="hidden" name="terms">
                                </div>
                            </div>
                        </div>

                        <wizard-button v-if="!wizardProps.isLastStep" @click.native="nextTab()"
                            class="wizard-footer-right w-100" :style="wizardProps.fillButtonStyle">
                            {{ orderForm.is_pay_later && wizardProps.activeTabIndex != 0 
                            ? 'Bestill kurs, betal senere' :'Til betaling' }}
                        </wizard-button>
                    </div> <!-- end package-details -->
                </div> <!-- end row -->
            </tab-content>

            <tab-content :title="trans('site.checkout.payment-details')" icon="fas fa-credit-card"
                         :before-change="validateForm">

                <wizard-button  v-if="wizardProps.activeTabIndex > 0"  @click.native="wizardProps.prevTab();" 
                    class="back-btn">
                    Back
                </wizard-button>

                <div id="checkout-display"></div>

            </tab-content>

            <!-- <div style="display: inline;" slot="custom-buttons-right" slot-scope="props"
             v-if="props.activeTabIndex === 0">
                <button type="button" class="wizard-btn" v-if="coursePackage.is_pay_later_allowed" 
                @click="payLaterClicked()" style="background-color: rgb(193, 41, 56);
                    border-color: rgb(193, 41, 56); color: white;">
                    Bestill kurs, betal senere
                </button>

                <button type="button" class="vipps-btn" @click="vippsCheckout();" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i>
                    <span>Hurtigutsjekk med</span>
                    <img src="/images-new/vipps.png" class="inline" alt="vipps-buy-button"
                        :style="isLoading ? 'opacity: .8;' : ''">
                </button>
            </div> -->
            
            <template slot="footer" slot-scope="props">
                
                <div class="wizard-footer-right">
                    <!-- needed to hide the buttons on the footer -->
                    <wizard-button v-if="!props.isLastStep" @click.native="nextTab()"
                        class="wizard-footer-right d-none" :style="props.fillButtonStyle">
                    </wizard-button>
                </div>
            </template>
            
            <button slot="finish" class="d-none">{{ trans('site.checkout.finish') }}</button>
        </form-wizard>

    </div> <!-- end card -->
</template>

<style>
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

    .vipps-btn {
        border: none !important;
        color: #fff;
        background-color: #fe5b24 !important;
        font-weight: 600 !important;
        padding: 0.6180469716em 1.41575em !important;
        position: relative;
    }

    .vipps-btn img.inline {
        height: 2ex;
        display: inline;
        vertical-align: text-bottom;
    }

    .vipps-btn i.fa {
        top: 0;
        bottom: 0;
        left: 8px;
        display: flex;
        align-items: center;
        position: absolute;
        z-index: 1;
    }
</style>

<script>
    import {FormWizard, TabContent} from 'vue-form-wizard'
    import 'vue-form-wizard/dist/vue-form-wizard.min.css'
    import CourseDetails from './partials/details.vue';
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
            countryCode: String,
            terms: String,
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
                    is_pay_later: 0,
                    terms: false
                },
                singleCourseDiscount: 500,
                groupCourseDiscount: 1000,
                isMonthly: false,
                monthlyPrice: 0,
                monthlyPriceFormatted: 0,
                totalDiscount: 0,
                studentDiscount: 0,
                couponDiscount: 0,
                couponDiscountType: 1, // 0 = additional, 1 = total
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
                hasPaidCourse: false,
                isLoading: false,
                wizardProps: {},
                requestUrl: '/course/'+this.course.id
            }
        },

        computed: {
            coupon() {
                return this.orderForm.coupon;
            },

            notAllowedPaymentMode() {
                return this.orderForm.payment_mode_id > 1;
            }
        },

        components: {
            CourseDetails
        },

        mounted() {
            this.checkHasPaidCourse();
            this.loadOptions();
            if (this.orderForm.coupon) {
                this.checkDiscount(this.orderForm.coupon);
            }

            this.wizardProps = this.$refs.wizard;        
        },

        methods: {
            checkHasPaidCourse() {
                axios.get('/has-paid-course/').then(response => {

                    this.hasPaidCourse = response.data;

                    this.packageChanged();
                })
            },

            getCurrentUser() {
                axios.get('/current-user').then(response => {

                    this.currentUser = response.data;
                })
            },

            setSelectedPackage(package_id) {
                this.orderForm.package_id = package_id;
                this.packageChanged();
            },

            packageChanged() {
                const selectedPackageId = this.orderForm.package_id;
                const self = this;
                this.packages.forEach(function(pkg) {
                    if (pkg.id === selectedPackageId) {
                        self.coursePackage =  pkg;
                    }
                });

                this.studentDiscount = 0;
                this.isMonthly = false;

                if( this.hasPaidCourse && this.coursePackage.has_student_discount) {
                    this.studentDiscount = this.singleCourseDiscount;
                    if (this.course.type === 'Group') {
                        this.studentDiscount = this.groupCourseDiscount;
                    }
                }

                this.saleDiscount = this.coursePackage.sale_discount;

                this.totalDiscount = this.orderForm.coupon 
                ? (this.couponDiscountType === 0 ? this.couponDiscount + this.saleDiscount : this.couponDiscount) 
                : this.saleDiscount;

                
                this.origPrice = parseFloat(this.coursePackage.full_payment_price);
                this.orderForm.price = this.coursePackage.full_payment_price;

                let calculatedSveaPrice = parseInt(this.orderForm.campaign_initial_fee) +
                    parseInt(this.orderForm.campaign_admin_fee * this.orderForm.campaign_months);

                this.totalPrice = this.origPrice - this.studentDiscount - this.totalDiscount + calculatedSveaPrice;

                // check if part payment
                if (this.orderForm.payment_mode_id === 2) {
                    this.isMonthly = true;
                    this.monthlyPrice = this.totalPrice/this.orderForm.campaign_months;
                }
            },

            checkDiscount(val, e) {

                this.couponDiscount = 0;
                if (val) {
                    axios.get('/course/'+this.course.id+'/check_coupon_discount/'+val).then(response => {

                        this.couponDiscount = response.data.discountCoupon.discount;
                        this.couponDiscountType = response.data.discountCoupon.type;
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

                    this.invalidCred = false;
                    this.loginText = i18n.site.front.form.login;
                    this.isLoginDisabled = false;
                    this.currentUser = response.data.user;
                    this.loadOptions();
                    this.checkHasPaidCourse();

                    if (response.data.user.course_link) {
                        window.location.href = response.data.user.course_link;
                    }

                    if(!response.data.user.could_buy_course) {
                        window.location.href = '/course/' + this.course.id;
                    }

                    this.$toasted.global.showSuccessMsg({
                        message : response.data.success
                    });

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
                this.orderForm.payment_mode_id = 3; // Faktura
                return axios.post(this.requestUrl+'/checkout/validate-form', this.orderForm).then(response => {
                    this.removeValidationError();
                    this.checkHasPaidCourse();
                    this.getCurrentUser();

                    console.log(response);
                    if (response.data.course_link) {
                        window.location.href = response.data.course_link;
                    }

                    if (response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    }

                    $("#checkout-display").html(response.data);
                    return true;

                }).catch(error => {

                    this.processError(error);

                });
            },

            nextTab() {
                // check if from first tab
                if (this.$refs.wizard.activeTabIndex === 0) {
                    this.orderForm.is_pay_later = 0;
                }

                this.$refs.wizard.nextTab();
            },

            payLaterClicked() {
                this.orderForm.is_pay_later = 1;
                this.$refs.wizard.nextTab();
            },

            vippsCheckout() {
                this.isLoading = true;
                this.orderForm.payment_mode_id = 5; // Vipps
                console.log("vipps checkout");
                return axios.post(this.requestUrl+'/checkout/vipps', this.orderForm).then(response => {
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
            }


        }

    }
</script>