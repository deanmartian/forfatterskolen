<template>
    <div class="card" id="scrollHere">
        <h1 class="font-barlow-regular mb-4 p-3">
            {{ trans('site.learner.upgrades-text') }} {{courseTaken.package.course.title}}
        </h1>

        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="trans('site.paginate.next')" :backButtonText="trans('site.back')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="" :startIndex="startIndex">
            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td>
                            <h3 class="font-barlow-medium">{{ trans('site.front.course-text') }}</h3>
                            <p>
                                <b>{{courseTaken.package.course.title}}</b>
                            </p>
                        </td>
                        <td>
                            <h3 class="font-barlow-medium">{{ trans('site.learner.current-package-text') }}</h3>
                            <p>
                                <b>{{courseTaken.package.variation}}</b>
                            </p>
                            <div v-html="courseTaken.package.description_formatted">
                            </div>
                        </td>
                        <td width="30%">
                            <h3>{{ trans('site.front.form.course-package') }}:</h3>
                            <div class="package-option custom-radio">
                                <input type="radio" name="package_id" :id="currentPackage.variation"
                                       v-model="orderForm.package_id"
                                       :value="currentPackage.id">
                                <label :for="currentPackage.variation" v-text="currentPackage.variation" class="font-weight-normal"></label>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h3 class="font-barlow-medium">{{ trans('site.learner.upgrade-to-text') }}:</h3>
                            <p>
                                <b>{{currentPackage.variation}}</b>
                            </p>
                            <div v-html="currentPackage.description_formatted">
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="text-right h3">{{ trans('site.front.price') }}:</td>
                        <td class="text-right h3 text-red" width="200">
                            {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
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
            </tab-content>

            <tab-content :title="trans('site.front.form.user-information')" icon="fas fa-id-card"
                         :before-change="validateForm" style="min-height: 300px">

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
    </div>
</template>

<script>
    export default {

        props: {
            courseTaken: Object,
            currentPackage: Object,
            currentUser: Object,
        },

        data() {
            return {
                startIndex: 0,
                orderForm: {
                    email: '',
                    first_name: '',
                    last_name: '',
                    street: '',
                    zip: '',
                    city: '',
                    phone: '',
                    password: '',
                    price: 0,
                    package_id: this.currentPackage.id,
                    payment_plan_id: 8,
                    payment_mode_id: 1,
                    mobile_number: "",
                    order_type: 6
                },
                currencyOptions: {
                    thousandsSeparator: '.',
                    decimalSeparator: ',',
                    spaceBetweenAmountAndSymbol: true
                },
                totalPrice: 0,
                hasPaidCourse: false,
                isLoading: false,
                requestUrl: '/account/upgrade-course/' + this.courseTaken.id
            }
        },

        methods: {

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

            checkHasPaidCourse() {
                axios.get('/has-paid-course/').then(response => {

                    this.hasPaidCourse = response.data;

                    this.calculatePrice();
                })
            },

            calculatePrice() {
                if (this.currentPackage.course_type === 3 || this.currentPackage.course_type === 2) {
                    this.orderForm.price = this.currentPackage.full_payment_upgrade_price;
                }

                if (this.currentPackage.course_type === 3 && this.courseTaken.package.course_type === 2) {
                    this.orderForm.price = this.currentPackage.full_payment_standard_upgrade_price;
                }

                this.totalPrice = this.orderForm.price;
            },

            validateForm() {
                let self = this;
                self.removeValidationError();

                return axios.post(self.requestUrl+'/validate-form', self.orderForm).then(response => {

                    self.scrollTop();
                    $("#checkout-display").html(response.data);
                    return true;

                }).catch(error => {

                    this.processError(error);

                });
            },

            scrollTop() {
                let time = 5;
                $([document.documentElement, document.body]).animate({
                    scrollTop: $("#scrollHere").offset().top
                }, 2000);
            }

        },

        mounted() {
            this.loadOptions();
            this.checkHasPaidCourse();
        }
    }
</script>