<template>
  <div>
    <KTPortlet v-bind:title="'Request for payment'" >
      <template v-slot:toolbar>
        <b-button variant="primary" class='mr-2' @click.prevent="importExcel()">Import</b-button>
        <b-button variant="success" @click.prevent="submit()">Submit</b-button>
      </template> 

      <template v-slot:body>
        Body
      </template>

      <template v-slot:foot>
        Footer
      </template>
    </KTPortlet>
  </div>
</template>

<script>

import KTPortlet from "@/views/partials/content/Portlet.vue";

export default {
  name: "blank",
  mounted() {
 
  },
  data(){
    return {

    }
  },
  methods: {
    submit(){
        alert("submit");
    },
    importExcel(){

    }
  },
  components: {
    KTPortlet
  },
  created() {

  },
  
};
</script>