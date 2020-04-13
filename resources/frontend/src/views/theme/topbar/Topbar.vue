/* eslint-disable */ 
<template>
  <!-- begin:: Header Topbar -->
  <div class="kt-header__topbar">
    <!--begin: Search -->
    <div
      class="kt-header__topbar-item kt-header__topbar-item--search"
      id="kt_quick_search_toggle"
    >
      <div class="kt-header__topbar-wrapper" data-toggle="dropdown">
        <span class="kt-header__topbar-icon">
          <img
            svg-inline
            class="kt-svg-icon"
            src="@/assets/media/icons/svg/General/Search.svg"
            alt=""
          />
        </span>
      </div>
      <div
        class="dropdown-menu dropdown-menu-fit dropdown-menu-lg dropdown-menu-right"
        v-on:click.stop
      >
        <KTSearchDefault></KTSearchDefault>
      </div>
    </div>
    <!--end: Search -->

 

    <!--begin: Quick Actions -->
    <div class="kt-header__topbar-item">
      <div
        class="kt-header__topbar-wrapper"
        id="kt_quick_action_toggle"
        data-toggle="dropdown"
      >
        <span class="kt-header__topbar-icon">
          <img
            svg-inline
            class="kt-svg-icon"
            src="@/assets/media/icons/svg/Media/Equalizer.svg"
            alt=""
          />
        </span>
      </div>
      <div
        class="dropdown-menu dropdown-menu-fit dropdown-menu-xl dropdown-menu-right"
        v-on:click.stop
      >
        <KTDropdownQuickAction></KTDropdownQuickAction>
      </div>
    </div>
    <!--end: Quick Actions -->

    <!--begin: User Bar -->
    <div class="kt-header__topbar-item kt-header__topbar-item--user">
      <div
        class="kt-header__topbar-wrapper"
        id="kt_user_toggle"
        data-toggle="dropdown"
      >
        <div class="kt-header__topbar-user">
          <span class="kt-header__topbar-welcome kt-hidden-mobile">Hi,</span>
          <span class="kt-header__topbar-username kt-hidden-mobile">{{ this.$store.getters.currentUser.first_name }}</span>
          <img
         
            alt="Pic"
            src="@/assets/media/users/default.jpg"
          />
          <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
          <span
        
            class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold"
            >{{ this.$store.getters.currentUser.first_name.charAt(0) }}</span>
        </div>
      </div>
      <div
        class="dropdown-menu dropdown-menu-fit dropdown-menu-xl dropdown-menu-right"
        v-on:click.stop
      >
        <KTDropdownUser></KTDropdownUser>
      </div>
    </div>
    <!--end: User Bar -->
  </div>
  <!-- end:: Header Topbar -->
</template>

<script>
import KTSearchDefault from "@/views/theme/topbar/SearchDefault.vue";
//import KTDropdownNotification from "@/views/theme/topbar/DropdownNotification.vue";
import KTDropdownQuickAction from "@/views/theme/topbar/DropdownQuickAction.vue";
//import KTDropdownMyCart from "@/views/theme/topbar/DropdownMyCart.vue";
//import KTDropdownLanguage from "@/views/theme/topbar/DropdownLanguage.vue";
import KTDropdownUser from "@/views/theme/topbar/DropdownUser.vue";
//import i18nService from "@/common/i18n.service.js";

export default {
  name: "KTTopbar",
  data() {
    return {
      //languageFlag: "",
      //languages: i18nService.languages
     // user_first_name : ''
    };
  },
  mounted() {
  /*   if(this.$store.getters.currentUser) {
      this.user_first_name = getName();
    } */
    //console.log("user",this.$store.getters.currentUser);
    //console.log("user",this.$store.getters.currentUser.first_name);
  },
  components: {
    KTSearchDefault,
  //  KTDropdownNotification,
  KTDropdownQuickAction,
  //  KTDropdownMyCart,
  //  KTDropdownLanguage,
    KTDropdownUser
  },
  methods: {
   /*  onLanguageChanged() {
      this.languageFlag = this.languages.find(val => {
        return val.lang === i18nService.getActiveLanguage();
      }).flag;
    } */
  },
  computed: {
   /* 
    getLanguageFlag() {
      return this.onLanguageChanged();
    } */
  }
};
</script>
