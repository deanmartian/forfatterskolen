<template>
    <div class="card p-4" id="scrollHere">

        <h1 class="font-barlow-regular mb-4" v-html="trans('site.learner.upgrades-text') + ' '
                + shopManuscript.title">
        </h1>

        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="trans('site.paginate.next')" :backButtonText="trans('site.back')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="" :startIndex="startIndex">
            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list" :before-change="validateUpgrade">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>
                                    <h3 class="mb-3">{{ trans('site.learner.current-script-details-text') }}</h3>
                                    <p class="margin-top">
                                        <b>{{ trans('site.learner.script') }}:</b> <br>
                                        {{ shopManuscript.title}}
                                    </p>

                                    <p>
                                        <b>{{ trans('site.learner.description-text') }}:</b> <br>
                                        {{ shopManuscript.description}}
                                    </p>
                                    <p>
                                        <b>{{ trans('site.learner.max-number-of-words-text') }}:</b> <br>
                                        {{ shopManuscript.max_words}} ords
                                    </p>
                                </td>
                                <td>
                                    <h3 class="mb-4"> {{ trans('site.learner.upgrade-to-text') }} </h3>
                                    <div class="custom-radio mb-1" v-for="shopManuscriptUpgrade in shopManuscriptUpgrades" 
                                        :key="shopManuscriptUpgrade.upgrade_shop_manuscript_id">
                                        <input type="radio" name="shop_manuscript_id"
                                               :value="shopManuscriptUpgrade.upgrade_shop_manuscript_id"
                                               :id="shopManuscriptUpgrade.upgrade_manuscript.title"
                                               v-model="upgradeForm.shop_manuscript_id" 
                                               @change="packageChanged(shopManuscriptUpgrade)">
                                        <label :for="shopManuscriptUpgrade.upgrade_manuscript.title">
                                            {{ shopManuscriptUpgrade.upgrade_manuscript.title }}
                                            - {{ shopManuscriptUpgrade.upgrade_manuscript.max_words }} ord
                                            ({{ (parseFloat(shopManuscriptUpgrade.price) 
                                                + parseFloat(shopManuscriptUpgrade.price_25_additional)) 
                                                | currency('Kr', 2, currencyOptions) }})</label>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="w-100 table">
                            <tbody>
                            <tr>
                                <td class="text-right h3">{{ trans('site.front.price') }}:</td>
                                <td class="text-right h3 text-red" width="150">
                                    {{ upgradeForm.price | currency('Kr', 2, currencyOptions) }}
                                </td>
                            </tr>
                            <tr v-if="upgradeForm.additional > 0">
                                <td class="text-right h3">Mva 25%:</td>
                                <td class="text-right h3 text-red" width="150">
                                    {{ upgradeForm.additional | currency('Kr', 2, currencyOptions) }}
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
                           v-model="upgradeForm.email"
                           :disabled="currentUser"
                           :placeholder="trans('site.front.form.email')">
                </div> <!-- end email form-group -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="first_name" class="control-label">
                            {{ trans('site.first-name') }}
                        </label>
                        <input type="text" id="first_name" class="form-control" name="first_name" required
                               v-model="upgradeForm.first_name"
                               :disabled="currentUser"
                               :placeholder="trans('site.first-name')">
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="control-label">
                            {{ trans('site.last-name') }}
                        </label>
                        <input type="text" id="last_name" class="form-control" name="last_name" required
                               v-model="upgradeForm.last_name"
                               :disabled="currentUser"
                               :placeholder="trans('site.last-name')">
                    </div>
                </div> <!-- end first and last name -->

                <div class="form-group">
                    <label for="street" class="control-label">
                        {{ trans('site.front.form.street') }}
                    </label>
                    <input type="text" id="street" class="form-control" name="street" required
                           v-model="upgradeForm.street"
                           :placeholder="trans('site.checkout.street')">
                </div> <!-- end street -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="zip" class="control-label">{{ trans('site.front.form.zip') }}</label>
                        <input type="text" id="zip" class="form-control" name="zip" required
                               v-model="upgradeForm.zip" :placeholder="trans('site.checkout.zip')">
                    </div>
                    <div class="col-md-6">
                        <label for="city" class="control-label">{{ trans('site.front.form.city') }}</label>
                        <input type="text" id="city" class="form-control" name="city" required
                               v-model="upgradeForm.city" :placeholder="trans('site.checkout.city')">
                    </div>
                </div> <!-- end zip, city -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="phone" class="control-label">
                            {{ trans('site.front.form.phone-number') }}
                        </label>
                        <input type="text" id="phone" class="form-control" name="phone" required
                               v-model="upgradeForm.phone" :placeholder="trans('site.checkout.phone')">
                    </div>

                    <div class="col-md-6" v-if="!currentUser">
                        <label for="password" class="control-label">
                            {{ trans('site.front.form.create-password') }}
                        </label>
                        <input type="password" id="password" class="form-control"
                               name="password" required :placeholder="trans('site.front.form.create-password')"
                               v-model="upgradeForm.password">
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
            shopManuscriptTaken: Object,
            shopManuscriptUpgrades: Array,
            currentUser: Object,
            shopManuscript: Object
        },

        data() {
            return {
                startIndex: 0,
                upgradeForm: {
                    email: '',
                    first_name: '',
                    last_name: '',
                    street: '',
                    zip: '',
                    city: '',
                    phone: '',
                    password: '',
                    price: 0,
                    package_id: 0,
                    payment_plan_id: 8,
                    payment_mode_id: 1,
                    mobile_number: "",
                    order_type: 7,
                    shop_manuscript_id: null,
                    additional: 0
                },
                currencyOptions: {
                    thousandsSeparator: '.',
                    decimalSeparator: ',',
                    spaceBetweenAmountAndSymbol: true
                },
                requestUrl: '/account/upgrade-manuscript/' + this.shopManuscriptTaken.id
            }
        },

        computed: {
            totalPrice() {
                return parseFloat(this.upgradeForm.price) + parseFloat(this.upgradeForm.additional);
            }
        },

        methods: {
            loadOptions() {
                this.upgradeForm.email = this.currentUser ? this.currentUser.email : '';
                this.upgradeForm.first_name = this.currentUser ? this.currentUser.first_name : '';
                this.upgradeForm.last_name = this.currentUser ? this.currentUser.last_name : '';
                this.upgradeForm.street = this.currentUser && this.currentUser.address ? this.currentUser.address.street
                    : '';
                this.upgradeForm.zip = this.currentUser && this.currentUser.address ? this.currentUser.address.zip : '';
                this.upgradeForm.city = this.currentUser && this.currentUser.address ? this.currentUser.address.city : '';
                this.upgradeForm.phone = this.currentUser && this.currentUser.address ? this.currentUser.address.phone : '';
                this.upgradeForm.national_id = this.currentUser && this.currentUser.address ? this.currentUser.address.national_id : '';
            },

            packageChanged(shopManuscriptUpgrade) {
                this.upgradeForm.price = shopManuscriptUpgrade.price;
                this.upgradeForm.additional = shopManuscriptUpgrade.price_25_additional;
            },

            validateUpgrade() {
                if (!this.upgradeForm.shop_manuscript_id) {
                    this.$toasted.global.showErrorMsg({
                        message : 'Please select a package'
                    });
                    return false;
                }

                return true;
            },

            validateForm() {
                let self = this;
                self.removeValidationError();

                return axios.post(self.requestUrl+'/validate-form', self.upgradeForm).then(response => {

                    if (response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        $("#checkout-display").html(response.data);
                    }
                    
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
            console.log("here here here");
            console.log(this.shopManuscriptUpgrades);
            this.loadOptions();
        }

    }
</script>