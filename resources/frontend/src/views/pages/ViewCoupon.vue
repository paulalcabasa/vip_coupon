<template>
  <div>

    <b-alert variant="success" show v-show="submitFlag">
      <span class="mr-2">Voucher for Coupon No. <strong>{{ couponId }}</strong> has been generated!</span>
      <span>System will send an email to :
        <b-badge class="mr-1 mb-1" variant="info" :key="index" v-for="(email,index) in couponDetails.email">{{ email }}</b-badge>
        to print the voucher.
      </span>
      <!-- <b-link href="#" style="color:#fff;" @click.prevent="printCoupon"><u>Click here to print</u></b-link> -->
    </b-alert>

  

    <KTPortlet v-bind:title="'Coupon'">
      <template v-slot:toolbar>
        <b-button :disabled="disableActions" v-show="approveFlag" @click="approve" size="sm" variant="success">Approve</b-button>
        <b-button :disabled="disableActions" v-show="approveFlag" @click="reject" size="sm" variant="danger" class="ml-2">Reject</b-button>
        <b-button :disabled="disableActions" v-show="isAbletoEdit" @click="edit" size="sm" variant="primary" class="ml-2">Update</b-button>
        <b-button  :disabled="disableActions" @click="resendRequestor" v-if="couponDetails.status_id == 12 || couponDetails.status_id == 3" size="sm" variant="primary" class="ml-2">Resend</b-button>
      
     </template> 
      <template v-slot:body>
        <b-tabs content-class="mt-3">
          <b-tab title="Details" active>
            <b-container fluid>
              <b-row>
                <b-col sm="2">
                  <label class="kt-font-bold">Coupon No.</label>
                </b-col>
                <b-col sm="4">
                  <span class="kt-font-bold kt-font-info">{{ couponDetails.coupon_id }}</span>
                </b-col>
                   <b-col sm="2">
                  <label class="kt-font-bold">Promo Code</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.promo_id }}</b-col>
              </b-row>
              
              <b-row>
                <b-col sm="2">
                  <label class="kt-font-bold">Coupon Type</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.coupon_type }}</b-col>
                <b-col sm="2">
                  <label class="kt-font-bold">Promo</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.promo_name }}</b-col>
              </b-row>

              <b-row>
                <b-col sm="2">
                  <label class="kt-font-bold">Vehicle Type</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.vehicle_type }}</b-col>
                <b-col sm="2">
                  <label class="kt-font-bold">Created by</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.created_by }}</b-col>
              </b-row>

              <b-row>
                 <b-col sm="2">
                  <label class="kt-font-bold">Dealer</label>
                </b-col>
                <b-col sm="4">
                  <span class="kt-font-bold kt-font-info">{{ couponDetails.account_name }}</span>
                </b-col>

             
                <b-col sm="2">
                  <label class="kt-font-bold">Coupon expiry date</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.coupon_expiry_date_formatted }}</b-col>
              </b-row>
             
              <b-row>
                <b-col sm="2">
                  <label class="kt-font-bold">Description</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.description }}</b-col>
                <b-col sm="2">
                  <label class="kt-font-bold">Purpose</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.purpose }}</b-col>
              </b-row>
             
            
              <b-row>
               
                <b-col sm="2">
                  <label class="kt-font-bold">Date Created</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.date_created }}</b-col>

                 <b-col sm="2">
                  <label class="kt-font-bold">Status</label>
                </b-col>
                <b-col sm="4">
                  <b-badge class="mr-1" :variant="statusColors[couponDetails.status.trim().toLowerCase()]">{{ couponDetails.status.toLowerCase() }}</b-badge>
                </b-col>
              </b-row>

             

              <b-row>
               <b-col sm="2">
                  <label class="kt-font-bold">Email</label>
                </b-col>
                <b-col sm="4">
                  <b-badge class="mr-1 mb-1" variant="info" :key="index" v-for="(email,index) in couponDetails.email">{{ email }}</b-badge>
                </b-col>

                <b-col sm="2">
                  <label class="kt-font-bold">Attachment</label>
                </b-col>
                <b-col sm="4"><b-link variant="primary" target="_blank" :href="downloadUrl()">{{ couponDetails.filename }}</b-link></b-col>
              </b-row>

              <b-row>
               <b-col sm="2">
                  <label class="kt-font-bold">Date sent</label>
                </b-col>
                <b-col sm="4">{{ couponDetails.date_sent}}</b-col>
              </b-row>

            </b-container>
          </b-tab>
          <b-tab title="Denomination" >
            <b-table striped hover :items="denomination" :fields="fields">
              <template v-slot:cell(cs_number)="data">
                <span v-html="data.value"></span>
              </template>
           
            </b-table> 
          </b-tab>
          <b-tab title="Voucher">
            <b-table striped hover :items="voucherItems" :fields="voucherFields">
              <template v-slot:cell(voucher_code)="data">
                <span v-html="maskVoucher(data.value)"></span>
              </template>
            </b-table>
          </b-tab>
          <b-tab title="Timeline">
            <Timeline2 v-bind:datasrc="timelines"></Timeline2>
          </b-tab>
