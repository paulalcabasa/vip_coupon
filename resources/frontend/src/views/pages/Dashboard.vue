<template>
  <div>
    
    <!-- Statistics -->
    <div class="row">
      <div class="col-lg-4 col-xl-4 order-lg-1 order-xl-1">
        <KTPortlet solidClass="kt-portlet--solid-dark">
          <template v-slot:body>
            <Statistics :title="totalCoupons" desc="Coupons" icon="flaticon-file" />
          </template>
        </KTPortlet>
      </div>
      <div class="col-lg-4 col-xl-4 order-lg-1 order-xl-1">
        <KTPortlet solidClass="kt-portlet--solid-info">
          <template v-slot:body>
            <Statistics :title="totalPrinted" desc="Printed" icon="flaticon2-printer" />
          </template>
        </KTPortlet>
      </div>
      <div class="col-lg-4 col-xl-4 order-lg-1 order-xl-1">
        <KTPortlet solidClass="kt-portlet--solid-success">
          <template v-slot:body>
            <Statistics :title="totalClaimed" desc="Claimed" icon="flaticon-like" />
          </template>
        </KTPortlet>
      </div>
    </div>
    <!-- end of statistics -->

    <div class="row">
      <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
        <KTPortlet v-bind:title="'Recent claims'">
          <template v-slot:body>
             <b-table striped hover :items="recentClaims" :fields="recentClaimsFields"></b-table>
          </template>
        </KTPortlet>
      </div>
    </div>


 
  </div>
</template>

<script>
import KTPortlet from "@/views/partials/content/Portlet.vue";
import Statistics from "@/views/partials/widgets/Statistics.vue";
import axios from 'axios';
export default {
  name: "dashboard",
  components: {
    KTPortlet,
    Statistics
  },
  data() {
    return {
      totalCoupons : "0",
      totalPrinted : "0",
      totalClaimed : "0",
      recentClaims: [],
      recentClaimsFields: [
         {
           key: 'account_name',
           label: 'Dealer',
           sortable: true
         },
         {
           key: 'amount',
           sortable: false
         },
         {
           key: 'date_claimed',
           label: 'Date claimed',
           sortable: true,
           // Variant applies to the whole column, including the header and footer
           
         }
       ],
      
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData(){
      this.$Progress.start();
      var self = this;
      axios.get('api/dashboard/statistics').then( res => {
        self.totalCoupons = res.data.totalCoupons;
        self.totalPrinted = res.data.voucherStats.printed;
        self.totalClaimed = res.data.voucherStats.claimed;
        self.recentClaims = res.data.recentClaims;
        this.$Progress.finish();
      }).catch(err => {
        this.$Progress.fail();
      });
    }
  }
};
</script>
