<template>
  <div>
  
    <BlockUI :message="blockui.msg" :html="blockui.html" v-if="blockui.state"></BlockUI>
    <b-row>
      <b-col sm="5">
        <KTPortlet v-bind:title="'Coupon'" >
          <template v-slot:toolbar>
            <b-button v-if="isAbleToApprove" @click="approve" size="sm" variant="success"><i class="flaticon2-check-mark"></i></b-button>
            <b-button v-if="isAbleToApprove" @click="reject" size="sm" variant="danger" class="ml-2"><i class="flaticon2-cross"></i></b-button>
            <b-button v-if="isAbleToPrint" size="sm" variant="primary" class="ml-2"><i class="flaticon2-print"></i></b-button>
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
                  <b-badge class="mr-1" variant="secondary">{{ couponDetails.status }}</b-badge>
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
          <b-table striped hover :items="denomination">
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
      couponDetails : [],
      blockui : {
          msg : 'Please wait',
          html : '<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>',
          state : true
      },
      isAbleToApprove : false,
      isAbleToPrint : false,
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
    //self.blockui.state = false;
    console.log(this.action, couponDetails)
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
              );
              this.loadCouponHeader();
              this.loadTimeline();
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
              );
              this.loadCouponHeader();
              this.loadTimeline();
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
    }
  }
  
};
</script>