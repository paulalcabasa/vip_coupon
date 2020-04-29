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
                        Toolbar
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
        this.loadData();
    },
    data(){
        return {
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
        }
    }
};
</script>