<!-- v-if="user.user_type_id != 51" -->
          <b-tab title="Approval" >
            <b-table striped hover :items="approvalItems" :fields="approvalFields"></b-table>
            <b-button @click="resendApproval" v-if="couponDetails.status_id == 1" size="sm" variant="primary" class="ml-2">Resend</b-button>
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
import axiosRetry from 'axios-retry';
import jwtService from '@/common/jwt.service.js'
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
              key: 'voucher_code', 
              label: 'Code', 
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
        /* {
          key: 'mail_sent_flag', 
          label: 'Is Notified', 
          sortable: false, 
          sortDirection: 'desc' 
        }, */
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
      statusColors : badge.badgeColors,
      isAbleToApprove : false,
      isAbleToPrint : false,
      isAbletoEdit : false,
      isAbleToIssue : false,
      isAbleToReceiveByFleet : false,
      isAbleToReceiveByDealer : false,
      denomination: [],
      timelines: [],
      disableActions : false,
      submitFlag : false,
      user : JSON.parse(jwtService.getUser()),
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

     

      axiosRetry(axios, { retries: 3 });

      let couponApi = 'api/coupon/show/' + this.couponId;
      let timelineApi = 'api/timeline/show/' + self.couponId;
      let denominationApi = 'api/denomination/show/' + self.couponId;
      let voucherApi = 'api/voucher/get/' + self.couponId;
      let approvalApi = 'api/approval/coupon/get/' + self.couponId;

      const couponReq = axios.get(couponApi);
      const timelineReq = axios.get(timelineApi);
      const denominationReq = axios.get(denominationApi);
      const voucherReq = axios.get(voucherApi);
      const approvalReq = axios.get(approvalApi);

      axios.all([couponReq, timelineReq, denominationReq, voucherReq, approvalReq]).then(axios.spread((...responses) => {
        const couponRes = responses[0];
        const timelineRes = responses[1];
        const denominationRes = responses[2];
        const voucherRes = responses[3];
        const approvalRes = responses[4];
     
        // use/access the results 
        self.couponDetails = couponRes.data;
        self.couponDetails.email = couponRes.data.email.split(";");
        if(couponRes.data.status.trim() == "PENDING" && self.action == "approve"){
          self.isAbleToApprove = true;
        }
        if(couponRes.data.status.trim() == "APPROVED" && self.action == "view"){
          self.isAbleToPrint = true;
        }
        if( (couponRes.data.status.trim() == "PENDING") && self.action == "view" && couponRes.data.approve_ctr == 0){
          self.isAbletoEdit = true;
        }
        if( (couponRes.data.status.trim() == "REJECTED") && self.action == "view"){
          self.isAbletoEdit = true;
        }
        if(couponRes.data.status.trim() == "PRINTED" && self.action == "view"){
          self.isAbleToIssue = true;
        }
        if(couponRes.data.status.trim() == "ISSUED" && self.action == "view"){
          self.isAbleToReceiveByFleet = true;
        }
        if(couponRes.data.status.trim() == "FLEET RECEIVED" && self.action == "view" && couponRes.data.coupon_type_id == 2){
          self.isAbleToReceiveByDealer = true;
        }
        
        self.timelines = timelineRes.data;
        self.denomination = denominationRes.data;
        self.voucherItems = voucherRes.data;
        self.approvalItems = approvalRes.data;

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
            // update status
            self.couponDetails.status = res.data.status;
            self.disableActions = false;
            if(action != "generate"){
              self.action = "view";
              swalWithBootstrapButtons.fire(
                title,
                res.data.message,
                messageState
              );
            }
           
            if(action == "generate"){
              //self.makeToast('success',res.data.message,'System message');
              self.couponId = res.data.couponId
           //   window.open(process.env.VUE_APP_API_URL + '/api/print-coupon/' + res.data.couponId);
              self.couponDetails.status = "generated";
              self.submitFlag = true;
          /*     self.$router.push({ 
                name : 'view-coupon',
                params : {
                  'action' : 'view',
                  'couponId' : self.couponId
                } 
              }); */
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
      this.doAction('api/coupon/reject', 'Rejected!','error', 'reject', 'Are you sure to reject?');
    }, 
    print(){
      this.doAction('api/coupon/generate', 'Generated!','success', 'generate', 'Are you sure to generate?');
    },
    issue(){
      this.doAction('api/coupon/issue', 'Issued','success', 'issue', 'Are you sure to issue?');
    },
    receiveFleet(){
      this.doAction('api/coupon/receive/fleet', 'Received','success', 'receive-fleet', 'Are you sure to receive coupons by fleet sales?');
    },
    receiveDealer(){
      this.doAction('api/coupon/receive/dealer', 'Received','success', 'receive-fleet', 'Are you sure to receive coupons by dealer?');
    },
    maskVoucher(voucherCode){
      let endStr = voucherCode.substr(5,voucherCode.length);
      let maskedCode = 'XXXXX' + endStr;
      return maskedCode;
    },
    printCoupon(){
      window.open(process.env.VUE_APP_API_URL + '/api/print-coupon/' + this.couponId);
    },
    downloadUrl(){
      return process.env.VUE_APP_API_URL + '/' + this.couponDetails.attachment;
    },
    resendApproval(){
      var self = this;
      const swalWithBootstrapButtons = this.$swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })
      
      swalWithBootstrapButtons.fire({
        title: 'Are you sure to resend the approval?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
 
      }).then((result) => {
        if (result.value) {
          self.$Progress.start();
     
          axios.post('api/approval/resend', {
            coupon_id : self.couponDetails.coupon_id,
            current_approval_hierarchy : self.couponDetails.current_approval_hierarchy,
            user    : self.$store.getters.currentUser
          }).then(res => {
            swalWithBootstrapButtons.fire(
              'System message',
              res.data.message,
              'success'
            );
          }).catch(err => {
            swalWithBootstrapButtons.fire(
              'System message',
              res.data.message,
              'error'
            );
            self.$Progress.fail();
          }).finally( () => {
            self.$Progress.finish();
          });
          
        } 
      });
    },
    resendRequestor(){
      var self = this;
      const swalWithBootstrapButtons = this.$swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })
      
      swalWithBootstrapButtons.fire({
        title: 'Are you sure to resend to requestor?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
 
      }).then((result) => {
        if (result.value) {
          self.$Progress.start();
     
          axios.post('api/coupon/resend', {
            coupon_id : self.couponDetails.coupon_id,
            user    : self.$store.getters.currentUser
          }).then(res => {
            swalWithBootstrapButtons.fire(
              'System message',
              res.data.message,
              'success'
            );
          }).catch(err => {
            swalWithBootstrapButtons.fire(
              'System message',
              res.data.message,
              'error'
            );
            self.$Progress.fail();
          }).finally( () => {
            self.$Progress.finish();
          });
          
        } 
      });
    }
  },

  computed : {
    approveFlag: function(){
      if (this.couponDetails.status.trim().toLowerCase() == "pending" && this.action == "approve") {
        return true;
      }
      return false;
    },
    printFlag: function(){
      if (this.couponDetails.status.trim().toLowerCase() == "approved" && this.action == "view") {
        return true;
      }
      return false;
    },
    /* receiveFleetFlag: function(){
      if (this.couponDetails.status.trim().toLowerCase() == "printed" && this.action == "view") {
        return true;
      }
      return false;
    }, */
    receiveDealerFlag: function(){
      if (this.couponDetails.status.trim().toLowerCase() == "printed" && this.action == "view" && this.couponDetails.coupon_type_id == 2) {
        return true;
      }
      return false;
    },
  }

  
  
  
};
</script>