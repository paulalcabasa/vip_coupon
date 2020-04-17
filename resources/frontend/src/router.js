import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
  mode: 'history',
  routes: [
    {
      path: "/",
      redirect: "/dashboard",
      component: () => import("@/views/theme/Base"),
     
      children: [
        {
          path: "/dashboard",
          name: "dashboard",
          component: () => import("@/views/pages/Dashboard.vue"),
          meta: {
            requiresAuth: true,
            title: 'Dashboard'
          }
        },
        {
          path: "/coupon",
          name: "coupon",
          component: () => import("@/views/pages/Coupon.vue"),
          meta: {
            requiresAuth: true,
            title: 'Coupon'
          }
        },
        {
          path: "/coupon/view/:couponId",
          name: "view-coupon",
          component: () => import("@/views/pages/ViewCoupon.vue"),
          meta: {
            requiresAuth: true,
            title: 'Coupon'
          }
        },
      ]
    },

    {
      path: "/",
      component: () => import("@/views/pages/auth/Auth"),
      children: [
        {
          name: "login",
          path: "/login",
          component: () => import("@/views/pages/auth/Login")
        },
        {
          name: "register",
          path: "/register",
          component: () => import("@/views/pages/auth/Register")
        }
      ]
    },
    {
      path: "*",
      redirect: "/404"
    },
    {
      // the 404 route, when none of the above matches
      path: "/404",
      name: "404",
      component: () => import("@/views/pages/error/Error-1.vue")
    } 
  ]
});
