<template>
    <div>
         <!-- <button class="btn btn-success margin-top" @click="showFormModal()" v-if="books.length === 0">
            Add
        </button> -->
        <div class="table-users">
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>Author</th>
                    <th>Name of book</th>
                    <!-- <th></th> -->
                </tr>
                </thead>
                <tbody>
                <tr v-for="book in books" :key="book.id">
                    <td>
                        <a :href="'/learner/' + project.user_id" v-if="project.user">
                            {{ project.user.full_name }}
                        </a>
                    </td>
                    <td>
                        {{ book.book_name }}
                    </td>
                    <!-- <td>
                        <button class="btn btn-xs btn-primary" @click="showFormModal(book)">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" @click="showDeleteModal(book)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td> -->
                </tr>
                </tbody>
            </table>
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
                <!-- <v-select :options="learners" label="full_name" v-model="selected_learner" @input="setSelectedLearner($event)"
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
    </div>
</template>

<script>

export default {
     props: ['current-project', 'learners', 'project-user'],

    data() {
        return {
            project: this.currentProject,
            books: this.currentProject.books,
            modalTitle: '',
            form: {
                id: '',
                user_id: this.currentProject.user_id,
                book_name: '',
                isbn_hardcover_book: '',
                isbn_ebook: '',
            },
            selected_learner: '',
            book: {},
            searchQuery: '',
            searchLearnerList: [],
            isLoading: false
        }
    },

    methods: {
        showFormModal(data = null) {
            this.modalTitle = 'Add Book';
            if (data) {
                this.modalTitle = 'Edit Book';
                this.searchQuery = this.projectUser ? this.projectUser.full_name : '';
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
                    location.reload();
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

        setSelectedLearner(value) {
            this.form.user_id = value ? value.id : "";
            //this.projectForm.user_id = value ? value.id : "";
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
            this.form.user_id = learner.id;
            //this.selected_learner = learner.first_name + " " + learner.last_name;
            this.searchQuery = learner.first_name + " " + learner.last_name;
            this.searchLearnerList = [];
        },
    }
}
</script>
