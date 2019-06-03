<template>
    <div>
        <div class="card" v-if="!isDone">
            <div class="card-content">
                <div v-if="!isStarted">
                    <h3 class="card-title text-center">{{ trans("site.start-taking-survey") }}</h3>
                    <p class="mt-3">
                        <span class="flow-text margin-top">{{ survey.title }}</span> <br/>
                        {{ survey.description }}
                    </p>

                    <button class="btn btn-success w-100 mt-3" v-if="hasQuestion"
                            @click="isStarted = true">{{ trans("site.start-survey") }}</button>
                </div>

                <form v-if="isStarted" @submit.stop.prevent="handleSubmit">
                    <div v-for="(question, index) in survey.questions" v-if="index == currentIndex">
                        <p>
                            <b>
                                {{ question.title }}
                            </b>
                        </p>

                        <div class="input-field col s12" v-if="question.question_type === 'text'">
                            <input id="answer" type="text" class="field" v-model="answers[question.id]">
                            <label for="answer">Answer</label>
                        </div>

                        <div class="form-group" v-if="question.question_type === 'textarea'">
                            <span>Provide Answer</span>
                            <textarea class="form-control" cols="20" rows="10" v-model="answers[question.id]"></textarea>
                        </div>

                        <div class="form-group mb-0" v-if="question.question_type === 'radio'"
                             v-for="(value, key) in JSON.parse(question.option_name)">
                            <input type="radio" :id="key"
                                   :value="value" v-model="answers[question.id]"/>
                            <label :for="key">{{ value }}</label>
                        </div>

                        <div class="form-group mb-0" v-if="question.question_type === 'checkbox'"
                             v-for="(value, key, index) in JSON.parse(question.option_name)">
                            <input type="checkbox" :id="key" :value="value" @click="checkboxCond(question.id,$event)"
                                   v-model="answersChecked"
                            :key="index"/>
                            <label :for="key">{{ value }}</label>
                        </div>

                        <div class="btn-group d-flex" role="group">
                            <button class="btn btn-secondary w-100" @click="prevQuestion()" type="button"
                                    :disabled="!answers.length || index === 0">
                                {{ trans("site.back") }}
                            </button>

                            <button class="btn btn-success w-100"
                                    @click="nextQuestion(question.id, answers[question.id], question.question_type)"
                                    type="button" :disabled="!answers[question.id] && !hasAnswered"
                                    v-show="index != (lastQuestion - 1)">
                                {{ trans("site.next-question") }}
                            </button>

                            <button class="btn btn-success w-100"
                                    @click="submitQuestion(question.id, answers[question.id], question.question_type)"
                                    type="button" :disabled="(!answers[question.id] && !hasAnswered) || isLoading"
                                    v-show="index == (lastQuestion - 1)">
                                <i class="fa fa-pulse fa-spinner" v-show="isLoading"></i>
                                {{ trans("site.finish") }}
                            </button>
                        </div>
                    </div>

                    <p class="text-center" v-if="!hasQuestion">
                        <b>{{ trans("site.nothing-to-show") }}</b>
                    </p>
                </form>
            </div>
        </div>

        <div id="successModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <h3>
                            Takk for din tilbakemelding, den betyr mye for oss!
                        </h3>
                        <a href="/account/dashboard">Kontrollpanel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
          'survey'
        ],

        data() {
            return {
                hasQuestion: !!this.survey.questions.length,
                isStarted: false,
                currentIndex: 0,
                answers: [],
                answersChecked: [],
                currentChecked: [],
                hasAnswered: false,
                lastQuestion: this.survey.questions.length,
                isLoading: false,
                isDone: false
            }
        },

        mounted() {
          console.log(i18n.site);
        },

        methods: {
            checkboxCond(question_id, event) {
                if (event.target.checked) {
                    this.hasAnswered = true;
                    /*
                     original code before the one below but have some malfunction
                    if (this.currentChecked.indexOf(event.target.value) === -1) this.currentChecked.push(event.target.value);

                    // check if has answers or pressed back and returned to checkbox option
                    if (this.answers.length) {
                        // push the value to the answers object
                        Vue.set(this.answers, question_id, this.currentChecked.join(', '));
                    }*/

                    // check if there's an answer or clicked previous
                    if (this.answers.length) {

                        // add value to array
                        if (this.currentChecked.indexOf(event.target.value) === -1) this.currentChecked.push(event.target.value);

                        // check if this question is already answered and get the answer
                        if (this.answers[question_id]) {
                            // split the answers
                            let checkedAnswers = this.answers[question_id].split(', ');

                            // loop the current checked answers and push it to the old answer
                            $.each(this.currentChecked, function(k, v){
                               checkedAnswers.push(v);
                            });
                            // update the new answer
                            Vue.set(this.answers, question_id, checkedAnswers.join(', '));
                        }
                    } else {
                        if (this.currentChecked.indexOf(event.target.value) === -1) this.currentChecked.push(event.target.value);
                    }

                } else {
                    // remove selected value
                    this.currentChecked.splice(this.currentChecked.indexOf(event.target.value), 1);

                    // check if has answers or pressed back and returned to checkbox option
                    if (this.answers.length) {

                        // check if there's answer for this question
                        if (this.answers[question_id]) {
                            // explode the answers
                            this.currentChecked = this.answers[question_id].split(', ');
                        }

                        // check if the current checked checkbox is 1
                        if (this.currentChecked.length === 1) {
                            // since it's the only selected set the object to null
                            Vue.set(this.answers, question_id, null);
                            // remove on the current checked
                            this.currentChecked.splice(this.currentChecked.indexOf(event.target.value), 1);
                            this.hasAnswered = false;
                        }

                        // check if the checked checkbox is more than one
                        if (this.currentChecked.length > 1) {
                            // remove on the current checked
                            this.currentChecked.splice(this.currentChecked.indexOf(event.target.value), 1);
                            // set a new value for this question which excludes the removed value
                            Vue.set(this.answers, question_id, this.currentChecked.join(', '));

                            // check if after those condition there's still an option left
                            if (this.currentChecked.length) {
                                this.hasAnswered = true;
                            } else {
                                Vue.set(this.answers, question_id, null);
                                this.hasAnswered = false;
                            }
                        }
                    }
                }

                if (!this.currentChecked.length) {
                    this.hasAnswered = false;
                }
            },
            prevQuestion() {
                this.currentIndex = this.currentIndex - 1;
            },

            nextQuestion(question_id, answer, type) {
                if (this.currentIndex !== (this.lastQuestion - 1)) {
                    this.currentIndex = this.currentIndex + 1;
                }

                if (type === 'checkbox') {
                    Vue.set(this.answers, question_id, this.currentChecked.join(', '));
                    this.currentChecked = [];
                    this.hasAnswered = false;
                } else {
                    Vue.set(this.answers, question_id, answer);
                }
            },

            submitQuestion(question_id, answer, type) {
                this.nextQuestion(question_id, answer, type);
                this.isLoading = true;
                axios.post('/account/take-survey/'+this.survey.id, this.answers).then(response => {
                    $("#successModal").modal("show");
                    this.isDone = true;
                }).catch(error => {
                   console.log(error);
                });
            },
        }
    }
</script>