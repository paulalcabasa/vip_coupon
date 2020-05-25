import Vue from "vue";
import Router from "vue-router";
import Base from "@/views/theme/Base";
import Dashboard from "@/views/pages/Dashboard.vue";
import Coupon from "@/views/pages/Coupon.vue";
import ViewCoupon from "@/views/pages/ViewCoupon.vue";
import AllCoupons from "@/views/pages/AllCoupons.vue";
import Approval from "@/views/pages/Approval.vue";
import PaymentRequest from "@/views/pages/PaymentRequest.vue";
import AllPayments from "@/views/pages/AllPayments.vue";
import ViewPaymentRequest from "@/views/pages/ViewPaymentRequest.vue";

import Auth from "@/views/pages/auth/Auth";
import Login from "@/views/pages/auth/Login";
import Register from "@/views/pages/auth/Register";
import Error404 from "@/views/pages/error/Error-1.vue";



Vue.use(Router);

export default new Router({
  mode: 'history',
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
          path: "/payment/request",
          name: "payment-request",
          component: PaymentRequest,
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
        {
          path: "/payments",
          name: "all-payments",
          component: AllPayments,
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
        {
          path: "view-payment-request/:action/:paymentHeaderId",
          name: "view-payment-request",
          component: ViewPaymentRequest,
          props: true,
          meta: {
            requiresAuth: true,
            title: 'Approval'
          }
        },
        {
          path: "/payments/approval",
          name: "payments-approval",
          component: AllPayments,
          meta: {
            requiresAuth: true,
            title: 'Approval'
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
