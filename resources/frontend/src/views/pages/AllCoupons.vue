<template>
    <div>
        <BlockUI :message="blockui.msg" :html="blockui.html" v-if="blockui.state"></BlockUI>
        <KTPortlet v-bind:title="'Coupons'" >
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
                            <b-badge class="mr-1" variant="secondary">{{ row.value }}</b-badge>
                        </template>

                        <template v-slot:cell(actions)="row">
                            <b-button size="sm" @click="info(row.item)" class="mr-1">
                                <i class="fa fa-search"></i>
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
    </div>
</template>

<script>
 
import KTPortlet from "@/views/partials/content/Portlet.vue";
import axios from 'axios'; 
export default {
    name: "coupons",
    mounted() {
        this.loadCoupons();
    },
    data(){
        return {
            items: [],
            fields: [
                { 
                    key: 'actions', 
                    label: 'Actions' 
                },
                { 
                    key: 'coupon_id', 
                    label: 'Coupon No.', 
                    sortable: true, 
                    sortDirection: 'desc' 
                },
                { 
                    key: 'account_name', 
                    label: 'Account Name', 
                    sortable: true, 
                    class: 'text-center' 
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
            filterOn: [],
            blockui : {
                msg : 'Please wait',
                html : '<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>',
                state : false
            }
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
        }
    },
    methods: {
        info(item) {
            this.$router.push({ 
                name : 'view-coupon', 
                params : { 
                    couponId : item.coupon_id,
                    action : 'view'
                } 
            });
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },
        loadCoupons(){
            var self = this;
            self.blockui.state = true;
            return new Promise(resolve => {
                axios.get('api/coupon/get')
                    .then( (res) => {
                        self.items = res.data;
                        self.totalRows = self.items.length;
                        resolve(res);
                    })
                    .catch( err => {
                        self.$router.push({name : '404'});
                        resolve(err);
                    })
                    .finally( () => {
                        self.blockui.state = false;
                    });
            });
        }
    }

};
</script>