<template>

    <div class="card global-card">
        <div class="card-body py-0">
            <table class="table table-global">
                <thead>
                <tr>
                    <th>{{ trans('site.order-history.product') }}</th>
                    <th>{{ trans('site.order-history.package') }}</th>
                    <th>{{ trans('site.date') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="order in orders" :key="order.id">
                    <td>{{ order.item }}</td>
                    <td>{{ order.package_variation }}</td>
                    <td>{{ order.created_at_formatted }}</td>
                    <td>
                        <template v-if="order.price">
                            <button class="btn blue-link" @click="viewOrder(order)">
                                <i class="fas fa-eye"></i>
                            </button>

                            <button class="btn blue-link btn-sm" @click="downloadRecord(order)"
                                    :disabled="isLoading && currentOrder.id === order.id">
                                <i class="fas fa-spinner fa-pulse" v-if="isLoading && currentOrder.id === order.id"></i>
                                <i class="fas fa-download"></i>
                            </button>

                            <button class="btn blue-outline-btn d-inline-block"
                                    @click="companyEdit(order)">
                                {{ trans('site.order-history.edit-company') }}
                            </button>
                        </template>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <b-modal
                ref="orderModal"
                :title="''"
                size="lg"
                id="orderModal"
                centered
                hide-footer
        >

            <div class="row">
                <div class="col-sm-6">
                    <div class="receipt-logo-container">
                        <img src="/images-new/logo-tagline.png" alt="Logo" class="w-100">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="receipt-papermoon-address">
                        <span style="font-weight: 600;">{{ trans('site.order-history.fs-name') }}</span> <br>
                        <span style="font-weight: 600;">{{ trans('site.order-history.fs-address1') }}</span> <br>
                        <span>{{ trans('site.order-history.fs-address2') }}, {{ trans('site.order-history.fs-country') }}</span> <br>
                        <span>{{ trans('site.order-history.fs-site') }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <!-- <div class="receipt-invoice-pink">
                        <span style="font-size: 19px; font-weight: 600;">{{ trans('site.order-history.invoice-copy') }}</span>
                        <div class="receipt-pink-bg">
                            <div>
                                <span>{{ trans('site.order-history.amount-to-pay') }}</span>
                                <span style="float: right;">{{ currentOrder.total_formatted }}</span>
                            </div>
                        </div>
                    </div> -->
                    <div v-if="currentOrder.company" class="customer-name-address">
                        <span>{{ currentOrder.company.company_name }}</span> <br>
                        <span>{{ currentOrder.company.street_address }}</span> <br>
                        <span>{{ currentOrder.company.post_number }} {{ currentOrder.company.place }}</span><br>
                        <span>{{ currentOrder.company.customer_number }}</span>
                    </div>
                    <div class="customer-name-address" v-else>
                        <span>{{ user.full_name }}</span> <br>
                        <span>{{ user.address.street }}</span> <br>
                        <span>{{ user.address.zip }} {{ user.address.city }}</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 mt-4" style="padding: 0px;" v-if="currentOrder">
                <hr style="height: 1px; background-color: #4c8485; border: none;">
                <div class="row ml-auto receipt-table">
                    <div class="col-sm-6">
                        <div>
                            <span>{{ trans('site.order-history.invoice-number') }}</span>
                            <span class="float-right">{{ currentOrder.id | leadingZeros(currentOrder.id) }}</span>
                        </div>
                        <div>
                            <span>{{ trans('site.order-history.customer-number') }}</span>
                            <span class="float-right">{{ user.id | leadingZeros(user.id) }}</span>
                        </div>
                        <div>
                            <span>{{ trans('site.order-history.customer-reference') }}</span>
                            <span class="float-right">Sven Inge Henningsen</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div>
                            <span>{{ trans('site.order-history.invoice-date') }}</span>
                            <span class="float-right">{{ currentOrder.created_at_formatted }}</span>
                        </div>
                        <div>
                            <span>{{ trans('site.order-history.payment-terms') }}</span>
                            <span class="float-right">{{ currentOrder.payment_plan ? currentOrder.payment_plan.plan : '' }}</span>
                        </div>
                        <div>
                            <span>{{ trans('site.order-history.interest') }}</span>
                            <span class="float-right">12%</span>
                        </div>
                    </div>
                </div>
                <hr style="height: 1px; background-color: #4c8485; border: none;">
            </div>

            <div class="row mt-4" style="padding: 0px;" v-if="currentOrder">
                <div class="col-sm-4">
                    <span>{{ trans('site.order-history.description') }}</span><br>
                    <span style="font-weight: 600;">{{ currentOrder.packageVariation }}</span>
                </div>
                <div class="col-sm-2">
                    <span>{{ trans('site.order-history.vat') }}</span><br>
                    <span>{{ [2, 7, 9, 10].includes(currentOrder.type) ? '25%' : '0%' }}</span>
                </div>
                <div class="col-sm-2">
                    <span>{{ trans('site.order-history.quantity') }}</span><br>
                    <span>1 stk</span>
                </div>
                <div class="col-sm-2">
                    <span>{{ trans('site.order-history.price') }}</span><br>
                    <span>{{ currentOrder.price_formatted }}</span>
                </div>
                <div class="col-sm-2">
                    <span>{{ trans('site.order-history.sum') }}</span><br>
                    <span>{{ currentOrder.total_formatted }}</span>
                </div>
            </div>

            <br><br>
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <div>
                        <span style="font-weight: 600;">{{ trans('site.order-history.total-vat') }}</span>
                        <span style="font-weight: 600;" class="float-right">{{ currentOrder.total_formatted }}</span>
                    </div>
                    <div>
                        <span style="font-weight: 600;">{{ trans('site.order-history.total-to-pay') }}</span>
                        <span style="font-weight: 600;" class="float-right">{{ currentOrder.total_formatted }}</span>
                    </div>
                </div>
            </div>

            <br><br><br>
            <div class="row mt-4 receipt-footer">
                <div class="col-sm-6">
                    <hr style="height: 1px; background-color: #4c8485; border: none;">
                    <div class="row">
                        <div class="col-sm-7">
                            <div>{{ trans('site.order-history.fs-name') }}</div>
                            <div>{{ trans('site.order-history.fs-address1') }}</div>
                            <div>{{ trans('site.order-history.fs-address2') }}</div>
                            <div>{{ trans('site.order-history.fs-country') }} <span>{{ trans('site.order-history.organization') }}</span> </div>
                        </div>
                        <div class="col-sm-5">
                            <!-- <div>OCR-nummer 10355</div>
                            <div>Bankgiro 5633-2190</div> -->
                        </div>
                    </div>
                    <hr style="height: 1px; background-color: #4c8485; border: none;">
                </div>
            </div>

        </b-modal>

        <b-modal
                ref="editCompany"
                :title="trans('site.order-history.edit-company')"
                size="lg"
                @hidden="closeCompany()"
                centered
                hide-footer
        >

            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{trans('site.order-history.company-number')}}: </label>
                        <input v-model="company.customer_number" name="customer_number" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{trans('site.order-history.company-name')}}: </label>
                        <input v-model="company.company_name" name="company_name" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{trans('site.order-history.company-address')}}: </label>
                        <input v-model="company.street_address" name="street_address" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{trans('site.order-history.company-post-number')}}: </label>
                        <input v-model="company.post_number" name="post_number" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{trans('site.order-history.company-place')}}: </label>
                        <input v-model="company.place" name="place" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <button class="es-button btn-md btn-primary float-right" @click="companySave()">{{ trans('site.save') }}</button>
        </b-modal>
    </div>

