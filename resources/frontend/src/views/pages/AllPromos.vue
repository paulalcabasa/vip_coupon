<template>
  <div>
    <KTPortlet v-bind:title="'Promo'" >
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
import axios from 'axios';
import axiosRetry from 'axios-retry';
export default {
    name: "blank",
    components: {
        KTPortlet
    },
    data(){
        return {

        }
    },
    mounted() {
        this.loadData();
    },
    created() {

    },
    methods : {
        makeToast(variant = null,body,title) {
            this.$bvToast.toast(body, {
                title: `${title}`,
                variant: variant,
                solid: true
            })
        },
        loadData(){
            var self = this;
            self.$Progress.start();
            axiosRetry(axios, { retries: 3 });

            let promoApi = 'api/promo/';
            const promoReq = axios.get(promoApi);
          
            axios.all([promoReq]).then(axios.spread((...responses) => {
                const promoRes = responses[0];
               
                self.$Progress.finish();
            })).catch(errors => {
                self.makeToast('error',"Failed to load resources, please refresh the page.",'System message');
                self.$Progress.fail();
                // react on errors.
            }).finally(() => {
                
            });
        }
    }

};
</script>