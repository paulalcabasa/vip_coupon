(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-593108cc"],{"212a":function(e,t,a){"use strict";var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"kt-portlet",class:e.portletClass},[e.hasTitleSlot||e.title?a("div",{staticClass:"kt-portlet__head",class:e.headClass},[a("div",{staticClass:"kt-portlet__head-label"},[e.hasTitleSlot?e._t("title"):e._e(),e.hasTitleSlot?e._e():a("h3",{staticClass:"kt-portlet__head-title"},[e._v(" "+e._s(e.title)+" ")])],2),a("div",{staticClass:"kt-portlet__head-toolbar"},[e._t("toolbar")],2)]):e._e(),a("div",{staticClass:"kt-portlet__body",class:{bodyClass:e.bodyClass,"kt-portlet__body--fit":e.bodyFit,"kt-portlet__body--fluid":e.bodyFluid}},[e._t("body")],2),e.hasFootSlot?a("div",{staticClass:"kt-portlet__foot kt-portlet__body--fit"},[e._t("foot")],2):e._e()])},o=[],s={name:"KTPortlet",props:{title:String,headSize:String,fluidHeight:Boolean,fluidHalfHeight:Boolean,headOverlay:Boolean,headClass:String,bodyClass:String,bodyFit:Boolean,bodyFluid:Boolean},components:{},methods:{},computed:{portletClass:function(){var e={"kt-portlet--height-fluid":this.fluidHeight,"kt-portlet--height-fluid-half":this.fluidHalfHeight,"kt-portlet--head-overlay":this.headOverlay};return e[this.headSizeClass]=this.headSizeClass,e},hasTitleSlot:function(){return!!this.$slots["title"]},hasFootSlot:function(){return!!this.$slots["foot"]},headSizeClass:function(){return!!this.headSize&&"kt-portlet--head-".concat(this.headSize)}}},l=s,i=a("2877"),r=Object(i["a"])(l,n,o,!1,null,null,null);t["a"]=r.exports},"5cf3":function(e,t,a){"use strict";var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("KTPortlet",{attrs:{title:e.title},scopedSlots:e._u([e.hasTitleSlot?{key:"title",fn:function(){return[a("h3",{staticClass:"kt-portlet__head-title"},[e._t("title")],2)]},proxy:!0}:null,{key:"toolbar",fn:function(){return[a("button",{directives:[{name:"b-tooltip",rawName:"v-b-tooltip.hover",modifiers:{hover:!0}}],staticClass:"btn btn-clean btn-sm btn-icon btn-icon-md",class:{active:e.isOpen},attrs:{type:"button",title:"Show codes"},on:{click:function(t){return e.toggle()}}},[a("i",{staticClass:"la la-code"})])]},proxy:!0},{key:"body",fn:function(){return[a("transition",{attrs:{name:"fade"}},[e.isOpen?a("div",{staticClass:"kt-portlet__code",class:{"kt-portlet__code--show":e.isOpen}},[a("button",{directives:[{name:"b-tooltip",rawName:"v-b-tooltip.hover",modifiers:{hover:!0}}],staticClass:"btn btn-clean btn-sm btn-icon btn-icon-md float-right",attrs:{href:"#",title:"Copy codes"},on:{click:function(t){return e.copyCode(t)}}},[a("i",{staticClass:"la la-copy"})]),e.hasGeneralCode?a("div",[e._t("code")],2):e._e(),e.hasGeneralCode||e.hasSingleCodeType?e._e():a("ul",{staticClass:"nav nav-tabs nav-tabs-line",attrs:{role:"tablist"}},[e.hasHtmlCode?a("li",{staticClass:"nav-item"},[a("a",{staticClass:"nav-link active",attrs:{"data-tab":"0","data-toggle":"tab",href:"#",role:"tab","aria-selected":"true"},on:{click:e.setActiveTab}},[e._v("HTML")])]):e._e(),e.hasJsCode?a("li",{staticClass:"nav-item"},[a("a",{staticClass:"nav-link",attrs:{"data-tab":"1","data-toggle":"tab",href:"#",role:"tab","aria-selected":"false"},on:{click:e.setActiveTab}},[e._v(" JS ")])]):e._e(),e.hasScssCode?a("li",{staticClass:"nav-item"},[a("a",{staticClass:"nav-link",attrs:{"data-tab":"2","data-toggle":"tab",href:"#",role:"tab","aria-selected":"false"},on:{click:e.setActiveTab}},[e._v("SCSS")])]):e._e()]),e.hasGeneralCode||e.hasSingleCodeType?e._e():a("div",[a("b-tabs",{staticClass:"kt-hide-tabs",attrs:{"content-class":"mt-3"},model:{value:e.tabIndex,callback:function(t){e.tabIndex=t},expression:"tabIndex"}},[a("b-tab",{attrs:{active:""}},[a("highlight-code",{attrs:{lang:"html"}},[e._t("html")],2)],1),a("b-tab",[a("highlight-code",{attrs:{lang:"js"}},[e._t("js")],2)],1),a("b-tab",[a("highlight-code",{attrs:{lang:"scss"}},[e._t("scss")],2)],1)],1)],1),e.hasSingleCodeType?a("div",[e.hasHtmlCode?a("highlight-code",{attrs:{lang:"html"}},[e._t("html")],2):e._e(),e.hasJsCode?a("highlight-code",{attrs:{lang:"js"}},[e._t("js")],2):e._e(),e.hasScssCode?a("highlight-code",{attrs:{lang:"scss"}},[e._t("scss")],2):e._e()],1):e._e()]):e._e()]),e._t("preview")]},proxy:!0}],null,!0)})},o=[],s=(a("4160"),a("212a")),l=a("f1cd"),i=a.n(l),r={name:"KTCodePreview",props:{title:String},data:function(){return{tabIndex:0,isOpen:!1}},components:{KTPortlet:s["a"]},methods:{setActiveTab:function(e){for(var t=e.target.closest('[role="tablist"]'),a=t.querySelectorAll('[data-toggle="tab"]'),n=0;n<a.length;n++)a[n].classList.remove("active");e.target.classList.add("active"),this.tabIndex=parseInt(e.target.getAttribute("data-tab"))},toggle:function(){this.isOpen=!this.isOpen},copyCode:function(e){var t=e.target.closest(".kt-portlet__code"),a=t.querySelectorAll(".kt-portlet__code .tab-pane.active")[0];a="undefined"!==typeof a?a.textContent:t.textContent,i()(a)}},computed:{hasTitleSlot:function(){return!!this.$slots["title"]},hasSingleCodeType:function(){var e=this,t=0;return["html","js","scss"].forEach((function(a){e.$slots.hasOwnProperty(a)&&t++})),1===t},hasGeneralCode:function(){return!!this.$slots["code"]},hasJsCode:function(){return!!this.$slots["js"]},hasScssCode:function(){return!!this.$slots["scss"]},hasHtmlCode:function(){return!!this.$slots["html"]}}},d=r,c=(a("afce"),a("2877")),f=Object(c["a"])(d,n,o,!1,null,"e705e41c",null);t["a"]=f.exports},a96e:function(e,t,a){},afce:function(e,t,a){"use strict";var n=a("a96e"),o=a.n(n);o.a},e2b8:function(e,t,a){"use strict";a.r(t);var n=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("b-alert",{staticClass:"alert alert-elevate",attrs:{show:"",variant:"light"}},[a("div",{staticClass:"alert-icon"},[a("i",{staticClass:"flaticon-warning kt-font-brand"})]),a("div",{staticClass:"alert-text"},[a("b",[e._v("Tables")]),e._v(" For displaying tabular data, <b-table> supports pagination, filtering, sorting, custom rendering, various style options, events, and asynchronous data. For simple display of tabular data without all the fancy features, BootstrapVue provides two lightweight alternative components <b-table-lite> and <b-table-simple>. "),a("a",{staticClass:"kt-link kt-link--brand kt-font-bold",attrs:{href:"https://bootstrap-vue.js.org/docs/components/table",target:"_blank"}},[e._v(" See documentation. ")])])]),a("div",{staticClass:"row"},[a("div",{staticClass:"col-md-12"},[a("KTCodePreview",{attrs:{title:"Basic usage"},scopedSlots:e._u([{key:"preview",fn:function(){return[a("div",[a("b-table",{attrs:{striped:"",hover:"",items:e.code1.items}})],1)]},proxy:!0},{key:"html",fn:function(){return[e._v(" "+e._s(e.code1.html)+" ")]},proxy:!0},{key:"js",fn:function(){return[e._v(" "+e._s(e.code1.js)+" ")]},proxy:!0}])}),a("KTCodePreview",{attrs:{title:"Using variants for table cells"},scopedSlots:e._u([{key:"preview",fn:function(){return[a("p",[e._v(" Record data may also have additional special reserved name keys for colorizing rows and individual cells (variants), and for triggering additional row detail. The supported optional item record modifier properties (make sure your field keys do not conflict with these names): ")]),a("div",[a("b-table",{attrs:{hover:"",items:e.code2.items}})],1)]},proxy:!0},{key:"html",fn:function(){return[e._v(" "+e._s(e.code2.html)+" ")]},proxy:!0},{key:"js",fn:function(){return[e._v(" "+e._s(e.code2.js)+" ")]},proxy:!0}])}),a("KTCodePreview",{attrs:{title:"Fields as a simple array"},scopedSlots:e._u([{key:"preview",fn:function(){return[a("p",[e._v(" Fields can be a simple array, for defining the order of the columns, and which columns to display: ")]),a("div",[a("b-table",{attrs:{striped:"",hover:"",items:e.code3.items,fields:e.code3.fields}})],1)]},proxy:!0},{key:"html",fn:function(){return[e._v(" "+e._s(e.code3.html)+" ")]},proxy:!0},{key:"js",fn:function(){return[e._v(" "+e._s(e.code3.js)+" ")]},proxy:!0}])}),a("KTCodePreview",{attrs:{title:"Fields as an array of objects"},scopedSlots:e._u([{key:"preview",fn:function(){return[a("p",[e._v(" Fields can be a an array of objects, providing additional control over the fields (such as sorting, formatting, etc). Only columns (keys) that appear in the fields array will be shown: ")]),a("div",[a("b-table",{attrs:{striped:"",hover:"",items:e.code4.items,fields:e.code4.fields}})],1)]},proxy:!0},{key:"html",fn:function(){return[e._v(" "+e._s(e.code4.html)+" ")]},proxy:!0},{key:"js",fn:function(){return[e._v(" "+e._s(e.code4.js)+" ")]},proxy:!0}])}),a("KTCodePreview",{attrs:{title:"Table style options"},scopedSlots:e._u([{key:"preview",fn:function(){return[a("p",[e._v(" Table style options "),a("code",[e._v("fixed")]),e._v(", "),a("code",[e._v("stacked")]),e._v(", "),a("code",[e._v("caption-top")]),e._v(", "),a("code",[e._v("no-border-collapse")]),e._v(", sticky headers, sticky columns, and the table sorting feature, all require BootstrapVue's custom CSS. ")]),a("div",[a("b-form-group",{attrs:{label:"Table Options","label-cols-lg":"2"}},[a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.striped,callback:function(t){e.$set(e.code5,"striped",t)},expression:"code5.striped"}},[e._v(" Striped ")]),a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.bordered,callback:function(t){e.$set(e.code5,"bordered",t)},expression:"code5.bordered"}},[e._v(" Bordered ")]),a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.borderless,callback:function(t){e.$set(e.code5,"borderless",t)},expression:"code5.borderless"}},[e._v(" Borderless ")]),a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.outlined,callback:function(t){e.$set(e.code5,"outlined",t)},expression:"code5.outlined"}},[e._v(" Outlined ")]),a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.small,callback:function(t){e.$set(e.code5,"small",t)},expression:"code5.small"}},[e._v(" Small ")]),a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.hover,callback:function(t){e.$set(e.code5,"hover",t)},expression:"code5.hover"}},[e._v(" Hover ")]),a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.dark,callback:function(t){e.$set(e.code5,"dark",t)},expression:"code5.dark"}},[e._v(" Dark ")]),a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.fixed,callback:function(t){e.$set(e.code5,"fixed",t)},expression:"code5.fixed"}},[e._v(" Fixed ")]),a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.footClone,callback:function(t){e.$set(e.code5,"footClone",t)},expression:"code5.footClone"}},[e._v(" Foot Clone ")]),a("b-form-checkbox",{attrs:{inline:""},model:{value:e.code5.noCollapse,callback:function(t){e.$set(e.code5,"noCollapse",t)},expression:"code5.noCollapse"}},[e._v(" No border collapse ")])],1),a("b-form-group",{attrs:{label:"Head Variant","label-cols-lg":"2"}},[a("b-form-radio-group",{staticClass:"mt-lg-2",model:{value:e.code5.headVariant,callback:function(t){e.$set(e.code5,"headVariant",t)},expression:"code5.headVariant"}},[a("b-form-radio",{attrs:{value:null,inline:""}},[e._v("None")]),a("b-form-radio",{attrs:{value:"light",inline:""}},[e._v("Light")]),a("b-form-radio",{attrs:{value:"dark",inline:""}},[e._v("Dark")])],1)],1),a("b-form-group",{attrs:{label:"Table Variant","label-for":"table-style-variant","label-cols-lg":"2"}},[a("b-form-select",{attrs:{options:e.code5.tableVariants,id:"table-style-variant"},scopedSlots:e._u([{key:"first",fn:function(){return[a("option",{attrs:{value:""}},[e._v("-- None --")])]},proxy:!0}]),model:{value:e.code5.tableVariant,callback:function(t){e.$set(e.code5,"tableVariant",t)},expression:"code5.tableVariant"}})],1),a("b-table",{attrs:{striped:e.code5.striped,bordered:e.code5.bordered,borderless:e.code5.borderless,outlined:e.code5.outlined,small:e.code5.small,hover:e.code5.hover,dark:e.code5.dark,fixed:e.code5.fixed,"foot-clone":e.code5.footClone,"no-border-collapse":e.code5.noCollapse,items:e.code5.items,fields:e.code5.fields,"head-variant":e.code5.headVariant,"table-variant":e.code5.tableVariant}})],1)]},proxy:!0},{key:"html",fn:function(){return[e._v(" "+e._s(e.code5.html)+" ")]},proxy:!0},{key:"js",fn:function(){return[e._v(" "+e._s(e.code5.js)+" ")]},proxy:!0}])})],1)])],1)},o=[],s=a("5cf3"),l=a("bf23"),i={data:function(){return{code1:{html:'<div>\n    <b-table striped hover :items="items"></b-table>\n  </div>',js:"export default {\n    data() {\n      return {\n        items: [\n          { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },\n          { age: 21, first_name: 'Larsen', last_name: 'Shaw' },\n          { age: 89, first_name: 'Geneva', last_name: 'Wilson' },\n          { age: 38, first_name: 'Jami', last_name: 'Carney' }\n        ]\n      }\n    }\n  }",items:[{age:40,first_name:"Dickerson",last_name:"Macdonald"},{age:21,first_name:"Larsen",last_name:"Shaw"},{age:89,first_name:"Geneva",last_name:"Wilson"},{age:38,first_name:"Jami",last_name:"Carney"}]},code2:{html:'<div>\n    <b-table hover :items="items"></b-table>\n  </div>',js:"export default {\n    data() {\n      return {\n        items: [\n          { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },\n          { age: 21, first_name: 'Larsen', last_name: 'Shaw' },\n          {\n            age: 89,\n            first_name: 'Geneva',\n            last_name: 'Wilson',\n            _rowVariant: 'danger'\n          },\n          {\n            age: 40,\n            first_name: 'Thor',\n            last_name: 'MacDonald',\n            _cellVariants: { age: 'info', first_name: 'warning' }\n          },\n          { age: 29, first_name: 'Dick', last_name: 'Dunlap' }\n        ]\n      }\n    }\n  }",items:[{age:40,first_name:"Dickerson",last_name:"Macdonald"},{age:21,first_name:"Larsen",last_name:"Shaw"},{age:89,first_name:"Geneva",last_name:"Wilson",_rowVariant:"danger"},{age:40,first_name:"Thor",last_name:"MacDonald",_cellVariants:{age:"info",first_name:"warning"}},{age:29,first_name:"Dick",last_name:"Dunlap"}]},code3:{html:'<div>\n    <b-table striped hover :items="items" :fields="fields"></b-table>\n  </div>',js:"export default {\n    data() {\n      return {\n        // Note `isActive` is left out and will not appear in the rendered table\n        fields: ['first_name', 'last_name', 'age'],\n        items: [\n          { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },\n          { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },\n          { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },\n          { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }\n        ]\n      }\n    }\n  }",fields:["first_name","last_name","age"],items:[{isActive:!0,age:40,first_name:"Dickerson",last_name:"Macdonald"},{isActive:!1,age:21,first_name:"Larsen",last_name:"Shaw"},{isActive:!1,age:89,first_name:"Geneva",last_name:"Wilson"},{isActive:!0,age:38,first_name:"Jami",last_name:"Carney"}]},code4:{html:'<div>\n    <b-table striped hover :items="items" :fields="fields"></b-table>\n  </div>',js:"export default {\n    data() {\n      return {\n        // Note 'isActive' is left out and will not appear in the rendered table\n        fields: [\n          {\n            key: 'last_name',\n            sortable: true\n          },\n          {\n            key: 'first_name',\n            sortable: false\n          },\n          {\n            key: 'age',\n            label: 'Person age',\n            sortable: true,\n            // Variant applies to the whole column, including the header and footer\n            variant: 'danger'\n          }\n        ],\n        items: [\n          { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },\n          { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },\n          { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },\n          { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }\n        ]\n      }\n    }\n  }",fields:[{key:"last_name",sortable:!0},{key:"first_name",sortable:!1},{key:"age",label:"Person age",sortable:!0,variant:"danger"}],items:[{isActive:!0,age:40,first_name:"Dickerson",last_name:"Macdonald"},{isActive:!1,age:21,first_name:"Larsen",last_name:"Shaw"},{isActive:!1,age:89,first_name:"Geneva",last_name:"Wilson"},{isActive:!0,age:38,first_name:"Jami",last_name:"Carney"}]},code5:{html:'<div>\n    <b-form-group label="Table Options" label-cols-lg="2">\n      <b-form-checkbox v-model="striped" inline>Striped</b-form-checkbox>\n      <b-form-checkbox v-model="bordered" inline>Bordered</b-form-checkbox>\n      <b-form-checkbox v-model="borderless" inline>Borderless</b-form-checkbox>\n      <b-form-checkbox v-model="outlined" inline>Outlined</b-form-checkbox>\n      <b-form-checkbox v-model="small" inline>Small</b-form-checkbox>\n      <b-form-checkbox v-model="hover" inline>Hover</b-form-checkbox>\n      <b-form-checkbox v-model="dark" inline>Dark</b-form-checkbox>\n      <b-form-checkbox v-model="fixed" inline>Fixed</b-form-checkbox>\n      <b-form-checkbox v-model="footClone" inline>Foot Clone</b-form-checkbox>\n      <b-form-checkbox v-model="noCollapse" inline>No border collapse</b-form-checkbox>\n    </b-form-group>\n    <b-form-group label="Head Variant" label-cols-lg="2">\n      <b-form-radio-group v-model="headVariant" class="mt-lg-2">\n        <b-form-radio :value="null" inline>None</b-form-radio>\n        <b-form-radio value="light" inline>Light</b-form-radio>\n        <b-form-radio value="dark" inline>Dark</b-form-radio>\n      </b-form-radio-group>\n    </b-form-group>\n    <b-form-group label="Table Variant" label-for="table-style-variant" label-cols-lg="2">\n      <b-form-select\n        v-model="tableVariant"\n        :options="tableVariants"\n        id="table-style-variant"\n      >\n        <template v-slot:first>\n          <option value="">-- None --</option>\n        </template>\n      </b-form-select>\n    </b-form-group>\n\n    <b-table\n      :striped="striped"\n      :bordered="bordered"\n      :borderless="borderless"\n      :outlined="outlined"\n      :small="small"\n      :hover="hover"\n      :dark="dark"\n      :fixed="fixed"\n      :foot-clone="footClone"\n      :no-border-collapse="noCollapse"\n      :items="items"\n      :fields="fields"\n      :head-variant="headVariant"\n      :table-variant="tableVariant"\n    ></b-table>\n  </div>',js:"export default {\n    data() {\n      return {\n        fields: ['first_name', 'last_name', 'age'],\n        items: [\n          { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },\n          { age: 21, first_name: 'Larsen', last_name: 'Shaw' },\n          { age: 89, first_name: 'Geneva', last_name: 'Wilson' }\n        ],\n        tableVariants: [\n          'primary',\n          'secondary',\n          'info',\n          'danger',\n          'warning',\n          'success',\n          'light',\n          'dark'\n        ],\n        striped: false,\n        bordered: false,\n        borderless: false,\n        outlined: false,\n        small: false,\n        hover: false,\n        dark: false,\n        fixed: false,\n        footClone: false,\n        headVariant: null,\n        tableVariant: '',\n        noCollapse: false\n      }\n    }\n  }",fields:["first_name","last_name","age"],items:[{age:40,first_name:"Dickerson",last_name:"Macdonald"},{age:21,first_name:"Larsen",last_name:"Shaw"},{age:89,first_name:"Geneva",last_name:"Wilson"}],tableVariants:["primary","secondary","info","danger","warning","success","light","dark"],striped:!1,bordered:!1,borderless:!1,outlined:!1,small:!1,hover:!1,dark:!1,fixed:!1,footClone:!1,headVariant:null,tableVariant:"",noCollapse:!1}}},components:{KTCodePreview:s["a"]},mounted:function(){this.$store.dispatch(l["a"],[{title:"Vue Bootstrap",route:"alert"},{title:""}])}},r=i,d=a("2877"),c=Object(d["a"])(r,n,o,!1,null,null,null);t["default"]=c.exports},f1cd:function(e,t){function a(e){if(navigator.clipboard)return navigator.clipboard.writeText(e).catch((function(e){throw void 0!==e?e:new DOMException("The request is not allowed","NotAllowedError")}));var t=document.createElement("span");t.textContent=e,t.style.whiteSpace="pre",document.body.appendChild(t);var a=window.getSelection(),n=window.document.createRange();a.removeAllRanges(),n.selectNode(t),a.addRange(n);var o=!1;try{o=window.document.execCommand("copy")}catch(s){console.log("error",s)}return a.removeAllRanges(),window.document.body.removeChild(t),o?Promise.resolve():Promise.reject(new DOMException("The request is not allowed","NotAllowedError"))}e.exports=a}}]);
//# sourceMappingURL=chunk-593108cc.c21ca3e6.js.map