(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-bcac1d44"],{"212a":function(t,e,o){"use strict";var s=function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"kt-portlet",class:t.portletClass},[t.hasTitleSlot||t.title?o("div",{staticClass:"kt-portlet__head",class:t.headClass},[o("div",{staticClass:"kt-portlet__head-label"},[t.hasTitleSlot?t._t("title"):t._e(),t.hasTitleSlot?t._e():o("h3",{staticClass:"kt-portlet__head-title"},[t._v(" "+t._s(t.title)+" ")])],2),o("div",{staticClass:"kt-portlet__head-toolbar"},[t._t("toolbar")],2)]):t._e(),o("div",{staticClass:"kt-portlet__body",class:{bodyClass:t.bodyClass,"kt-portlet__body--fit":t.bodyFit,"kt-portlet__body--fluid":t.bodyFluid}},[t._t("body")],2),t.hasFootSlot?o("div",{staticClass:"kt-portlet__foot kt-portlet__body--fit"},[t._t("foot")],2):t._e()])},a=[],n={name:"KTPortlet",props:{title:String,headSize:String,fluidHeight:Boolean,fluidHalfHeight:Boolean,headOverlay:Boolean,headClass:String,bodyClass:String,bodyFit:Boolean,bodyFluid:Boolean,solidClass:String},components:{},methods:{},computed:{portletClass:function(){var t={"kt-portlet--height-fluid":this.fluidHeight,"kt-portlet--height-fluid-half":this.fluidHalfHeight,"kt-portlet--head-overlay":this.headOverlay};return t[this.headSizeClass]=this.headSizeClass,void 0!==this.solidClass&&(t[this.solidClass]=!0),t},hasTitleSlot:function(){return!!this.$slots["title"]},hasFootSlot:function(){return!!this.$slots["foot"]},headSizeClass:function(){return!!this.headSize&&"kt-portlet--head-".concat(this.headSize)}}},i=n,l=o("2877"),r=Object(l["a"])(i,s,a,!1,null,null,null);e["a"]=r.exports},b946:function(t,e,o){"use strict";o.r(e);var s=function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",[t.blockui.state?o("BlockUI",{attrs:{message:t.blockui.msg,html:t.blockui.html}}):t._e(),o("b-row",[o("b-col",{attrs:{sm:"5"}},[o("KTPortlet",{attrs:{title:"Coupon"},scopedSlots:t._u([{key:"toolbar",fn:function(){return[t.isAbleToApprove?o("b-button",{attrs:{size:"sm",variant:"success"},on:{click:t.approve}},[o("i",{staticClass:"flaticon2-check-mark"})]):t._e(),t.isAbleToApprove?o("b-button",{staticClass:"ml-2",attrs:{size:"sm",variant:"danger"},on:{click:t.reject}},[o("i",{staticClass:"flaticon2-cross"})]):t._e(),t.isAbleToPrint?o("b-button",{staticClass:"ml-2",attrs:{size:"sm",variant:"primary"}},[o("i",{staticClass:"flaticon2-print"})]):t._e()]},proxy:!0},{key:"body",fn:function(){return[o("b-container",{attrs:{fluid:""}},[o("b-row",[o("b-col",{attrs:{sm:"4"}},[o("label",[t._v("Coupon No.")])]),o("b-col",{attrs:{sm:"8"}},[o("span",{staticClass:"kt-font-bold kt-font-info"},[t._v(t._s(t.couponDetails.coupon_id))])])],1),o("b-row",[o("b-col",{attrs:{sm:"4"}},[o("label",[t._v("Dealer")])]),o("b-col",{attrs:{sm:"8"}},[o("span",{staticClass:"kt-font-bold kt-font-info"},[t._v(t._s(t.couponDetails.account_name))])])],1),o("b-row",[o("b-col",{attrs:{sm:"4"}},[o("label",{staticClass:"text-bold"},[t._v("Created by")])]),o("b-col",{attrs:{sm:"8"}},[t._v(t._s(t.couponDetails.created_by))])],1),o("b-row",[o("b-col",{attrs:{sm:"4"}},[o("label",{staticClass:"text-bold"},[t._v("Date Created")])]),o("b-col",{attrs:{sm:"8"}},[t._v(t._s(t.couponDetails.date_created))])],1),o("b-row",[o("b-col",{attrs:{sm:"4"}},[o("label",{staticClass:"text-bold"},[t._v("Status")])]),o("b-col",{attrs:{sm:"8"}},[o("b-badge",{staticClass:"mr-1",attrs:{variant:"secondary"}},[t._v(t._s(t.couponDetails.status))])],1)],1)],1)]},proxy:!0}])})],1),o("b-col",{attrs:{sm:"7"}},[o("KTPortlet",{attrs:{title:"Timeline"},scopedSlots:t._u([{key:"body",fn:function(){return[o("Timeline2",{attrs:{datasrc:t.timelines}})]},proxy:!0}])})],1)],1),o("KTPortlet",{attrs:{title:"Denomination"},scopedSlots:t._u([{key:"toolbar",fn:function(){return[o("h5",[t._v("Total amount : "+t._s(t.formatPrice(t.total)))])]},proxy:!0},{key:"body",fn:function(){return[o("b-table",{attrs:{striped:"",hover:"",items:t.denomination},scopedSlots:t._u([{key:"cell(cs_number)",fn:function(e){return[o("span",{domProps:{innerHTML:t._s(e.value)}})]}}])})]},proxy:!0}])})],1)},a=[],n=(o("13d5"),o("b680"),o("d3b7"),o("ac1f"),o("5319"),o("212a")),i=(o("2f62"),function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"kt-timeline-v2"},[o("div",{staticClass:"kt-timeline-v2__items kt-padding-top-25 kt-padding-bottom-30"},[o("perfect-scrollbar",{staticStyle:{"max-height":"100vh",position:"relative"}},[t._l(t.datasrc,(function(e,s){return[o("div",{key:"item-"+s,staticClass:"kt-timeline-v2__item"},[o("div",{staticClass:"kt-timeline-v2__item-cricle"},[o("i",{staticClass:"fa fa-genderless kt-font-info"})]),o("div",{staticClass:"kt-timeline-v2__item-text kt-padding-top-5",class:{"kt-timeline-v2__item-text--bold":e.bold},domProps:{innerHTML:t._s(e.text)}})])]}))],2)],1)])}),l=[],r={name:"timeline-2",components:{},props:{datasrc:Array}},c=r,u=o("2877"),d=Object(u["a"])(c,i,l,!1,null,null,null),p=d.exports,f=o("bc3a"),m=o.n(f),b={name:"ViewCoupon",components:{KTPortlet:n["a"],Timeline2:p},data:function(){return{couponId:null,action:"",couponDetails:[],blockui:{msg:"Please wait",html:'<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>',state:!0},isAbleToApprove:!1,isAbleToPrint:!1,denomination:[],timelines:[]}},mounted:function(){this.couponId=this.$route.params.couponId,this.action=this.$route.params.action,this.loadCouponHeader(),this.loadTimeline(),this.loadDenomination(),console.log(this.action,couponDetails)},created:function(){},computed:{total:function(){return this.denomination.reduce((function(t,e){return parseFloat(t)+parseFloat(e.amount)*parseFloat(e.quantity)}),0)}},methods:{loadCouponHeader:function(){var t=this,e=this;return new Promise((function(o){m.a.get("api/coupon/show/"+t.couponId).then((function(t){e.couponDetails=t.data,o(t)})).catch((function(e){t.$router.push({name:"404"}),o(e)})).finally((function(){e.blockui.state=!1}))}))},loadTimeline:function(){var t=this;return new Promise((function(e){m.a.get("api/timeline/show/"+t.couponId).then((function(o){t.timelines=o.data,e(o)})).catch((function(o){t.$router.push({name:"404"}),e(o)})).finally((function(){t.blockui.state=!1}))}))},loadDenomination:function(){var t=this;return new Promise((function(e){m.a.get("api/denomination/show/"+t.couponId).then((function(o){t.denomination=o.data,e(o)})).catch((function(o){t.$router.push({name:"404"}),e(o)})).finally((function(){t.blockui.state=!1}))}))},formatPrice:function(t){return parseFloat(t).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,"$1,")},approve:function(){var t=this,e=this,o=this.$swal.mixin({customClass:{confirmButton:"btn btn-success",cancelButton:"btn btn-danger"},buttonsStyling:!1});o.fire({title:"Are you sure?",icon:"info",showCancelButton:!0,confirmButtonText:"Yes",cancelButtonText:"Cancel"}).then((function(s){s.value&&(e.blockui.state=!0,m.a.post("api/coupon/approve",{couponId:e.couponId,userId:e.$store.getters.currentUser.user_id,userSource:e.$store.getters.currentUser.user_source_id,status:2,action:1}).then((function(e){e.data.error||(o.fire("Approved!",e.data.message,"success"),t.loadCouponHeader(),t.loadTimeline())})).catch((function(t){o.fire("System message",res.data.message,"error")})).finally((function(){e.blockui.state=!1})))}))},reject:function(){var t=this,e=this,o=this.$swal.mixin({customClass:{confirmButton:"btn btn-success",cancelButton:"btn btn-danger"},buttonsStyling:!1});o.fire({title:"Are you sure?",icon:"info",showCancelButton:!0,confirmButtonText:"Yes",cancelButtonText:"Cancel"}).then((function(s){s.value&&(e.blockui.state=!0,m.a.post("api/coupon/reject",{couponId:e.couponId,userId:e.$store.getters.currentUser.user_id,userSource:e.$store.getters.currentUser.user_source_id,status:6,action:9}).then((function(e){e.data.error||(o.fire("Rejected",e.data.message,"error"),t.loadCouponHeader(),t.loadTimeline())})).catch((function(t){o.fire("System message",res.data.message,"error")})).finally((function(){e.blockui.state=!1})))}))}}},h=b,_=Object(u["a"])(h,s,a,!1,null,null,null);e["default"]=_.exports}}]);
//# sourceMappingURL=chunk-bcac1d44.a903310e.js.map