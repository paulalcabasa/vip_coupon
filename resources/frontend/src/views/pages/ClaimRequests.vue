<template>
    <div>
        <KTPortlet v-bind:title="'Claims'" >
            <template v-slot:toolbar>
                <b-col lg="12" class="my-1">
                    <b-form-group
                    label="Filter"
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    label-for="filterInput"
                    class="mb-0"
                    >
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
                        :items="items"
                        :fields="fields"
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
                            <b-button size="sm" @click="info(row.item)" class="mr-1">
                                <i class="fa fa-search"></i>
                            </b-button>
                          <!--    <b-button v-if="row.item.status.trim() == 'pending'" size="sm" @click="edit(row.item)" class="mr-1">
                                <i class="fa fa-edit"></i>
                            </b-button>  -->
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
import badge from '@/common/config/status.config.json';
import jwtService from '@/common/jwt.service.js';

export default {
    name: "claimRequests",
    mounted() {
        this.loadRequests();
      
    },
    data(){
        return {
            statusColors : badge.badgeColors,
            items: [],
            currentRoute : '',
            fields: [
                { 
                    key: 'actions', 
                    label: 'Actions' 
                },
                { 
                    key: 'account_name', 
                    label: 'Dealer', 
                    sortable: true, 
                },
                { 
                    key: 'id', 
                    label: 'Claim Request No.', 
                    sortable: true, 
                },
              
                { 
                    key: 'created_by', 
                    label: 'Created By', 
                    sortable: true, 
                    class: 'text-center' 
                },
                { 
                    key: 'date_created', 
                    label: 'Date Created', 
                    sortable: true, 
                    class: 'text-center' 
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
                
            ],
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [5, 10, 15, 20, 25, 30],
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: []
        }
    },
    components: {
        KTPortlet
    },
    created() {

    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                return { text: f.label, value: f.key }
            })
        },
        currentRouteName() {
            return this.$route.name;
        }
    },
    methods: {
        info(item) {
            var action = "";
            var self = this;
        
            this.$router.push({ 
                name : 'view-claim-request', 
                params : { 
                    claimHeaderId : item.id
                } 
            });
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },
        loadRequests(){
            var self = this;
            var apiUrl = "";
            let status = "";
            let user = JSON.parse(jwtService.getUser());

       
          
            
            self.$Progress.start();
            return new Promise(resolve => {
                axios.get('api/claim-requests/get', {
                    params : {
                        userId : user.user_id,
                        sourceId : user.user_source_id,
                        userType : user.user_type_id,
                        vehicleType : user.sales_group_name,
                        status : 'all'
                    }
                }).then( (res) => {
                        self.items = res.data;
                        self.totalRows = self.items.length;
                        self.$Progress.finish();
                        resolve(res);
                    }).catch( err => {
                        self.$bvToast.toast('Failed loading resources, please refresh the page.', {
                            title: 'System message',
                            variant: 'danger',
                            solid: true
                        });
                        self.$Progress.fail();
                        resolve(err);
                    }).finally( () => {
                        
                    });
            });
        }
    }

};
</script>