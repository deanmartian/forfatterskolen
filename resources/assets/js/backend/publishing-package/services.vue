<template>
<div class="admin-main-content">
    <div class="row mx-0">
        <div class="col-md-12 p-4">
            <div class="card" style="margin-top:10px">
                <div class="card-header">
                    <a href="/project" class="btn btn-default">
                        <i class="fa fa-angle-double-left"></i> Back
                    </a>
                    <button class="btn btn-primary btn-sm rounded pull-right" @click="showTemplate()" style="margin-bottom:10px">
                        Add Services
                    </button>
                </div> <!-- end card-header -->

                <div class="card-body table-users">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th width="600">Description</th>
                                <th>Price</th>
                                <th>Per word / hour</th>
                                <th>Per unit</th>
                                <th>Min char/word</th>
                                <th>Slug</th>
                                <th>Service Type</th>
                                <th>Is Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(template, index) in list">
                                <tr class="packages" v-if="paginate(index, perPage, currentPage)"
                                :class="{ show: template.show_switch }" :key="index">
                                        <td>{{ template.product_service }}</td>
                                        <td v-html="template.short_description"></td>
                                        <td>{{ template.price }}</td>
                                        <td>{{ template.per_word_hour }}</td>
                                        <td>{{ template.per_unit }}</td>
                                        <td>{{ template.base_char_word }}</td>
                                        <td>{{ template.slug }}</td>
                                        <td>{{ template.service_type }}</td>
                                        <td>
                                            <toggle-button :color="'#337ab7'"
                                                :labels="{checked: 'Yes', unchecked: 'No'}" v-model="template.is_active"
                                                :width="60" :height="25" :font-size="14" :data-id="template.id"
                                                @change="isActiveHandler" sync=""/>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm inline-block" @click="showTemplate(template)" style="text-transform: capitalize">
                                                <i class="fa fa-edit"></i>
                                            </button> &nbsp;
                                        </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <b-pagination
                        v-model="currentPage"
                        :limit="5"
                        :total-rows="rows"
                        :per-page="perPage"
                        :hide-ellipsis="false"
                    >
                    </b-pagination>
                </div>
            </div>
        </div>
    </div>

    <b-modal ref="modal" :title="'Services'" @hidden="closeModal()" size="lg" 
    centered :no-enforce-focus="true" no-close-on-backdrop>
        <div class="form-group">
            <label>Services</label>
            <input type="text" class="form-control" name="product_service" v-model="form.product_service">
        </div>
        <div class="form-group">
            <label> Description </label>
            <editor id="publishing-package-description-editor" name="description" v-model="form.description"
                    api-key="58zy6vmujertl448ngd6tq81e40p4een1t8y9lnkq9licymu"
                    :init="tinymceInit400">
            </editor>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" class="form-control" step=".01" name="price" v-model="form.price">
        </div>
        <div class="form-group">
            <label>Per ord / time</label>
            <input type="number" class="form-control" step=".01" name="per_word_hour" v-model="form.per_word_hour">
        </div>
        <div class="form-group">
            <label>Minimum tegn / ord</label>
            <input type="number" class="form-control" step=".01" name="base_char_word" v-model="form.base_char_word">
        </div>
        <div class="form-group">
            <label>Enhet</label>
            <select class="form-control" name="per_unit" v-model="form.per_unit">
                <option selected>Open this select menu</option>
                <option value="hour">Time</option>
                <option value="words">Ord</option>
                <option value="char">Karakter</option>
            </select>
        </div>
        <div class="form-group">
            <label>Service Type</label>
            <input type="text" class="form-control" name="service_type" v-model="form.service_type">
        </div>
        <div class="form-group">
            <label>Is Active</label> <br>
            <toggle-button :color="'#337ab7'"
                            :labels="{checked: 'Yes', unchecked: 'No'}" v-model="form.is_active"
                            :width="60" :height="25" :font-size="14" checked/>
        </div>
        <div slot="modal-footer">
            <button class="btn btn-primary btn-sm" @click="saveService()" :disabled="isLoading">
                <i class="fa fa-pulse fa-spinner" v-if="isLoading"></i> Submit
            </button>
        </div>
    </b-modal>
</div>
</template>

<script>
import VuePaginate from 'vue-paginate'
import Editor from '@tinymce/tinymce-vue'
export default {
    data() {
        return {
            list: [],
            form: {
                id: '',
                product_service: '',
                description: '',
                price: '',
                per_word_hour: '',
                per_unit: '',
                base_char_word: 0,
                service_type: '',
                is_active: true
            },
            perPage: 10,
            currentPage: 1,
            requestURL: '/admin',
            tinymceInit400: {
                
            },
            isLoading: false
        }
    },

    components: {
        VuePaginate,
        'editor': Editor
    },

    computed: {
        rows() {
            return this.list.length
        }
    },

    methods: {
        showTemplate(template = null) {
            if (template) {
                this.form = {
                    id: template.id,
                    product_service: template.product_service,
                    price: template.price,
                    per_word_hour: template.per_word_hour,
                    description: template.description,
                    per_unit: template.per_unit,
                    base_char_word: template.base_char_word,
                    service_type: template.service_type,
                    is_active: !!template.is_active
                };
            }
            this.$refs.modal.show();
        },

        closeModal() {
            this.form = {
                id: '',
                product_service: '',
                price: '',
                per_word_hour: '',
                description: '',
                per_unit: '',
                base_char_word: 0,
                service_type: ''
            };
            this.$refs.modal.hide();
        },

        saveService() {
            this.removeValidationError();
            this.isLoading = true;
            axios.post('/save-service', this.form).then( response => {
                
                this.getList();
                this.closeModal();
                this.$toasted.global.showSuccessMsg({
                    message: 'Service saved.'
                });
                this.isLoading = false;

            }).catch( error => {

                this.processError(error);
                this.isLoading = false;

            })
        },

        paginate(index, perPage, currentPage)
        {
            if(index >= (perPage * currentPage) - perPage && index <= (perPage * currentPage) - 1)
            {
                return true
            }

            return false
        },

        isActiveHandler(event) {
        let elem = event.srcEvent;
        let updatedValue = event.value;
        let id = elem.target.closest('.vue-js-switch').getAttribute('data-id');
        let data = {
            field: 'is_active',
            value: updatedValue ? 1 : 0
        };

        axios.post('/service/'+id+'/update-field', data).then((response) => {
            this.$toasted.global.showSuccessMsg({
                message : 'Success'
            });
        });
    },

        getList() {
            axios.get('/all-services').then( response => {
                this.list = response.data;
            });
        }
    },

    mounted() {
        this.getList();
    }
}
</script>
