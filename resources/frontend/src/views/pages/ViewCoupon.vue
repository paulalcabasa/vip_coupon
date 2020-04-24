<template>
  <div>
  
    <BlockUI :message="blockui.msg" :html="blockui.html" v-if="blockui.state"></BlockUI>
    <b-row>
      <b-col sm="5">
        <KTPortlet v-bind:title="'Coupon'" >
          <template v-slot:toolbar>
            <b-button v-if="isAbleToApprove" @click="approve" size="sm" variant="success"><i class="flaticon2-check-mark"></i></b-button>
            <b-button v-if="isAbleToApprove" @click="reject" size="sm" variant="danger" class="ml-2"><i class="flaticon2-cross"></i></b-button>
            <b-button v-if="isAbleToPrint" @click="print" size="sm" variant="primary" class="ml-2"><i class="flaticon2-print"></i></b-button>
            <b-button v-if="isAbletoEdit" @click="edit" size="sm" variant="primary" class="ml-2"><i class="flaticon2-edit"></i></b-button>
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

      <b-col sm="7">
        <KTPortlet v-bind:title="'Timeline'" >
          <template v-slot:body>
            <Timeline2 v-bind:datasrc="timelines"></Timeline2>
          </template>
        </KTPortlet>
      </b-col>
    </b-row>

    <KTPortlet v-bind:title="'Denomination'" >
       <template v-slot:toolbar>
        <h5>Total amount : {{ formatPrice(total) }}</h5>
      </template> 
      <template v-slot:body>
          <b-table striped hover :items="denomination" :fields="fields">
            <template v-slot:cell(cs_number)="data">
              <span v-html="data.value"></span>
            </template>
          </b-table>
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
      blockui : {
          msg : 'Please wait',
          html : '<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>',
          state : true
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
              label: 'Quantity By', 
              sortable: true, 
              class: 'text-center' 
          },    
          { 
              key: 'cs_number', 
              label: 'CS Numbers', 
              sortable: true, 
          },    
      ],

      statusColors : badge.badgeColors,
      isAbleToApprove : false,
      isAbleToPrint : false,
      isAbletoEdit : false,
      denomination: [],
      timelines: []
    }
  },
  mounted() {
    this.couponId = this.$route.params.couponId;
    this.action = this.$route.params.action;
    this.loadCouponHeader();
    this.loadTimeline();
    this.loadDenomination();
    
  },
  created() {

  },
  computed : {
    total : function() {
      return this.denomination.reduce( (acc,item) => parseFloat(acc) + (parseFloat(item.amount) * parseFloat(item.quantity)),0);
    }
  },
  methods: {
    loadCouponHeader(){
      var self = this;
      return new Promise(resolve => {
        axios.get('api/coupon/show/' + this.couponId)
        .then( (res) => {
          self.couponDetails = res.data;
          if(res.data.status.trim() == "PENDING" && self.action == "approve"){
            self.isAbleToApprove = true;
          }
          if(res.data.status.trim() == "APPROVED" && self.action == "view"){
            self.isAbleToPrint = true;
          }
          if(res.data.status.trim() == "PENDING" && self.action == "view"){
            self.isAbletoEdit = true;
          }
          
          resolve(res);
          
        })
        .catch( err => {
          this.$router.push({name : '404'});
          resolve(err);
        })
        .finally( () => {
          self.blockui.state = false;
        });
      });
    },
    loadTimeline(){
      var self = this;
      return new Promise(resolve => {
        axios.get('api/timeline/show/' + self.couponId)
        .then( (res) => {
          self.timelines = res.data;
          resolve(res); 
        })
        .catch( err => {
          self.$router.push({name : '404'});
          resolve(err);
        })
        .finally( () => {
          self.blockui.state = false;
        })
      });
    },
    loadDenomination(){
      var self = this;
      return new Promise(resolve => {
        axios.get('api/denomination/show/' + self.couponId)
        .then( (res) => {
          self.denomination = res.data;
          resolve(res);
        })
        .catch( err => {
          self.$router.push({name : '404'});
          resolve(err);
        })
        .finally( () => {
          self.blockui.state = false;
        })
      });
    },
    formatPrice(value){
      return (parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    },
    approve(){
      var self = this;
      const swalWithBootstrapButtons = this.$swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })
      
      swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        //reverseButtons: true
      }).then((result) => {
        if (result.value) {
          self.blockui.state = true;
          axios.post('api/coupon/approve', {
            couponId : self.couponId,
            userId   : self.$store.getters.currentUser.user_id,
            userSource   : self.$store.getters.currentUser.user_source_id,
            status : 2,
            action : 1
          }).then(res => {
            if(!res.data.error){
              swalWithBootstrapButtons.fire(
                'Approved!',
                res.data.message,
                'success'
              ).then(() => {
                self.$router.push({ 
                  name : 'approval'
                });
              });
            }
          }).catch(err => {
            swalWithBootstrapButtons.fire(
              'System message',
              res.data.message,
              'error'
            );
          }).finally( () => {
              self.blockui.state = false;
          });
          
        } 
      });
    },
    reject(){
      var self = this;
      const swalWithBootstrapButtons = this.$swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })
      
      swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        //reverseButtons: true
      }).then((result) => {
        if (result.value) {
          self.blockui.state = true;
          axios.post('api/coupon/reject', {
            couponId : self.couponId,
            userId   : self.$store.getters.currentUser.user_id,
            userSource   : self.$store.getters.currentUser.user_source_id,
            status : 6,
            action : 9
          }).then(res => {
            if(!res.data.error){
              swalWithBootstrapButtons.fire(
                'Rejected',
                res.data.message,
                'error'
              ).then(() => {
                self.$router.push({ 
                  name : 'approval'
                });
              });

            }
          }).catch(err => {
            swalWithBootstrapButtons.fire(
              'System message',
              res.data.message,
              'error'
            );
          }).finally( () => {
            self.blockui.state = false;
          });
          
        } 
      });
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
    makeToast(variant = null,body,title) {
      this.$bvToast.toast(body, {
        title: `${title}`,
        variant: variant,
        solid: true
      })
    },
    print(){
      var self = this;
      self.blockui.state = true;
      axios.post('api/coupon/generate',{
        couponId  : self.couponId,
        userId    : self.$store.getters.currentUser.user_id,
        userSource: self.$store.getters.currentUser.user_source_id,
      }).then(res => {
        if(!res.data.error){
          self.makeToast('success',res.data.message,'System message');
        //  self.loadCouponHeader();
          window.open(process.env.VUE_APP_API_URL + '/api/print-coupon/' + res.data.couponId);
        }
        else {
          self.makeToast('danger',res.data.message,'System message');
        }
      }).catch(err => {
        self.makeToast('danger',err,'System message');
        console.log(err);
      }).finally( () => {
        self.blockui.state = false;
      });
    //  console.log(process.env.VUE_APP_API_URL + '/api/print-coupon/' + this.couponId);
      //window.open(process.env.VUE_APP_API_URL + '/api/print-coupon/' + this.couponId);
    }
  }
  
};
</script>