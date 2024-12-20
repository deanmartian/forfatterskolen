<template>
    <div class="card p-4 publishing-order" id="scrollHere">
        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="trans('site.paginate.next')" :backButtonText="trans('site.back')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="" :startIndex="startIndex"
                     ref="wizard">
            <tab-content :title="'Bestillingsskjema'" icon="fa fa-clipboard-list" :before-change="validateOrder">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>
                                {{ trans('site.title') }}
                            </label>
                            <input type="text" name="title" class="form-control" v-model="orderForm.title">
                        </div>

                        <div class="form-group">
                            <label>
                                {{ trans('site.description') }}
                            </label>
                            <textarea class="form-control" name="description" cols="30" rows="10" 
                                v-model="orderForm.description"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="mb-0">
                                {{ trans('site.learner.manuscript-text') }}
                            </label>

                            <FileUpload
                            :accept="'application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,' 
                            + 'application/pdf, application/vnd.oasis.opendocument.text'" 
                            @fileSelected="handleFileSelected('manuscript', $event)"/>
                            <input type="hidden" name="manuscript">
                        </div>

                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <td class="text-right h3">{{ trans('site.front.price') }}:</td>
                                <td class="text-right h3 text-red" style="width: 150px">
                                    {{ orderForm.price | currency('Kr', 2, currencyOptions) }}
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
                    </div>
                </div>
            </tab-content>

            <tab-content :title="trans('site.front.form.user-information')" icon="fas fa-id-card"
                         :before-change="validateForm" style="min-height: 300px">

                <wizard-button  v-if="wizardProps.activeTabIndex > 0"  @click.native="wizardProps.prevTab();" 
                    class="back-btn">
                    {{ trans('site.back') }}
                </wizard-button>

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

            <template slot="footer" slot-scope="props">
                <div class="wizard-footer-left">
                    <wizard-button v-if="props.activeTabIndex > 0"
                                    @click.native="props.prevTab();"
                                    :style="props.fillButtonStyle">
                        {{ trans('site.back') }}
                    </wizard-button>
                </div>
                <div class="wizard-footer-right">
                    <wizard-button @click.native="handleNextTab(props)" 
                        class="wizard-footer-right"
                        :class="{'w-100': props.activeTabIndex === 1 }"
                                    :style="props.fillButtonStyle" :disabled="isLoadingSubmit">
                            <i class="fa fa-pulse fa-spinner" v-if="isLoadingSubmit"></i> 
                            {{ props.isLastStep ? trans('site.front.buy')
                                : trans('site.learner.next-text') }}
                    </wizard-button>
                </div>
            </template>
        </form-wizard>
    </div>
</template>

<script>
import FileUpload from '../../components/FileUpload.vue';

export default {
    props: ['shop-manuscript', 'user', 'project'],
    data() {
        return {
            currentUser: this.user,
            startIndex: 0,
            orderForm: {
                email: '',
                first_name: '',
                last_name: '',
                street: '',
                zip: '',
                city: '',
                phone: '',
                title: '',
                description: '',
                price: this.shopManuscript.full_payment_price,
                project_id: this.project.id,
                word_count: 0
            },
            currencyOptions: {
                thousandsSeparator: '.',
                decimalSeparator: ',',
                spaceBetweenAmountAndSymbol: true
            },
            isLoadingSubmit: false,
            wizardProps: {},
        }
    },

    computed: {
        totalPrice() {
            return parseFloat(this.orderForm.price);
        }
    },

    components: {
        FileUpload,
    },

    methods: {
        scrollTop() {
            jQuery([document.documentElement, document.body]).animate({
                scrollTop: $("#scrollHere").offset().top
            }, 1000);
        },

        handleNextTab(props) {
            // Call the next tab method
            props.nextTab();
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

        validateOrder() {
            this.removeValidationError();
            this.isLoadingSubmit = true;
            
            return new Promise((resolve, reject) => {
                let formData = new FormData();
                $.each(this.orderForm, function(k, v) {
                    formData.append(k, v);
                });
                // Add your form data here if needed

                axios.post('/account/self-publishing/publishing/order/validate', formData)
                    .then(response => {
                        this.orderForm.excess_words_amount = response.data.excess_words_amount;
                        this.orderForm.word_count = response.data.word_count;
                        this.orderForm.price = response.data.price;
                        this.orderForm.price = parseFloat(this.orderForm.price) + response.data.excess_words_amount;

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
        },

        handleFileSelected(type, file) {
            this.orderForm.manuscript = file;
            this.computeManuscriptPrice();
        },

        computeManuscriptPrice() {
            let formData = new FormData();
            $.each(this.orderForm, function(k, v) {
                formData.append(k, v);
            });

            formData.append('is_manuscript_only', true);

            axios.post('/account/self-publishing/publishing/order/validate', formData).then(response => {
                console.log(response);
                this.orderForm.excess_words_amount = response.data.excess_words_amount;
                this.orderForm.price = response.data.price;
                this.orderForm.price = parseFloat(this.orderForm.price) + response.data.excess_words_amount;
            });
        },

        validateForm() {
            this.removeValidationError();

            let formData = new FormData();
            this.orderForm.payment_mode_id = 3; // Faktura
            $.each(this.orderForm, function(k, v) {
                formData.append(k, v);
            });
            this.isLoadingSubmit = true;
            return axios.post('/account/self-publishing/publishing/order/process', formData).then(response => {
                this.removeValidationError();
                //this.isLoadingSubmit = false;
                window.location.href = '/publishing-service/thank-you';
                //return false;

            }).catch(error => {

                this.processError(error);
                this.isLoadingSubmit = false;

            });
        }
    },

    mounted() {
        this.wizardProps = this.$refs.wizard;
        this.loadOptions();
    }
}
</script>

<style>
.publishing-order .file-upload {
    border-radius: 4px;
    background-color: #f8f8ff;
    border-color: rgb(56, 78, 183, 30%);
    font-family: Inter;
    font-weight: 700;
    min-height: 50px;
    padding: 0;
}

.publishing-order .table {
    margin-top: 30px;
}
</style>

