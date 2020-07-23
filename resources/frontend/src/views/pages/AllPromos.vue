<template>
  <div>
    <KTPortlet v-bind:title="'Promo'" >
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
                  <b-button class="ml-3 btn btn-primary" @click="newPromo">New promo</b-button>
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
              :items="promos"
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
                <b-button size="sm" @click="preview(row.item)" class="mr-1">
                    <i class="flaticon2-printer"></i>
                </b-button>
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

    <b-modal 
      ref="promo-input" 
      title="Promo"
      hide-footer
    >
      <template v-slot:modal-header="{ close }">
        <h5>Promo</h5>
        <!-- Emulate built in modal header close button action -->
        <b-link size="sm" @click="close()"><i class="flaticon2-cross"></i></b-link>
      </template>

      <template v-slot:default>
        <b-alert variant="success" show v-if="submitFlag">{{ message }}</b-alert>
        <b-form @submit="onSubmit">
          <b-form-group
            label="Promo Name"
            label-for="promo_name"
          >
            <b-form-input
              id="promo-name"
              type="text"
              v-model="form.promo_name"
              required
              placeholder="Enter promo name"
            ></b-form-input>
          </b-form-group>

          <b-form-group
            label="Coupon expiry date"
            label-for="coupon-expiry-date"
          >
            <b-form-input
              id="coupon-expiry-date"
              type="date"
              
              placeholder="Enter coupon expiry date"
              v-model="form.coupon_expiry_date"
            ></b-form-input>
          </b-form-group>

          <b-form-group
            label="Effective date from"
            label-for="effective-date-from"
          >
            <b-form-input
              id="effective-date-from"
              type="date"
              required
              placeholder="Enter promo effective date from"
              v-model="form.effective_date_from"
            ></b-form-input>
          </b-form-group>

          <b-form-group
            label="Effective date to"
            label-for="effective-date-to"
          >
            <b-form-input
              id="effective-date-to"
              type="date"
              required
              placeholder="Enter promo effective date to"
              v-model="form.effective_date_to"
            ></b-form-input>
          </b-form-group>

          <b-form-group
            label="Coupon Type"
          >
            <b-form-select 
                v-model="form.coupon_type" 
                :options="couponTypes"
                value-field="id"
                text-field="name"
            ></b-form-select>
          </b-form-group>

          <b-form-group
            label="Terms"
          >
            <vue-editor v-model="form.terms" :editor-toolbar="customToolbar"></vue-editor>
          </b-form-group>
          <b-button type="button" @click="cancelPromo" variant="danger" :disabled="formBusy" v-if="action == 'update'" class="mr-2">Cancel Promo</b-button>
          <b-button type="submit" variant="primary" :disabled="formBusy" v-if="form.status_id != 10">Save</b-button>
         
        </b-form> 
      </template>

    </b-modal>

  </div>
</template>
<style>
.ql-editor strong{
     font-weight:bold;
 }
</style>
<script>

