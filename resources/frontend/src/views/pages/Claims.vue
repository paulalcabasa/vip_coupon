<template>
    <div>
        <KTPortlet v-bind:title="'Claims'" >
            <template v-slot:body>
                <b-form>
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

                <b-button type="submit" @click.prevent="getClaims()" variant="primary">Submit</b-button>
             
            </b-form>
            </template>
        </KTPortlet>
        <KTPortlet v-bind:title="'List'">
            <template v-slot:toolbar>
                <b-col lg="12" class="my-1">
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
            </template> 

            <template v-slot:body>
                <b-container fluid>
                    <b-table
                        show-empty
                        small
                        stacked="md"
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
                    >
                    
                        <template v-slot:cell(status)="row">
                            <b-badge class="mr-1" :variant="statusColors[row.value.trim()]">{{ row.value }}</b-badge>
                        </template>

                        <template v-slot:cell(actions)="row">
                            <b-button size="sm" @click="info(row.item)" class="mr-1">
                                <i class="fa fa-search"></i>
                            </b-button>
                        </template>
                    </b-table>

               
                </b-container>
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

<script>
 
import KTPortlet from "@/views/partials/content/Portlet.vue";
import axios from 'axios'; 
import badge from '@/common/config/status.config.json';
import axiosRetry from 'axios-retry';
import jwtService from '@/common/jwt.service.js';
export default {
    name: "coupons",
    mounted() {
       
        this.user = JSON.parse(jwtService.getUser());
    },
    data(){
        return {
            statusColors : badge.badgeColors,
            items: [],
            user : [],
            form : {
                startDate : '',
                endDate : '',
                formBusy : false
            },
            fields: [
                { 
                    key: 'coupon_type', 
                    label: 'Coupon Type', 
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
                    key: 'account_name', 
                    label: 'Dealer', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'claim_date', 
                    label: 'Claim Date', 
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
                { 
                    key: 'plate_no', 
                    label: 'Plate No', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'cs_number', 
                    label: 'CS Number', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'service_invoice_number', 
                    label: 'Service Invoice No.', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'service_date', 
                    label: 'Service Date', 
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
            filterOn: []
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
            return new Promise(resolve => {

                axiosRetry(axios, { retries: 3 });
                
                let user = JSON.parse(jwtService.getUser());

                //console.log(user);
                axios.get('api/claims/get', {
                    params : {
                        startDate : self.form.startDate,
                        endDate : self.form.endDate
                    }
                }).then( (res) => {
                    self.items = res.data;
                    self.totalRows = self.items.length;
                    self.$Progress.finish();
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
        }
    }

};
</script>