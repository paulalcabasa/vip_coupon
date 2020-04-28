<template>
  <div>

    <b-row>
      <b-col sm="6">
        <KTPortlet v-bind:title="'Request for payment'" >
          <template v-slot:toolbar>
            <b-button variant="success" @click.prevent="submit()">Upload</b-button>
          </template> 

          <template v-slot:body>
            <b-form-file
                v-model="file"
                :state="Boolean(file)"
                placeholder="Choose a file or drop it here..."
                drop-placeholder="Drop file here..."
              ></b-form-file>
          </template>

        </KTPortlet>
      </b-col>
    </b-row>
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
         file: null,
       file2: null
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