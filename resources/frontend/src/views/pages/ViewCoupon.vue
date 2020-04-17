<template>
  <div>
    <b-container fluid>
      <b-row>
        <b-col sm="5">
          <KTPortlet v-bind:title="'Coupon'" >
            <template v-slot:toolbar>
              <b-button variant="success" @click.prevent="submitRequest()">Print</b-button>
            </template> 
            <template v-slot:body>
                <b-container fluid>
                    <b-row>
                      <b-col sm="4">
                        <label>Coupon No.</label>
                      </b-col>
                      <b-col sm="8">
                        <span class="kt-font-bold kt-font-info">000001</span>
                      </b-col>
                    </b-row>
                    <b-row>
                      <b-col sm="4">
                        <label>Dealer</label>
                      </b-col>
                      <b-col sm="8">
                        <span class="kt-font-bold kt-font-info">PASIG</span>
                      </b-col>
                    </b-row>
                    <b-row>
                      <b-col sm="4">
                        <label class="text-bold">Created by</label>
                      </b-col>
                      <b-col sm="8">
                        Paul Alcabasa
                      </b-col>
                    </b-row>
                    <b-row>
                      <b-col sm="4">
                        <label class="text-bold">Date Created</label>
                      </b-col>
                      <b-col sm="8">
                        May 29, 2020
                      </b-col>
                    </b-row>
                    <b-row>
                      <b-col sm="4">
                        <label class="text-bold">Status</label>
                      </b-col>
                      <b-col sm="8">
                        <b-badge class="mr-1" variant="secondary">Pending</b-badge>
                      </b-col>
                    </b-row>
                  </b-container>
            </template>
          </KTPortlet>
        </b-col>

        <b-col sm="7">
          <KTPortlet v-bind:title="'Timeline'" >
            <template v-slot:body>
              <Timeline2 v-bind:datasrc="timelines"></Timeline2>
            </template>
          </KTPortlet>
        </b-col>
      </b-row>
    </b-container>

    <KTPortlet v-bind:title="'Denomination'" >
       <template v-slot:toolbar>
        <h5>Total amount : 15,000</h5>
      </template> 
      <template v-slot:body>
          <b-table striped hover :items="items"></b-table>
      </template>
    </KTPortlet>

  </div>
</template>

<script>
import KTPortlet from "@/views/partials/content/Portlet.vue";
import { mapGetters } from "vuex";
import Timeline2 from "@/views/partials/widgets/Timeline2.vue";
import timelines from "@/common/mock/widget-timeline.json";
export default {
  name: "ViewCoupon",
  components: {
    KTPortlet,
    Timeline2
  },
  data(){
    return {
      couponId : null,
      items: [
         { amount: 1000, quantity: '10', cs_numbers: 'CS991, CS223, CS445' },
         { amount: 1000, quantity: '10', cs_numbers: 'CS991, CS223, CS445' },
         { amount: 1000, quantity: '10', cs_numbers: 'CS991, CS223, CS445' },

       ],
       timelines: [
         {
           icon :'fa fa-genderless kt-font-info',
           text : 'Approved by Grace Servanez.',
           time : '15:30'
         },
         {
           icon :'fa fa-genderless kt-font-success',
           text : 'Coupon has been issued by <strong>Regina Copon.</strong>',
           time : '15:30'
         },
         {
           icon :'fa fa-genderless kt-font-success',
           text : 'Received by <strong>Jho Hilario</strong>.',
           time : '15:30'
         },
         {
           icon :'fa fa-genderless kt-font-success',
           text : 'Printed by Jho Hilario.',
           time : '15:30'
         },
         {
           icon :'fa fa-genderless kt-font-success',
           text : 'Received by Pasig dealer.',
           time : '15:30'
         }
       ],
    }
  },
  mounted() {
    this.couponId = this.$route.params.couponId;
    console.log(this.timelines2);

  },
  created() {

  },
  methods: {
    loadData(){
     
    }
  }
  
};
</script>