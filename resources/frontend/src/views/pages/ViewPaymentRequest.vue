<template>
    <div>
        <b-row>
            <b-col sm="4">
                <KTPortlet v-bind:title="'Payment Request'" >
                    <template v-slot:body>
                        <div class="mb-3">
                            <span >Payment Reference No.</span>
                            <span class="kt-font-bold kt-font-info display-value">{{ paymentHeader.id }}</span>
                        </div>
                        <div class="mb-3">
                            <span>Submitted by</span>
                            <span class="kt-font-bold display-value">{{ paymentHeader.created_by }}</span>
                        </div>
                        <div class="mb-3">
                            <span>Date submitted</span>
                            <span class="kt-font-bold display-value">{{ paymentHeader.date_created }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="text-bold">Status</span>
                            <span class="display-value">
                                <b-badge class="mr-1" :variant="statusColors[paymentHeader.status.trim().toLowerCase()]">{{ paymentHeader.status.toLowerCase() }}</b-badge>
                            </span>
                        </div>
                    </template>
                </KTPortlet>
            </b-col>

            <b-col sm="8">
                <KTPortlet v-bind:title="'Voucher Codes'" >
                    <template v-slot:toolbar>
                        <b-button v-if="action == 'view' && paymentHeader.status.trim() == 'pending'" size="sm" variant="danger" @click.prevent="cancel()" :disabled="formBusy">Cancel</b-button>
                        <b-button v-if="action == 'approve' && paymentHeader.status.trim() == 'pending'" class="mr-2" size="sm" variant="success" @click.prevent="approve()" :disabled="formBusy">Approve</b-button>
                        <b-button v-if="action == 'approve' && paymentHeader.status.trim() == 'pending'"  size="sm" variant="danger" @click.prevent="reject()" :disabled="formBusy">Reject</b-button>
                    </template> 

                    <template v-slot:body>
                    
                        <b-table striped hover :items="paymentLineItems" :fields="paymentLineFields"></b-table>
                        
                    </template>

                
                </KTPortlet>
            </b-col>
        </b-row>
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
        this.paymentHeaderId = this.$route.params.paymentHeaderId;
        this.action = this.$route.params.action;
        this.loadData();
    },
    data(){
        return {
            formBusy : false,
            action : '',
            statusColors : badge.badgeColors,
            paymentHeaderId : '',
            paymentHeader : {
                id : '',
                status : '',
                created_by : '',
                date_created : ''
            },
            paymentLineItems : [],
            paymentLineFields : [
                { 
                    key: 'coupon_no', 
                    label: 'Coupon No', 
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

            let paymentLineApi = 'api/payment/lines/get/' + this.paymentHeaderId;
            let paymentHeaderApi = 'api/payment/header/get/' + this.paymentHeaderId;
   
            const paymentLineReq = axios.get(paymentLineApi);
            const paymentHeaderReq = axios.get(paymentHeaderApi);
            axios.all([paymentLineReq, paymentHeaderReq]).then(axios.spread((...responses) => {
                const  paymentRes = responses[0];
                const  paymentHeaderRes = responses[1];
                self.paymentLineItems = paymentRes.data;
                self.paymentHeader = paymentHeaderRes.data;

                self.$Progress.finish();
            })).catch(errors => {
                self.makeToast('error',"Failed to load resources, please refresh the page.",'System message');
                self.$Progress.fail();
                // react on errors.
            }).finally(() => {
                
            });
        },
        doAction(apiUrl, action, status, verb, toastState){
            var self = this;
            const swalWithBootstrapButtons = this.$swal.mixin({
                customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
      
            swalWithBootstrapButtons.fire({
                title: "Are you sure to " + action +" this payment request?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                //reverseButtons: true
            }).then((result) => {
                
                if (result.value) {
                    self.$Progress.start();
                    self.formBusy = true;
                    axios.post(apiUrl,{
                        paymentHeaderId: self.paymentHeaderId,
                        userId         : self.$store.getters.currentUser.user_id,
                        userSource     : self.$store.getters.currentUser.user_source_id,
                        status         : status,
                        statusVerb     : verb
                    }).then(res => {
                        self.makeToast(toastState,res.data.message,'System message');
                        self.paymentHeader.status = res.data.status;
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
        cancel(){
            this.doAction('api/payment/update/status', 'cancel', 4, 'cancelled', 'danger');
        },
        approve(){
            this.doAction('api/payment/update/status', 'approve', 2, 'approved', 'success');
        },
        reject(){
            this.doAction('api/payment/update/status', 'reject', 6, 'rejected', 'danger');
        }
    }
};
</script>