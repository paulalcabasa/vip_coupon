<template>
  <div>
    <b-row>
      <b-col sm="6">
        <KTPortlet v-bind:title="'Coupon'" >
          <template v-slot:toolbar>
            <b-button :disabled="disableActions" v-if="isAbleToApprove" @click="approve" size="sm" variant="success"><i class="flaticon2-check-mark"></i></b-button>
            <b-button :disabled="disableActions" v-if="isAbleToApprove" @click="reject" size="sm" variant="danger" class="ml-2"><i class="flaticon2-cross"></i></b-button>
            <b-button :disabled="disableActions" v-if="isAbleToPrint" @click="print" size="sm" variant="primary" class="ml-2"><i class="flaticon2-print"></i></b-button>
            <b-button :disabled="disableActions" v-if="isAbletoEdit" @click="edit" size="sm" variant="primary" class="ml-2"><i class="flaticon2-edit"></i></b-button>
            <b-button :disabled="disableActions" v-if="isAbleToIssue" @click="issue" size="sm" variant="success" class="ml-2"><i class="flaticon-paper-plane"></i></b-button>
            <b-button :disabled="disableActions" v-if="isAbleToReceiveByFleet" @click="receiveFleet" size="sm" variant="primary" class="ml-2"><i class="flaticon-like"></i></b-button>
            <b-button :disabled="disableActions" v-if="isAbleToReceiveByDealer" @click="receiveDealer" size="sm" variant="success" class="ml-2"><i class="la la-truck"></i></b-button>
          </template> 
          <template v-slot:body>
            <b-container fluid>
              <b-row>
                <b-col sm="4">
                  <label>Coupon No.</label>
                </b-col>
                <b-col sm="8">
                  <span class="kt-font-bold kt-font-info">{{ couponDetails.coupon_id }}</span>
                </b-col>
              </b-row>
              <b-row>
                <b-col sm="4">
                  <label>Dealer</label>
                </b-col>
                <b-col sm="8">
                  <span class="kt-font-bold kt-font-info">{{ couponDetails.account_name }}</span>
                </b-col>
              </b-row>
              <b-row>
                <b-col sm="4">
                  <label class="text-bold">Created by</label>
                </b-col>
                <b-col sm="8">{{ couponDetails.created_by }}</b-col>
              </b-row>
              <b-row>
                <b-col sm="4">
                  <label class="text-bold">Date Created</label>
                </b-col>
                <b-col sm="8">{{ couponDetails.date_created }}</b-col>
              </b-row>
              <b-row>
                <b-col sm="4">
                  <label class="text-bold">Status</label>
                </b-col>
                <b-col sm="8">
                  <b-badge class="mr-1" :variant="statusColors[couponDetails.status.trim().toLowerCase()]">{{ couponDetails.status.toLowerCase() }}</b-badge>
                </b-col>
              </b-row>
            </b-container>
          </template>
        </KTPortlet>
      </b-col>

      <b-col sm="6">
        <KTPortlet v-bind:title="'Timeline'" >
          <template v-slot:body>
            <Timeline2 v-bind:datasrc="timelines"></Timeline2>
          </template>
        </KTPortlet>
      </b-col>
    </b-row>

    <KTPortlet v-bind:title="'Denomination'">
      <template v-slot:body>
        <b-tabs content-class="mt-3">
          <b-tab title="Amount" active>
            <b-table striped hover :items="denomination" :fields="fields">
              <template v-slot:cell(cs_number)="data">
                <span v-html="data.value"></span>
              </template>
            </b-table> 
          </b-tab>
          <b-tab title="Voucher">
            <b-table striped hover :items="voucherItems" :fields="voucherFields"></b-table>
          </b-tab>
        </b-tabs>
      </template>
    </KTPortlet>
  </div>
</template>

<script>
import KTPortlet from "@/views/partials/content/Portlet.vue";
import { mapGetters } from "vuex";
import Timeline2 from "@/views/partials/widgets/Timeline2.vue";
import axios from 'axios';
import badge from '@/common/config/status.config.json';

