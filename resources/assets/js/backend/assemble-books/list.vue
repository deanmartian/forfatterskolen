<template>
    <div class="admin-main-content">
        <div class="row mx-0">
            <div class="col-md-12">

                <div class="card" style="margin-top:10px">
                    <div class="card-header">
                        <a href="/project" class="btn btn-default">
                            <i class="fa fa-angle-double-left"></i> Back
                        </a>
                    </div> <!-- end card-header -->

                    <div class="card-body table-users">
                        <section>
                            <h5>
                                Print Cover
                            </h5>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="printCover in printCovers" :key="printCover.id">
                                    <td>{{ printCover.name }}</td>
                                    <td>{{ printCover.type }}</td>
                                    <td>{{ printCover.price }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-xs" @click="showCoverOrColorModal(printCover, 'cover')">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </section> <!-- end print cover -->

                        <section style="margin-top: 50px">
                            <h5>
                                Print Color
                            </h5>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="printColor in printColors" :key="printColor.id">
                                    <td>{{ printColor.name }}</td>
                                    <td>{{ printColor.type }}</td>
                                    <td>{{ printColor.price }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-xs" @click="showCoverOrColorModal(printColor, 'color')">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </section> <!-- end print color -->

                        <section style="margin-top: 50px">
                            <div class="h5">
                                Print Count
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="printCount in printCounts" :key="printCount.id">
                                        <td>{{ printCount.name }}</td>
                                        <td>{{ printCount.value }}</td>
                                        <td>{{ printCount.price }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-xs" @click="showPrintOrHelpModal(printCount, 'count')">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </section> <!-- end print count -->

                        <section style="margin-top: 50px">
                            <div class="h5">
                                Marketing Help
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="marketingHelp in marketingHelps" :key="marketingHelp.id">
                                        <td>{{ marketingHelp.name }}</td>
                                        <td>{{ marketingHelp.value }}</td>
                                        <td>{{ marketingHelp.price }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-xs" @click="showPrintOrHelpModal(marketingHelp, 'help')">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </section> <!-- end marketing help -->
                    </div>
                </div>
            </div>
        </div>

        <b-modal
                ref="coverOrColorModal"
                :title="coverOrColorModalTitle"
                @hidden="closeCoverOrColorModal()"
                size="md"
                centered
                :no-enforce-focus="true"
                no-close-on-backdrop>

            <div class="form-group">
                <label>
                    Name
                </label>
                <input type="text" class="form-control" name="name" v-model="coverOrColorForm.name">
            </div>

            <div class="form-group">
                <label>
                    Type
                </label>
                <input type="text" class="form-control" name="type" v-model="coverOrColorForm.type" disabled>
            </div>

            <div class="form-group">
                <label>
                    Price
                </label>
                <input type="number" class="form-control" name="price" v-model="coverOrColorForm.price">
            </div>

            <div slot="modal-footer">
                <button class="btn btn-primary btn-sm" @click="saveCoverOrColor()" :disabled="isLoading">
                    <i class="fas fa-spinner fa-pulse" v-if="isLoading"></i>
                    Submit
                </button>
            </div>
        </b-modal>

        <b-modal
                ref="printOrHelpModal"
                :title="printOrHelpModalTitle"
                @hidden="closePrintOrHelpModal()"
                size="md"
                centered
                :no-enforce-focus="true"
                no-close-on-backdrop>

            <div class="form-group">
                <label>
                    Name
                </label>
                <input type="text" class="form-control" name="name" v-model="printOrHelpForm.name">
            </div>

            <div class="form-group">
                <label>
                    Value
                </label>
                <input type="number" class="form-control" name="value" v-model="printOrHelpForm.value">
            </div>

            <div class="form-group">
                <label>
                    Price
                </label>
                <input type="number" class="form-control" name="price" v-model="printOrHelpForm.price">
            </div>

            <div slot="modal-footer">
                <button class="btn btn-primary btn-sm" @click="savePrintOrHelp()" :disabled="isLoading">
                    <i class="fas fa-spinner fa-pulse" v-if="isLoading"></i>
                    Submit
                </button>
            </div>
        </b-modal>
    </div>
</template>

<script>
export default {
    data() {
        return {
            printCovers: [],
            printColors: [],
            printCounts: [],
            marketingHelps: [],
            coverOrColorModalTitle: 'Print Cover',
            coverOrColorForm: {
                id: '',
                name: '',
                type: '',
                price: '',
                formType: ''
            },
            printOrHelpModalTitle: 'Print Count',
            printOrHelpForm: {
                id: '',
                name: '',
                value: '',
                price: '',
                formType: ''
            },
            isLoading: false,
        }
    },

    methods: {
        getOptions() {
            axios.get('/assemble-book-packages/all-options').then(response => {
                let data = response.data;
                this.printCovers = data.print_covers;
                this.printColors = data.print_colors;
                this.printCounts = data.print_counts;
                this.marketingHelps = data.marketing_helps;
            });
        },

        showCoverOrColorModal(data, type) {
            this.coverOrColorModalTitle = 'Print Cover';
            if (type === 'color') {
                this.coverOrColorModalTitle = 'Print Color';
            }

            this.coverOrColorForm = {
                id: data.id,
                name: data.name,
                type: data.type,
                price: data.price,
                formType: type
            };
            this.$refs.coverOrColorModal.show();
        },

        closeCoverOrColorModal() {
            this.coverOrColorForm = {
                id: '',
                name: '',
                type: '',
                price: '',
                formType: ''
            };
        },

        saveCoverOrColor() {
            this.isLoading = true;
            this.removeValidationError();
            axios.post('/assemble-book-packages/save-cover-or-color', this.coverOrColorForm).then( response => {

                let object = this.coverOrColorForm.formType === 'cover' ? this.printCovers : this.printColors;
                this.updateRecordFromObject(object, this.coverOrColorForm.id, response.data);
                this.isLoading = false;
                this.$toasted.global.showSuccessMsg({
                    message: 'Record saved.'
                });

                this.$refs.coverOrColorModal.hide();

            }).catch( error => {
                this.isLoading = false;
                this.processError(error);
            });
        },

        showPrintOrHelpModal(data, type) {
            this.printOrHelpModalTitle = 'Print Count';
            if (type === 'help') {
                this.printOrHelpModalTitle = 'Marketing Help';
            }

            this.printOrHelpForm = {
                id: data.id,
                name: data.name,
                value: data.value,
                price: data.price,
                formType: type
            };
            this.$refs.printOrHelpModal.show();
        },

        closePrintOrHelpModal() {
            this.printOrHelpForm = {
                id: '',
                name: '',
                type: '',
                price: '',
                formType: ''
            };
        },

        savePrintOrHelp() {
            this.isLoading = true;
            this.removeValidationError();
            axios.post('/assemble-book-packages/save-count-or-help', this.printOrHelpForm).then( response => {
                let object = this.printOrHelpForm.formType === 'count' ? this.printCounts : this.marketingHelps;
                this.updateRecordFromObject(object, this.printOrHelpForm.id, response.data);
                this.isLoading = false;
                this.$toasted.global.showSuccessMsg({
                    message: 'Record saved.'
                });

                this.$refs.printOrHelpModal.hide();
            }).catch( error => {
                this.isLoading = false;
                this.processError(error);
            });
        },
    },

    mounted() {
        this.getOptions();
    }
}
</script>

