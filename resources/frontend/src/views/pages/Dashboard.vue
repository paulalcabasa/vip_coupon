<template>
  <div>
    
    <!-- Statistics -->
    <div class="row">
      <div class="col-lg-4 col-xl-4 order-lg-1 order-xl-1">
        <KTPortlet solidClass="kt-portlet--solid-dark">
          <template v-slot:body>
            <Statistics title="1,500" desc="Requests" icon="flaticon-list" />
          </template>
        </KTPortlet>
      </div>
      <div class="col-lg-4 col-xl-4 order-lg-1 order-xl-1">
        <KTPortlet solidClass="kt-portlet--solid-info">
          <template v-slot:body>
            <Statistics title="25" desc="Pending" icon="flaticon2-refresh" />
          </template>
        </KTPortlet>
      </div>
      <div class="col-lg-4 col-xl-4 order-lg-1 order-xl-1">
        <KTPortlet solidClass="kt-portlet--solid-success">
          <template v-slot:body>
            <Statistics title="975" desc="Printed" icon="flaticon2-printer" />
          </template>
        </KTPortlet>
      </div>
    </div>
    <!-- end of statistics -->

    <div class="row">
      <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">
        <KTPortlet v-bind:title="'Recent Requests'">
          <template v-slot:body>
             <b-table striped hover :items="items" :fields="fields"></b-table>
          </template>
        </KTPortlet>
      </div>
    </div>


 
  </div>
</template>

<script>
import KTPortlet from "@/views/partials/content/Portlet.vue";
import Statistics from "@/views/partials/widgets/Statistics.vue";

export default {
  name: "dashboard",
  components: {
    KTPortlet,
    Statistics
  },
  data() {
    return {
     
      fields: [
         {
           key: 'last_name',
           sortable: true
         },
         {
           key: 'first_name',
           sortable: false
         },
         {
           key: 'age',
           label: 'Person age',
           sortable: true,
           // Variant applies to the whole column, including the header and footer
           
         }
       ],
       items: [
         { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
         { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
         { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },
         { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }
       ]
    };
  },
  mounted() {
    
  },
  methods: {
 
  }
};
</script>
