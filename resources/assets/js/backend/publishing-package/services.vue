<template>
<div class="admin-main-content">
    <div class="row mx-0">
        <div class="col-md-12 p-4">
            <div class="card" style="margin-top:10px">
                <div class="card-header">
                    <button class="btn btn-primary btn-sm rounded pull-right" @click="showTemplate()" style="margin-bottom:10px">
                        Add Services
                    </button>
                </div> <!-- end card-header -->

                <div class="card-body table-users">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Description</th>
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
                    </table>
                </div>
            </div>
        </div>
    </div>

    <b-modal ref="modal" :title="trans('site.publishing.services')" @hidden="closeModal()" size="lg" 
    centered :no-enforce-focus="true" no-close-on-backdrop>
        <div class="form-group">
            <label>{{ trans('site.publishing.services') }}</label>
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
            <label>{{ trans('site.publishing.services-price') }}</label>
            <input type="number" class="form-control" step=".01" name="price" v-model="form.price">
        </div>
        <div class="form-group">
            <label>{{ trans('site.publishing.services-per-word-hour') }}</label>
            <input type="number" class="form-control" step=".01" name="per_word_hour" v-model="form.per_word_hour">
        </div>
        <div class="form-group">
            <label>{{ trans('site.publishing.base-char-word') }}</label>
            <input type="number" class="form-control" step=".01" name="base_char_word" v-model="form.base_char_word">
        </div>
        <div class="form-group">
            <label>{{ trans('site.publishing.services-per-unit') }}</label>
            <select class="form-control" name="per_unit" v-model="form.per_unit">
                <option selected>Open this select menu</option>
                <option value="hour">{{ trans('site.publishing.services-hour') }}</option>
                <option value="words">{{ trans('site.publishing.services-words') }}</option>
                <option value="char">{{ trans('site.publishing.services-characters') }}</option>
            </select>
        </div>
        <div class="form-group">
            <label>{{ trans('site.publishing.service-type') }}</label>
            <input type="text" class="form-control" name="service_type" v-model="form.service_type">
        </div>
        <div class="form-group">
            <label>Is Active</label> <br>
            <toggle-button :color="'#337ab7'"
                            :labels="{checked: 'Yes', unchecked: 'No'}" v-model="form.is_active"
                            :width="60" :height="25" :font-size="14" checked/>
        </div>
        <div slot="modal-footer">
            <button class="btn btn-primary btn-sm" @click="saveTemplate()">
                {{ trans('site.submit') }}
            </button>
        </div>
    </b-modal>
</div>
</template>

<script>

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
                    
                }
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
    },

    mounted() {
        
    }
}
</script>
