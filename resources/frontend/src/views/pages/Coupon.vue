<template>
  <div>
    
    <KTPortlet v-bind:title="title" >
      <template v-slot:toolbar>
        <b-button 
          v-if="action == 'create'" 
          class="btn btn-success btn-elevate"
          @click.prevent="submit()" 
          :disabled="disableSubmit"
          id="submit"
        >Submit</b-button>
        <b-button v-if="action == 'edit'" variant="success" @click.prevent="update()">Save</b-button>
      </template> 
  
      <template v-slot:body>
        <BlockUI :message="blockui.msg" :html="blockui.html" v-if="blockui.state"></BlockUI>
        <b-container fluid>
          <b-row class="my-1">
              <b-col sm="3">
                  <label>Dealer</label>
              </b-col>
              <b-col sm="9">
                  <b-form-select 
                      v-model="dealer" 
                      :options="dealers"
                      value-field="id"
                      text-field="account_name"
                  ></b-form-select>
              </b-col>
          </b-row>
          <b-row>
              <b-col sm="3">
                  Denomination
              </b-col>
              <b-col sm="9">
                  <table class="table" v-if="denominationLoaded">
                      <thead>
                          <tr>
                              <th width="150">Amount</th>    
                              <th width="100">Quantity</th>    
                              <th>CS No.</th>    
                              <th></th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr v-for="(row,index) in denominations" :key="index">
                              
                              <td>
                                  <b-form-input
                                      v-model="row.amount"
                                  ></b-form-input>
                              </td>
                              <td>
                                  <b-form-input
                                      v-model="row.quantity"
                                  ></b-form-input>
                              </td>
                              <td>
                                  <vue-tags-input
                                      placeholder="add vehicle"
                                      v-model="row.csNumber"
                                      :tags="row.csNumbers"
                                      @tags-changed="newTags => row.csNumbers = newTags"
                                  />
                              </td>
                              <td>
                                <a href="#" @click.prevent="deleteDenomination(index)"><i class="fa fa-trash text-danger"></i></a>
                              </td>
                          </tr>
                      </tbody>
                      <tfoot>
                        <tr>
                          <th>Total</th>
                          <th>{{ total }}</th>
                          <th></th>
                          <th><b-link @click.prevent="addDenomination()"><i class="fa fa-plus-square text-success"></i> Add</b-link></th>
                        </tr>
                      </tfoot>
                  </table>
              </b-col>
          </b-row>
        </b-container>
      </template>


    </KTPortlet>
  </div>
</template>

<script>  
import KTPortlet from "@/views/partials/content/Portlet.vue";
import axios from 'axios';
import VueTagsInput from '@johmun/vue-tags-input';
import axiosRetry from 'axios-retry';

