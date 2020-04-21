<template>
  <div class="kt-timeline-v2">
    <div class="kt-timeline-v2__items kt-padding-top-25 kt-padding-bottom-30">
      <perfect-scrollbar style="max-height: 100vh; position: relative;">
        <template v-for="(item, i) in datasrc">
          <div class="kt-timeline-v2__item" :key="`item-${i}`">
          
            <div class="kt-timeline-v2__item-cricle">
              <i class="fa fa-genderless kt-font-info"></i>
            </div>
            <div
              class="kt-timeline-v2__item-text kt-padding-top-5"
              v-bind:class="{ 'kt-timeline-v2__item-text--bold': item.bold }"
              v-html="item.text"
            ></div>
            
          </div>
        </template>
      </perfect-scrollbar>
    </div>
  </div>
</template>

<script>
export default {
  name: "timeline-2",
  components: {},
  props: {
    datasrc: Array
  }
};
</script>
