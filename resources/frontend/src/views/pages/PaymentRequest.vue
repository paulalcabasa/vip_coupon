<template>
  <div>

  
    <b-card v-show="isError" bg-variant="danger" text-variant="white"  class="mb-3">
    <b-card-text>
      <h6>{{ title}}</h6>
      <p>{{ message }} </p>
      <p class="mb-0">{{ invalidVoucherCodes }}</p>
    </b-card-text>
    </b-card>
     
    <b-row>
      

      <b-col sm="6">
        <KTPortlet v-bind:title="'Request for payment'" >
          
          <template v-slot:toolbar>
            <b-link variant="primary" :href="downloadUrl()">Download template</b-link>
          </template> 

          <template v-slot:body>
            <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
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
      message : '',
      title : ''
    }
  },
  methods: {
    handleFileUpload(){
      this.file = this.$refs.file.files[0];
    },
    downloadUrl(){
      return process.env.VUE_APP_API_URL + '/api/download/voucher-template';
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
          self.message = res.data.message;
          self.isError = true;
          self.title = "Transaction failed!";
        }
        else {
          self.isError = false;
          self.makeToast('success',res.data.message,'System message');
        //  self.file = null;
        }
        self.$Progress.finish();
      
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
    
    
  },
  components: {
    KTPortlet
  },
  created() {

  },
  
};
</script>