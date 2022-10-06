<template>
    <div>
        <div class="page-toolbar">
            <h3><i class="fa fa-file-text-o"></i> Project: {{ project.name }}</h3>
            <div class="clearfix"></div>
        </div>

        <div class="col-md-12">
            <button class="btn btn-success margin-top" @click="showFormModal()">
                Add
            </button>
            <div class="table-users">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>Author</th>
                        <th>Name of book</th>
                        <th>ISBN number for hardcover book</th>
                        <th>ISBN number for ebook</th>
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
                            {{ book.isbn_hardcover_book }}
                        </td>
                        <td>
                            {{ book.isbn_ebook }}
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
                <label>Name of book</label>
                <input type="text" class="form-control" name="book_name" v-model="form.book_name">
            </div>

            <div class="form-group">
                <label>ISBN number for hardcover book</label>
                <input type="text" class="form-control" name="isbn_hardcover_book" v-model="form.isbn_hardcover_book">
            </div>

            <div class="form-group">
                <label>ISBN number for ebook</label>
                <input type="text" class="form-control" name="isbn_ebook" v-model="form.isbn_ebook">
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

        props: ['current-project'],

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
                book: {},
                isLoading: false,
            }
        },

        methods: {
            showFormModal(data = null) {
                this.modalTitle = 'Add Book';
                if (data) {
                    this.modalTitle = 'Edit Book';
                    this.form = {
                        id: data.id,
                        user_id: this.currentProject.user_id,
                        book_name: data.book_name,
                        isbn_hardcover_book: data.isbn_hardcover_book,
                        isbn_ebook: data.isbn_ebook,
                    }
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
            },

            saveForm() {
                this.isLoading = true;
                axios.post('/project/' + this.project.id + '/book/save', this.form).then(response => {
                    this.isLoading = false;
                    let message = '';
                    if (this.form.id) {
                        this.updateRecordFromObject(this.books, this.form.id, response.data);
                        message = 'Book updated';
                    } else {
                        this.books.push(response.data);
                        message = 'Book created';
                    }

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
            }
        },

        mounted() {
            console.log("project details here");
            console.log(this.currentProject);
        }

    }
</script>