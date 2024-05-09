<template>
    <div class="margin-top">
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
    </div>
</template>

<script>
    export default {

        props: ['current-project'],

        data() {
            return {
                project: this.currentProject,
                noteForm: {
                    id: '',
                    notes: ''
                },
                isLoading: false,
            }
        },

        computed: {
            formattedNotes() {
                return this.project.short_notes ? this.nl2br(this.project.short_notes) : null;
            }
        },

        methods: {
            nl2br(str) {
                return str.replace(/\n/g, '<br>');
            },
            
            showNotes() {
                this.noteForm = {
                    id: this.project.id,
                    notes: this.project.notes
                };
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
        },

        mounted() {
            console.log("project notes here");
            console.log(this.project);
        }

    }
</script>