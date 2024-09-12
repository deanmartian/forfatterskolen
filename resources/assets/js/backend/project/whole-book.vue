<template>
    <div>
        <button class="btn btn-success" @click="showWholeBookFormModal()">
            Add
        </button>

        <div class="table-users">
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>Book</th>
                    <th>Description</th>
                    <th>Date Uploaded</th>
                    <th>Designer</th>
                    <th width="150">Details</th>
                    <th width="150"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(wholeBook, index) in wholeBooks" :key="wholeBook.id">
                    <td>
                        <template v-if="wholeBook.dropbox_link">
                            <a :href="wholeBook.dropbox_link" target="_blank">{{ formattedContent(wholeBook) }}</a>
                        </template>
                        <template v-else>
                            <a href="javascript:;" @click="showManuscript(wholeBook)" >{{ formattedContent(wholeBook) }}</a>
                        </template>
                    </td>
                    <td>
                        {{ wholeBook.description }}
                    </td>
                    <td>
                        {{ wholeBook.date_uploaded }}
                    </td>
                    <td>
                        {{ wholeBook.designer ? wholeBook.designer.full_name : '' }}
                    </td>
                    <td>
                        <template v-if="wholeBook.width">
                            <b>Width:</b> {{ wholeBook.width }} (mm) <br>
                            <b>Height:</b> {{ wholeBook.height }} (mm) <br>

                            <template v-if="wholeBook.page_count">
                                <b>Page Count:</b> {{ wholeBook.page_count }} <br>
                                <a href="#" v-if="wholeBook.designer_description" 
                                    @click="showDescriptionModal(wholeBook.designer_description)">
                                    Description
                                </a>
                            </template>
                        </template>
                    </td>
                    <td>
                        <a class="btn btn-xs btn-success"
                            :href="'/project/' + project.id + '/whole-book/' + wholeBook.id + '/download'">
                            <i class="fa fa-download"></i>
                        </a>

                        <button class="btn btn-xs btn-primary" @click="showWholeBookFormModal(wholeBook)">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button class="btn btn-xs btn-danger" @click="showDeleteBookFormModal(wholeBook)">
                            <i class="fa fa-trash"></i>
                        </button>

                        <toggle-button :color="'#337ab7'"
                        class="mt-3"
                               :labels="{checked: 'Completed', unchecked: 'Pending'}"
                               v-model="wholeBookStatusBoolean[index]"
                               :width="110" :height="25" :font-size="14" @change="updateBookStatus(wholeBook, index)"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

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

            <div class="form-group">
                <label>Send Book to Graphic Designer</label>
                <toggle-button :color="'#337ab7'"
                               :labels="{checked: 'Yes', unchecked: 'No'}"
                               v-model="wholeBookForm.send_to_designer"
                               :width="70" :height="30" :font-size="16"/>
            </div>

            <div v-if="wholeBookForm.send_to_designer">
                <div class="form-group">
                    <label>
                        Graphic Designer
                    </label>
                    <select name="designer_id" class="form-control" v-model="wholeBookForm.designer_id">
                        <option value="" selected disabled>- Select Designer -</option>
                        <option :value="designer.id" v-for="designer in designers" :key="'designer' + designer.id">
                            {{ designer.full_name }}
                        </option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="width">Width (mm)</label>
                        <input 
                            type="number" 
                            id="width" 
                            v-model="wholeBookForm.width"
                            class="form-control" 
                            placeholder="Width in mm">
                    </div>
            
                    <div class="form-group col-md-6">
                        <label for="height">Height (mm)</label>
                        <input 
                            type="number" 
                            id="height" 
                            v-model="wholeBookForm.height"
                            class="form-control" 
                            placeholder="Height in mm">
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>

            <div slot="modal-footer">
                <button class="btn btn-sm btn-primary" @click="saveWholeBookForm()" :disabled="isLoading">
                    <i class="fa fa-spinner fa-pulse" v-if="isLoading"></i> Save
                </button>
            </div>

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
                ref="descriptionModal"
                title="Description"
                centered
                hide-footer
        >

            <div v-html="designerDescription">
            </div>
        </b-modal>
    </div>
</template>

<script>


export default {
     props: ['current-project', 'whole-book-list', 'designers'],
    data() {
        return {
            project: this.currentProject,
            modalTitle: '',
            wholeBooks: this.wholeBookList,
            wholeBookForm: {
                id: '',
                book_content: '',
                book_file: [],
                description: '',
                is_file: true,
                send_to_designer: false,
                designer_id: null,
                width: null,
                height: null
            },
            designerDescription: null,
            wholeBookStatusBoolean: [], // Will hold boolean statuses
            isLoading: false
        }
    },
    created() {
        // Initialize the boolean array based on status
        this.wholeBookStatusBoolean = this.wholeBooks.map(book => book.status === 'completed');
    },
    methods: {
        showWholeBookFormModal(data = null) {
            this.modalTitle = 'Add Book';
            if (data) {
                this.modalTitle = 'Edit Book';
                this.wholeBookForm = {
                    id: data.id,
                    is_file: !!data.is_file,
                    book_content: data.book_content,
                    description: data.description,
                    send_to_designer: data.designer_id ? true : false,
                    designer_id: data.designer_id,
                    width: data.width,
                    height: data.height,
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

        formattedContent (book) {
            if (book.is_file) {
                return book.filename;
            }

            return 'Details'
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

        showDescriptionModal(description) {
            this.designerDescription = description;
            this.$refs.descriptionModal.show();
        },

        updateBookStatus(book, index) {
            const updatedStatus = this.wholeBookStatusBoolean[index] ? 'completed' : 'pending';
            this.wholeBooks[index].status = updatedStatus;
            
            axios.post('/project/whole-book/' + book.id + '/update-status', {status: updatedStatus}).then(response => {
                this.$toasted.global.showSuccessMsg({
                    message : 'Status Updated'
                });
            });
        }
    }

}
</script>

<style scoped>
.mt-3 {
    margin-top: 1rem;
}
</style>
