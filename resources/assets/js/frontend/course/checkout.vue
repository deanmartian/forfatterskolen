<template>
    <div class="card">

        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="trans('site.paginate.next')" :backButtonText="trans('site.paginate.previous')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="">
            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td width="25%">
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

                                    <template
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
                                    </template>

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

                                <h5 style="font-weight: 400">{{ trans('site.checkout.package') }}:</h5>
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
                            <td class="text-right h3">{{ trans('site.front.price') }}:</td>
                            <td class="text-right h3 text-red">
                                {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
                            </td>
                        </tr>

                        <tr v-if="studentDiscount">
                            <td></td>
                            <td class="text-right h3">Studentrabatt:</td>
                            <td class="text-right h3 text-red">
                                {{ studentDiscount | currency('Kr', 2, currencyOptions) }}
                            </td>
                        </tr>

                        <tr v-if="totalDiscount">
                            <td></td>
                            <td class="text-right h3">{{ trans('site.front.discount') }}:</td>
                            <td class="text-right h3 text-red">
                                {{ totalDiscount | currency('Kr', 2, currencyOptions) }}
                            </td>
                        </tr>

                        <tr v-if="isMonthly">
                            <td></td>
                            <td class="text-right h3">{{ trans('site.checkout.per-month') }}:</td>
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
                         :before-change="validateForm">
                <form @submit.prevent="handleLogin($event)" v-if="!currentUser" class="mb-4">
                    <div class="row">
                        <div class="col-sm-12">
                            <span>
                                {{ trans('site.front.form.already-registered-text') }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4 mb-0">
                            <input type="text" name="email" :placeholder="trans('site.front.form.email')"
                                   class="form-control" v-model="loginForm.email" required>

                            <p style="margin-top: 7px;">
                                <a href="/auth/login?t=passwordreset" tabindex="-1" class="text-red">
                                    {{ trans('site.front.form.reset-password') }}?
                                </a>
                            </p>
                        </div>

                        <div class="form-group col-sm-4 mb-0">
                            <input type="password" name="login_password" :placeholder="trans('site.front.form.password')"
                                   class="form-control" v-model="loginForm.password" required>
                        </div>
                        <div class="form-group col-sm-4 mb-0">
                            <button type="submit" class="btn site-btn-global"
                                    :disabled="isLoginDisabled">
                                <i class="fas fa-spinner fa-spin" v-if="isLoginDisabled"></i>
                                {{ loginText }}
                            </button>
                        </div>
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
            </tab-content>

            <tab-content :title="trans('site.checkout.payment-details')" icon="fas fa-credit-card"
                         :before-change="validateForm">

                <div id="checkout-display"></div>

            </tab-content>

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
                    payment_mode_id: 1,
                    mobile_number: "",
                    campaign_code: '',
                    campaign_months: 0,
                    campaign_initial_fee: 0,
                    campaign_admin_fee: 0,
                },
                singleCourseDiscount: 500,
                groupCourseDiscount: 1000,
                isMonthly: false,
                monthlyPrice: 0,
                monthlyPriceFormatted: 0,
                totalDiscount: 0,
                studentDiscount: 0,
                couponDiscount: 0,
                couponDiscountFormatted: 0,
                totalPrice: 0,
                totalPriceFormatted: 0,
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

        mounted() {
            this.checkHasPaidCourse();
            this.loadOptions();
            if (this.orderForm.coupon) {
                this.checkDiscount(this.orderForm.coupon);
            }
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

                this.totalDiscount = this.couponDiscount;
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
                return axios.post(this.requestUrl+'/checkout/validate-form', this.orderForm).then(response => {
                    this.removeValidationError();
                    this.checkHasPaidCourse();
                    this.getCurrentUser();

                    console.log(response);

                    $("#checkout-display").html(response.data);
                    return true;

                }).catch(error => {

                    this.processError(error);

                });
            },

            prevTab() {
                console.log("adfafd");
            }


        }

    }
</script>