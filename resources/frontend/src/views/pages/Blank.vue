<template>
  <div>
    <h1>blank page</h1>
    <p>THIS IS THE NEWS : {{ this.$store.getters.getNews }}</p>
  </div>
</template>

<script>
import { SET_BREADCRUMB } from "@/store/breadcrumbs.module";    
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
  created() {

  }
};
</script>