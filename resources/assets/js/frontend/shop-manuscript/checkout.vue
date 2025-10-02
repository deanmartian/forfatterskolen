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

                            <div v-if="tempFileInfo" class="temp-file-container">
                                <div class="temp-file-details">
                                    <span class="temp-file-name">{{ tempFileInfo.original_name }}</span>
                                    <span v-if="tempFileInfo.word_count" class="temp-file-meta">
                                        ~{{ tempFileInfo.word_count }} {{ trans('site.learner.words-text') }}
                                    </span>
                                </div>
                                <button type="button" @click="removeFile">x</button>
                            </div>

                            <FileUpload
                            :accept="'application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,application/vnd.oasis.opendocument.text'"
                            @fileSelected="handleFileSelected('manuscript', $event)" v-else/>
                            <input type="hidden" name="manuscript">

                            <div class="word-count-feedback mt-3" v-if="wordCountFeedback"
                                 :class="{'text-danger': wordCountFeedbackIsError}">
                                {{ wordCountFeedback }}
                            </div>
                            <div class="word-count-feedback mt-2" v-if="wordCountPriceFeedback"
                                 :class="{'text-danger': wordCountPriceFeedbackIsError}" v-html="wordCountPriceFeedback">
                            </div>

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
                            :accept="'application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,' 
                            + 'application/pdf, application/vnd.oasis.opendocument.text'" 
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

                            <tr v-if="orderForm.totalDiscount">
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
                                        && props.activeTabIndex > 0) || isLoadingSubmit">
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
                            class="wizard-footer-right w-100" :style="props.fillButtonStyle">
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
import FileUpload from '../../components/FileUpload.vue';
    export default {

        props: {
            user: Object,
            shopManuscript: Object,
            assignmentTypes: Array,
            userHasPaidCourse: Boolean,
            origPrice: [Number, String],
            tempFile: Object,
            storeTempUploadUrl: {
                type: String,
                default: '/shop-manuscript/store-temp-upload',
            },
            excessPerWord: {
                type: Number,
                default: 0,
            },
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
                    price: this.tempFile ? this.tempFile.price : this.origPrice,
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
                    has_vat: !this.userHasPaidCourse,
                    //is_pay_later: !this.userHasPaidCourse,
                    additional: !this.userHasPaidCourse ? (this.shopManuscript.full_payment_price * .25) : 0,
                    excess_words_amount: this.tempFile ? this.tempFile.excess_words_amount || 0 : 0,
                    temp_file: this.tempFile ? 'uploaded' : null,
                    manuscript: null,
                    synopsis: null,
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
                originalPrice: this.tempFile ? this.tempFile.price : this.origPrice,
                isSveaPayment: true,
                invalidCred: false,
                isLoginDisabled: false,
                loginText: i18n.site.front.form.login,
                isNewCustomer: false,
                manuscriptName: i18n.site['learner.files-text'],
                synopsisName: i18n.site['learner.files-text'],
                hasPaidCourse: false,
                isLoading: false,
                isLoadingSubmit: false,
                wizardProps: {},
                requestUrl: '/shop-manuscript/'+this.shopManuscript.id,
                tempFileInfo: this.tempFile ? Object.assign({}, this.tempFile) : null,
                wordCountFeedback: '',
                wordCountFeedbackIsError: false,
                wordCountPriceFeedback: '',
                wordCountPriceFeedbackIsError: false,
                wordCountProcessing: false,
                manuscriptBaseWordLimit: 17500,
                allowedWordCountExtensions: ['docx', 'pdf', 'doc', 'odt'],
            }
        },

        computed: {
            totalPrice() {
                return parseFloat(this.orderForm.price) - this.orderForm.totalDiscount + parseFloat(this.orderForm.additional);
            }
        },

        components: {
            FileUpload,
        },

        methods: {

            getFileExtension(fileName) {
                if (!fileName) {
                    return '';
                }

                const match = fileName.toLowerCase().match(/\.([^.]+)$/);
                return match ? match[1] : '';
            },

            mammothAvailable() {
                return typeof window !== 'undefined'
                    && typeof window.mammoth !== 'undefined'
                    && typeof window.mammoth.extractRawText === 'function';
            },

            shouldUseMammothForExtension(extension) {
                if (!extension) {
                    return false;
                }

                const preferredExtensions = ['doc', 'docx'];
                return preferredExtensions.includes(extension) && this.mammothAvailable();
            },

            countWordsFromText(text) {
                if (typeof text !== 'string') {
                    return 0;
                }

                const normalised = text.replace(/[\r\n\t]+/g, ' ').trim();
                if (!normalised) {
                    return 0;
                }

                const matches = normalised.match(/\S+/g);
                return matches ? matches.length : 0;
            },

            extractWordCountWithMammoth(file) {
                return new Promise((resolve, reject) => {
                    if (!file || !this.mammothAvailable()) {
                        resolve(null);
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = (event) => {
                        const arrayBuffer = event && event.target ? event.target.result : null;

                        if (!arrayBuffer) {
                            resolve(null);
                            return;
                        }

                        window.mammoth.extractRawText({ arrayBuffer })
                            .then((result) => {
                                const text = result && typeof result.value === 'string' ? result.value : '';
                                resolve(this.countWordsFromText(text));
                            })
                            .catch((error) => {
                                reject(error);
                            });
                    };

                    reader.onerror = () => {
                        reject(reader.error || new Error('Kunne ikke lese dokumentet.'));
                    };

                    try {
                        reader.readAsArrayBuffer(file);
                    } catch (error) {
                        reject(error);
                    }
                });
            },

            setWordCountFeedback(message, isError = false) {
                this.wordCountFeedback = message;
                this.wordCountFeedbackIsError = !!isError;
            },

            setWordCountPriceFeedback(message, isError = false) {
                this.wordCountPriceFeedback = message;
                this.wordCountPriceFeedbackIsError = !!isError;
            },

            resetWordCountMessages() {
                this.setWordCountFeedback('', false);
                this.setWordCountPriceFeedback('', false);
            },

            showGlobalAlert(messages, type = 'danger') {
                const normalisedMessages = Array.isArray(messages)
                    ? messages.filter((message) => !!message)
                    : (messages ? [messages] : []);
                const uniqueMessages = Array.from(new Set(normalisedMessages.map((message) => message.trim())));

                if (!uniqueMessages.length) {
                    return;
                }

                let alertElement = document.getElementById('fixed_to_bottom_alert');
                if (!alertElement) {
                    alertElement = document.createElement('div');
                    alertElement.id = 'fixed_to_bottom_alert';
                    alertElement.className = 'alert global-alert-box';
                    alertElement.setAttribute('role', 'alert');
                    alertElement.style.zIndex = '9';
                    alertElement.style.minWidth = '300px';

                    const closeButton = document.createElement('a');
                    closeButton.href = '#';
                    closeButton.className = 'close';
                    closeButton.setAttribute('data-dismiss', 'alert');
                    closeButton.setAttribute('aria-label', 'close');
                    closeButton.setAttribute('title', 'close');
                    closeButton.innerHTML = '&times;';
                    closeButton.addEventListener('click', (event) => {
                        event.preventDefault();
                        if (alertElement.parentNode) {
                            alertElement.parentNode.removeChild(alertElement);
                        } else {
                            alertElement.remove();
                        }
                    });

                    const list = document.createElement('ul');
                    alertElement.appendChild(closeButton);
                    alertElement.appendChild(list);

                    document.body.appendChild(alertElement);
                }

                alertElement.classList.add('alert', 'global-alert-box');
                alertElement.classList.remove('alert-danger', 'alert-success', 'alert-info', 'alert-warning', 'alert-primary');
                alertElement.classList.add(`alert-${type}`);

                let list = alertElement.querySelector('ul');
                if (!list) {
                    list = document.createElement('ul');
                    alertElement.appendChild(list);
                }

                list.innerHTML = '';
                uniqueMessages.forEach((message) => {
                    const item = document.createElement('li');
                    item.innerHTML = message;
                    list.appendChild(item);
                });

                alertElement.style.display = 'block';
                alertElement.classList.remove('d-none');
            },

            calculateExcessWordsAmount(wordCount) {
                if (!Number.isInteger(wordCount) || wordCount <= this.manuscriptBaseWordLimit) {
                    return 0;
                }

                const excessWords = wordCount - this.manuscriptBaseWordLimit;
                const perWord = Number(this.excessPerWord) || 0;
                return excessWords * perWord;
            },

            storeTempFileOnServer(file, providedWordCount = null) {
                if (!file) {
                    return Promise.resolve({});
                }

                const formData = new FormData();
                const tokenElement = document.head.querySelector('meta[name="csrf-token"]');
                if (tokenElement) {
                    formData.append('_token', tokenElement.getAttribute('content'));
                }

                formData.append('manuscript', file);

                if (Number.isInteger(providedWordCount) && providedWordCount > 0) {
                    formData.append('word_count', providedWordCount);
                }

                return axios.post(this.storeTempUploadUrl, formData)
                    .then((response) => response.data)
                    .catch((error) => {
                        if (error && error.response) {
                            const data = error.response.data || {};
                            const errorMessages = [];

                            if (data.message && data.message !== 'The given data was invalid.') {
                                errorMessages.push(data.message);
                            }

                            if (data.errors) {
                                Object.values(data.errors).forEach((entry) => {
                                    if (Array.isArray(entry)) {
                                        entry.forEach((item) => {
                                            if (item) {
                                                errorMessages.push(item);
                                            }
                                        });
                                    } else if (entry) {
                                        errorMessages.push(entry);
                                    }
                                });
                            }

                            if (!errorMessages.length) {
                                errorMessages.push('Kunne ikke lagre resultatet på serveren. Prøv igjen senere.');
                            }

                            const uploadError = new Error(errorMessages[0]);
                            uploadError.alertMessages = errorMessages;
                            uploadError.responseData = data;

                            throw uploadError;
                        }

                        throw error;
                    });
            },

            async processManuscriptFile(file) {
                if (!file) {
                    this.orderForm.manuscript = null;
                    this.orderForm.temp_file = null;
                    this.tempFileInfo = null;
                    this.orderForm.excess_words_amount = 0;
                    this.resetWordCountMessages();
                    return;
                }

                const extension = this.getFileExtension(file.name);

                if (!this.allowedWordCountExtensions.includes(extension)) {
                    this.orderForm.manuscript = null;
                    this.orderForm.temp_file = null;
                    this.tempFileInfo = null;
                    this.orderForm.excess_words_amount = 0;
                    this.setWordCountFeedback('Vennligst velg en DOCX-, PDF-, DOC- eller ODT-fil for ordtelling.', true);
                    this.setWordCountPriceFeedback('');
                    return;
                }

                let storedSuccessfully = false;
                this.wordCountProcessing = true;
                this.setWordCountFeedback(this.shouldUseMammothForExtension(extension)
                    ? 'Bruker Mammoth til å beregne antall ord ...'
                    : 'Laster opp og beregner antall ord ...');
                this.setWordCountPriceFeedback('');

                try {
                    const useMammoth = this.shouldUseMammothForExtension(extension);
                    let serverData = null;

                    if (useMammoth) {
                        try {
                            const mammothWordCount = await this.extractWordCountWithMammoth(file);
                            if (Number.isInteger(mammothWordCount) && mammothWordCount > 0) {
                                serverData = await this.storeTempFileOnServer(file, mammothWordCount);
                            } else {
                                serverData = await this.storeTempFileOnServer(file);
                            }
                        } catch (mammothError) {
                            console.error('Unable to count words with Mammoth for checkout form', mammothError);
                            serverData = await this.storeTempFileOnServer(file);
                        }
                    } else {
                        serverData = await this.storeTempFileOnServer(file);
                    }

                    storedSuccessfully = true;
                    const serverWordCount = Number.isInteger(serverData && serverData.word_count)
                        ? serverData.word_count
                        : null;

                    this.tempFileInfo = {
                        original_name: file.name,
                        word_count: serverWordCount,
                        formatted_price: serverData && serverData.formatted_price ? serverData.formatted_price : null,
                        price: serverData && typeof serverData.price !== 'undefined' ? serverData.price : null,
                        plan: serverData && serverData.plan ? serverData.plan : null,
                    };

                    this.orderForm.temp_file = 'uploaded';
                    this.orderForm.manuscript = null;
                    this.orderForm.excess_words_amount = serverWordCount
                        ? this.calculateExcessWordsAmount(serverWordCount)
                        : 0;

                    if (serverWordCount) {
                        const priceMessage = this.tempFileInfo && this.tempFileInfo.formatted_price
                            ? `Prisen for ditt manus er ${this.tempFileInfo.formatted_price}.`
                            : 'Prisen er oppdatert.';
                        this.setWordCountFeedback(`Manuskriptet inneholder omtrent ${serverWordCount} ord.`);
                        this.setWordCountPriceFeedback(priceMessage, false);
                    } else if (serverData && serverData.message) {
                        this.setWordCountFeedback('Beregningen ble fullført.');
                        this.setWordCountPriceFeedback(serverData.message, false);
                    } else {
                        this.setWordCountFeedback('Beregningen ble fullført.');
                        this.setWordCountPriceFeedback('');
                    }

                    try {
                        await this.computeManuscriptPrice();
                        this.originalPrice = parseFloat(this.shopManuscript.full_payment_price)
                            + (this.orderForm.excess_words_amount || 0);
                    } catch (pricingError) {
                        this.setWordCountFeedback('Kunne ikke oppdatere prisen. Se varselet for detaljer.', true);
                        this.setWordCountPriceFeedback('');
                        throw pricingError;
                    }
                } catch (error) {
                    const errorMessages = Array.isArray(error && error.alertMessages)
                        ? error.alertMessages.filter((message) => !!message)
                        : [];
                    const fallbackMessage = typeof error === 'string'
                        ? error
                        : (error && error.message)
                            ? error.message
                            : 'Kunne ikke beregne antall ord. Prøv igjen senere.';
                    const messagesToShow = errorMessages.length ? errorMessages : [fallbackMessage];
                    this.showGlobalAlert(messagesToShow, 'danger');
                    this.setWordCountFeedback('Kunne ikke beregne antall ord. Se varselet for detaljer.', true);
                    this.setWordCountPriceFeedback('');
                    this.tempFileInfo = null;
                    this.orderForm.temp_file = null;
                    this.orderForm.excess_words_amount = 0;
                    this.orderForm.manuscript = null;
                    if (storedSuccessfully) {
                        axios.get('/forget-session-key/temp_uploaded_file');
                    }
                    this.genreChanged();
                } finally {
                    this.wordCountProcessing = false;
                }
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
                    this.hasPaidCourse = response.data;
                    this.genreChanged();
                    this.orderForm.price = this.originalPrice; // to set original price instead of the 2900
                })
            },

            genreChanged() {
                let shopManuscript = this.shopManuscript;
                let totalDiscount = this.hasPaidCourse ? (shopManuscript.full_payment_price * 0.05) : 0;
                let price = parseFloat(this.shopManuscript.full_payment_price) + this.orderForm.excess_words_amount;
                let additional = price * .25;

                if (this.orderForm.genre === 10) {
                    price = price + ((price - totalDiscount) * .50);
                    additional = price * .25; // get the new additional price
                }

                if (this.orderForm.genre === 17) { // novelle genre
                    price = price + ((price - totalDiscount) * .30);
                    additional = price * .25; // get the new additional price
                }

                //this.orderForm.totalDiscount = totalDiscount;
                this.orderForm.price = price;
                this.orderForm.additional = !this.hasPaidCourse ? additional : 0;
            },

            async handleFileSelected(type, file) {
                if (type === 'synopsis') {
                    this.orderForm.synopsis = file;
                } else {
                    this.orderForm.manuscript = file;
                    await this.processManuscriptFile(file);
                }
            },

            computeManuscriptPrice() {
                const formData = new FormData();

                Object.entries(this.orderForm).forEach(([key, value]) => {
                    if (key === 'manuscript') {
                        if (value instanceof File) {
                            formData.append(key, value);
                        }
                        return;
                    }

                    if (typeof value === 'boolean') {
                        formData.append(key, value ? 1 : 0);
                        return;
                    }

                    if (value === undefined || value === null) {
                        formData.append(key, '');
                        return;
                    }

                    formData.append(key, value);
                });

                formData.append('is_manuscript_only', true);

                return axios.post(this.requestUrl + '/checkout/validate-order', formData)
                    .then((response) => {
                        if (response.data && typeof response.data.word_count !== 'undefined' && this.tempFileInfo) {
                            this.tempFileInfo.word_count = response.data.word_count;
                        }

                        this.orderForm.excess_words_amount = response.data.excess_words_amount || 0;
                        this.genreChanged();
                        this.originalPrice = parseFloat(this.shopManuscript.full_payment_price)
                            + (this.orderForm.excess_words_amount || 0);

                        return response;
                    })
                    .catch((error) => {
                        this.processError(error);
                        throw error;
                    });
            },

            removeFile() {
                axios.get('/forget-session-key/temp_uploaded_file').then(response => {
                    this.orderForm.temp_file = null;
                    this.tempFileInfo = null;
                    this.orderForm.manuscript = null;
                    this.orderForm.excess_words_amount = 0;
                    this.originalPrice = this.origPrice;
                    this.resetWordCountMessages();
                    this.genreChanged();
                    this.orderForm.price = this.origPrice;
                });
            }
        },

        mounted() {
            this.wizardProps = this.$refs.wizard;
            this.loadOptions();
            this.checkHasPaidCourse();

            if (this.tempFile) {
                this.originalPrice = this.tempFile.price;
                this.orderForm.excess_words_amount = this.tempFile.excess_words_amount;
                if (this.tempFile.word_count) {
                    this.setWordCountFeedback(`Manuskriptet inneholder omtrent ${this.tempFile.word_count} ord.`);
                    this.setWordCountPriceFeedback('Prisen er oppdatert.');
                }
            }
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
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 20px;
        font-size: 15px;
        padding: 10px 15px;
        text-align: left;
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

    .temp-file-details {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .temp-file-name {
        font-weight: 700;
        word-break: break-word;
    }

    .temp-file-meta {
        font-size: 13px;
        font-weight: 400;
        color: #555;
    }

</style>