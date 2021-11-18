<template>
    <div class="card p-4" id="scrollHere">

        <h1 class="font-barlow-regular mb-4" v-html="trans('site.front.buy') + ' '
                + assignment.title">
        </h1>


        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="trans('site.paginate.next')" :backButtonText="trans('site.back')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="">
            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list">

                <div class="row">
                    <div class="col-sm-12">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>
                                    <h3>{{ assignment.title }}</h3>
                                    <b>{{ trans('site.learner.description-text') }}:</b>
                                    {{ assignment.description }} <br>
                                    <b>{{ trans('site.learner.deadline') }}:</b>
                                    <span v-html="assignment.submission_date_time_text"></span> <br>

                                    <b>{{ trans('site.learner.max-number-of-words-text') }}:</b>
                                    {{ assignment.max_words }} {{ trans('site.learner.words-text') }}
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="text-right h3">{{ trans('site.front.price') }}:</td>
                                <td class="text-right h3 text-red" width="150">
                                    {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right h3">{{ trans('site.front.total') }}:</td>
                                <td class="text-right h3 text-red">
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
                </div>

            </tab-content>

            <tab-content :title="trans('site.checkout.payment-details')" icon="fas fa-credit-card"
                         :before-change="validateForm">

                <div id="checkout-display"></div>

            </tab-content>
        </form-wizard>

    </div>
</template>

<script>
    export default {

        props: {
            assignment: Object
        },

        data() {
            return {
                currentUser: null,
                orderForm: {
                    email: '',
                    first_name: '',
                    last_name: '',
                    street: '',
                    zip: '',
                    city: '',
                    phone: '',
                    password: '',
                    price: this.assignment.add_on_price,
                    package_id: 0,
                    payment_plan_id: 8,
                    payment_mode_id: 1,
                    mobile_number: "",
                    order_type: 8,
                    assignment_id: this.assignment.id,
                },
                currencyOptions: {
                    thousandsSeparator: '.',
                    decimalSeparator: ',',
                    spaceBetweenAmountAndSymbol: true
                },
                requestUrl: '/account/upgrade/assignment/' + this.assignment.id
            }
        },

        computed: {
            totalPrice() {
                return this.orderForm.price;
            }
        },

        methods: {
            getCurrentUser() {
                axios.get('/current-user').then(response => {
                    this.currentUser = response.data;
                    this.loadOptions();
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

            validateForm() {
                let self = this;
                self.removeValidationError();

                return axios.post(self.requestUrl+'/validate-form', self.orderForm).then(response => {
                    $("#checkout-display").html(response.data);
                    return true;

                }).catch(error => {

                    this.processError(error);

                });
            }
        },

        mounted() {
            this.getCurrentUser();
        }

    }
</script>