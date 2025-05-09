<template>
    <div>
        <button class="btn btn-sm btn-success margin-top" @click="showFormModal()">
            Add Project
        </button>

        <button class="btn btn-sm btn-success margin-top" @click="showNotesModal()">
            Notes
        </button>

        <a href="/services" class="btn btn-sm btn-success margin-top">
            Publishing Services
        </a>

        <a href="/assemble-book-packages" class="btn btn-sm btn-success margin-top">
            Assemble Book Package Options
        </a>

        <a href="/self-publishing/orders" class="btn btn-sm btn-success margin-top">
            Orders
        </a>

        <a href="/book-publisher/calculator" class="btn btn-sm btn-success margin-top">
            Book Publisher
        </a>

        <a href="/storage-books" class="btn btn-sm btn-success margin-top">
            Storage Books
        </a>

        <div class="form-group margin-top" style="width: 100px">
            <label>Filter</label>
            <select name="filter" class="form-control" v-model="filter">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="lead">Lead</option>
                <option value="finished">Finished</option>
                <option value="closed">Closed</option>
            </select>
        </div>

        <div class="table-users">
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>Project Number</th>
                    <th>Name</th>
                    <th>Learner</th>
                    <th>Status</th>
                    <th width="700">Description</th>
                    <th>Date</th>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="project in filteredProjects" :key="project.id">
                    <td>
                        <a :href="'/project/' + project.id">
                            {{ project.identifier}}
                        </a>
                    </td>
                    <td>
                        <a :href="'/project/' + project.id">
                            {{ project.name }}
                        </a>
                    </td>
                    <td>
                        <a :href="'/learner/' + project.user_id" v-if="project.user">
                            {{ project.user.full_name }}
                        </a>
                    </td>
                    <td>
                        {{ capitalize(project.status) }}
                    </td>
                    <td>
                        {{ project.description }}
                    </td>
                    <td>
                        {{ project.start_date}} - {{ project.end_date }} <br>
                        <span class="small" v-if="project.is_finished">Finished</span>
                    </td>
                    <td>
                        <button class="btn btn-xs btn-primary" @click="showFormModal(project)">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" @click="showDeleteModal(project)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <b-modal
                ref="projectFormModal"
                :title="projectModalTitle"
                size="md"
                @hidden="closeProjectFormModal()"
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

            <!-- <div class="form-group">
                <label>Standard Activity</label>
                <v-select :options="activityList" label="activity" v-model="selected_activity" @input="setSelectedActivity($event)"
                          name="activity" style="display: inline-block"
                          :style="currentActivity ? 'width :87%' : 'width: 90%'"></v-select>
                <button class="btn btn-default btn-sm" @click="showActivityModal(false)" v-if="currentActivity">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-default btn-sm" @click="showActivityModal(true)">
                    <i class="fa fa-plus" style="color: #009975"></i>
                </button>
            </div> -->

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
            <!-- <div class="form-group">
                <label>Finished</label> <br>
                <toggle-button :color="'#337ab7'"
                               :labels="{checked: 'Yes', unchecked: 'No'}"
                               v-model="projectForm.is_finished"
                               :width="60" :height="25" :font-size="14"/>
            </div> -->

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveProject()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

        </b-modal>

        <b-modal
                ref="activityFormModal"
                :title="activityModalTitle"
                size="md"
                @hidden="closeActivityFormModal()"
                centered
                no-close-on-backdrop
        >

            <div class="form-group">
                <label>Activity</label>
                <input type="text" class="form-control" name="activity" v-model="activityForm.activity" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" cols="30" rows="10" class="form-control" v-model="activityForm.description"></textarea>
            </div>

            <div class="form-group">
                <label>Invoicing</label>

                <div class="btn-group" role="group" id="invoicing-option">
                    <button type="button" class="btn btn-default" :class="{'active': activityForm.invoicing === 2}"
                            @click="activityForm.invoicing = 2"> Always </button>
                    <button type="button" class="btn btn-default" :class="{'active': activityForm.invoicing === 1}"
                            @click="activityForm.invoicing = 1"> Sometimes </button>
                    <button type="button" class="btn btn-default" :class="{'active': activityForm.invoicing === 0}"
                            @click="activityForm.invoicing = 0"> Never </button>
                </div>
            </div>

            <template v-if="activityForm.invoicing != 0">
                <div class="form-group">
                    <label>Project</label>
                    <v-select :options="projectList" label="name" v-model="selected_project" @input="setSelectedProject($event)"
                              name="project"></v-select>
                </div>

                <div class="form-group">
                    <label>Hourly Rate</label>
                    <input type="number" class="form-control" name="hourly_rate" v-model="activityForm.hourly_rate">
                </div>
            </template>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-secondary" @click="$refs.activityFormModal.hide()">
                    Cancel
                </button>
                <button class="btn btn-sm btn-primary" @click="saveActivity()" :disabled="isActivityLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isActivityLoading"></i> Save
                </button>
                <button class="btn btn-sm btn-danger" @click="showDeleteActivityModal()" v-if="currentActivity && !isAdd">
                    Delete
                </button>
            </div>

        </b-modal>

        <b-modal
                ref="deleteActivityModal"
                title="Delete Activity"
                size="sm"
                centered
                no-close-on-backdrop
        >

            <p>
                Are you sure you want to delete this record?
            </p>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-danger" @click="deleteActivity()" :disabled="isDeleting">
                    <i class="fa fa-spinner fa-pulse" v-if="isDeleting"></i> Delete
                </button>
            </div>
        </b-modal>

        <b-modal
                ref="deleteProjectModal"
                title="Delete Project"
                size="sm"
                centered
                no-close-on-backdrop
        >

            <p>
                Are you sure you want to delete this record?
            </p>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-danger" @click="deleteProject()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Delete
                </button>
            </div>
        </b-modal>

        <b-modal
                ref="notesModal"
                :title="'Notes'"
                size="md"
                @hidden="closeNotesModal()"
                centered
                no-close-on-backdrop
        >

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" id="" cols="30" rows="10" class="form-control" v-model="notesForm.notes"></textarea>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveNotes()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import moment from 'moment';
    export default {
        props: ['learners', 'activities', 'projects', 'project-notes', 'next-project-number', 'editors'],
        data() {
            return {
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
                projectNumber: this.nextProjectNumber,
                projectModalTitle: '',
                selected_learner: '',
                selected_activity: '',
                selected_project: '',
                selected_editor: '',
                activityList: this.activities,
                projectList: this.projects,
                learnerList: this.learners,
                editorList: this.editors,
                activityModalTitle: 'Activity',
                project: {},
                activityForm: {
                    id: '',
                    activity: '',
                    description: '',
                    invoicing: 1, //0 - never, 1 - sometimes, 2-always
                    project_id: '',
                    hourly_rate: '',
                },
                notes: this.projectNotes,
                notesForm: {
                    notes: this.projectNotes
                },
                searchQuery: '',
                searchLearnerList: [],
                isAdd: true,
                currentActivity: '',
                isLoading: false,
                isActivityLoading: false,
                isDeleting: false,
                filter: 'active'
            }
        },

        computed: {
            
            filteredProjects() {
                return this.projects.filter(project => {
                    return !this.filter || (project.status && project.status.toLowerCase().indexOf(this.filter) > -1);
                });
            } 
        },

        methods: {
            capitalize(str) {
                return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
            },

            closeProjectFormModal() {
                this.selected_learner = '';
                this.selected_activity = '';
                this.currentActivity = '';
                this.projectForm = {
                    id: '',
                    name: '',
                    number: '',
                    user_id: '',
                    activity_id: '',
                    start_date: '',
                    end_date: '',
                    description: '',
                    status: 'active'
                }
            },

            showFormModal(data = null) {
                this.projectModalTitle = 'Add Project';
                this.projectForm.start_date = moment().format("YYYY-MM-DD");
                this.projectForm.number = this.projectNumber;
                if (data) {
                    this.projectModalTitle = 'Edit Project';
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

                }
                this.$refs.projectFormModal.show();
            },

            setSelectedLearner(value) {
                this.projectForm.user_id = value ? value.id : "";
            },

            setSelectedActivity(value) {
                this.projectForm.activity_id = value ? value.id : "";
                this.currentActivity = value;
            },

            setSelectedEditor(value) {
                this.projectForm.editor_id = value ? value.id : "";
            },

            saveProject() {
                this.isLoading = true;
                this.removeValidationError();
                axios.post('/project/save', this.projectForm).then(response => {
                    this.isLoading = false;
                    let project = response.data.project;
                    this.projectNumber = response.data.nextProjectNumber;

                    if (this.projectForm.id) {
                        this.updateRecordFromObject(this.projectList, this.projectForm.id, project);
                    } else {
                        this.projectList.push(project);
                    }

                    this.$refs.projectFormModal.hide();

                    this.$toasted.global.showSuccessMsg({
                        message : 'Project saved.'
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

            showDeleteModal(project) {
                this.project = project;
                this.$refs.deleteProjectModal.show();
            },

            deleteProject() {
                this.isLoading = true;
                axios.delete('/project/' + this.project.id + '/delete' ).then(response => {
                    this.deleteRecordFromObject(this.projectList, this.project.id);
                    this.isLoading = false;
                    this.$toasted.global.showSuccessMsg({
                        message : 'Project deleted'
                    });
                    this.$refs.deleteProjectModal.hide();
                });
            },

            showActivityModal(isAdd = false) {
                this.$refs.activityFormModal.show();
                this.isAdd = isAdd;
                if (this.currentActivity && !isAdd) {
                    let activity = this.currentActivity;
                    this.activityForm = {
                        id: activity.id,
                        activity: activity.activity,
                        description: activity.description,
                        invoicing: activity.invoicing,
                        project_id: activity.project_id,
                        hourly_rate: activity.hourly_rate,
                    };
                    const index = _.findIndex(this.projectList, {id: activity.project_id});
                    if (index >= 0) {
                        this.currentProject = this.projectList[index];
                        this.selected_project = this.currentProject.name;
                    }
                }
            },

            closeActivityFormModal() {
                this.activityForm = {
                    id: '',
                    activity: '',
                    description: '',
                    invoicing: 1,
                    project_id: '',
                    hourly_rate: '',
                };
                this.selected_project = '';
            },

            setSelectedProject(value) {
                this.activityForm.project_id = value ? value.id : "";
            },

            saveActivity() {
                this.isActivityLoading = true;
                this.removeValidationError();
                axios.post('/project/activity/save', this.activityForm).then(response => {
                    this.isActivityLoading = false;
                    if (this.activityForm.id) {
                        this.updateRecordFromObject(this.activityList, this.activityForm.id, response.data);
                        this.currentActivity = response.data;
                        this.selected_activity = response.data.activity;
                    } else {
                        this.activityList.push(response.data);
                        this.selected_activity = response.data.activity;
                        this.projectForm.activity_id = response.data.id;
                        this.currentActivity = response.data;
                    }
                    this.$refs.activityFormModal.hide();
                }).catch(error => {
                    this.isActivityLoading = false;
                    this.processError(error);
                })
            },

            showDeleteActivityModal() {
                this.$refs.deleteActivityModal.show();
            },

            deleteActivity() {
                this.isDeleting = true;
                axios.delete('/project/activity/' + this.currentActivity.id + '/delete').then(response => {
                    this.deleteRecordFromObject(this.activityList, this.currentActivity.id);
                    this.isDeleting = false;
                    this.currentActivity = '';
                    this.selected_activity = '';
                    this.projectForm.activity_id = '';
                    this.$refs.deleteActivityModal.hide();
                    this.$refs.activityFormModal.hide();
                    this.$toasted.global.showSuccessMsg({
                        message : 'Activity deleted'
                    });
                });
            },

            closeNotesModal() {
                this.notesForm.notes = this.notes;
            },

            showNotesModal() {
                this.$refs.notesModal.show();
            },

            saveNotes() {
                let data = {
                    setting_value: this.notesForm.notes
                };

                this.isLoading = true;
                axios.post('/settings/create/project-notes', data).then(response => {
                    this.notes = response.data.setting_value;
                    this.isLoading = false;

                    this.$refs.notesModal.hide();

                    this.$toasted.global.showSuccessMsg({
                        message : 'Notes saved successfully'
                    });
                })
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
        },

        mounted() {
            console.log(this.projects);
        }
    }
</script>

<style>
    :root {
        --vs-font-size: 1.5rem;
    }

    #invoicing-option > .btn {
        border-color: #d8d8d8;
        outline: none;
    }

    #invoicing-option > .btn:first-child:not(:last-child):not(.dropdown-toggle) {
        border-bottom-right-radius: 0;
        border-top-right-radius: 0;
    }

    #invoicing-option .btn-default {
        color: #333;
        background-color: #fff;
    }

    #invoicing-option > .btn:before {
        position: relative;
        top: 2px;
        margin-left: -2px;
        display: inline-block;
        font-family: 'Glyphicons Halflings';
        font-style: normal;
        font-weight: 400;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        font-smoothing: antialiased;
        content: "\e157";
        color: #444;
    }

    #invoicing-option > .btn.active {
        background-color: #eee;
        border-color: #adadad;
        color: #272727;
    }

    #invoicing-option > .btn.active:before {
        content: "\e067";
        color: #009975;
    }
</style>