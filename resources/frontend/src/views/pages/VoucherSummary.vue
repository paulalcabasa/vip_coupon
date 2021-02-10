<template>
    <div>

        <b-alert variant="info" show v-show="searchFlag && items.length == 0">
            No results found.
        </b-alert>

        <KTPortlet v-bind:title="'Voucher Summary'" >
            <template v-slot:toolbar>
                
            
                <b-btn class="btn btn-primary" @click="search" :disabled="form.formBusy">Search</b-btn>
            </template> 
            <template v-slot:body>
                <b-form>
                <b-row>
                    <b-col sm="6">
                        <b-form-group label="Creation Date From">
                            <b-form-input
                                type="date"
                                required
                                v-model="form.startDate"
                                placeholder="Enter start date"
                            ></b-form-input>
                        </b-form-group>

                        <b-form-group  label="Creation Date To" >
                            <b-form-input
                                type="date"
                                required
                                v-model="form.endDate"
                                placeholder="Enter to date"
                            ></b-form-input>
                        </b-form-group>

                        <b-form-group  label="Vehicle type" >
                            <b-form-select 
                                v-model="form.vehicleType" 
                                :options="vehicle_types"
                                value-field="id"
                                text-field="text"
                            ></b-form-select>
                        </b-form-group>

                         
                    </b-col>
                    <b-col sm="6">

                        <b-form-group  label="Dealer" >
                            <b-form-select 
                                v-model="form.dealer" 
                                :options="dealers"
                                value-field="id"
                                text-field="account_name"
                                v-show="user.user_type_id != 51"
                            ></b-form-select>
                            <b-form-input
                                :value="user.account_name"
                                disabled
                                v-show="user.user_type_id == 51"
                            ></b-form-input>
                        </b-form-group>

                        <b-form-group  label="Coupon Type" >
                            <b-form-select 
                                v-show="user.user_type_id == 47"
                                v-model="form.couponType" 
                                :options="coupon_types"
                                value-field="id"
                                text-field="name"
                            ></b-form-select>
                        </b-form-group>
                    </b-col>
                </b-row>
            </b-form>
            </template>
        </KTPortlet>

        <KTPortlet v-bind:title="'List'" v-show="items.length > 0">
            <template v-slot:toolbar>
                <download-excel
                      class = "btn btn-primary"
                    :fields = "fields"
                    :data   = "items"
                      type  = "csv"
                      name  = "voucher-summary.xls"
                >
                    Download
                </download-excel>
            </template> 

            <template v-slot:body>
                <div>
                    <b-row>
                    <b-col lg='6'></b-col>
                    <b-col lg="6" class="my-1">
                        <b-form-group
                            label="Filter"
                            label-cols-sm="3"
                            label-align-sm="right"
                            label-size="sm"
                            label-for="filterInput"
                            class="mb-0"
                        >
                            <b-input-group size="sm">
                                <b-form-input
                                v-model="filter"
                                type="search"
                                id="filterInput"
                                placeholder="Type to Search"
                                ></b-form-input>
                                <b-input-group-append>
                                <b-button :disabled="!filter" @click="filter = ''"><i class="fa fa-eraser"></i></b-button>
                                </b-input-group-append>
                            </b-input-group>
                        </b-form-group>
                    </b-col>
                    </b-row>
                    <b-table 
                        responsive 
                        show-empty
                        small
                        :items="items"
                        :fields="tableFields"
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :filterIncludedFields="filterOn"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"
                        :busy="form.formBusy"
                    >
                        <template v-slot:table-busy>
                            <div class="text-center text-danger my-2">
                                <b-spinner class="align-middle"></b-spinner>
                                <strong>Loading...</strong>
                            </div>
                        </template>

                    </b-table>

                </div>
            </template>

            <template v-slot:foot>
                <b-row>
                    <b-col sm="4" md="4" class="my-1">
                        <b-form-group
                        label="Per page"
                        label-cols-sm="6"
                        label-cols-md="4"
                        label-cols-lg="3"
                        label-align-sm="right"
                        label-size="sm"
                        label-for="perPageSelect"
                        class="mb-0"
                        >
                        <b-form-select
                            v-model="perPage"
                            id="perPageSelect"
                            size="sm"
                            :options="pageOptions"
                        ></b-form-select>
                        </b-form-group>
                    </b-col>
                    <b-col sm="4" md="4"></b-col>
                    <b-col sm="4" md="4" class="my-1">
                        <b-pagination
                        v-model="currentPage"
                        :total-rows="totalRows"
                        :per-page="perPage"
                        align="fill"
                        size="sm"
                        class="my-0"
                        ></b-pagination>
                    </b-col>  
                </b-row>
            </template>
        </KTPortlet>

    </div>
