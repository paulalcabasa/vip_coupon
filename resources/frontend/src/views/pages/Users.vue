<template>
  <div>
    <KTPortlet v-bind:title="'Users'" >
      <template v-slot:toolbar>
        <b-col lg="12" class="my-1">
          <b-form-group class="mb-0">
            <b-input-group size="sm">
                <b-form-input
                v-model="filter"
                type="search"
                id="filterInput"
                placeholder="Type to Search"
                ></b-form-input>
                <b-input-group-append>
                  <b-button :disabled="!filter" @click="filter = ''"><i class="fa fa-eraser"></i></b-button>
       
                </b-input-group-append>
            </b-input-group>
          </b-form-group>
        </b-col>
      </template> 

      <template v-slot:body>
        <b-container fluid>
          <b-table
              show-empty
              small
              stacked="md"
              :items="purposes"
              :fields="promoFields"
              :current-page="currentPage"
              :per-page="perPage"
              :filter="filter"
              :filterIncludedFields="filterOn"
              :sort-by.sync="sortBy"
              :sort-desc.sync="sortDesc"
              :sort-direction="sortDirection"
              @filtered="onFiltered"
          >

              <template v-slot:cell(status)="row">
                  <b-badge class="mr-1" :variant="statusColors[row.value.trim()]">{{ row.value }}</b-badge>
              </template>

              <template v-slot:cell(actions)="row">
                  <b-button size="sm" @click="edit(row)" class="mr-1">
                      <i class="fa fa-search"></i>
                  </b-button>
              </template>
              <template v-slot:cell(name)="row">
                {{ row.item.first_name + " " + row.item.last_name}}
              </template>
          </b-table>
        </b-container>
      </template>

      <template v-slot:foot>
        <b-row>
          <b-col sm="4" md="4" class="my-1">
              <b-form-group
              label="Per page"
              label-cols-sm="6"
              label-cols-md="4"
              label-cols-lg="3"
              label-align-sm="right"
              label-size="sm"
              label-for="perPageSelect"
              class="mb-0"
              >
              <b-form-select
                  v-model="perPage"
                  id="perPageSelect"
                  size="sm"
                  :options="pageOptions"
              ></b-form-select>
              </b-form-group>
          </b-col>
          <b-col sm="4" md="4"></b-col>
          <b-col sm="4" md="4" class="my-1">
              <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
              align="fill"
              size="sm"
              class="my-0"
              ></b-pagination>
          </b-col>  
        </b-row>
      </template>
    </KTPortlet>


  </div>
</template>

<script>

import KTPortlet from "@/views/partials/content/Portlet.vue";
import axios from 'axios';
import axiosRetry from 'axios-retry';
import badge from '@/common/config/status.config.json';
import jwtService from '@/common/jwt.service.js';
export default {
    name: "AllPromos",
    components: {
        KTPortlet
    },
    data(){
        return {
         
          purposes : [],
          statusColors : badge.badgeColors,
          promoFields: [
            
                { 
                    key: 'account_name', 
                    label: 'Dealer', 
                    sortable: true, 
                },
                { 
                    key: 'name', 
                    label: 'Name', 
                    sortable: true, 
                },
                { 
                    key: 'email_address', 
                    label: 'Email', 
                    sortable: true, 
                },  
            ],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [5, 10, 15, 20, 25, 30],
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: [], 
            submitFlag : false,
            message : '',
            formBusy : false
        }
    },
    mounted() {
        this.loadData();
    },
    created() {

    },
    methods : {
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },
        makeToast(variant = null,body,title) {
            this.$bvToast.toast(body, {
                title: `${title}`,
                variant: variant,
                solid: true
            })
        },
       
        loadData(){
            var self = this;
            self.$Progress.start();
            axiosRetry(axios, { retries: 3 });

            let purposeApi = 'api/user/dealer';
            const purposeReq = axios.get(purposeApi);
          
            axios.all([purposeReq]).then(axios.spread((...responses) => {
                const purposeRes = responses[0];
                self.purposes = purposeRes.data;
                self.$Progress.finish();
            })).catch(errors => {
                self.makeToast('error',"Failed to load resources, please refresh the page.",'System message');
                self.$Progress.fail();
                // react on errors.
            }).finally(() => {
                
            });
        },
        newPurpose(){
          this.$refs['purpose-form'].show();
          this.action = "create";
          this.resetForm();
        },
        onSubmit(evt) {
          evt.preventDefault();
          this.formBusy = true;
          if(this.action == "create"){
            this.create();
          }
          else if(this.action == "update"){
            this.update();
          }
        },  
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                return { text: f.label, value: f.key }
            })
        }
    },

};
</script>