<template>
    <div class="package-content">
        <div id="basic-service">
            <h2 class="text-center" style="margin-top: 20px; margin-bottom: 20px;">
                {{ trans('site.publishing-service-calculator.sub-title') }}
            </h2>

            <div class="form-group">
                <h2>
                    {{ trans('site.publishing-service-calculator.script-length') }}
                </h2>
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
                        {{ trans('site.learner.words-text') }}:
                    </span>
                    <span>
                        <input type="text" name="words" v-model="order.word_count">
                    </span>
                </div>
            </div> <!-- end form-group -->

            <h2>
                {{ trans('site.publishing-service-calculator.upload-your-book') }}
            </h2><br>
            <div class="input-group package-select-file">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" @change="onFileChange" id="inputGroupFile04" accept=".docx">
                    <label class="custom-file-label" for="inputGroupFile04">{{ manuscriptName }}</label>
                </div>
            </div>

            <h2 class="services-included">
                {{ trans('site.publishing-service-calculator.services-included') }}
            </h2><br>
            <div class="row">
                <div class="col-md-4" v-for="(service, index) in activeServicesComputed" :key="index">
                    <div class="form-check-container bg-package-light" @change="inputTriggered($event)">
                        <div class="form-check">
                            <p-check name="check" color="default" :value="service.id" v-model="checkedActiveService">
                                {{ service.product_service }}
                            </p-check>
                        </div>
                    </div>

                    <div class="description-container" v-html="service.description">
                    </div>
                </div>
            </div> <!-- end row -->
        </div> <!-- end basic-service -->

        <hr>
        <div class="total">
            <span>
                {{ trans('site.total') }}
            </span>
        </div>
        <div class="character-words">
            <span>{{ trans('site.learner.words-text') }}: </span>
            <span>{{ Math.round(order.word_count).toLocaleString() }}</span>
        </div>
        <div class="price-">
            <span>{{ trans('site.front.price') }}: </span>
            <span>{{ Number(total).toFixed(2).toLocaleString() }} {{ trans('site.publishing.price-unit') }}</span>
        </div>
    </div>
</template>

<script>
export default {
    props: ['service-list'],
    data() {
        return {
            activeServices: this.serviceList,
            checkedActiveService: [],
            uploadManuscript: {
                char_count: 0,
                word_count: 100
            },

            order: {
                title: null,
                description: null,
                file: null,
                totalWords: 0,
                totalCharacters: 0,
                totalPrice: 0,
                char_count: 0,
                word_count: 10000,
            },

            chooseFileText: 'Velg fil du vil beregne',
            manuscriptName: this.chooseFileText,
        }
    },

    computed: {
        activeServicesComputed() {
            let scope = this;
            let services = scope.activeServices;
            let count = 0
            if(scope.order.word_count > 0){ //scope.uploadManuscript.word_count
                services.forEach(function (item, index){
                    if(item.per_unit=='char' || item.per_unit=='words'){
                        count = (item.per_unit=='char') ? scope.roundCount(scope.order.char_count, item.base_char_word) 
                        : scope.roundCount(scope.order.word_count, item.base_char_word);
                        services[index]['computation'] = parseFloat((( count / item.per_word_hour) * item.price)).toFixed(2);
                        if (item.id === 3) { // check if Redaktor 1
                            if (count <= 10000) {
                                services[index]['computation'] = 2000;
                            } else {
                                let deductedCount = count - 10000;
                                services[index]['computation'] = 2000 + ((deductedCount / item.per_word_hour) * item.price);
                            }
                        }
                    }else{
                        // hour computation
                        services[index]['computation'] = parseFloat((item.per_word_hour * item.price)).toFixed(2);
                    }
                })
            }
            return services
        }
    },

    methods: {
        calculatePrice() {
            let scope = this;
            let total = 0;
            let count = 0;
            let active = scope.activeServices;

            this.order.char_count = this.order.word_count * 6; // set the char count to correct the computation
            // check active
            active.forEach(function(item){
                let exists = scope.checkedActiveService.some(function(field) {
                    return field == item.id
                });
                if(exists){
                    total = total + (!isNaN(item.computation) ? parseFloat(item.computation) : 0)
                }
            });
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
        },

        inputTriggered(e){
            if(e.target.checked){
                e.currentTarget.style.backgroundColor = "#727C96"
                e.currentTarget.style.color = "#FFFFFF"
            } else {
                e.currentTarget.style.backgroundColor = "#ECECEC"
                e.currentTarget.style.color = "#9D9D9D"
            }
        },
    },

    mounted() {
        console.log("mounted");
        console.log("Ads fad fasf ");
        console.log(this.activeServices);
    }
}
</script>

<style scoped>
    .bg-package-dark{
        background-color: #727C96;
        color: #FFFFFF;
    }

    .bg-package-light{
        background-color: #ECECEC;
        color: #9D9D9D;
    }

    .package-content .form-check-container {
        padding: 20px;
        display: block;
        margin: 8px;
        border-radius: 6px;
    }

    .form-check {
        position: relative;
        display: block;
        padding-left: 1.25rem;
    }

    .pretty {
        position: relative;
        margin-right: 1em;
        white-space: nowrap;
        line-height: 1;
    }

    .description-container {
        font-size: .9em;
        margin: -1px -1px 0 -1px;
        padding: 20px 20px 10px 20px;
        text-align: left;
        background: #fddeca;
        border: 1px solid #FFC39C;
    }
    
</style>

    