<template>
  <div>
    
    <b-alert variant="success" show v-show="submitFlag">
      <span class="mr-2">Coupon No. <strong>{{ couponDetails.coupon_id }}</strong> has been created!</span> 
      <b-link href="#" style="color:#fff;" @click.prevent="viewCoupon"><u>Click here to view</u></b-link>
    </b-alert>

    <b-alert variant="danger" show v-show="disableEditFlag">
      <span class="mr-2">Coupon No. <strong>{{ couponDetails.coupon_id }}</strong> cannot be updated because it has an ongoing approval.</span> 
      <b-link href="#" style="color:#fff;" @click.prevent="viewCoupon"><u>Click here to view</u></b-link>
    </b-alert>


    <KTPortlet v-bind:title="title" v-if="enableEdit">
      <template v-slot:toolbar>
        <b-button 
          v-if="action == 'create'" 
          class="btn btn-success btn-elevate"
          @click.prevent="submit()" 
          :disabled="disableSubmit"
          id="submit"
        >Submit</b-button>
        <b-button v-if="action == 'edit'" class="mr-2" variant="info" @click.prevent="viewCoupon()">View</b-button>
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
                      v-show="user.user_type_id != 51"
                  ></b-form-select>
                  <b-form-input
                      :value="user.account_name"
                      disabled
                      v-show="user.user_type_id == 51"
                  ></b-form-input>
              </b-col>
          </b-row>
          <b-row class="my-3">
              <b-col sm="3">
                  <label>Coupon Type</label>
              </b-col>
              <b-col sm="9">
                  <b-form-select 
                      disabled
                      v-model="coupon_type" 
                      :options="coupon_types"
                      value-field="id"
                      text-field="name"
                  ></b-form-select>
              </b-col>
          </b-row>
          <b-row class="my-3" v-show="coupon_type == 1">
              <b-col sm="3">
                  <label>Vehicle type</label>
              </b-col>
              <b-col sm="9">
                  <b-form-select 
                      v-model="vehicle_type" 
                      :options="vehicle_types"
                      value-field="id"
                      text-field="text"
                  ></b-form-select>
              </b-col>
          </b-row>


          <b-row class="my-3">
              <b-col sm="3">
                  <label>Promo</label>
              </b-col>
              <b-col sm="9">
                  <b-form-select 
                      v-model="promo" 
                      :options="promos"
                  ></b-form-select>
              </b-col>
          </b-row>

          <b-row class="my-3" v-if="promo != null">
              <b-col sm="3">
                  <label>Coupon Expiry Date</label>
              </b-col>
              <b-col sm="9">
                  <b-form-input 
                     :value="promo.coupon_expiry_date_formatted"
                     disabled="disabled"
                  ></b-form-input>
              </b-col>
          </b-row>

          <b-row class="my-3">
              <b-col sm="3">
                  <label>Description</label>
              </b-col>
              <b-col sm="9">
                 <b-form-textarea
                  v-model="description"
                  placeholder="Enter description..."
                  rows="3"
                  max-rows="6"
                ></b-form-textarea>
              </b-col>
          </b-row>


           <b-row class="my-3">
              <b-col sm="3">
                  <label>Purpose</label>
              </b-col>
              <b-col sm="9">
                  <b-form-select 
                      v-model="purpose" 
                      :options="purposes"
                  ></b-form-select>
                  <b-form-text v-if="purpose != null">{{ purpose.require_cs_no_flag == 'Y' ? 'Upon selecting this option, you are required to assign CS Numbers on each denomination.' : ''}}</b-form-text>
              </b-col>
          </b-row>

          <b-row class="my-3">
              <b-col sm="3">
                  <label>Email</label>
              </b-col>
              <b-col sm="9">
                  <vue-tags-input
                      placeholder="add email"
                      v-model="emailRecipient"
                      :validation="emailValidation"
                      :tags="emailRecipients"
                      @tags-changed="newTags => emailRecipients = newTags"
                  />
              </b-col>
          </b-row>

          <b-row class="my-3">
              <b-col sm="3">
                  <label>Attachment</label>
              </b-col>
              <b-col sm="9">
                  <input type="file"  v-if="uploadReady"  id="file" ref="file" v-on:change="handleFileUpload()"/>
                  <b-link variant="primary" target="_blank" :href="downloadUrl()" v-show="couponDetails.filename">{{ couponDetails.filename }}</b-link>
                  <b-form-text>Allowable max file size is 5mb. PNG, JPEG and PDF are the only acceptable file formats.</b-form-text>
              </b-col>
          </b-row>
         
         
          <b-row v-if="denominationLoaded && purpose != null">
              <b-col sm="3">
                  Denomination
              </b-col>
              <b-col sm="9">
                  <table class="table" >
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
                                  <b-form-select
                                    :options="denominationOptions"
                                      v-model="row.amount"
                                  ></b-form-select>
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
                                       :validation="csNumValidation"
                                       :avoidAddingDuplicates="false"
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
<style scoped>
/* form-control {
    display: block;
    width: 100%;
    height: calc(1.5em + 1.3rem + 2px);
    padding: 0.65rem 1rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #e2e5ec;
    border-radius: 4px; */
  .vue-tags-input {
    max-width:100%;
  }