export default {
  name: "coupon",
  mounted() {
    this.action = this.$route.params.action;
    this.couponId = this.$route.params.couponId;
    this.loadDealers();
    if(this.action == "create") {
      this.denominations = [
        {
            amount : 100,
            quantity : 0,
            csNumbers : [],
            csNumber : ''
        },
        {
            amount : 500,
            quantity : 0,
            csNumbers : [],
            csNumber : ''
        },
        {
            amount : 1000,
            quantity : 0,
            csNumbers : [],
            csNumber : ''
        }
      ];
      this.denominationLoaded = true;
      this.title = "New Coupon";
    }
    else if(this.action == "edit"){
      this.loadCouponHeader();
      this.loadDenomination();
      this.title = "Edit coupon # " + this.couponId;
      this.denominationLoaded = false;
    }

  },
  components: {
    KTPortlet,
    VueTagsInput

  },
  data(){
    return {
        disableSubmit : false, 
        dealer: '',
        dealers : [],
        denominations : [],
        couponId : '',
        blockui : {
          msg : 'Please wait',
          html : '<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>',
          state : false
        },
        action : '',
        title : '',
        couponDetails : {
          created_by : '',
          date_created : '',
          status : '',
          coupon_id : ''
        },
        denominationLoaded : false
    }    
  },
 
  methods: {
    loadDealers(){
      var self = this;
      this.$Progress.start();
    
      axiosRetry(axios, { retries: 3 });
      
      axios.get('/api/dealers').then(res => {
          this.dealers = [
            {
              'id' : '',
              'account_name' : 'SELECT A DEALER'
            }
          ];
          res.data.map( (row) => {
            this.dealers.push({
              'id' : row.id,
              'account_name' : row.account_name
            });
          });
        this.$Progress.finish();
      })
      .catch(error => {
        self.makeToast('danger','Failed loading dealers, please refresh the page to continue.','System message');
        console.log(error);
        this.$Progress.fail();
      })


    },
    deleteDenomination(index){
      if(this.denominations.length > 1){
        this.denominations.splice(index,1)
      }
      else {
        this.makeToast('danger','There must be atleast one denomination amount.','System message');
      }
    },
    addDenomination(){
      this.denominations.push({
        amount : 0,
        quantity : 0,
        csNumbers : [],
        csNumber : ''
      });
    },
    makeToast(variant = null,body,title) {
       this.$bvToast.toast(body, {
         title: `${title}`,
         variant: variant,
         solid: true
       })
    },
    submit(){
       var self = this;
      
       if(self.dealer == ''){
         this.makeToast('danger','Select the dealer','System message');
         return false;
       }

       if(this.total <= 0){
         this.makeToast('danger','Amount should have a value.','System message');
         return false;
      } 

      axiosRetry(axios, { retries: 3 });

         // set spinner to submit button
      self.disableSubmit = true;
      self.$Progress.start();
       axios.post('api/coupon/submit', {
         dealerId    : self.dealer,
         denominations: self.denominations,
         createdBy   : self.$store.getters.currentUser.user_id,
         userSource   : self.$store.getters.currentUser.user_source_id,
       }).then(res => {
         
        self.$Progress.finish();
        if(res.data.error){
           this.makeToast('danger',res.data.message + " : " + (res.data.invalid_cs_numbers),'System message');
         }
         else {
           this.makeToast('success', res.data.message ,'System message');
           setTimeout( () => {
            this.$router.push(
              { 
                name : 'view-coupon', 
                params : { 
                  couponId : res.data.couponId,
                  action : 'view'
                } 
              }
            );
           },1000)
         }
       })
       .catch(err => {
         console.log(err);
         self.$Progress.fail();
       })
       .finally( () => {
        self.disableSubmit = false;
       });
    },
    loadCouponHeader(){
      var self = this;
      return new Promise(resolve => {

        axiosRetry(axios, { retries: 3 });
        axios.get('api/coupon/show/' + this.couponId)
        .then( (res) => {
          self.couponDetails = res.data;
          self.dealer = res.data.dealer_id;
          resolve(res);
        })
        .catch( err => {
          self.makeToast('danger','Failed loading data, please refresh the page to continue.','System message');
          resolve(err);
        })
        .finally( () => {
          self.blockui.state = false;
        });
      });
    },
    loadDenomination(){
      var self = this;
      return new Promise(resolve => {
        axiosRetry(axios, { retries: 3 });
        axios.get('api/denomination/show/' + self.couponId)
        .then( (res) => {
          var denomination = res.data;
          denomination.map(data => {
            var csNumbers = [];
            data.csNumbers.map(csNumber => {
              csNumbers.push({
                'text' : csNumber,
                'value' : csNumber
              });
            });
            self.denominations.push({
              'amount' : data.amount,
              'quantity' : data.quantity,
              'csNumbers' : csNumbers,
              'csNumber' : ''
            });
          });
          resolve(res);
        })
        .then( () => {
          this.denominationLoaded = true;
        })
        .catch( err => {
          console.log(err);
          self.makeToast('danger','Failed loading data, please refresh the page to continue.','System message');
          //self.$router.push({name : '404'});
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
    update(){
      var self = this;
      self.$Progress.start();
      axiosRetry(axios, { retries: 3 });
      axios.post('api/coupon/update',{
        couponId     : self.couponId,
        dealerId     : self.dealer,
        denominations: self.denominations,
        createdBy    : self.$store.getters.currentUser.user_id,
        userSource   : self.$store.getters.currentUser.user_source_id
      }).then(res => {
        self.makeToast('success',res.data.original.message,'System message');
        self.$Progress.finish();
        console.log(res.data);
      }).catch(err => {
        self.$Progress.fail();
        self.makeToast('danger',err,'System message')
        console.log(err);
      });
    },
     

  },

  computed : {
    total : function() {
      return this.denominations.reduce( (acc,item) => parseFloat(acc) + (parseFloat(item.amount) * parseFloat(item.quantity)),0);
    }
  }
 
  
};
</script>