</template>

<script>
    export default {

        props: ['orderHistory', 'user'],

        data() {
            return {
                orders: this.orderHistory,
                currentOrder: {
                    pkg: {},
                    payment_mode: {},
                    payment_plan: {},
                    company: {}
                },

                selectedOrderID: null,
                company: {
                    id: '',
                    order_id: '',
                    customer_number: '',
                    company_name: '',
                    street_address: '',
                    post_number: '',
                    place: ''
                },

                isLoading:false
            }
        },

        filters: {
            leadingZeros: function (num) {
                return String(num).padStart(6, '0')
            },
            replaceCommawithSpace: function (data) {
                if(data){
                    data = data + ' '
                    let fdata = ''
                    data.match(/\d+/g).forEach(element => {
                        fdata = fdata + element + ' '
                    });
                    return fdata
                }
            }
        },

        methods: {
            viewOrder(order) {
                this.currentOrder = order;
                this.currentOrder.pkg = order.package;
                this.$refs.orderModal.show();
            },

            downloadRecord(order) {
                let self = this;
                this.isLoading = true;
                this.currentOrder = order;
                this.currentOrder.pkg = order.package;
                let url = '/account/order/'+ order.id +'/download/';
                let filename = order.id + ".pdf";

                axios({
                    url: url,
                    method: 'GET',
                    responseType: 'blob', // important
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();
                    self.isLoading = false;
                });
            },

            companyEdit(order){
                let scope = this;

                if(order){
                    scope.selectedOrderID = order.id
                    scope.company.order_id = order.id;
                }

                if(order.company){
                    scope.company.id = order.company.id;
                    scope.company.order_id = order.company.order_id;
                    scope.company.customer_number = order.company.customer_number;
                    scope.company.company_name = order.company.company_name;
                    scope.company.street_address = order.company.street_address;
                    scope.company.post_number = order.company.post_number;
                    scope.company.place = order.company.place;
                }

                scope.$refs.editCompany.show();
            },

            closeCompany() {
                this.company = {
                    id: '',
                    order_id: '',
                    customer_number: '',
                    company_name: '',
                    street_address: '',
                    post_number: '',
                    place: ''
                }
            },

            companySave(){
                let scope = this
                this.removeValidationError();
                axios.post('/account/order/' + scope.selectedOrderID + '/save-company', scope.company)
                    .then(response => {
                        window.swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Record saved successfully',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            scope.updateRecordFromObject(scope.orders, scope.selectedOrderID, response.data);
                            this.$refs.editCompany.hide();
                        })
                    }).catch(error => {
                        this.processError(error);
                })
            }
        }

    }
</script>

<style>
    .receipt-logo-container img {
        height: 100px;/*64*/
        width: 260px;
        -o-object-fit: contain;
        object-fit: contain;
    }

    #orderModal .modal-body {
        padding: 22px 30px;
    }

    .receipt-papermoon-address {
        padding-top: 30px; /*70*/
    }

    .customer-name-address{
        padding-top: 26px
    }

    .receipt-pink-bg{
        padding: 20px;
        background-color: #4c8485;
    }

    .receipt-pink-bg span{
        color: black;
        font-weight: 500;
    }
</style>