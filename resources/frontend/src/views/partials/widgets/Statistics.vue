<template>
   <div class="media">
        <div class="media-body">
            <h3 class="statistics__number">{{ title }}</h3>
            <span class="statistics__desc">{{ desc }}</span>
        </div>
        <div class="media-icon">
            <span style="font-size:5em;"><i :class="icon"></i></span>
        </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";


export default {
  name: "Statistics",
  components: {
    
  },
  data() {
    return {
    
    };
  },
  props: {
    title: String,
    desc: String,
    icon: String
  },
  mounted() {
  
  },
  computed: {
    ...mapGetters(["layoutConfig"])
  }
};
</script>

<style scoped>

.media {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-align: start;
    align-items: flex-start;
    padding-top:10px;
}

.media-body {
    -ms-flex: 1;
    flex: 1;
   
}

.media-icon {
    margin-left: 1.25rem !important;
    align-self: center !important;
}

h3 {
  font-size:2.75rem;
}
.statistics__desc {
    color: #fff;
    text-transform: uppercase !important;
    font-size: 1.2rem;
    font-weight:500;
}
</style>