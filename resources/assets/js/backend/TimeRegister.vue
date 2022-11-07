<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-body">
                <button class="btn btn-primary pull-right btn-xs" @click="showTimeFormModal()">
                    + Add Time Register
                </button>
                <h4>Time Register</h4>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Project</th>
                        <th>Date</th>
                        <th>Number of hours</th>
                        <th>Time Used</th>
                        <th>Invoice</th>
                        <th width="200">Description</th>
                        <th width="150"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="timeList in timeLists">
                        <td>
                            {{ timeList.project_id ? timeList.project.name : '' }}
                        </td>
                        <td>{{ timeList.date }}</td>
                        <td>{{ timeList.time }}</td>
                        <td>{{ timeList.time_used }}</td>
                        <td v-html="timeList.file_link"></td>
                        <td>{{ timeList.description }}</td>
                        <td>
                            <button class="btn btn-xs btn-primary" @click="showTimeFormModal(timeList)">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button class="btn btn-xs btn-danger" @click="showDeleteTimeModal(timeList)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <b-modal
                ref="timeFormModal"
                size="lg"
                @hidden="closeTimeFormModal()"
                centered
        >
            <div slot="modal-title">
                <h4 class="modal-title">{{ modalTitle }}</h4>
            </div>

            <div class="form-group">
                <label>Project</label>
                <v-select :options="projects" label="name" v-model="selected_project" @input="setSelectedProject($event)"
                          name="project"></v-select>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control" v-model="timeForm.date" required>
            </div>
            <div class="form-group">
                <label>Number of hours</label>
                <input type="text" name="time" class="form-control" v-model="timeForm.time" required>

                <button type="button" class="btn btn-xs" @click="adjustTime(1)">+1</button>
                <button type="button" class="btn btn-xs" @click="adjustTime(0.5)">+1/2</button>
                <button type="button" class="btn btn-xs" @click="adjustTime(-0.5)">-1/2</button>
                <button type="button" class="btn btn-xs" @click="adjustTime(-1)">-1</button>
            </div>

            <template v-if="timeForm.id">
                <div class="form-group">
                    <label>Time Used</label>
                    <input type="number" name="time_used" class="form-control" v-model="timeForm.time_used">
                </div>

                <div class="form-group">
                    <label>Invoice file</label>
                    <input type="file" name="invoice_file" class="form-control"
                           @change="onFileChange"
                           id="manuscript"
                           accept="application/pdf">
                </div>
            </template>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"  cols="30" rows="10" v-model="timeForm.description" class="form-control"></textarea>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveTime()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

        </b-modal>

        <b-modal
                ref="deleteTimeModal"
                title="Delete Time"
                size="sm"
                centered
        >

            <p>
                Are you sure you want to delete this record?
            </p>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-danger" @click="deleteTime()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Delete
                </button>
            </div>
        </b-modal>

        <b-modal
                ref="timerModal"
                title=""
                size="sm"
                centered
                no-close-on-backdrop
                hide-footer
        >

            <div class="text-center">
                <span class="time d-block">{{ setTimerMinutes(timer.min) }}:{{ setTimerSeconds(timer.sec) }}</span>
                <button class="btn btn-primary btn-lg" style="border-radius: 25px" @click="toggleTimer()">
                    <i class="fa fa-play" v-if="!timer.is_started"></i>
                    <i class="fa fa-pause" v-if="timer.is_started"></i>
                </button>
            </div>

        </b-modal>
    </div>
</template>

