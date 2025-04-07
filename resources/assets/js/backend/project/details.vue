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
            <a :href="'/project/' + project.id + '/marketing-plan'" class="btn btn-primary btn-sm">
                Marketing Plans
            </a>
            <a :href="'/project/' + project.id + '/progress-plan'" class="btn btn-primary btn-sm">
                Fremdriftsplan
            </a>
            <a :href="'/project/' + project.id + '/contract'" class="btn btn-primary btn-sm">
                Contract
            </a>
            <a :href="'/project/' + project.id + '/invoice'" class="btn btn-primary btn-sm">
                Invoices
            </a>
            <a :href="'/project/' + project.id + '/storage'" class="btn btn-primary btn-sm">
                Storage
            </a>
            <a :href="'/project/' + project.id + '/e-book'" class="btn btn-primary btn-sm">
                Ebook
            </a>
            <a :href="'/project/' + project.id + '/audio'" class="btn btn-primary btn-sm">
                Audio
            </a>
            <a :href="'/project/' + project.id + '/print'" class="btn btn-primary btn-sm">
                Print
            </a>
            <div class="pull-right">
                <button class="btn btn-success btn-sm" @click="showLearnerFormModal()">
                    <i class="fa fa-user"></i> Add Learner
                </button>

                <button class="btn btn-primary btn-sm" @click="showProjectFormModal()">
                    <i class="fa fa-edit"></i> Edit Project
                </button>
            </div>
            <div class="clearfix"></div>
        </div>

        <b-modal
                ref="learnerFormModal"
                :title="'Add Learner'"
                size="md"
                @hidden="closeLearnerFormModal()"
                centered
                no-close-on-backdrop
        >

            <div class="form-group">
                <label>Email</label>
                <input type="text" class="form-control" name="name" v-model="learnerForm.email" required>
            </div>

            <div class="form-group">
                <label>Firstname</label>
                <input type="text" name="first_name" class="form-control no-border-left" v-model="learnerForm.first_name" required>
            </div>

            <div class="form-group">
                <label>Lastname</label>
                <input type="text" name="last_name" class="form-control no-border-left" v-model="learnerForm.last_name" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="text" name="password" class="form-control no-border-left" v-model="learnerForm.password" required>
                <button class="btn btn-success btn-sm margin-top" type="button" @click="generatePassword()">
                    Generate
                </button>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveLearner()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

        </b-modal>

        <b-modal
                ref="projectFormModal"
                :title="projectModalTitle"
                size="md"
                centered
                no-close-on-backdrop
        >

            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" v-model="projectForm.name" required>
            </div>

            <div class="form-group">
                <label>Number</label>
                <input type="number" class="form-control" name="number" v-model="projectForm.number" required>
            </div>

            <div class="form-group">
                <label>Learner</label>
                <!-- <v-select :options="learnerList" label="full_name" v-model="selected_learner" @input="setSelectedLearner($event)"
                          name="learner_id"></v-select> -->

                <div class="dropdown-container">
                    <input type="text" v-model="searchQuery" @input="fetchLearners" class="form-control" placeholder="Search for users">
                    <div class="dropdown-results" v-if="searchLearnerList.length">
                        <div v-for="learner in searchLearnerList" :key="learner.id" @click="selectLearner(learner)">
                            {{ learner.first_name + " " + learner.last_name }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Start date</label>
                <input type="date" class="form-control" name="start_date" v-model="projectForm.start_date">
            </div>

            <div class="form-group">
                <label>End date</label>
                <input type="date" class="form-control" name="end_date" v-model="projectForm.end_date">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" cols="30" rows="10" class="form-control" v-model="projectForm.description"></textarea>
            </div>

            <div class="form-group">
                <label>Editor</label>
                <v-select :options="editorList" label="full_name" v-model="selected_editor" @input="setSelectedEditor($event)"
                          name="editor_id"></v-select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" v-model="projectForm.status">
                    <option value="active">Active</option>
                    <option value="lead">Lead</option>
                    <option value="finished">Finished</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveProject()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

        </b-modal>
    </div>
</template>

<script>
    import { quillEditor } from 'vue-quill-editor'

    export default {

        props: ['current-project', 'learners', 'activities', 'editors'],
        data() {
            return {
                project: this.currentProject,
                learnerList: this.learners,
                activityList: this.activities,
                editorList: this.editors,
                learnerForm: {
                    project_id: this.currentProject.id,
                    email: '',
                    first_name: '',
                    last_name: '',
                    password: ''
                },
                projectModalTitle: '',
                projectForm: {
                    id: '',
                    name: '',
                    number: '',
                    user_id: '',
                    activity_id: '',
                    start_date: '',
                    end_date: '',
                    description: '',
                    editor_id: '',
                    status: 'active'
                },
                currentActivity: '',
                selected_activity: '',
                selected_learner: '',
                selected_editor: '',
                searchQuery: '',
                searchLearnerList: [],
                isLoading: false,
            }
        },
        methods: {
            showLearnerFormModal() {
                this.$refs.learnerFormModal.show();
            },

            closeLearnerFormModal() {
                this.learnerForm = {
                    project_id: this.project.id,
                    email: '',
                    first_name: '',
                    last_name: '',
                    password: ''
                }
            },

            generatePassword() {
                axios.get('/learner/generate-password').then(response => {
                    this.learnerForm.password = response.data;
                });
            },

            saveLearner() {
                this.isLoading = true;
                this.removeValidationError();
                axios.post('/project/' + this.project.id + '/learner/add', this.learnerForm).then(response => {
                    this.isLoading = false;
                    this.learnerList.push(response.data.user);
                    this.project = response.data.project;
                    this.$refs.learnerFormModal.hide();

                    this.$toasted.global.showSuccessMsg({
                        message : 'Learner added'
                    });

                }).catch(error => {
                    this.isLoading = false;
                    this.processError(error);
                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });
                });
            },

            showProjectFormModal() {
                this.projectModalTitle = 'Edit Project';
                let data = this.project;
                this.projectForm = {
                    id: data.id,
                    name: data.name,
                    number: data.identifier,
                    user_id: data.user_id,
                    activity_id: data.activity_id,
                    start_date: data.start_date,
                    end_date: data.end_date,
                    description: data.description,
                    editor_id: data.editor_id,
                    status: data.status
                };

                if (data.user) {
                    this.searchQuery = data.user.full_name;
                } 

                const actIndex = _.findIndex(this.activityList, {id: data.activity_id});
                const learnerIndex = _.findIndex(this.learnerList, {id: data.user_id});
                const editorIndex = _.findIndex(this.editorList, {id: data.editor_id});
                console.log(data);

                if (actIndex >= 0) {
                    this.currentActivity = this.activityList[actIndex];
                    this.selected_activity = this.currentActivity.activity;
                }

                if (learnerIndex >= 0) {
                    this.selected_learner = this.learnerList[learnerIndex].full_name;
                }

                if (editorIndex >= 0) {
                    this.selected_editor = this.editorList[editorIndex].full_name;
                }

                this.$refs.projectFormModal.show();
            },

            setSelectedLearner(value) {
                this.form.user_id = value ? value.id : "";
                this.projectForm.user_id = value ? value.id : "";
            },

            setSelectedEditor(value) {
                this.projectForm.editor_id = value ? value.id : "";
            },

            saveProject() {
                this.isLoading = true;
                this.removeValidationError();
                axios.post('/project/save', this.projectForm).then(response => {
                    console.log(response);
                    this.isLoading = false;

                    //this.project = response.data.project;
                    this.$refs.projectFormModal.hide();

                    this.$toasted.global.showSuccessMsg({
                        message : 'Project added'
                    });

                    location.reload();
                }).catch(error => {
                    this.isLoading = false;
                    this.processError(error);
                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });
                });
            },

            fetchLearners() {
                if (this.searchQuery.length > 1) {
                    fetch(`/learners/search?search=${this.searchQuery}`)
                        .then(response => response.json())
                        .then(data => {
                            this.searchLearnerList = data;
                        })
                        .catch(error => console.error('Error fetching data:', error));
                } else {
                    this.searchLearnerList = [];
                }
            },
            selectLearner(learner) {
                this.projectForm.user_id = learner.id;
                this.selected_learner = learner.first_name + " " + learner.last_name;
                this.searchQuery = learner.first_name + " " + learner.last_name;
                this.searchLearnerList = [];
            },
        }
    }
</script>