<template>
    <div>
        <div class="panel">
            <div class="panel-header" style="padding: 10px">
                <em><b>Time Register</b></em>
            </div>
            <div class="panel-body">
                <button class="btn btn-success btn-sm pull-right" @click="showTimeFormModal()">
                    + Add Time Register
                </button>
                <div class="clearfix"></div>
                <div class="table-users">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Number of hours</th>
                            <th>Notes</th>
                            <th width="150"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(projectTimeRegister, index) in projectTimeRegisters" :key="index">
                            <td>{{ projectTimeRegister.date }}</td>
                            <td>{{ projectTimeRegister.time }}</td>
                            <td v-html="projectTimeRegister.notes_formatted"></td>
                            <td>
                                <button class="btn btn-xs btn-primary" @click="showTimeFormModal(projectTimeRegister)">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button class="btn btn-xs btn-danger" @click="showDeleteTimeModal(projectTimeRegister)">
                                    <i class="fa fa-trash"></i>
                                </button>

                                <button class="btn btn-success btn-xs" @click="showTimeUsedModal(projectTimeRegister)">
                                    Time Used
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <b-modal
                ref="timeFormModal"
                size="md"
                @hidden="closeTimeFormModal()"
                centered
        >
            <div slot="modal-title">
                <h4 class="modal-title">{{ modalTitle }}</h4>
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

            <div class="form-group">
                <!-- <label>Invoice file</label>
                <input type="file" name="invoice_file" class="form-control"
                       @change="onFileChange"
                       id="manuscript"
                       accept="application/pdf"> -->
                <label>Notes</label>
                <textarea name="notes" cols="30" rows="10" class="form-control" v-model="timeForm.notes"></textarea>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveTime()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

        </b-modal>

        <b-modal
                ref="timeUsedModal"
                title="Time Used"
                size="lg"
                centered
                hide-footer
        >

            <button class="btn btn-success btn-sm addTimeUsedBtn pull-right" @click="showTimeUsedFormModal()">
                Add Time Used
            </button>

            <div class="clearfix"></div>

            <div class="table-responsive margin-top">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time Used</th>
                        <th>Description</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(timeUsed, index) in timeUsedList" :key="'timeusedmodal-' + index">
                        <td>
                            {{ timeUsed.date }}
                        </td>
                        <td>
                            {{ timeUsed.time_used }}
                        </td>
                        <td>
                            {{ timeUsed.description }}
                        </td>
                        <td>
                            <button class='btn btn-primary btn-xs' @click="showTimeUsedFormModal(timeUsed)">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button class="btn btn-xs btn-danger" @click="showDeleteTimeUsedModal(timeUsed)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </b-modal>

        <b-modal
                ref="timeUsedFormModal"
                :title="timeUsedFormModalTitle"
                @hidden="closeTimeUsedFormModal()"
                size="md"
                centered
        >

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control" v-model="timeUsedForm.date" required>
            </div>

            <div class="form-group">
                <label>Time Used</label>
                <input type="number" name="time_used" class="form-control" v-model="timeUsedForm.time_used" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" cols="30" rows="10" class="form-control" v-model="timeUsedForm.description"></textarea>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveTimeUsedForm()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>
        </b-modal>

        <b-modal
                ref="deleteTimeUsedModal"
                title="Delete Time Used"
                size="sm"
                centered
        >

            <p>
                Are you sure you want to delete this record?
            </p>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-danger" @click="deleteTimeUsed()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Delete
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
    </div>
</template>

<script>

    export default {

        props: ['current-project','project-time-list'],

        data() {
            return {
                project: this.currentProject,
                projectTimeRegisters: this.projectTimeList,
                modalTitle: '',
                timeForm: {
                    id: '',
                    learner_id: this.currentProject.user_id,
                    project_id: this.currentProject.id,
                    date: '',
                    time: '',
                    invoice_file: '',
                    notes: ''
                },
                timeUsedList: [],
                timeUsedFormModalTitle: '',
                timeUsedForm: {
                    time_used_id: '',
                    time_register_id: '',
                    date: '',
                    time_used: '',
                    description: ''
                },
                isLoading: false
            }
        },

        methods: {
            showTimeFormModal(data = null) {
                this.modalTitle = 'Add Time';
                this.timeForm.learner_id = this.project.user_id;
                this.timeForm.project_id = this.project.id;

                if (data) {
                    this.modalTitle = 'Edit Time';
                    this.timeForm.id = data.id;
                    this.timeForm.project_id = data.project_id;
                    this.timeForm.date = data.date;
                    this.timeForm.time = data.time;
                    this.timeForm.notes = data.notes;

                }
                this.$refs.timeFormModal.show();
            },

            closeTimeFormModal() {
                this.timeForm = {
                    id: '',
                    learner_id: this.currentProject.user_id,
                    project_id: this.currentProject.id,
                    date: '',
                    time: '',
                    notes: '',
                };
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
                        this.updateRecordFromObject(this.projectTimeRegisters, response.data.id, response.data);
                    } else {
                        this.projectTimeRegisters.push(response.data);
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
                    this.deleteRecordFromObject(this.projectTimeRegisters, this.timeForm.id);
                    this.$refs.deleteTimeModal.hide();
                    this.$toasted.global.showSuccessMsg({
                        message : 'Record deleted'
                    });
                });
            },

            showTimeUsedModal(timeRegister) {

                axios.get('/time-register/' + timeRegister.id + '/time-used-list').then(response => {
                    this.timeUsedList = response.data;
                    this.timeUsedForm.time_register_id = timeRegister.id;
                    this.$refs.timeUsedModal.show();
                });
            },

             showTimeUsedFormModal(data = null) {
                this.timeUsedFormModalTitle = 'Add Time used';
                if (data) {
                    this.timeUsedFormModalTitle = 'Edit Time used';
                    this.timeUsedForm.time_used_id = data.id;
                    this.timeUsedForm.date = data.date;
                    this.timeUsedForm.time_used = data.time_used;
                    this.timeUsedForm.description = data.description;
                }
                this.$refs.timeUsedFormModal.show();
            },

            closeTimeUsedFormModal() {
                this.timeUsedForm.time_used_id = '';
                this.timeUsedForm.date = '';
                this.timeUsedForm.time_used = '';
                this.timeUsedForm.description = '';
            },

            saveTimeUsedForm() {
                this.isLoading = true;
                this.removeValidationError();

                axios.post('/time-register/' + this.timeUsedForm.time_register_id + '/save-time-used', this.timeUsedForm)
                    .then(response => {
                        console.log(response);
                        this.isLoading = false;
                        this.timeUsedList = response.data;
                        this.$refs.timeUsedFormModal.hide();
                        this.$toasted.global.showSuccessMsg({
                            message : 'Time used saved'
                        });
                    }).catch(error => {
                        this.isLoading = false;
                        this.processError(error);
                        this.$toasted.global.showErrorMsg({
                            message : 'Error in form'
                        });
                });
            },

            showDeleteTimeUsedModal(data) {
                this.timeUsedForm.time_used_id = data.id;
                this.$refs.deleteTimeUsedModal.show();
            },

            deleteTimeUsed() {
                this.isLoading = true;
                axios.delete('/time-register/time-used/' + this.timeUsedForm.time_used_id + '/delete').then(response => {
                    this.isLoading = false;
                    this.deleteRecordFromObject(this.timeUsedList, this.timeUsedForm.time_used_id);
                    this.$refs.deleteTimeUsedModal.hide();
                    this.$toasted.global.showSuccessMsg({
                        message : 'Record deleted'
                    });
                });
            }
        }
    }
</script>