import KTPortlet from "@/views/partials/content/Portlet.vue";
import axios from 'axios';
import axiosRetry from 'axios-retry';
import badge from '@/common/config/status.config.json';
import jwtService from '@/common/jwt.service.js';
import { VueEditor } from 'vue2-editor'
export default {
    name: "AllPromos",
    components: {
        KTPortlet,
        VueEditor
    },
    data(){
        return {
          promos : [],
          statusColors : badge.badgeColors,
          promoFields: [
                { 
                    key: 'actions', 
                    label: 'Actions' 
                },
                { 
                    key: 'id', 
                    label: 'Promo Code', 
                    sortable: true, 
                },
                { 
                    key: 'promo_name', 
                    label: 'Promo Name', 
                    sortable: true, 
                },
                { 
                    key: 'coupon_expiry_date', 
                    label: 'Coupon Expiry Date', 
                    sortable: true, 
                },
                { 
                    key: 'effective_date_from', 
                    label: 'Effective Date From', 
                    sortable: true, 
                },
                { 
                    key: 'effective_date_to', 
                    label: 'Effective Date To', 
                    sortable: true, 
                },
                {
                    key: 'status',
                    label: 'Status',
                    formatter: (value, key, item) => {
                        return value;
                    },
                    sortable: true,
                    sortByFormatted: true,
                    filterByFormatted: true
                },
                { 
                    key: 'remarks', 
                    label: 'Remarks', 
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
            form : {
              id : '',
              promo_name : '',
              coupon_expiry_date : '',
              effective_date_from : '',
              effective_date_to : '',
              terms : '',
              coupon_type : '',
              status_id : ''
            },
            action : '',
            user : JSON.parse(jwtService.getUser()),
            submitFlag : false,
            message : '',
            formBusy : false,
            editor: null,
            customToolbar: [
              ["bold", "italic", "underline"],
              [{ list: "ordered" }, { list: "bullet" }],
              [{ indent: "-1" }, { indent: "+1" }]
            ],
            couponTypes : []
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
        resetForm(){
          this.form = {
            id                 : '',
            promo_name         : '',
            coupon_expiry_date : '',
            effective_date_from: '',
            effective_date_to  : '',
            terms              : '',
            coupon_type        : '',
            status_id          : ''
          };
          this.formBusy = false;
        },
        loadData(){
            var self = this;
            self.$Progress.start();
            axiosRetry(axios, { retries: 3 });

            var promoApi = '';
             if(self.user.user_type_id == 45){
              promoApi = 'api/promos/1';
              
            }
            else if(self.user.user_type_id == 47){
              promoApi = 'api/promos/2';
            }
            else {
              promoApi = 'api/promos';
            }
            console.log(promoApi);
            
            let couponTypeUrl = 'api/coupon-types/get';
            const promoReq = axios.get(promoApi);
            const couponTypeReq = axios.get(couponTypeUrl);

            axios.all([promoReq, couponTypeReq]).then(axios.spread((...responses) => {
                const promoRes = responses[0];
                const couponTypeRes = responses[1];
                self.promos = promoRes.data;
                self.couponTypes.push({
                  id : '',
                  name : 'Please select a coupon type'
                });
                couponTypeRes.data.map( (row) => {
                  self.couponTypes.push({
                    id : row.id,
                    name : row.name,
                    user_type_id : row.user_type_id
                  });
                });
                self.$Progress.finish();
            })).catch(errors => {
                self.makeToast('error',"Failed to load resources, please refresh the page.",'System message');
                self.$Progress.fail();
                // react on errors.
            }).finally(() => {
                
            });
        },
        newPromo(){
          this.$refs['promo-input'].show();
          this.action = "create";
          this.resetForm();
        },
        onSubmit(evt) {
          evt.preventDefault();
          this.formBusy = true;
          if(this.action == "create"){
            this.createPromo();
          }
          else if(this.action == "update"){
            this.updatePromo();
          }
        },
        createPromo(){
          this.$Progress.start();
          axios.post('api/promo/create',{
            promo : this.form
          }).then( res => {
            //this.submitFlag = true;
            this.makeToast('success',res.data.message,'System message');
            this.message = res.data.message;
            this.promos = res.data.promos;
            this.$refs['promo-input'].hide();
             this.$Progress.finish();
          }).catch( err => {
            this.makeToast('danger',err,'System message');
             this.$Progress.fail();
          });
        },
        edit(row){
          this.formBusy = false;
          this.action = "update";
          this.form = {
            id                 : row.item.id,
            promo_name         : row.item.promo_name,
            coupon_expiry_date : row.item.coupon_expiry_date_orig,
            effective_date_from: row.item.effective_date_from_orig,
            effective_date_to  : row.item.effective_date_to_orig,
            index              : row.index,
            terms              : row.item.terms,
            coupon_type        : row.item.coupon_type_id,
            status_id          : row.item.status_id
          };
          this.$refs['promo-input'].show();
        },
        updatePromo(){
          this.$Progress.start();
          axios.patch('api/promo/update',{
            promo : this.form
          }).then( res => {
            //this.submitFlag = true;
            this.makeToast('success',res.data.message,'System message');
            this.message = res.data.message;
            this.promos = res.data.promos;
       
            this.$refs['promo-input'].hide();
            this.$Progress.finish();
          }).catch( err => {
            this.makeToast('danger',err,'System message');
             this.$Progress.fail();
             this.formBusy = false;
          });
        },
        preview(promo){
          window.open(process.env.VUE_APP_LARAVEL_BASEURL + '/api/preview-coupon/' + promo.id);
        },
        cancelPromo(){
          const swalWithBootstrapButtons = this.$swal.mixin({
              customClass: {
              confirmButton: 'btn btn-success',
              cancelButton: 'btn btn-danger'
              },
              buttonsStyling: false
          })
      
          swalWithBootstrapButtons.fire({
              title: "Are you sure to cancel this promo?",
              icon: 'info',
              showCancelButton: true,
              confirmButtonText: 'Yes',
              cancelButtonText: 'Cancel',
              //reverseButtons: true
          }).then((result) => {
              if (result.value) {
              //  console.log(this.form);
                self.$Progress.start();
                self.formBusy = true;
                  axios.post(apiUrl,{
                      claimHeaderId: self.claimHeaderId,
                      userId         : self.$store.getters.currentUser.user_id,
                      userSource     : self.$store.getters.currentUser.user_source_id,
                      status         : status,
                      statusVerb     : verb
                  }).then(res => {
                      self.makeToast(toastState,res.data.message,'System message');
                      self.claimHeader.status = res.data.status;
                      self.$Progress.finish();
                      self.formBusy = false;

                  }).catch(err => {
                      self.makeToast('error',err,'System message');
                      self.$Progress.fail();
                      self.formBusy = false;
                  });
              }
          });

          
        }
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