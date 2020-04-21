<template>
  <div>
    <KTPortlet v-bind:title="'Introduction to forms and controls'" >
      <template v-slot:toolbar>
        Toolbar
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
  components: {
    KTPortlet
  },
  created() {

  },
  
};
</script>