<script>
    export default {

        props: ['time-registers', 'learner-id', 'projects'],

        data() {
            return {
                timeLists: this.timeRegisters,
                modalTitle: '',
                timeForm: {
                    id: '',
                    learner_id: this.learnerId,
                    project_id: '',
                    date: '',
                    time: '',
                    time_used: 0,
                    invoice_file: '',
                    description: '',
                },
                selected_project: '',
                invoiceFilename: '',
                timeData: {},
                timer: {
                    min: 10,
                    sec: 0,
                    is_started: 0
                },
                isLoading: false,
            }
        },

        methods: {
            setSelectedProject(value) {
                this.timeForm.project_id = value ? value.id : "";
            },

            showTimerModal(data) {
                this.timeData = data;
                this.$refs.timerModal.show();
            },

            showTimeFormModal(data = null) {
                this.modalTitle = 'Add Time';
                if (data) {
                    this.modalTitle = 'Edit Time';
                    this.timeForm.id = data.id;
                    this.timeForm.project_id = data.project_id;
                    this.timeForm.date = data.date;
                    this.timeForm.time = data.time;
                    this.timeForm.time_used = data.time_used;
                    this.timeForm.description = data.description;

                    const index = _.findIndex(this.projects, {id: data.project_id});
                    if (index >= 0) {
                        let project = this.projects[index];
                        this.selected_project = project.name;
                    }
                }
                this.$refs.timeFormModal.show();
            },

            closeTimeFormModal() {
                this.timeForm = {
                    id: '',
                    learner_id: this.learnerId,
                    project_id: '',
                    date: '',
                    time: '',
                    description: '',
                }
                this.selected_project = '';
            },

            adjustTime(time) {
                let timeField = isNaN(parseFloat(this.timeForm.time)) ? 0 : parseFloat(this.timeForm.time);
                this.timeForm.time =  timeField + parseFloat(time);
            },

            onFileChange(e) {
                let files = e.target.files;

                if (!files.length)
                {
                    this.invoiceFilename = i18n.site['learner.files-text'];
                    this.timeForm.invoice_file = [];
                    return;
                }

                this.invoiceFilename = files[0].name;
                this.timeForm.invoice_file = files[0];

                $(".validation-err").remove();
            },

            saveTime() {
                this.isLoading = true;

                let formData = new FormData();
                $.each(this.timeForm, function(k, v) {
                    formData.append(k, v);
                });

                axios.post('/time-register/save', formData).then(response => {
                    this.isLoading = false;
                    if (this.timeForm.id) {
                        this.updateRecordFromObject(this.timeLists, response.data.id, response.data);
                    } else {
                        this.timeLists.push(response.data);
                    }

                    this.$toasted.global.showSuccessMsg({
                        message : 'Record saved'
                    });
                    this.$refs.timeFormModal.hide();
                }).catch(error => {
                    this.isLoading = false;
                    this.processError(error);
                });
            },

            showDeleteTimeModal(time) {
                this.timeForm.id = time.id;
                this.$refs.deleteTimeModal.show();
            },

            deleteTime() {
                this.isLoading = true;
                axios.delete('/time-register/' + this.timeForm.id + '/delete').then(response => {
                    this.isLoading = false;
                    this.deleteRecordFromObject(this.timeLists, this.timeForm.id);
                    this.$refs.deleteTimeModal.hide();
                    this.$toasted.global.showSuccessMsg({
                        message : 'Record deleted'
                    });
                });
            },

            setTimerMinutes: function (minutes) {
                return (minutes < 10) ? '0' + minutes.toString() : minutes.toString()
            },

            setTimerSeconds: function (seconds) {
                return (seconds < 10) ? '0' + seconds.toString() : seconds.toString()
            },

            toggleTimer() {
                console.log(this.timeData);
                this.timer.is_started = !this.timer.is_started
            }
        },

        mounted() {
            console.log("time register here ssss");
            console.log(this.timeLists)
            console.log(this.learnerId)
        }

    }
</script>

<style>
    .fade.show {
        opacity: 1;
    }

    .modal.fade .modal-dialog{
        -webkit-transform: translate(0,0);
        -ms-transform: translate(0,0);
        -o-transform: translate(0,0);
        transform: translate(0,0);
    }

    .modal-backdrop {
        background-color: #0009;
    }

    .modal-title {
        display: inline-block;
    }

    .time {
        font-family: 'Digital' !important;
        font-size: 54px;
        line-height: 1.2em;
    }
</style>