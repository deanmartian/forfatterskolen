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
            <a :href="'/project/' + project.id + '/contract'" class="btn btn-primary btn-sm">
                Contract
            </a>
            <a :href="'/project/' + project.id + '/invoice'" class="btn btn-primary btn-sm">
                Invoices
            </a>
            <a :href="'/project/' + project.id + '/storage'" class="btn btn-primary btn-sm">
                Storage
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

        <div class="margin-top">
            <div class="col-md-6">
                <div class="panel">
                    <div class="panel-header" style="padding: 10px">
                        <em><b>Notes</b></em>
                        <button class="btn btn-primary btn-sm pull-right" @click="showNotes()">
                            Notes
                        </button>
                    </div>
                    <div class="panel-body" v-html="formattedNotes">
                    </div>
                </div>

                <button class="btn btn-success margin-top" @click="showWholeBookFormModal()">
                    Add
                </button>

                <div class="table-users">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>Book</th>
                            <th>Description</th>
                            <th>Date Uploaded</th>
                            <th width="300"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="wholeBook in wholeBooks" :key="wholeBook.id">
                            <td>
                                <a href="javascript:;" @click="showManuscript(wholeBook)" >{{ formattedContent(wholeBook) }}</a>
                            </td>
                            <td>
                                {{ wholeBook.description }}
                            </td>
                            <td>
                                {{ wholeBook.date_uploaded }}
                            </td>
                            <td>
                                <a class="btn btn-xs btn-success"
                                   :href="'/project/' + project.id + '/whole-book/' + wholeBook.id + '/download'">
                                    <i class="fa fa-download"></i>
                                </a>

                                <button class="btn btn-xs btn-primary" @click="showWholeBookFormModal(wholeBook)">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <button class="btn btn-xs btn-danger" @click="showDeleteBookCritiqueFormModal(wholeBook)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-success margin-top" @click="showBookCritiqueFormModal()">
                    Add Book Critique
                </button>

                <div class="table-users">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>Book</th>
                            <th>Description</th>
                            <th>Date Uploaded</th>
                            <th>Feedback</th>
                            <th width="300"></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bookCritique in bookCritiques" :key="bookCritique.id">
                                <td>
                                    <a href="javascript:;" @click="showManuscript(bookCritique)" >{{ formattedContent(bookCritique) }}</a>
                                </td>
                                <td>
                                    {{ bookCritique.description }}
                                </td>
                                <td>
                                    {{ bookCritique.date_uploaded }}
                                </td>
                                <td>
                                    <button class="btn btn-success btn-xs" v-if="!bookCritique.feedback"
                                     @click="showBookCritiqueFeedbackModal(bookCritique)">
                                        Add Feedback
                                    </button>
                                    <div v-else v-html="bookCritique.feedback_file">
                                        
                                    </div>
                                </td>
                                <td>
                                    <a class="btn btn-xs btn-success"
                                    :href="'/project/' + project.id + '/whole-book/' + bookCritique.id + '/download'">
                                        <i class="fa fa-download"></i>
                                    </a>

                                    <button class="btn btn-xs btn-primary" @click="showBookCritiqueFormModal(bookCritique)">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <button class="btn btn-xs btn-danger" @click="showDeleteBookFormModal(bookCritique)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel">
                    <div class="panel-header" style="padding: 10px">
                        <em><b>Learner</b></em>
                    </div>
                    <div class="panel-body">
                        <a :href="'/learner/' + project.user_id" v-if="project.user">
                            {{ project.user.full_name }}
                        </a>
                    </div>
                </div>

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

                <div class="panel" v-if="project.user_id">
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
                    <tr v-for="book in books" :key="book.id">
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
                <v-select :options="learnerList" label="full_name" v-model="selected_learner" @input="setSelectedLearner($event)"
                          name="learner_id"></v-select>
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
                <label>Status</label>
                <select name="status" class="form-control" v-model="projectForm.status">
                    <option value="active">Active</option>
                    <option value="lead">Lead</option>
                    <option value="finished">Finished</option>
                </select>
            </div>

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
                ref="wholeBookFormModal"
                :title="modalTitle"
                size="md"
                @hidden="closeWholeBookFormModal()"
                centered
                no-close-on-backdrop
        >

            <div class="form-group">
                <toggle-button :color="'#337ab7'"
                               :labels="{checked: 'File Upload', unchecked: 'Write Book'}"
                               v-model="wholeBookForm.is_file"
                               :width="150" :height="30" :font-size="16" @change="removeValidationError()"/>
            </div>

            <div class="form-group" v-if="wholeBookForm.is_file">
                <label>Upload Book</label>
                <input type="file" name="book_file" class="form-control"
                       @change="onWholeBookFileChange"
                       accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
            </div>

            <div class="form-group" v-if="!wholeBookForm.is_file">
                <label>Write Book</label>
                <quill-editor ref="wholeBookEditor" :content="wholeBookForm.book_content"
                              @change="onEditorChange($event)"></quill-editor>
                <input type="hidden" name="book_content">
            </div>

            <div class="form-group">
                <label>
                    Description
                </label>
                <textarea name="description" cols="30" rows="10" class="form-control" v-model="wholeBookForm.description"></textarea>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveWholeBookForm()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

        </b-modal>

        <b-modal
                ref="bookCritiqueFormModal"
                :title="modalTitle"
                size="md"
                @hidden="closeBookCritiqueFormModal()"
                centered
                no-close-on-backdrop
        >

            <div class="form-group">
                <toggle-button :color="'#337ab7'"
                               :labels="{checked: 'File Upload', unchecked: 'Write Book'}"
                               v-model="bookCritiqueForm.is_file"
                               :width="150" :height="30" :font-size="16" @change="removeValidationError()"/>
            </div>

            <div class="form-group" v-if="bookCritiqueForm.is_file">
                <label>Upload Book</label>
                <input type="file" name="book_file" class="form-control"
                       @change="onBookCritiqueFileChange"
                       accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
            </div>

            <div class="form-group" v-if="!bookCritiqueForm.is_file">
                <label>Write Book</label>
                <quill-editor ref="wholeBookEditor" :content="bookCritiqueForm.book_content"
                              @change="onBookCritiqueEditorChange($event)"></quill-editor>
                <input type="hidden" name="book_content">
            </div>

            <div class="form-group">
                <label>
                    Description
                </label>
                <textarea name="description" cols="30" rows="10" class="form-control" v-model="bookCritiqueForm.description"></textarea>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveBookCritique()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

        </b-modal>

        <b-modal
            ref="bookCritiqueFeedbackModal"
            :title="'Feedback'"
            size="md"
            @hidden="closeBookCritiqueFormModal()"
            centered
            no-close-on-backdrop
        >

            <div class="form-group">
                <label>Feedback</label>
                <input type="file" name="feedback" class="form-control"
                        @change="onBookCritiqueFeedbackChange"
                        accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
                        application/vnd.oasis.opendocument.text">
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveBookCritiqueFeedback()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

        </b-modal>

        <b-modal
                ref="deleteBookCritiqueFormModal"
                title="Delete Book"
                size="sm"
                centered
        >

            <p>
                Are you sure you want to delete this record?
            </p>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-danger" @click="deleteBookCritique()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Delete
                </button>
            </div>
        </b-modal>

        <b-modal
                ref="wholeBookContentModal"
                :title="''"
                size="md"
                centered
                no-close-on-backdrop
                hide-footer
        >

            <div v-html="wholeBookForm.book_content" class="whole-book-container"></div>

        </b-modal>

        <b-modal
                ref="deleteBookFormModal"
                title="Delete Book"
                size="sm"
                centered
        >

            <p>
                Are you sure you want to delete this record?
            </p>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-danger" @click="deleteWholeBook()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Delete
                </button>
            </div>
        </b-modal>

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
    import { quillEditor } from 'vue-quill-editor'

    export default {

        props: ['current-project', 'learners', 'activities', 'time-registers', 'project-time-list', 'projects',
            'whole-book-list', 'editor-and-admin-list', 'task-list', 'book-critique-list'],

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
                wholeBooks: this.wholeBookList,
                wholeBookForm: {
                    id: '',
                    book_content: '',
                    book_file: [],
                    description: '',
                    is_file: true
                },
                wholeBookFilename: '',
                bookCritiqueForm: {
                    id: '',
                    book_content: '',
                    book_file: [],
                    description: '',
                    is_file: true,
                    is_book_critique: true,
                },
                bookCritiqueFilename: '',
                bookCritiqueFeedbackFilename: '',
                bookCritiques: this.bookCritiqueList,
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
                    status: 'active'
                },
                activityList: this.activities,
                learnerList: this.learners,
                currentActivity: '',
                selected_activity: '',
                selected_project: '',
                activityModalTitle: 'Activity',
                activityForm: {
                    id: '',
                    activity: '',
                    description: '',
                    invoicing: 1, //0 - never, 1 - sometimes, 2-always
                    project_id: '',
                    hourly_rate: '',
                },
                projectList: this.projects,
                learnerForm: {
                    project_id: this.currentProject.id,
                    email: '',
                    first_name: '',
                    last_name: '',
                    password: ''
                },
                taskForm: {
                    id: '',
                    project_id: '',
                    assigned_to: '',
                    task: ''
                },
                tasks: this.taskList,
                isAdd: true,
                isActivityLoading: false,
                isDeleting: false,
                isLoading: false,
            }
        },

        computed: {
            formattedNotes() {
                return this.project.short_notes ? this.nl2br(this.project.short_notes) : null;
            }
        },

        components: {
            quillEditor
        },

        methods: {
            nl2br(str) {
                return str.replace(/\n/g, '<br>');
            },

            setSelectedLearner(value) {
                this.form.user_id = value ? value.id : "";
                this.projectForm.user_id = value ? value.id : "";
            },

            setSelectedActivity(value) {
                this.projectForm.activity_id = value ? value.id : "";
                this.currentActivity = value;
            },

            showNotes() {
                this.noteForm = {
                    id: this.project.id,
                    notes: this.project.notes
                },
                this.$refs.notesModal.show();
            },

            saveNotes() {
                this.isLoading = true;
                axios.post('/project/' + this.project.id + '/notes/save', this.noteForm).then(response => {
                    this.isLoading = false;
                    this.project = response.data;
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
                    console.log(response);
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
                    status: data.status
                };

                const actIndex = _.findIndex(this.activityList, {id: data.activity_id});
                const learnerIndex = _.findIndex(this.learnerList, {id: data.user_id});
                if (actIndex >= 0) {
                    this.currentActivity = this.activityList[actIndex];
                    this.selected_activity = this.currentActivity.activity;
                }

                if (learnerIndex >= 0) {
                    this.selected_learner = this.learnerList[learnerIndex].full_name;
                }

                this.$refs.projectFormModal.show();
            },

            saveProject() {
                this.isLoading = true;
                this.removeValidationError();
                axios.post('/project/save', this.projectForm).then(response => {
                    this.isLoading = false;

                    this.project = response.data.project;
                    this.$refs.projectFormModal.hide();

                    this.$toasted.global.showSuccessMsg({
                        message : 'Project added'
                    });
                }).catch(error => {
                    this.isLoading = false;
                    this.processError(error);
                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });
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

            showWholeBookFormModal(data = null) {
                this.modalTitle = 'Add Book';
                if (data) {
                    this.modalTitle = 'Edit Book';
                    this.wholeBookForm = {
                        id: data.id,
                        is_file: !!data.is_file,
                        book_content: data.book_content,
                        description: data.description
                    };
                }

                this.$refs.wholeBookFormModal.show();
            },

            closeWholeBookFormModal() {
                this.wholeBookForm = {
                    id: '',
                    book_content: '',
                    book_file: [],
                    description: '',
                    is_file: true
                }
            },

            onWholeBookFileChange(e) {
                let files = e.target.files;

                if (!files.length)
                {
                    this.wholeBookFilename = i18n.site['learner.files-text'];
                    this.wholeBookForm.book_file = [];
                    return;
                }

                this.wholeBookFilename = files[0].name;
                this.wholeBookForm.book_file = files[0];

                $(".validation-err").remove();
            },

            onEditorChange({ html, text }) {
                this.wholeBookForm.book_content = html;
            },

            formattedContent (book) {
                if (book.is_file) {
                    return book.filename;
                }

                return 'Details'
            },

            REMOVE_HTML(content = '') {
                // eslint-disable-next-line no-useless-escape
                let stripedHtml = content.replace(/<br\s*[\/]?>/gi, '\n')
                stripedHtml = stripedHtml.replace(/<[^>]+>/g, '') // Remove html tags

                return stripedHtml
            },

            showManuscript(book) {
                if (book.is_file) {
                    /*
                    * file_link was set in \App\Models\AssignmentManuscript::getFileLinkAttribute
                    * file_link is <a> tag
                    * */
                    let divlink = document.createElement('div');
                    divlink.innerHTML = book.file_link;
                    divlink.getElementsByTagName("a")[0].target="_blank";
                    divlink.getElementsByTagName("a")[0].click()

                } else {
                    this.wholeBookForm.book_content = book.book_content;
                    this.$refs.wholeBookContentModal.show();
                }
            },

            saveWholeBookForm() {
                this.isLoading = true;
                this.removeValidationError();

                let formData = new FormData();
                $.each(this.wholeBookForm, function(k, v) {
                    formData.append(k, v);
                });

                axios.post('/project/' + this.project.id + '/whole-book/save', formData).then(response => {
                    this.isLoading = false;
                    this.$refs.wholeBookFormModal.hide();

                    if (this.wholeBookForm.id) {
                        this.updateRecordFromObject(this.wholeBooks, response.data.id, response.data);
                    } else {
                        this.wholeBooks.push(response.data);
                    }

                    this.$toasted.global.showSuccessMsg({
                        message : 'Book saved'
                    });
                }).catch(error => {
                    this.isLoading = false;
                    this.processError(error);
                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });
                })
            },

            showDeleteBookFormModal(book) {
                this.wholeBookForm.id = book.id;
                this.$refs.deleteBookFormModal.show();
            },

            deleteWholeBook() {
                this.isLoading = true;
                axios.delete('/project/whole-book/' + this.wholeBookForm.id + '/delete').then(response => {
                    this.isLoading = false;
                    this.deleteRecordFromObject(this.wholeBooks, this.wholeBookForm.id);
                    this.$refs.deleteBookFormModal.hide();
                    this.$toasted.global.showSuccessMsg({
                        message : 'Record deleted'
                    });
                });
            },

            showBookCritiqueFeedbackModal(data) {
                this.bookCritiqueForm.id = data.id;
                this.$refs.bookCritiqueFeedbackModal.show();
            },

            showBookCritiqueFormModal(data) {
                this.modalTitle = 'Add Book Critique';
                if (data) {
                    this.modalTitle = 'Edit Book Critique';
                    this.bookCritiqueForm = {
                        id: data.id,
                        is_file: !!data.is_file,
                        book_content: data.book_content,
                        description: data.description
                    };
                }

                this.$refs.bookCritiqueFormModal.show();
            },

            closeBookCritiqueFormModal() {
                this.bookCritiqueForm = {
                    id: '',
                    book_content: '',
                    book_file: [],
                    description: '',
                    is_file: true,
                    is_book_critique: true,
                    feedback: []
                }
            },

            onBookCritiqueFileChange(e) {
                let files = e.target.files;

                if (!files.length)
                {
                    this.bookCritiqueFilename = i18n.site['learner.files-text'];
                    this.bookCritiqueForm.book_file = [];
                    return;
                }

                this.bookCritiqueFilename = files[0].name;
                this.bookCritiqueForm.book_file = files[0];

                $(".validation-err").remove();
            },

            onBookCritiqueFeedbackChange(e) {
                let files = e.target.files;

                if (!files.length)
                {
                    this.bookCritiqueFeedbackFilename = i18n.site['learner.files-text'];
                    this.bookCritiqueForm.feedback = [];
                    return;
                }

                this.bookCritiqueFeedbackFilename = files[0].name;
                this.bookCritiqueForm.feedback = files[0];

                $(".validation-err").remove();
            },

            onBookCritiqueEditorChange({ html, text }) {
                this.bookCritiqueForm.book_content = html;
            },

            saveBookCritiqueFeedback() {
                this.isLoading = true;
                this.removeValidationError();

                let formData = new FormData();
                $.each(this.bookCritiqueForm, function(k, v) {
                    formData.append(k, v);
                });

                axios.post('/project/book-critique/' + this.bookCritiqueForm.id + '/feedback', formData).then(response => {
                    this.isLoading = false;
                    this.$refs.bookCritiqueFeedbackModal.hide();

                    this.updateRecordFromObject(this.bookCritiques, response.data.id, response.data);

                    this.$toasted.global.showSuccessMsg({
                        message : 'Feedback saved'
                    });
                });
            },

            saveBookCritique() {
                this.isLoading = true;
                this.removeValidationError();

                let formData = new FormData();
                $.each(this.bookCritiqueForm, function(k, v) {
                    formData.append(k, v);
                });

                axios.post('/project/' + this.project.id + '/whole-book/save', formData).then(response => {
                    this.isLoading = false;
                    this.$refs.bookCritiqueFormModal.hide();
                    console.log(response);

                    if (this.bookCritiqueForm.id) {
                        this.updateRecordFromObject(this.bookCritiques, response.data.id, response.data);
                    } else {
                        this.bookCritiques.push(response.data);
                    }

                    this.$toasted.global.showSuccessMsg({
                        message : 'Book saved'
                    });
                }).catch(error => {
                    this.isLoading = false;
                    this.processError(error);
                    this.$toasted.global.showErrorMsg({
                        message : 'Error in form'
                    });
                })
            },

            showDeleteBookFormModal(book) {
                this.bookCritiqueForm.id = book.id;
                this.$refs.deleteBookCritiqueFormModal.show();
            },

            deleteBookCritique() {
                this.isLoading = true;
                axios.delete('/project/book-critique/' + this.bookCritiqueForm.id + '/delete').then(response => {
                    this.isLoading = false;
                    this.deleteRecordFromObject(this.bookCritiques, this.bookCritiqueForm.id);
                    this.$refs.deleteBookCritiqueFormModal.hide();
                    this.$toasted.global.showSuccessMsg({
                        message : 'Record deleted'
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

            closeTaskFormModal() {
                this.taskForm = {
                    id: '',
                    project_id: this.currentProject.id,
                    assigned_to: '',
                    task: '',
                };
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
            console.log(this.project);
        }

    }
</script>

<style>
    .ql-editor {
        min-height: 20rem !important;
    }

    .whole-book-container {
        max-height: 500px;
        overflow: auto;
    }

    .whole-book-container p {
        margin-bottom: 0;
    }

    .see-more {
        color: #862736;
        font-weight: bold;
    }
</style>