</template>
<style>

table.b-table[aria-busy='true'] {
  opacity: 0.6;
}
</style>
<script>
 
import KTPortlet from "@/views/partials/content/Portlet.vue";
import axios from 'axios'; 
import badge from '@/common/config/status.config.json';
import axiosRetry from 'axios-retry';
import jwtService from '@/common/jwt.service.js';


export default {
    name: "claimrequest",
    mounted() {
        this.loadDropdowns();
        this.user = JSON.parse(jwtService.getUser());
        this.form.dealer = this.user.dealer_id;
    },
    data(){
        return {
            items: [],
            user : [],
            dealers : [],
            coupon_types: [],
            searchFlag : false,
            form : {
                startDate : '',
                endDate : '',
                dealer : '',
                formBusy : false,
                couponType : 1,
                vehicleType : ''
            },
            vehicle_types : [
                {
                    id : '',
                    text : 'Please select a vehicle type'
                },
                {
                    id : 'LCV',
                    text : 'LCV'
                },
                {
                    id : 'CV',
                    text : 'CV'
                }
            ],
            fields : {
                'Ctrl No'              : 'voucher_no',
                'Voucher Code '        : 'voucher_code',
                'Voucher Amount'       : 'amount',
                'Claimed mount'        : 'claimed_amount',
                'Paid amount'          : 'paid_amount',
                'Dealer'               : 'account_name',
                'Customer Name'        : 'customer_name',
                'Service Invoice No.'  : 'service_invoice_number',
                'Service Date'         : 'service_date',
                'Expiration Date'      : 'expiration_date',
                'Voucher creation date': 'creation_date',
                'Coupon request date'  : 'coupon_request_date',
                'Requested by'         : 'coupon_requested_by',
                'Coupon Type'          : 'coupon_type',
                'Vehicle Type'         : 'vehicle_type',
                'Claim Request No'     : 'claim_request_id'
            }, 
            tableFields: [],  

            /* Table */
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [5, 10, 15, 20, 25, 30],
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: [],
        }
    },
    components: {
        KTPortlet
    },
    created() {
        for (let [key, value] of Object.entries(this.fields)) {
            this.tableFields.push({ 
                    key: value, 
                    label: key, 
                    sortable: true, 
                    class: 'text-center' 
            });
        }
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                return { text: f.label, value: f.key }
            })
        }
    },
    methods: {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },
        search(){
            this.$Progress.start();
            this.form.formBusy = true;
            
            var self = this;
            axios.get('api/report/voucher-summary', {
                params : {
                    voucherStartDate: self.form.startDate,
                    voucherEndDate  : self.form.endDate,
                    dealerId        : self.form.dealer,
                    couponType      : self.form.couponType,
                    vehicleType     : self.form.vehicleType
                }
            }).then(res => {
                this.items = res.data;
                this.$Progress.finish();
                this.form.formBusy = false;
                this.searchFlag = true;
            }).catch(err => {
                this.$Progress.fail();
                this.form.formBusy = false;
            })
        },
      
        setDefaultCouponType(){
            this.coupon_types.map((type) => {
                if(type.user_type_id == this.user.user_type_id){
                    this.form.couponType = type.id;
                }
            });
        },
        loadDropdowns(){
            axiosRetry(axios, { retries: 3 });
            var self = this;
            let couponTypeUrl = 'api/coupon-types/get';
            let dealerUrl = 'api/dealers';
            
          
            const couponTypeReq = axios.get(couponTypeUrl);
            const dealerReq = axios.get(dealerUrl);
        
            this.$Progress.start();
            axios.all([couponTypeReq, dealerReq]).then(axios.spread((...responses) => {
               const couponTypeRes = responses[0];
                const dealerRes = responses[1];
            
                
                couponTypeRes.data.map( (row) => {
                    self.coupon_types.push({
                        'id' : row.id,
                        'name' : row.name,
                        'user_type_id' : row.user_type_id
                    });
                });

                this.dealers = [
                    {
                    'id' : null,
                    'account_name' : 'SELECT A DEALER'
                    }
                ];

                dealerRes.data.map( (row) => {
                    this.dealers.push({
                    'id' : row.id,
                    'account_name' : row.account_name
                    });
                });

                self.$Progress.finish();
            })).then( () => {
                this.setDefaultCouponType();
            }).catch(errors => {
               // self.makeToast('error',"Failed to load resources, please refresh the page.",'System message');
                self.$Progress.fail();
            });
        },
    
        makeToast(variant = null,body,title) {
            this.$bvToast.toast(body, {
                title: `${title}`,
                variant: variant,
                solid: true
            })
        },
      
    },
   
};
</script>