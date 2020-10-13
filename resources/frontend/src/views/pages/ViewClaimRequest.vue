<template>
    <div>
        <KTPortlet v-bind:title="'Claim Request'">
             <template v-slot:toolbar>
                <b-button :disabled="formBusy" v-show="claimHeader.status.trim() != 'cancelled'" size="sm" @click="cancel" variant="danger" class="ml-2">Cancel</b-button>
            </template>
            <template v-slot:body>
                <b-tabs content-class="mt-3">
                    <b-tab title="Details" active>
                        <b-row>
                            <b-col sm="3">
                                <div class="mb-3">
                                    <span >Claim Request No.</span>
                                    <span class="kt-font-bold kt-font-info display-value">{{ claimHeader.id }}</span>
                                </div>
                            </b-col>
                            <b-col sm="3">
                                <div class="mb-3">
                                    <span>Submitted by</span>
                                    <span class="kt-font-bold display-value">{{ claimHeader.created_by }}</span>
                                </div>
                            </b-col>
                            <b-col sm="3">
                                <div class="mb-3">
                                    <span>Date submitted</span>
                                    <span class="kt-font-bold display-value">{{ claimHeader.date_created }}</span>
                                </div>
                            </b-col>
                            <b-col sm="3">
                                <div class="mb-3">
                                    <span class="text-bold">Status</span>
                                    <span class="display-value">
                                        <b-badge class="mr-1" :variant="statusColors[claimHeader.status.trim().toLowerCase()]">{{ claimHeader.status.toLowerCase() }}</b-badge>
                                    </span>
                                </div>
                            </b-col>
                            <b-col sm="3">
                                <div class="mb-3">
                                    <span class="text-bold">Attachment</span>
                                    <b-col sm="4"><b-link variant="primary" target="_blank" :href="downloadUrl()">{{ claimHeader.filename }}</b-link></b-col>
                                </div>
                            </b-col>
                            <b-col sm="3">
                                <div class="mb-3">
                                    <span class="text-bold">Total amount</span>
                                    <span class="kt-font-bold display-value">{{ formatPrice(claimHeader.total_amount) }}</span>
                                </div>
                            </b-col>
                        </b-row>
                    </b-tab>
                    <b-tab title="Voucher Codes">
                        <b-table style="overflow-x:scroll;" striped hover :items="claimLineItems" :fields="claimLineFields"></b-table>
                    </b-tab>
                    <b-tab title="Approval" >
                        <b-table striped hover :items="approvalItems" :fields="approvalFields"></b-table>
                    </b-tab>
                </b-tabs>
            </template>
        </KTPortlet>
    </div>
</template>

<style scoped>
    .display-value {
        display: inline-block;
        clear: both;
        width: 100%;
    }
</style>
<script>

import KTPortlet from "@/views/partials/content/Portlet.vue";
import axios from 'axios';
import badge from '@/common/config/status.config.json';

export default {
    name: "blank",
    mounted() {
        this.claimHeaderId = this.$route.params.claimHeaderId;
     
        this.loadData();
    },
    data(){
        return {
            formBusy : false,
            action : '',
            statusColors : badge.badgeColors,
            claimHeaderId : '',
            claimHeader : {
                id : '',
                status : '',
                created_by : '',
                date_created : '',
                attachment : '',
                filename : ''
            },
            claimLineItems : [],
            claimLineFields : [
                { 
                    key: 'coupon_no', 
                    label: 'Coupon No', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },
                { 
                    key: 'customer_name', 
                    label: 'Customer', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },
                { 
                    key: 'voucher_code', 
                    label: 'Code', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },
                { 
                    key: 'amount', 
                    label: 'Amount', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },
            
                { 
                    key: 'cs_number', 
                    label: 'CS Number', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },

                { 
                    key: 'service_invoice_no', 
                    label: 'Service Invoice No', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },

                { 
                    key: 'service_date', 
                    label: 'Service Date', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },

                { 
                    key: 'dealer_code', 
                    label: 'Dealer Code', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },

               
            
            ],
            approvalItems : [],
            approvalFields : [
                {
                key: 'hierarchy', 
                label: 'Hierarchy', 
                sortable: false, 
                sortDirection: 'desc' 
                },
                {
                key: 'approver_name', 
                label: 'Approver Name', 
                sortable: false, 
                sortDirection: 'desc' 
                },
                {
                key: 'email_address', 
                label: 'Email', 
                sortable: false, 
                sortDirection: 'desc' 
                },
                {
                key: 'date_sent', 
                label: 'Date Sent', 
                sortable: false, 
                sortDirection: 'desc' 
                },
                {
                key: 'status', 
                label: 'Status', 
                sortable: false, 
                sortDirection: 'desc' 
                },
                {
                key: 'date_approved', 
                label: 'Date Approved', 
                sortable: false, 
                sortDirection: 'desc' 
                },
                {
                key: 'remarks', 
                label: 'Remarks', 
                sortable: false, 
                sortDirection: 'desc' 
                },
            ],
        }
    },
    components: {
        KTPortlet
    },
    created() {

    },
    methods: {
        makeToast(variant = null,body,title) {
            this.$bvToast.toast(body, {
                title: `${title}`,
                variant: variant,
                solid: true
            })
        },
        loadData(){
            var self = this;

            self.$Progress.start();

            let claimLineApi = 'api/claim-request/lines/get/' + this.claimHeaderId;
            let claimHeaderApi = 'api/claim-request/header/get/' + this.claimHeaderId;
            let approvalApi = 'api/claim-request/approvers/get/' + this.claimHeaderId;

            const claimLineReq = axios.get(claimLineApi);
            const claimHeaderReq = axios.get(claimHeaderApi);
            const approvalReq = axios.get(approvalApi);
            axios.all([claimLineReq, claimHeaderReq, approvalReq]).then(axios.spread((...responses) => {
                const  claimRes = responses[0];
                const  claimHeaderRes = responses[1];
                const  approval = responses[2];

                self.claimLineItems = claimRes.data;
                self.claimHeader = claimHeaderRes.data;
               
                self.approvalItems = approval.data;
                
                self.$Progress.finish();
            })).catch(errors => {
                self.makeToast('error',"Failed to load resources, please refresh the page.",'System message');
                self.$Progress.fail();
                // react on errors.
            }).finally(() => {
                
            });
        },
        cancel(){
            var self = this;
            const swalWithBootstrapButtons = this.$swal.mixin({
                customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
      
            swalWithBootstrapButtons.fire({
                title: "Are you sure to cancel this claim request?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                //reverseButtons: true
            }).then((result) => {
                
                if (result.value) {
                    self.$Progress.start();
                    self.formBusy = true;
                    axios.post('api/claim-request/cancel',{
                        claimHeader : self.claimHeader
                    }).then(res => {
                        self.makeToast('error',res.data.message,'System message');
                        self.claimHeader.status = res.data.status;
                        self.$Progress.finish();
                        self.formBusy = false;
                    }).catch(err => {
                        self.makeToast('error',err,'System message');
                        self.$Progress.fail();
                        self.formBusy = false;
                    });
                }
            });
        },
       
        downloadUrl(){
            return process.env.VUE_APP_API_URL + '/' + this.claimHeader.attachment;
        },
        formatPrice(value){
            return (parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
        },
        
    }
};
</script>