<template>
    <div>
        <div class="panel">
            <div class="panel-header" style="padding: 10px">
                <em><b>Tasks</b></em>
            </div>
            <div class="panel-body">
                <button class="btn btn-success btn-sm pull-right" @click="showTaskFormModal()">
                    + Add Task
                </button>
                <div class="clearfix"></div>
                <div class="table-users">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assigned To</th>
                            <th width="150"></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="task in tasks" :key="task.id">
                                <td>
                                    {{ task.task }}
                                </td>
                                <td>
                                    {{ task.editor.full_name }}
                                </td>
                                <td>
                                    <button class="btn btn-success btn-xs" @click="showFinishTaskModal(task)">
                                        <i class="fa fa-check"></i>
                                    </button>

                                    <button class="btn btn-primary btn-xs" @click="showTaskFormModal(task)">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <button class="btn btn-danger btn-xs" @click="showDeleteTaskModal(task)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <b-modal
            ref="taskFormModal"
            size="md"
            @hidden="closeTaskFormModal()"
            centered
        >
            <div slot="modal-title">
                <h4 class="modal-title">{{ modalTitle }}</h4>
            </div>

            <div class="form-group">
                <label>Task</label>
                <textarea name="task" cols="30" rows="10" class="form-control" v-model="taskForm.task" required></textarea>
            </div>

            <div class="form-group">
                <label>Assign to</label>
                <select name="assign_to" class="form-control" v-model="taskForm.assigned_to" required>
                    <option value="" disabled selected>Select Editor</option>
                    <option :value="editor.id" v-for="editor in editorAndAdminList" :key="editor.id">
                        {{ editor.full_name }}
                    </option>
                </select>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveTask()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>
        </b-modal>

        <b-modal
            ref="finishTaskModal"
            title="Finish Task"
            size="sm"
            centered
            no-close-on-backdrop
        >

            <p>
                Are you sure you want to finish this task?
            </p>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-success" @click="finishTask()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Finish
                </button>
            </div>
        </b-modal>

        <b-modal
                ref="deleteTaskModal"
                title="Delete Task"
                size="sm"
                centered
                no-close-on-backdrop
        >

            <p>
                Are you sure you want to delete this record?
            </p>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-danger" @click="deleteTask()" :disabled="isDeleting">
                    <i class="fa fa-spinner fa-pulse" v-if="isDeleting"></i> Delete
                </button>
            </div>
        </b-modal>
    </div>
</template>

<script>

    export default {

        props: ['current-project', 'task-list', 'editor-and-admin-list'],

        data() {
            return {
                project: this.currentProject,
                tasks: this.taskList,
                modalTitle: '',
                taskForm: {
                    id: '',
                    project_id: '',
                    assigned_to: '',
                    task: ''
                },
                isLoading: false,
                isDeleting: false
            }
        },

        methods: {
            showTaskFormModal(data) {
                this.modalTitle = 'Add Task';
                this.taskForm.project_id = this.project.id;

                if (data) {
                    this.taskForm.id = data.id;
                    this.taskForm.assigned_to = data.assigned_to;
                    this.taskForm.task = data.task;
                }

                this.$refs.taskFormModal.show();
            },

            closeTaskFormModal() {
                this.taskForm = {
                    id: '',
                    project_id: this.currentProject.id,
                    assigned_to: '',
                    task: '',
                };
            },

            saveTask() {
                this.isLoading = true;
                
                axios.post('/task/save', this.taskForm).then(response => {
                    this.isLoading = false;
                    if (this.taskForm.id) {
                        this.updateRecordFromObject(this.tasks, response.data.id, response.data);
                    } else {
                        this.tasks.push(response.data);
                    }

                    this.$toasted.global.showSuccessMsg({
                        message : 'Record saved'
                    });
                    this.$refs.taskFormModal.hide();
                }).catch(error => {
                    this.isLoading = false;
                    this.processError(error);
                });
            },

            showFinishTaskModal(data) {
                this.taskForm.id = data.id;
                this.$refs.finishTaskModal.show();
            },

            finishTask() {
                this.isLoading = true;
                axios.post('/project/task/' + this.taskForm.id + '/finish').then(response => {
                    this.isLoading = false;
                    this.deleteRecordFromObject(this.tasks, this.taskForm.id);
                    this.$refs.finishTaskModal.hide();
                    this.$toasted.global.showSuccessMsg({
                        message : 'Record finished'
                    });
                });
            },

            showDeleteTaskModal(data) {
                this.taskForm.id = data.id;
                this.$refs.deleteTaskModal.show();
            },

            deleteTask() {
                this.isDeleting = true;
                axios.delete('/project/task/' + this.taskForm.id + '/delete').then(response => {
                    this.isDeleting = false;
                    this.deleteRecordFromObject(this.tasks, this.taskForm.id);
                    this.$refs.deleteTaskModal.hide();
                    this.$toasted.global.showSuccessMsg({
                        message : 'Record deleted'
                    });
                });
            },
        }
    }
</script>