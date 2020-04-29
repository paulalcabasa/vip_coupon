import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
  mode: 'history',
  base : 'vip_coupon',
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
          path: "/coupon/:action/",
          name: "create-coupon",
          component: () => import("@/views/pages/Coupon.vue"),
          meta: {
            requiresAuth: true,
            title: 'Coupon'
          }
        },
        {
          path: "/coupon/:action/:couponId",
          name: "edit-coupon",
          component: () => import("@/views/pages/Coupon.vue"),
          meta: {
            requiresAuth: true,
            title: 'Coupon'
          }
        },
        {
          path: "/view-coupon/:action/:couponId",
          name: "view-coupon",
          component: () => import("@/views/pages/ViewCoupon.vue"),
          meta: {
            requiresAuth: true,
            title: 'Coupon'
          }
        },
        {
          path: "/coupons",
          name: "all-coupon",
          component: () => import("@/views/pages/AllCoupons.vue"),
          meta: {
            requiresAuth: true,
            title: 'Coupons'
          }
        },
        {
          path: "/approval",
          name: "approval",
          component: () => import("@/views/pages/Approval.vue"),
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
        {
          path: "/payment/request",
          name: "payment-request",
          component: () => import("@/views/pages/PaymentRequest.vue"),
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
        {
          path: "/payments",
          name: "all-payments",
          component: () => import("@/views/pages/AllPayments.vue"),
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
        {
          path: "view-payment-request/:action/:paymentHeaderId",
          name: "view-payment-request",
          component: () => import("@/views/pages/ViewPaymentRequest.vue"),
          meta: {
            requiresAuth: true,
            title: 'Approval'
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
