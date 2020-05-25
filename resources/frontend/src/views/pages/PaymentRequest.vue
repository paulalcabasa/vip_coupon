<template>
  <div>

    <b-alert variant="success" show v-show="submitFlag">
      <span class="mr-2">Payment Request No. <strong>{{ paymentRequestNo}}</strong> has been created!</span> 
      <b-link href="#" style="color:#fff;" @click.prevent="viewRequest"><u>Click here to view</u></b-link>
    </b-alert>

    <b-card v-show="isError" bg-variant="danger" text-variant="white"  class="mb-3">
    <b-card-text>
      <h6>{{ title}}</h6>
      <p>{{ message }} </p>
      <p class="mb-0" v-if="invalidVoucherCodes != ''"><strong>Not existing</strong> : {{ invalidVoucherCodes }}</p>
      <p class="mb-0" v-if="claimedVoucherCodes != ''"><strong>Already claimed</strong> : {{ claimedVoucherCodes }}</p>
      <p class="mb-0" v-if="invalidCSNumbers != ''"><strong>Invalid CS Numbers</strong> : {{ invalidCSNumbers }}</p>
      <p class="mb-0" v-show="errors.length > 0">
        <strong>Errors : </strong>
        <ol>
          <li v-for="(row,index) in errors" :key="index">{{ row.voucher_code + " : " + row.message }}</li>
        </ol>
      </p>
      
    </b-card-text>
    </b-card>
     
    <b-row>
      

      <b-col sm="6">
        <KTPortlet v-bind:title="'Request for payment'" >
          
          <template v-slot:toolbar>
            <b-link variant="primary" :href="downloadUrl()">Download template</b-link>
          </template> 

          <template v-slot:body>
            <input type="file"  v-if="uploadReady"  id="file" ref="file" v-on:change="handleFileUpload()"/>
          </template>
          
          <template v-slot:foot>
            <b-button variant="success" @click="submit()" :disabled="formBusy">Submit</b-button>
          </template>

        </KTPortlet>

        

      </b-col>
    </b-row>
  </div>
</template>


<script>

import KTPortlet from "@/views/partials/content/Portlet.vue";
import objectToFormData from 'object-to-formdata';
import axios from 'axios';
export default {
  name: "blank",
  mounted() {
 
  },
  data(){
    return {
      file : null,
      formBusy : false,
      isError : false,
      invalidVoucherCodes : [],
      claimedVoucherCodes : [],
      invalidCSNumbers : [],
      message : '',
      title : '',
      uploadReady: true,
      errors :  [],
      paymentRequestNo : '',
      submitFlag : false
    }
  },
  methods: {
    handleFileUpload(){
      this.file = this.$refs.file.files[0];
    },
    downloadUrl(){
      return process.env.VUE_APP_API_URL + '/api/download/voucher-template';
    },
    clear () {
      this.uploadReady = false
      this.$nextTick(() => {
        this.uploadReady = true
      })
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

      if(self.file == null){
        self.makeToast('danger','Please select the file to upload.','System message');
        return false;
      };

      self.$Progress.start();
      self.formBusy = true;
      let formData = new FormData();
      formData.append('voucher_file', this.file);
      formData.append('userId', self.$store.getters.currentUser.user_id);
      formData.append('userSource', self.$store.getters.currentUser.user_source_id);
 
      axios.post( '/api/payment-request/submit',
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data',
          }
        }
      ).then(res => {
     
        if(res.data.error){
          self.invalidVoucherCodes = res.data.invalidVoucherCodes;
          self.claimedVoucherCodes = res.data.claimedVoucherCodes;
          self.invalidCSNumbers = res.data.invalidCSNumbers;
          self.errors = res.data.errors;
          self.message = res.data.message;
          self.isError = true;
          self.title = "Transaction failed!";
          self.clear();
          self.$Progress.fail();
        }
        else {
          self.isError = false;
         // self.makeToast('success',res.data.message,'System message');
          self.clear();
          self.submitFlag = true;

          self.paymentRequestNo =  res.data.paymentHeaderId;
          self.$Progress.finish();
         /*  setTimeout(() => {
            
            this.$router.push({
              name : 'view-payment-request',
              params : {
                'action' : 'view',
                'paymentHeaderId' : res.data.paymentHeaderId
              }
            });
          },1500); */
        }
   
      
      })
      .catch(function(){
        console.log('FAILURE!!');
        self.$Progress.fail();
        self.formBusy = false;
      })
      .finally( () => {
          self.formBusy = false;
      });
    },
    viewRequest(){
      this.$router.push({
        name : 'view-payment-request',
        params : {
          'action' : 'view',
          'paymentHeaderId' : this.paymentRequestNo
        }
      });
    }
    
    
  },
  components: {
    KTPortlet
  },
  created() {

  },
  
};
</script>