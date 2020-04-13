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
import { SET_BREADCRUMB } from "@/store/breadcrumbs.module";    
import KTPortlet from "@/views/partials/content/Portlet.vue";
import { mapGetters } from "vuex";
export default {
  name: "blank",
  mounted() {
     //  if (!this.isAuthenticated) {
     // this.$router.push({ name: "login" });
  //  }
 // console.log("auth state : " + this.isAuthenticated);
    this.$store.dispatch(SET_BREADCRUMB, [{ title: "Blank" }]);
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