</style>
<script>  
import KTPortlet from "@/views/partials/content/Portlet.vue";
import axios from 'axios';
import VueTagsInput from '@johmun/vue-tags-input';
import axiosRetry from 'axios-retry';
import jwtService from '@/common/jwt.service.js'
import objectToFormData from 'object-to-formdata';
export default {
  name: "coupon",                                                                                                                           
  mounted() {
    this.action = this.$route.params.action;
    this.dealer = this.user.dealer_id;
   
    this.loadDropdowns();
    
    this.couponId = this.$route.params.couponId;
    
    if(this.user.user_type_id != 51){
      this.loadDealers();
      this.dealer = '';
    }
        
    if(this.action == "create") {
      this.setDefaultDenomination();
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
        file : null,
        uploadReady: true,
        dealers : [],
        description : '',
        denominations : [],
        couponId : '',
        emailRecipient : '',
        emailRecipients : [],
        coupon_type : 1,
        coupon_types : [],
        promo : null,
        purpose : null,
        enableEdit : true,
        handlers: [],
        disableEditFlag : false,
        blockui : {
          msg : 'Please wait',
          html : '<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>',
          state : false
        },
        vehicle_type : '',
        denominationOptions : [100, 500, 1000, 5000, 10000],
        defaultDenominations : [
          {
              amount : 500,
              quantity : 0,
              csNumbers : [],
              csNumber : ''
          }
        ],
        action : '',
        title : '',
        couponDetails : {
          created_by : '',
          date_created : '',
          status : '',
          coupon_id : ''
        },
        purposes:[],
        promos:[],
        denominationLoaded : false,
        submitFlag : false,
        user : JSON.parse(jwtService.getUser()),
        emailValidation: [{
          classes: 'min-length',
          rule: tag => this.isEmailValid(tag.text),
          disableAdd: true,
        }],
        csNumValidation: [{
          classes: 'min-length',
          rule: tag => tag.text.length == 6 ? false : true,
          //disableAdd: true,
        }],
        reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
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
 
  methods: {
    downloadUrl(){
      return process.env.VUE_APP_API_URL + '/' + this.couponDetails.attachment;
    },
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
  
    loadDropdowns(){
      axiosRetry(axios, { retries: 3 });
      var self = this;
      let couponTypeUrl = 'api/coupon-types/get';
      //let promoUrl = 'api/promos/active';
      let purposeUrl = 'api/purpose/active';
     
     // const promoReq = axios.get(promoUrl);
      const purposeReq = axios.get(purposeUrl);
      const couponTypeReq = axios.get(couponTypeUrl);
 
      this.$Progress.start();
      axios.all([purposeReq, couponTypeReq]).then(axios.spread((...responses) => {
      //  const promoRes = responses[0];
        const purposeRes = responses[0];
        const couponTypeRes = responses[1];
     
        
        couponTypeRes.data.map( (row) => {
          self.coupon_types.push({
            'id' : row.id,
            'name' : row.name,
            'user_type_id' : row.user_type_id
          });
        });

        self.purposes.push({
          value : null,
          text : 'Please select a purpose'
        });

        purposeRes.data.map( (row) => {
          self.purposes.push({
            value : row,
            text : row.purpose
          });
        });

        self.$Progress.finish();
      })).then( () => {
        this.setDefaultCouponType();
      }).then( () => {
        axios.get('api/promos/active/' + self.coupon_type).then(res => {
          self.promos.push({
            value : null,
            text : 'Please select a promo'
          });

          res.data.map( (row) => {
            self.promos.push({
              value : row,
              text : row.promo_name
            });
          });

        });
      }).catch(errors => {
        self.makeToast('error',"Failed to load resources, please refresh the page.",'System message');
        self.$Progress.fail();
      });
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
        amount : 100,
        quantity : 0,
        csNumbers : [],
        csNumber : ''
      });
    },
    clearForm(){
      this.clearFile();
      this.description = '';
      this.setDefaultDenomination();
      this.purpose = null;
      this.promo = null;
      this.emailRecipients = [];
      this.emailRecipient = '';
    },
    setDefaultDenomination(){
      this.denominations = [];
      this.defaultDenominations.map(data => {
        this.denominations.push({
          'amount' : data.amount,
          'quantity' : 0,
          'csNumbers' : [],
          'csNumber' : ''
        });
      });
    },
    makeToast(variant = null,body,title) {
       this.$bvToast.toast(body, {
         title: `${title}`,
         variant: variant,
         solid: true
       })
    },
    validateCSNumbers(){
      var err = 0;
      this.denominations.map(data => {
        if(parseInt(data.quantity) != parseInt(data.csNumbers.length)) {
          err++;
        } 
      });
      return err > 0 ? true : false;
    },
    validateForm(){

      if(this.validateFileType()){
        this.makeToast('danger','PDF, JPEG and PNG are the only allowed file types.','System message');
        return true;
      }

      if(this.dealer == ''){
        this.makeToast('danger','Select the dealer','System message');
        return true;
      }

      if(this.coupon_type == 1 && this.vehicle_type == null){
        this.makeToast('danger','Please select the vehicle type','System message');
        return true;
      } 

      if(this.promo == null){
        this.makeToast('danger','Please select the promo','System message');
        return true;
      } 
      
      if(this.purpose == null){
        this.makeToast('danger','Please select the purpose','System message');
        return true;
      } 

      if(this.emailRecipients.length == 0){
        this.makeToast('danger','Please add email recipients.','System message');
        return true;
      } 

      if(this.validateFileSize()){
        this.makeToast('danger','Attachment file size must be less than 5mb.','System message');
        return true;
      }
    
      if(this.purpose.require_cs_no_flag == 'Y' && this.validateCSNumbers()){
        this.makeToast('danger','Coupon quantity should match with the quantity of CS Numbers.','System message');
        return true; 
      }

      if(this.total <= 0){
          this.makeToast('danger','Amount should have a value.','System message');
          return true;
      } 

    },
    submit(){
      var self = this;
      
      if(this.validateForm()){
        return false;
      }

      let formData = new FormData();
      formData.append('attachment', this.file);
      formData.append('dealerId', self.dealer);
      formData.append('denominations', JSON.stringify(self.denominations));
      formData.append('createdBy', self.$store.getters.currentUser.user_id);
      formData.append('userSource', self.$store.getters.currentUser.user_source_id);
      formData.append('couponType', self.coupon_type);
      formData.append('description', self.description);
      formData.append('purpose', self.purpose.id);
      formData.append('promo', self.promo.id);
      formData.append('vehicle_type', self.vehicle_type);
      formData.append('email', JSON.stringify(self.emailRecipients));
      
      axiosRetry(axios, { retries: 3 });

         // set spinner to submit button
      self.disableSubmit = true;
      self.$Progress.start();

       axios.post('api/coupon/submit',
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data',
          }
        }
      ).then(res => {
     
        self.$Progress.finish();
        if(res.data.error){
           this.makeToast('danger',res.data.message + " : " + (res.data.invalid_cs_numbers),'System message');
         }
         else {
          self.couponDetails.coupon_id = res.data.couponId;
          self.submitFlag = true;
         // self.clearForm();
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
    handleFileUpload(){
      this.file = this.$refs.file.files[0];
    },
    clearFile () {
      this.uploadReady = false
      this.$nextTick(() => {
        this.uploadReady = true
      })
    },
    loadCouponHeader(){
      var self = this;
      return new Promise(resolve => {
        axiosRetry(axios, { retries: 3 });
        axios.get('api/coupon/show/' + this.couponId)
        .then( (res) => {
          self.couponDetails = res.data;
          if(self.couponDetails.approve_ctr > 0 && self.couponDetails.status == 1){
            self.disableEditFlag = true;
            self.enableEdit = false;
          }
          self.dealer = res.data.dealer_id;
          console.log(res.data);
          self.promo = {
            id : res.data.promo_id,
            promo_name : res.data.promo_name,
            coupon_expiry_date_formatted : res.data.coupon_expiry_date_formatted,
            coupon_expiry_date : res.data.coupon_expiry_date,
            effective_date_from : res.data.effective_date_from,
            effective_date_to : res.data.effective_date_to
          };
          self.description = res.data.description;
          self.vehicle_type = res.data.vehicle_type;
          self.purpose = {
            id : res.data.purpose_id,
            purpose : res.data.purpose,
            require_cs_no_flag : res.data.require_cs_no_flag
          };
          res.data.email.split(';').map(email => {
            self.emailRecipients.push({
              text : email,
              tiClasses : ['ti-valid']
            });
          });
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
      if(this.validateForm()){
        return false;
      }

      // set spinner to submit button
      self.disableSubmit = true;
      self.$Progress.start();

      
      let formData = new FormData();
      formData.append('attachment', this.file);
      formData.append('dealerId', self.dealer);
      formData.append('denominations', JSON.stringify(self.denominations));
      formData.append('createdBy', self.$store.getters.currentUser.user_id);
      formData.append('userSource', self.$store.getters.currentUser.user_source_id);
      formData.append('couponType', self.coupon_type);
      formData.append('description', self.description);
      formData.append('purpose', self.purpose.id);
      formData.append('promo', self.promo.id);
      formData.append('couponId', self.couponId);
      formData.append('vehicle_type', self.vehicle_type);
      formData.append('email', JSON.stringify(self.emailRecipients));
      formData.append('status', self.couponDetails.status_id);
      
      axiosRetry(axios, { retries: 3 });

       axios.post('api/coupon/update',
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data',
          }
        }
      ).then(res => {
     
        self.$Progress.finish();
        if(res.data.error){
           this.makeToast('danger',res.data.message + " : " + (res.data.invalid_cs_numbers),'System message');
         }
         else {
          self.makeToast('success',res.data.message,'System message');

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
    viewCoupon(){
      this.$router.push(
        { 
          name : 'view-coupon', 
          params : { 
            couponId : this.couponDetails.coupon_id,
            action : 'view'
          } 
        }
      );
    },
    setDefaultCouponType(){
      this.coupon_types.map((type) => {
        if(type.user_type_id == this.user.user_type_id){
          this.coupon_type = type.id;
        }
      });
    },
    isEmailValid(email) {
      return (email== "")? "" : (this.reg.test(email)) ? false : true;
    },
    validateFileSize(){
      if(this.file != null){
        var size_mb = this.file.size / 1024 / 1024;
        if(size_mb > 5){
          return true;
        }
      }
      return false;
    },
    validateFileType(){
      if(this.file != null){
        if(this.file.type != "application/pdf" && this.file.type != "image/jpeg" && this.file.type != "image/png"){
          return true;
        }
      }
      return false;
    },

  },

  computed : {
    total : function() {
      return this.denominations.reduce( (acc,item) => parseFloat(acc) + (parseFloat(item.amount) * parseFloat(item.quantity)),0);
    },
   
  }
 
  
};
</script>