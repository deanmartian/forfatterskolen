<template>
    <div class="card">
        <form-wizard color="#c12938" error-color="#ff4949"
                     :nextButtonText="'Til betaling'" :backButtonText="trans('site.paginate.previous')"
                     :finishButtonText="trans('site.front.buy')" title="" subtitle="">
            <tab-content :title="'Compute'" icon="fa fa-clipboard-list" :before-change="validateOrderTab">
                <h1 class="text-center" style="margin-top: 20px; margin-bottom: 20px;">
                    HV OR MYE KOSTER REDAKØRTJENESTENE FOR DIN BOK?
                </h1>

                <div class="form-group">
                    <h3>Hvor langt er manuset ditt?</h3>
                    <vue-slider v-model="order.word_count"
                                ref="wordCountSlider"
                                :min="10000"
                                :max="150000"
                                :interval="1"
                                :tooltip="'none'"
                                :label-style="{ position: 'absolute', top: '-41px'}"
                                :processStyle="{'background-color' : '#c12937'}"
                                :stepStyle="{display: 'none'}"
                                :height="30"
                                :dotSize="30"
                                :change="calculatePrice()">
                    </vue-slider>

                    <div class="character-words">
                        <span>
                            Ord:
                        </span>
                        <span>
                            <input type="text" name="words" v-model="order.word_count">
                        </span>
                    </div>
                </div> <!-- end form-group -->

                <h2 class="services-included text-center text-red">
                    Du kan også laste opp boken din så regner kalkulatoren hva prisen blir.
                </h2><br>
                <div class="input-group package-select-file">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" @change="onFileChange" id="inputGroupFile04" accept=".docx">
                        <label class="custom-file-label" for="inputGroupFile04">{{ manuscriptName }}</label>
                    </div>
                </div>

                <div v-if="service.id === 3">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" v-model="order.title">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="" cols="30" rows="10" class="form-control"
                        v-model="order.description"></textarea>
                    </div>
                </div>

                <h2 class="services-included text-red">TJENESTER INKLUDERT</h2><br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="description-container">
                            <div v-html="service.description"></div>

                            <div class="form-check-container bg-package-light">
                                <i class="fa fa-check-square"></i>

                                <div class="form-check">
                                    {{ service.product_service }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row -->
            
            <div class="total" style="margin-top: 20px"><span>{{ trans('site.total') }}</span></div>
            <div style="margin-top:10px">
                <div class="character-words pull-left">
                    <b>{{ trans('site.learner.word') }}: </b>
                    <span>{{ Math.round(order.word_count).toLocaleString() }}</span>
                </div>
                <div class="pull-right price-">
                    <b>{{ trans('site.price') }}: </b>
                    <span>{{ Number(total).toFixed(2).toLocaleString() }} Kroner</span>
                </div>

                <div class="clearfix"></div>
            </div>
            </tab-content>

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
                           v-model="order.email"
                           :disabled="currentUser"
                           :placeholder="trans('site.front.form.email')">
                </div> <!-- end email form-group -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="first_name" class="control-label">
                            {{ trans('site.first-name') }}
                        </label>
                        <input type="text" id="first_name" class="form-control" name="first_name" required
                               v-model="order.first_name"
                               :disabled="currentUser"
                               :placeholder="trans('site.first-name')">
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="control-label">
                            {{ trans('site.last-name') }}
                        </label>
                        <input type="text" id="last_name" class="form-control" name="last_name" required
                               v-model="order.last_name"
                               :disabled="currentUser"
                               :placeholder="trans('site.last-name')">
                    </div>
                </div> <!-- end first and last name -->

                <div class="form-group">
                    <label for="street" class="control-label">
                        {{ trans('site.front.form.street') }}
                    </label>
                    <input type="text" id="street" class="form-control" name="street" required
                           v-model="order.street"
                           :placeholder="trans('site.checkout.street')">
                </div> <!-- end street -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="zip" class="control-label">{{ trans('site.front.form.zip') }}</label>
                        <input type="text" id="zip" class="form-control" name="zip" required
                               v-model="order.zip" :placeholder="trans('site.checkout.zip')">
                    </div>
                    <div class="col-md-6">
                        <label for="city" class="control-label">{{ trans('site.front.form.city') }}</label>
                        <input type="text" id="city" class="form-control" name="city" required
                               v-model="order.city" :placeholder="trans('site.checkout.city')">
                    </div>
                </div> <!-- end zip, city -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="phone" class="control-label">
                            {{ trans('site.front.form.phone-number') }}
                        </label>
                        <input type="text" id="phone" class="form-control" name="phone" required
                               v-model="order.phone" :placeholder="trans('site.checkout.phone')">
                    </div>

                    <div class="col-md-6" v-if="!currentUser">
                        <label for="password" class="control-label">
                            {{ trans('site.front.form.create-password') }}
                        </label>
                        <input type="password" id="password" class="form-control"
                               name="password" required :placeholder="trans('site.front.form.create-password')"
                               v-model="order.password">
                    </div>
                </div>
            </tab-content>

            <button slot="finish" class="wizard-btn" 
            style="background-color: rgb(193, 41, 56);border-color: rgb(193, 41, 56);color: white;"
             @click="submitBuy()">
                {{ trans('site.front.buy') }}
            </button>
        </form-wizard>
    </div>
</template>

<script>
export default {
    props: ['active-service', 'user'],

    data() {
        return {
            uploadManuscript: {
                char_count: 0,
                word_count: 100
            },
            order: {
                parent: 'publishing_services',
                parent_id: this.activeService.id,
                title: null,
                description: null,
                file: null,
                totalWords: 0,
                totalCharacters: 0,
                totalPrice: 0,
                char_count: 0,
                word_count: 10000,
                email: '',
                first_name: '',
                last_name: '',
                street: '',
                zip: '',
                city: '',
                phone: '',
                national_id: '',
                password: '',
            },
            service: this.activeService,
            chooseFileText: 'Velg fil du vil beregne',
            manuscriptName: this.chooseFileText,
            currentUser: this.user,
            loginForm: {
                email: '',
                password: ''
            },
            invalidCred: false,
            isLoginDisabled: false,
            loginText: i18n.site.front.form.login,
            isLoading: false
        }
    },

    methods: {
        getCurrentUser() {
            axios.get('/current-user').then(response => {

                this.currentUser = response.data;
            })
        },

        calculatePrice() {
            let scope = this;
            let total = 0;
            let service = scope.activeService;
            let count = 0;

            if(service.per_unit=='char' || service.per_unit=='words'){
                count = (service.per_unit=='char') ? scope.roundCount(scope.order.char_count, service.base_char_word) : scope.roundCount(scope.order.word_count, service.base_char_word)
                service['computation'] = parseFloat((( count / service.per_word_hour) * service.price)).toFixed(2)
            }else{
                // hour computation
                service['computation'] = parseFloat((service.per_word_hour * service.price)).toFixed(2)
            }

            this.order.char_count = this.order.word_count * 6; // set the char count to correct the computation
            total = total + (!isNaN(service.computation) ? parseFloat(service.computation) : 0);
            this.total = parseFloat(total).toFixed(2);
        },

        onFileChange(e){
            let files = e.target.files;

            if (!files.length)
            {
                this.manuscriptName = this.chooseFileText;
                this.uploadManuscript.manuscript = [];
                return;
            }

            this.manuscriptName = files[0].name;
            this.uploadManuscript.manuscript = files[0];
            this.countCharacters()
        },

        countCharacters(){
            let scope = this
            let formData = new FormData();
            $.each(this.uploadManuscript, function(k, v) {
                formData.append(k, v);
            });
            if (this.uploadManuscript.manuscript) {
                axios.post('/file/count-characters', formData).then(response => {
                    scope.order.word_count = response.data.word_count;
                    scope.order.char_count = response.data.char_count;
                    scope.$refs.wordCountSlider.setValue(response.data.word_count);
                    this.calculatePrice();
                }).catch(error => {
                    console.log('error', error)
                });
            }
        },

        loadOptions() {
            this.order.email = this.currentUser ? this.currentUser.email : '';
            this.order.first_name = this.currentUser ? this.currentUser.first_name : '';
            this.order.last_name = this.currentUser ? this.currentUser.last_name : '';
            this.order.street = this.currentUser && this.currentUser.address ? this.currentUser.address.street
                : '';
            this.order.zip = this.currentUser && this.currentUser.address ? this.currentUser.address.zip : '';
            this.order.city = this.currentUser && this.currentUser.address ? this.currentUser.address.city : '';
            this.order.phone = this.currentUser && this.currentUser.address ? this.currentUser.address.phone : '';
            this.order.national_id = this.currentUser && this.currentUser.address ? this.currentUser.address.national_id : '';
        },

        handleLogin(event) {
                this.isLoginDisabled = true;
                this.removeValidationError();

                axios.post('/auth/checkout/login', this.loginForm).then(response => {

                    this.invalidCred = false;
                    this.loginText = i18n.site.front.form.login;
                    this.isLoginDisabled = false;
                    this.currentUser = response.data.user;
                    this.loadOptions();

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

        validateOrderTab() {
            let scope = this;
            scope.removeValidationError();
            if (scope.service.id === 3) {
                if (!scope.order.title) {
                    this.customFieldError('title', 'Title field is required');

                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });

                    return false;
                }

                if (!scope.order.description) {
                    this.customFieldError('description', 'Description field is required');

                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });

                    return false;
                }
            }

            return true;
        },

        validateForm() {
            let scope = this;
            let formData = new FormData();

            scope.order.file = scope.uploadManuscript.manuscript;

            scope.order.totalWords = Math.round(scope.uploadManuscript.word_count).toLocaleString();
            scope.order.totalCharacters = Math.round(scope.uploadManuscript.char_count).toLocaleString();
            scope.order.totalPrice = Number(scope.total).toFixed(2).toLocaleString();

            $.each(this.order, function(k, v) {
                formData.append(k, v);
            });
            
            return axios.post('/publishing-service/checkout/validate-form', formData).then(response => {
                this.removeValidationError();
                this.getCurrentUser();

                window.location.href = '/publishing-service/thank-you';

                return true;

            }).catch(error => {

                this.processError(error);

            });
        },

        submitBuy() {
            if (this.currentUser) {
                console.log("submit buy");
            }
        }
    },

    mounted() {
        this.loadOptions();
    }
}
</script>

<style scoped>
.vue-slider {
    padding: 0 !important;
    margin-top: 10px;
    margin-bottom: 10px;
}

.description-container {
    font-size: .9em;
    margin: -1px -1px 0 -1px;
    padding: 20px 20px 10px 20px;
    text-align: left;
    background: #fddeca;
    border: 2px solid #eea270;
    min-height: 300px;
}
</style>
        