export default {
  name: "ViewCoupon",
  components: {
    KTPortlet,
    Timeline2
  },
  data(){
    return {
      couponId : null,
      action : '',
      couponDetails : {
        created_by : '',
        date_created : '',
        status : '',
        coupon_id : ''
      },
      fields: [
          { 
              key: 'amount', 
              label: 'Amount', 
              sortable: true, 
              sortDirection: 'desc' 
          },
         
          { 
              key: 'quantity', 
              label: 'Quantity', 
              sortable: true, 
              class: 'text-center' 
          },    
          { 
              key: 'cs_number', 
              label: 'CS Numbers', 
              sortable: true, 
          },    
      ],
      voucherItems : [],
      voucherFields : [
        { 
              key: 'voucher_no', 
              label: 'Voucher No.', 
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
              key: 'status', 
              label: 'Status', 
              sortable: true, 
              sortDirection: 'desc' 
        },
      ],

      statusColors : badge.badgeColors,
      isAbleToApprove : false,
      isAbleToPrint : false,
      isAbletoEdit : false,
      isAbleToIssue : false,
      isAbleToReceiveByFleet : false,
      isAbleToReceiveByDealer : false,
      denomination: [],
      timelines: [],
      disableActions : false
    }
  },
  mounted() {
    this.couponId = this.$route.params.couponId;
    this.action = this.$route.params.action;
  
    this.loadData();
  },
  created() {

  },
  computed : {
    total : function() {
      return this.denomination.reduce( (acc,item) => parseFloat(acc) + (parseFloat(item.amount) * parseFloat(item.quantity)),0);
    }
  },
  methods: {
    loadData(){

      var self = this;

      self.$Progress.start();

      let couponApi = 'api/coupon/show/' + this.couponId;
      let timelineApi = 'api/timeline/show/' + self.couponId;
      let denominationApi = 'api/denomination/show/' + self.couponId;
      let voucherApi = 'api/voucher/get/' + self.couponId;

      const couponReq = axios.get(couponApi);
      const timelineReq = axios.get(timelineApi);
      const denominationReq = axios.get(denominationApi);
      const voucherReq = axios.get(voucherApi);

      axios.all([couponReq, timelineReq, denominationReq, voucherReq]).then(axios.spread((...responses) => {
        const couponRes = responses[0];
        const timelineRes = responses[1];
        const denominationRes = responses[2];
        const voucherRes = responses[3];
     
        // use/access the results 
        self.couponDetails = couponRes.data;
        if(couponRes.data.status.trim() == "PENDING" && self.action == "approve"){
          self.isAbleToApprove = true;
        }
        if(couponRes.data.status.trim() == "APPROVED" && self.action == "view"){
          self.isAbleToPrint = true;
        }
        if(couponRes.data.status.trim() == "PENDING" && self.action == "view"){
          self.isAbletoEdit = true;
        }
        if(couponRes.data.status.trim() == "PRINTED" && self.action == "view"){
          self.isAbleToIssue = true;
        }
        if(couponRes.data.status.trim() == "ISSUED" && self.action == "view"){
          self.isAbleToReceiveByFleet = true;
        }
        if(couponRes.data.status.trim() == "FLEET RECEIVED" && self.action == "view"){
          self.isAbleToReceiveByDealer = true;
        }

        self.timelines = timelineRes.data;
        self.denomination = denominationRes.data;
        self.voucherItems = voucherRes.data;

        self.$Progress.finish();
      })).catch(errors => {
        self.makeToast('error',"Failed to load resources, please refresh the page.",'System message');
    
        self.$Progress.fail();
        // react on errors.
      }).finally(() => {
        
      });
    },  
    formatPrice(value){
      return (parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    },
    makeToast(variant = null,body,title) {
      this.$bvToast.toast(body, {
        title: `${title}`,
        variant: variant,
        solid: true
      })
    },
    edit(){
      this.$router.push({ 
          name : 'edit-coupon', 
          params : { 
              action : 'edit',
              couponId : this.couponId
          } 
      });
    },
    doAction(apiUrl, title, messageState, action, confirmMessage){
      var self = this;
      const swalWithBootstrapButtons = this.$swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })
      
      swalWithBootstrapButtons.fire({
        title: confirmMessage,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        //reverseButtons: true
      }).then((result) => {
        if (result.value) {
          self.$Progress.start();
          self.disableActions = true;
          axios.post(apiUrl, {
            couponId  : self.couponId,
            userId    : self.$store.getters.currentUser.user_id,
            userSource: self.$store.getters.currentUser.user_source_id
          }).then(res => {
          
            if(action != "print"){
              swalWithBootstrapButtons.fire(
                title,
                res.data.message,
                messageState
              ).then(() => {
                //self.loadData();
                 self.$router.push({ 
                  name : 'view-coupon',
                  params : {
                    'action' : 'view',
                    'couponId' : self.couponId
                  } 
                });
              });
            }

            if(action == "print"){
              self.makeToast('success',res.data.message,'System message');
              window.open(process.env.VUE_APP_API_URL + '/api/print-coupon/' + res.data.couponId);
              self.$router.push({ 
                name : 'view-coupon',
                params : {
                  'action' : 'view',
                  'couponId' : self.couponId
                } 
              });
            }

        
          }).catch(err => {
            swalWithBootstrapButtons.fire(
              'System message',
              res.data.message,
              'error'
            );
            self.disableActions = false;
            self.$Progress.fail();
          }).finally( () => {
            self.$Progress.finish();
          });
          
        } 
      });
    },
    approve(){
      this.doAction('api/coupon/approve', 'Approved!','success', 'approve', 'Are you sure to approve?');
    },
    reject(){
      this.doAction('api/coupon/approve', 'Rejected!','error', 'reject', 'Are you sure to reject?');
    }, 
    print(){
      this.doAction('api/coupon/generate', 'Printed!','success', 'print', 'Are you sure to print?');
    },
    issue(){
      this.doAction('api/coupon/issue', 'Issued','success', 'issue', 'Are you sure to issue?');
    },
    receiveFleet(){
      this.doAction('api/coupon/receive/fleet', 'Received','success', 'receive-fleet', 'Are you sure to receive coupons by fleet sales?');
    },
    receiveDealer(){
      this.doAction('api/coupon/receive/dealer', 'Received','success', 'receive-fleet', 'Are you sure to receive coupons by dealer?');
    }
  },
  
  
};
</script>