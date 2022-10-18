<template>
    <div>
        <div class="page-toolbar">
            <h3><i class="fa fa-file-text-o"></i> Project: {{ project.name }}</h3>
            <a :href="'/project/' + project.id + '/graphic-work'" class="btn btn-primary btn-sm">
                Graphic Work
            </a>
            <a :href="'/project/' + project.id + '/registration'" class="btn btn-primary btn-sm">
                Registration
            </a>
            <a :href="'/project/' + project.id + '/marketing'" class="btn btn-primary btn-sm">
                Marketing
            </a>
            <button class="btn btn-primary btn-sm">
                Page2
            </button>
            <button class="btn btn-primary btn-sm"> <!-- this should be the docx file of FS treadline saved -->
                Checklist
            </button>
            <button class="btn btn-primary btn-sm">
                Contract
            </button>
            <button class="btn btn-primary btn-sm">
                Invoices
            </button>
            <button class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-edit"></i> Edit Project
            </button>
            <div class="clearfix"></div>
        </div>

        <div class="margin-top">
            <div class="col-md-6">
                <div class="panel">
                    <div class="panel-header" style="padding: 10px">
                        <em><b>Notes</b></em>
                        <button class="btn btn-primary btn-sm pull-right" @click="showNotes()">
                            Notes
                        </button>
                    </div>
                    <div class="panel-body">
                        {{ project.notes }}
                    </div>
                </div>
            </div>

            <div class="col-md-6" v-if="project.user_id">
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
                                    <th>Invoice</th>
                                    <th width="150"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="projectTimeRegister in projectTimeRegisters">
                                    <td>{{ projectTimeRegister.date }}</td>
                                    <td>{{ projectTimeRegister.time }}</td>
                                    <td v-html="projectTimeRegister.file_link"></td>
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
            </div>
        </div>

        <div class="col-md-12">
            <button class="btn btn-success margin-top" @click="showFormModal()" v-if="books.length === 0">
                Add
            </button>
            <div class="table-users">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>Author</th>
                        <th>Name of book</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="book in books">
                        <td>
                            {{ project.user ? project.user.full_name : '' }}
                        </td>
                        <td>
                            {{ book.book_name }}
                        </td>
                        <td>
                            <button class="btn btn-xs btn-primary" @click="showFormModal(book)">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-xs btn-danger" @click="showDeleteModal(book)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <b-modal
                ref="formModal"
                :title="modalTitle"
                size="md"
                @hidden="closeFormModal()"
                centered
                no-close-on-backdrop
        >

            <div class="form-group">
                <label>Author</label>
                <v-select :options="learners" label="full_name" v-model="selected_learner" @input="setSelectedLearner($event)"
                          name="learner_id"></v-select>
            </div>

            <div class="form-group">
                <label>Name of book</label>
                <input type="text" class="form-control" name="book_name" v-model="form.book_name">
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveForm()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

        </b-modal>

        <b-modal
                ref="deleteModal"
                title="Delete Book"
                size="sm"
                centered
                no-close-on-backdrop
        >

            <p>
                Are you sure you want to delete this record?
            </p>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-danger" @click="deleteBook()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Delete
                </button>
            </div>
        </b-modal>

        <b-modal
                ref="notesModal"
                title="Edit Note"
                size="md"
                centered
                no-close-on-backdrop
        >

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" v-model="noteForm.notes" cols="30" rows="10" class="form-control"></textarea>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveNotes()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>
        </b-modal>

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
                <label>Invoice file</label>
                <input type="file" name="invoice_file" class="form-control"
                       @change="onFileChange"
                       id="manuscript"
                       accept="application/pdf">
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
                    <tr v-for="timeUsed in timeUsedList">
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
    </div>
</template>

<script>
    export default {

        props: ['current-project', 'learners', 'time-registers', 'project-time-list'],

        data() {
            return {
                project: this.currentProject,
                timeLists: this.timeRegisters,
                projectTimeRegisters: this.projectTimeList,
                books: this.currentProject.books,
                modalTitle: '',
                form: {
                    id: '',
                    user_id: this.currentProject.user_id,
                    book_name: '',
                    isbn_hardcover_book: '',
                    isbn_ebook: '',
                },
                noteForm: {
                    id: '',
                    notes: ''
                },
                book: {},
                selected_learner: '',
                timeForm: {
                    id: '',
                    learner_id: this.currentProject.user_id,
                    project_id: this.currentProject.id,
                    date: '',
                    time: '',
                    invoice_file: '',
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
                isLoading: false,
            }
        },

        methods: {
            setSelectedLearner(value) {
                this.form.user_id = value ? value.id : "";
            },

            showNotes() {
                this.noteForm = {
                    id: this.project.id,
                    notes: this.project.notes
                }
                this.$refs.notesModal.show();
            },

            saveNotes() {
                this.isLoading = true;
                axios.post('/project/' + this.project.id + '/notes/save', this.noteForm).then(response => {
                    this.isLoading = false;
                    this.project.notes = response.data.notes;
                    this.$toasted.global.showSuccessMsg({
                        message : 'Notes saved'
                    });
                    this.$refs.notesModal.hide();
                }).catch(error => {
                    this.isLoading = false;
                    this.processError(error);
                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });
                });
            },

            showFormModal(data = null) {
                this.modalTitle = 'Add Book';
                if (data) {
                    this.modalTitle = 'Edit Book';
                    this.form = {
                        id: data.id,
                        user_id: this.project.user_id,
                        book_name: data.book_name,
                        isbn_hardcover_book: data.isbn_hardcover_book,
                        isbn_ebook: data.isbn_ebook,
                    };
                }

                const index = _.findIndex(this.learners, {id: this.project.user_id});
                if (index >= 0) {
                    let learner = this.learners[index];
                    this.selected_learner = learner.full_name;
                }
                this.$refs.formModal.show();
            },

            closeFormModal() {
                this.form = {
                    id: '',
                    user_id: this.currentProject.user_id,
                    book_name: '',
                    isbn_hardcover_book: '',
                    isbn_ebook: '',
                }
                this.selected_learner = '';
            },

            saveForm() {
                this.isLoading = true;
                axios.post('/project/' + this.project.id + '/book/save', this.form).then(response => {
                    this.isLoading = false;
                    let message = '';
                    let data = response.data;

                    if (!this.project.user_id && this.form.user_id) {
                        location.reload();
                    }

                    if (this.form.id) {
                        this.updateRecordFromObject(this.books, this.form.id, data.book);
                        message = 'Book updated';
                    } else {
                        this.books.push(data.book);
                        message = 'Book created';
                    }

                    this.project = data.project;

                    this.$refs.formModal.hide();
                    this.$toasted.global.showSuccessMsg({
                        message : message
                    });

                }).catch(error => {
                    this.isLoading = false;
                    this.processError(error);
                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });
                });
            },

            showDeleteModal(book) {
                this.book = book;
                this.$refs.deleteModal.show();
            },

            deleteBook() {
                this.isLoading = true;
                axios.delete('/project/book/' + this.book.id + '/delete' ).then(response => {
                    this.deleteRecordFromObject(this.books, this.book.id);
                    this.isLoading = false;
                    this.$toasted.global.showSuccessMsg({
                        message : 'Book deleted'
                    });
                    this.$refs.deleteModal.hide();
                });
            },

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
        },

        mounted() {
            console.log("project details here");
            console.log(this.currentProject);
            console.log(this.learners);
        }

    }
</script>