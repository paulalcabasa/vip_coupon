<template>
  <div>
    <KTPortlet v-bind:title="'New Request'" >
      <template v-slot:toolbar>
        Toolbar
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
                                <th>Amount</th>    
                                <th>Quantity</th>    
                                <th>CS No.</th>    
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
                                        v-model="tag"
                                        :tags="tags"
                                        :autocomplete-items="filteredItems"
                                        @tags-changed="newTags => tags = newTags"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </b-col>
            </b-row>
        </b-container>
      </template>

      <template v-slot:foot>
        Footer
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
                csNumbers : []
            }
        ],
        tag: '',
        tags: [],
        autocompleteItems: [{
            text: 'Spain',
            }, {
                text: 'France',
            }, {
                text: 'USA',
            }, {
                text: 'Germany',
            }, {
                text: 'China',
            }],
            }
        
  },
  created() {
    axios.get('/api/dealers')
    .then(res => {
        this.dealers = res.data;
    })
    .catch(error => {
        console.log(error)
    });
  },
  computed: {
    filteredItems() {
      return this.autocompleteItems.filter(i => {
        return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
      });
    },
  },
  
};
</script>