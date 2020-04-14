<template>
  <div>
    <KTPortlet v-bind:title="'New Request'" >
      <template v-slot:toolbar>
        <b-button variant="success" @click.prevent="submitRequest()">Submit</b-button>
      </template> 

      <template v-slot:body>
        <b-container fluid>
            <b-row>
                <b-col sm="3">
                    <label>Dealer</label>
                </b-col>
                <b-col sm="9">
                    <b-form-select 
                        v-model="dealer" 
                        :options="dealers"
                        value-field="cust_account_id"
                        text-field="account_name"
                    ></b-form-select>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="3">
                    Denomination
                </b-col>
                <b-col sm="9">
                    <table class="table">
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
                            <th><a href="#" @click.prevent="addDenomination()" style="font-weight:normal;font-style:underline"><i class="fa fa-plus-square text-success"></i> Add</a></th>
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
import { SET_BREADCRUMB } from "@/store/breadcrumbs.module";    
import KTPortlet from "@/views/partials/content/Portlet.vue";
import { mapGetters } from "vuex";
import axios from 'axios';
import VueTagsInput from '@johmun/vue-tags-input';

export default {
  name: "request",
  mounted() {
    this.$store.dispatch(SET_BREADCRUMB, [{ title: "New Request" }]);
  },
  components: {
    KTPortlet,
    VueTagsInput

  },
  data(){
    return {
        dealer: null,
        dealers : [],
        denominations : [
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
        ],
      
    }    
  },
  created() {
    /* this.dealers = [
      {
        cust_account_id : 1,
        account_name : 'PASIG'
      }
    ]; */
    axios.get('/api/dealers')
    .then(res => {
        this.dealers = res.data;
    })
    .catch(error => {
        console.log(error)
    });
  },
  methods: {
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
     submitRequest(){
       var self = this;
       if(this.total <= 0){
         this.makeToast('danger','Amount should have a value.','System message');
         return false;
       }
        alert(self.dealer);
       axios.post('api/request/submit', {
         dealer_id : self.dealer,
         denominations : self.denominations
       }).then(res => {
         console.log(res);
       })
       .catch(err => {
         console.log(err);
       })
       .finally( () => {
       
       });
     }
  },

  computed : {
    total : function() {
      return this.denominations.reduce( (acc,item) => parseFloat(acc) + (parseFloat(item.amount) * parseFloat(item.quantity)),0);
    }
  }
 
  
};
</script>