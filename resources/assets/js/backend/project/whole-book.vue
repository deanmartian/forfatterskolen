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
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="wholeBook in wholeBooks" :key="wholeBook.id">
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
    </div>
</template>

<script>


export default {
     props: ['current-project', 'whole-book-list'],
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
                is_file: true
            },
            isLoading: false
        }
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
        }
    }

}
</script>
