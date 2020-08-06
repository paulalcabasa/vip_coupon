import Vue from "vue";
import Router from "vue-router";
import Base from "@/views/theme/Base";
import Dashboard from "@/views/pages/Dashboard.vue";
import Coupon from "@/views/pages/Coupon.vue";
import ViewCoupon from "@/views/pages/ViewCoupon.vue";
import AllCoupons from "@/views/pages/AllCoupons.vue";
import Approval from "@/views/pages/Approval.vue";
import ClaimRequest from "@/views/pages/ClaimRequest.vue";
import ClaimRequests from "@/views/pages/ClaimRequests.vue";
import ViewClaimRequest from "@/views/pages/ViewClaimRequest.vue";
import AllPromo from "@/views/pages/AllPromos.vue";
import AllPurpose from "@/views/pages/AllPurpose.vue";
import Claims from "@/views/pages/Claims.vue";
import VoucherSummary from "@/views/pages/VoucherSummary.vue";

import Auth from "@/views/pages/auth/Auth";
import Login from "@/views/pages/auth/Login";
import Register from "@/views/pages/auth/Register";
import Error404 from "@/views/pages/error/Error-1.vue";



Vue.use(Router);

export default new Router({
  mode: 'hash',
  base : 'vip_coupon',
  routes: [
    {
      path: "/",
      redirect: "/dashboard",
      component: Base,
     
      children: [
        {
          path: "/dashboard",
          name: "dashboard",
          component: Dashboard,
          meta: {
            requiresAuth: true,
            title: 'Dashboard'
          }
        },
        {
          path: "/coupon/:action/",
          name: "create-coupon",
          component : Coupon,
          props : true,
          meta: {
            requiresAuth: true,
            title: 'Coupon'
          }
        },
        {
          path: "/coupon/:action/:couponId",
          name: "edit-coupon",
          component: Coupon,
          props: true,
          meta: {
            requiresAuth: true,
            title: 'Coupon'
          }
        },
        {
          path: "/view-coupon/:action/:couponId",
          name: "view-coupon",
          props: true,
          component: ViewCoupon,
          meta: {
            requiresAuth: true,
            title: 'Coupon'
          }
        },
        {
          path: "/coupons",
          name: "all-coupon",
          component: AllCoupons,
          meta: {
            requiresAuth: true,
            title: 'Coupons'
          }
        },
        {
          path: "/approval",
          name: "approval",
          component: Approval,
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
        {
          path: "/claim/request",
          name: "claim-request",
          component: ClaimRequest,
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
        {
          path: "/claim-requests",
          name: "claim-requests",
          component: ClaimRequests,
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
        {
          path: "view-claim-request/:claimHeaderId",
          name: "view-claim-request",
          component: ViewClaimRequest,
          props: true,
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
      
        {
          path: "promo",
          name: "promo",
          component: AllPromo,
          meta: {
            requiresAuth: true,
            title: 'Promo'
          }
        },
        {
          path: "purpose",
          name: "purpose",
          component: AllPurpose,
          meta: {
            requiresAuth: true,
            title: 'Purpose'
          }
        },
        {
          path: "claims",
          name: "Claims",
          component: Claims,
          meta: {
            requiresAuth: true,
            title: 'Claims'
          }
        },
        {
          path: "voucher-summary",
          name: "voucher-summary",
          component: VoucherSummary,
          meta: {
            requiresAuth: true,
            title: 'Voucher Summary'
          }
        },
      ]
    },

    {
      path: "/",
      component: Auth,
      children: [
        {
          name: "login",
          path: "/login",
          component: Login
        },
        {
          name: "register",
          path: "/register",
          component: Register
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
      component: Error404
    } 
  ]
});
