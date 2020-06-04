<template>
  <div>
    <!--begin: Head -->
    <div
      class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"
      :style="{ backgroundImage: `url(${backgroundImage})` }"
    >
      <div class="kt-user-card__avatar">
        <img
          class="kt-hidden"
          alt="Pic"
          src="@/assets/media/users/300_25.jpg"
        />
        <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
        <span
          class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success"
          >{{ user.first_name.charAt(0) }}</span
        >
      </div>
      <div class="kt-user-card__name">{{ user.first_name + " " + user.last_name}}</div>
      
    </div>
    <!--end: Head -->
    <!--begin: Navigation -->
    <div class="kt-notification">
      <a href="#" class="kt-notification__item">
        <div class="kt-notification__item-icon">
          <i class="flaticon2-calendar-3 kt-font-success"></i>
        </div>
        <div class="kt-notification__item-details">
          <div class="kt-notification__item-title kt-font-bold">My Profile</div>
          <div class="kt-notification__item-time">
            Account settings and more
          </div>
        </div>
      </a>
     
      
   
    
      <div class="kt-notification__custom kt-space-between">
        <a
          href="#"
          v-on:click="onLogout()"
          class="btn btn-label btn-label-brand btn-sm btn-bold"
          >Sign Out</a
        >
       
      </div>
    </div>
    <!--end: Navigation -->
  </div>
</template>

<script>
import { LOGOUT } from "@/store/auth.module";
import jwtService from '@/common/jwt.service.js'
export default {
  name: "KTDropdownUser",
  data (){
    return { 
      user : JSON.parse(jwtService.getUser())
    }
  },
  methods: {
    onLogout() {
      this.$store
        .dispatch(LOGOUT)
        .then(() => this.$router.push({ name: "login" }));
    }
  },
  computed: {
    backgroundImage() {
      return process.env.BASE_URL + "assets/media/misc/bg-1.jpg";
    }
  }
};
</script>
