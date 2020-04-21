<template>
  <div>
    <KTPortlet v-bind:title="'New Coupon'" >
      <template v-slot:toolbar>
        <b-button variant="success" @click.prevent="submitRequest()">Submit</b-button>
      </template> 
  
      <template v-slot:body>
        <BlockUI :message="blockui.msg" :html="blockui.html" v-if="blockui.state"></BlockUI>
        <b-container fluid>
            <b-row>
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

export default {
  name: "coupon",
  mounted() {
   
  },
  components: {
    KTPortlet,
    VueTagsInput

  },
  data(){
    return {
        dealer: '',
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
        blockui : {
          msg : 'Please wait',
          html : '<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>',
          state : false
        }

    }    
  },
  created() {
    var self = this;
    self.blockui.state = true;
    axios.get('/api/dealers')
    .then(res => {
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
       // this.dealers = res.data;
    })
    .catch(error => {
      self.makeToast('danger','Failed loading dealers, please refresh the page to continue.','System message');
      console.log(error);
    })
    .finally( () => {
      self.blockui.state = false;
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
      
       if(self.dealer == ''){
         this.makeToast('danger','Select the dealer','System message');
         return false;
       }

       if(this.total <= 0){
         this.makeToast('danger','Amount should have a value.','System message');
         return false;
       }
       
       self.blockui.state = true;
       axios.post('api/coupon/submit', {
         dealerId    : self.dealer,
         denominations: self.denominations,
         createdBy   : self.$store.getters.currentUser.user_id,
         userSource   : self.$store.getters.currentUser.user_source_id,
       }).then(res => {
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
           },1500)
         }
       })
       .catch(err => {
         console.log(err);
       })
       .finally( () => {
         self.blockui.state = false;
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