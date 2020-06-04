<template>
  <ul class="kt-menu__nav">
    <template v-for="(menu, i) in menuItems">
      <KTMenuItem v-if="!menu.section" :menu="menu" :key="i"></KTMenuItem>
      <KTMenuSection v-if="menu.section" :menu="menu" :key="i"></KTMenuSection>
    </template>
  </ul>
</template>

<script>
import KTMenuItem from "@/views/theme/aside/MenuItem.vue";
import KTMenuSection from "@/views/theme/aside/MenuSection.vue";
import menuConfig from "@/common/config/menu.config.json";
import jwtService from "@/common/jwt.service.js";

export default {
  name: "KTAsideMenu",
  components: {
    KTMenuItem,
    KTMenuSection
  },
  data : {
    user : ''
  },
  mounted(){
   
  },
  computed: {
    menuItems: () => {
      let allMenu = menuConfig.aside.items;
      let filteredMenu = [];
      var user = JSON.parse(jwtService.getUser());

      allMenu.map(function(menu){
        if(menu.users.includes(user.user_type_name)){
          filteredMenu.push(menu);
        }
        
      });
      return filteredMenu;
    },
  }
};
</script>
