<template>
    <div class="package-content">
        <div id="basic-service">
            <h2 class="text-red">
                {{ service.product_service }}
            </h2>

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
        </div> <!-- end basic service -->

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

        <hr>

        <div class="send-inquiry pull-right">
            <button @click="addToCart()" class="button-red" :disabled="isLoading">
                <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i>
                Send forespørsel <i class="fa fa-long-arrow-right"></i>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['active-service', 'project-id'],

    data() {
        return {
            uploadManuscript: {
                char_count: 0,
                word_count: 100
            },
            order: {
                project_id: this.projectId,
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
            },
            service: this.activeService,
            chooseFileText: 'Velg fil du vil beregne',
            manuscriptName: this.chooseFileText,
            isLoading: false
        }
    },

    computed: {
        activeServiceComputed() {
            let scope = this;
            scope.backdoor;
            let service = scope.activeService;
            let count = 0
            if(scope.order.word_count > 0){ //scope.uploadManuscript.word_count
                if(service.per_unit=='char' || service.per_unit=='words'){
                    count = (service.per_unit=='char') ? scope.roundCount(scope.order.char_count, service.base_char_word) : scope.roundCount(scope.order.word_count, service.base_char_word)
                    service['computation'] = parseFloat((( count / service.per_word_hour) * service.price)).toFixed(2)
                }else{
                    // hour computation
                    service['computation'] = parseFloat((service.per_word_hour * service.price)).toFixed(2)
                }
            }
            return service;
        }
    },

    methods: {
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
                    console.log(response.data);
                    scope.order.word_count = response.data.word_count;
                    scope.order.char_count = response.data.char_count;
                    scope.$refs.wordCountSlider.setValue(response.data.word_count);
                    this.calculatePrice();
                }).catch(error => {
                    console.log('error', error)
                });
            }
        },

        roundCount(count, min){
            return (parseFloat(count) / parseFloat(min)) * parseFloat(min)
            //return Math.ceil(parseFloat(count) / parseFloat(min)) * parseFloat(min)
        },

        addToCart() {
            let scope = this;
            //scope.isLoading = true;
            scope.removeValidationError();
            let formData = new FormData();
            this.isLoading = true;

            if (scope.service.id === 3) {
                if (!scope.order.title) {
                    this.customFieldError('title', 'Title field is required');

                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });

                    this.isLoading = false;
                    return;
                }

                if (!scope.order.description) {
                    this.customFieldError('description', 'Description field is required');

                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });

                    this.isLoading = false;
                    return;
                }
            }

            scope.order.file = scope.uploadManuscript.manuscript;

            scope.order.totalWords = Math.round(scope.uploadManuscript.word_count).toLocaleString();
            scope.order.totalCharacters = Math.round(scope.uploadManuscript.char_count).toLocaleString();
            scope.order.totalPrice = Number(scope.total).toFixed(2).toLocaleString();

            $.each(scope.order, function(k, v) {
                formData.append(k, v);
            });
            
            axios.post('/account/self-publishing/add-to-cart', formData).then(response => {
                window.location.href = '/account/self-publishing/order';
                //scope.isLoading = false;
                /* this.$toasted.global.showSuccessMsg({
                    message : 'Enquiry Sent'
                }); */
            }).catch(error => {
                scope.isLoading = false;
                this.processError(error);
                /* this.$toasted.global.showErrorMsg({
                    message : i18n.site.form['error-message']
                }); */
            });
        }
    },

    mounted() {
        
    }
}
</script>

<style scoped>
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
