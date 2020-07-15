<template>
    <div>

        <b-alert variant="info" show v-show="searchFlag && items.length == 0">
            No results found.
        </b-alert>

        <b-alert variant="success" show v-show="submitFlag">
            <span class="mr-2">Claim Request No. <strong>{{ claimHeaderId }}</strong> has been created!</span> 
            <b-link href="#" style="color:#fff;" @click.prevent="viewRequest"><u>Click here to view</u></b-link>
        </b-alert>

        <b-card v-show="isError" bg-variant="danger" text-variant="white"  class="mb-3">
            <b-card-text>
            <h6>{{ title}}</h6>
            <p>{{ message }} </p>
            <p class="mb-0" v-if="invalidVoucherCodes != ''"><strong>Not existing</strong> : {{ invalidVoucherCodes }}</p>
            <p class="mb-0" v-if="claimedVoucherCodes != ''"><strong>Already claimed</strong> : {{ claimedVoucherCodes }}</p>
            <p class="mb-0" v-if="invalidCSNumbers != ''"><strong>Invalid CS Numbers</strong> : {{ invalidCSNumbers }}</p>
            <p class="mb-0" v-show="errors.length > 0">
                <strong>Errors : </strong>
                <ol>
                <li v-for="(row,index) in errors" :key="index">{{ row.voucher_code + " : " + row.message }}</li>
                </ol>
            </p>
            </b-card-text>
        </b-card>

        <KTPortlet v-bind:title="'Claim Request'" >
            <template v-slot:toolbar>
                <b-button type="submit" @click.prevent="getClaims()" variant="primary">Search</b-button>
          
            </template> 
            <template v-slot:body>
                <b-form>
                <b-row>
                    <b-col sm="6">
                        <b-form-group label="Claim Date From">
                            <b-form-input
                                type="date"
                                required
                                v-model="form.startDate"
                                placeholder="Enter start date"
                            ></b-form-input>
                        </b-form-group>

                        <b-form-group  label="Claim Date To" >
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
                                disabled
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
                <b-button type="submit" @click.prevent="submit" variant="success" :disabled="formBusy">Submit</b-button>
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
                        sticky-header
                        show-empty
                        small
                        :items="items"
                        :fields="fields"
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

                     <!--    <template v-slot:cell(amount)="row">
                            <b-form-input v-model="row.item.amount"/>
                        </template> -->

                        <template v-slot:cell(status)="row">
                            <b-badge class="mr-1" :variant="statusColors[row.value.trim()]">{{ row.value }}</b-badge>
                        </template>

                        <template v-slot:cell(actions)="row">
                            <b-button size="sm" @click="info(row.item)" class="mr-1">
                                <i class="fa fa-search"></i>
                            </b-button>
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
            statusColors : badge.badgeColors,
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
            fields: [
                { 
                    key: 'coupon_type', 
                    label: 'Coupon Type', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'account_name', 
                    label: 'Dealer', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'voucher_no', 
                    label: 'Voucher No', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'voucher_code', 
                    label: 'Voucher Code', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },
                { 
                    key: 'amount', 
                    label: 'Amount', 
                    sortable: true, 
                    class: 'text-center' 
                },
                
                { 
                    key: 'claim_date', 
                    label: 'Date Created', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'customer_name', 
                    label: 'Customer', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'cs_number', 
                    label: 'CS Number', 
                    sortable: true, 
                    class: 'text-center' 
                },
            ],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [5, 10, 15, 20, 25, 30],
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: [],
           
            isError : false,
            invalidVoucherCodes : [],
            claimedVoucherCodes : [],
            invalidCSNumbers : [],
            message : '',
            title : '',
            errors :  [],
            claimHeaderId : '',
            submitFlag : false,
            formBusy : false,
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
            ]
           
        }
    },
    components: {
        KTPortlet
    },
    created() {

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
        getClaims(){
            var self = this;
            
            self.$Progress.start();
            self.form.formBusy = true;
            return new Promise(resolve => {

                axiosRetry(axios, { retries: 3 });
                
                let user = JSON.parse(jwtService.getUser());

             
                axios.get('api/claims/get', {
                    params : {
                        startDate : self.form.startDate,
                        endDate : self.form.endDate,
                        dealerId : self.form.dealer,
                        couponType : self.form.couponType,
                        vehicleType : self.form.vehicleType
                    }
                }).then( (res) => {
                    self.items = res.data;
                    self.totalRows = self.items.length;
                    self.$Progress.finish();
                    self.form.formBusy = false;
                    self.searchFlag = true;
                    resolve(res);
                }).catch( err => {
                    self.$bvToast.toast('Failed loading resources, please submit again.', {
                        title: 'System message',
                        variant: 'danger',
                        solid: true
                    });
                    self.$Progress.fail();
                    resolve(err);
                })
                .finally( () => {
                    
                });
            });
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
                    'id' : '',
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
        submit(){

            var self = this;

            const swalWithBootstrapButtons = this.$swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
      
            swalWithBootstrapButtons.fire({
                title: 'Are you sure to submit?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                 //reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    self.formBusy = true;
                    axios.post( '/api/claim-request/submit', {
                        user        : this.user,
                        vouchers    : this.items,
                        dealer_id   : this.form.dealer,
                        coupon_type : this.form.couponType,
                        vehicle_type: this.form.vehicleType
                    }).then(res => {
                        
                        if(res.data.error){
                            self.invalidVoucherCodes = res.data.invalidVoucherCodes;
                            self.claimedVoucherCodes = res.data.claimedVoucherCodes;
                            self.invalidCSNumbers = res.data.invalidCSNumbers;
                            self.errors = res.data.errors;
                            self.message = res.data.message;
                            self.isError = true;
                            self.title = "Transaction failed!";
                            self.$Progress.fail();
                        }
                        else {
                            self.isError = false;
                            self.makeToast('success',res.data.message,'System message');
                
                            self.submitFlag = true;

                            self.claimHeaderId =  res.data.claimHeaderId;
                            self.$Progress.finish();
                        }
                
                    
                    })
                    .catch(function(){
                    // console.log('FAILURE!!');
                        self.$Progress.fail();
                        self.formBusy = false;
                    })
                    .finally( () => {
                        self.formBusy = false;
                    });
                }
            });
                        
        },
        makeToast(variant = null,body,title) {
            this.$bvToast.toast(body, {
                title: `${title}`,
                variant: variant,
                solid: true
            })
        },
        viewRequest(){
            this.$router.push({
               name : 'view-claim-request', 
                params : { 
                    claimHeaderId : this.claimHeaderId
                } 
            });
        }
    },
    watch: {
        'form.couponType' : function (val) {
            if(val == 2) {
                this.fields.push({ 
                    key: 'plate_no', 
                    label: 'Plate No', 
                    sortable: true, 
                    class: 'text-center' 
                });
                this.fields.push({ 
                    key: 'cs_number', 
                    label: 'CS Number', 
                    sortable: true, 
                    class: 'text-center' 
                });
                this.fields.push({ 
                    key: 'service_invoice_number', 
                    label: 'Service Invoice No.', 
                    sortable: true, 
                    class: 'text-center' ,  
                });
                this.fields.push({ 
                    key: 'service_date', 
                    label: 'Service Date', 
                    sortable: true, 
                    class: 'text-center' 
                });
            }
        }
    }

};
</script>