(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-40612c9a"],{"121f":function(e,i,t){"use strict";t.d(i,"a",(function(){return n})),t.d(i,"d",(function(){return s})),t.d(i,"c",(function(){return a})),t.d(i,"b",(function(){return o})),t.d(i,"e",(function(){return c}));var r=t("751a");function n(e){return Object(r["a"])({url:"/api/verify/checkisverifier",data:e,forceLogin:!0})}function s(e){return Object(r["a"])({url:"/api/verify/verifyInfo",data:e,forceLogin:!0})}function a(e){return Object(r["a"])({url:"/api/verify/verify",data:e,forceLogin:!0})}function o(e){return Object(r["a"])({url:"/api/verify/getVerifyType",data:e})}function c(e){return Object(r["a"])({url:"/api/verify/lists",data:e,forceLogin:!0})}},"1ba8":function(e,i,t){},7349:function(e,i,t){"use strict";var r=t("1ba8"),n=t.n(r);n.a},"852a":function(e,i,t){"use strict";t.r(i);var r=function(){var e=this,i=e.$createElement,t=e._self._c||i;return t("el-card",{staticClass:"box-card order-list"},[t("div",{staticClass:"clearfix",attrs:{slot:"header"},slot:"header"},[t("el-breadcrumb",{attrs:{separator:"/"}},[t("el-breadcrumb-item",{attrs:{to:{path:"/member/verification_list"}}},[e._v("核销记录")]),t("el-breadcrumb-item",[e._v("核销验证")])],1)],1),t("div",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticClass:"ns-verification"},[t("div",{staticClass:"ns-verification-order"},[t("p",{staticClass:"ns-site-name"},[e._v(e._s(e.verifyInfo.site_name))]),e._l(e.verifyInfo.item_array,(function(i,r){return t("div",{key:r,staticClass:"ns-goods-list"},[t("div",{staticClass:"ns-goods-img"},[t("el-image",{attrs:{fit:"cover",src:e.$img(i.img)},on:{error:function(i){return e.imageError(r)}}})],1),t("div",{staticClass:"ns-goods-info"},[t("p",[e._v(e._s(i.name))]),t("p",{staticClass:"ns-goods-price ns-text-color"},[e._v("￥"+e._s(i.price))]),t("p",[e._v("数量："+e._s(i.num))])])])})),t("div",{staticClass:"ns-order-info"},[e._l(e.verifyInfo.remark_array,(function(i,r){return t("p",{key:r},[e._v(e._s(i.title)+"："+e._s(i.value))])})),t("p",[e._v("核销类型："+e._s(e.verifyInfo.verify_type_name))]),e.verifyInfo.is_verify?[t("p",[e._v("核销状态：已核销")]),e.verifyInfo.verify_time?t("p",[e._v("核销人员："+e._s(e.verifyInfo.verifier_name))]):e._e(),e.verifyInfo.verify_time?t("p",[e._v("核销时间："+e._s(e.$timeStampTurnTime(e.verifyInfo.verify_time)))]):e._e()]:e._e()],2),t("div",{staticClass:"ns-btn"},[0==e.verifyInfo.is_verify?t("el-button",{on:{click:e.verify}},[e._v("确认使用")]):e._e()],1)],2)])])},n=[],s=t("5530"),a=t("121f"),o=t("2f62"),c={name:"verification_detail",components:{},data:function(){return{verify_code:"",verifyInfo:{},isSub:!1,loading:!0}},created:function(){this.verify_code=this.$route.query.code,this.getVerifyInfo()},computed:Object(s["a"])({},Object(o["b"])(["defaultGoodsImage"])),methods:{getVerifyInfo:function(){var e=this;Object(a["d"])({verify_code:this.verify_code}).then((function(i){i.code>=0?e.verifyInfo=i.data:(e.$message({message:i.message,type:"warning"}),e.$router.push("/member/index")),e.loading=!1})).catch((function(i){e.$message.error(i.message),e.$router.push("/member/index"),e.loading=!1}))},verify:function(){var e=this;this.isSub||(this.isSub=!0,Object(a["c"])({verify_code:this.verify_code}).then((function(i){i.code>=0?e.$message({message:i.message,type:"success",duration:2e3,onClose:function(){e.$router.push("/member/verification_list")}}):(e.$message({message:i.message,type:"warning"}),e.isSub=!1)})).catch((function(i){e.$message.error(i.message),e.isSub=!1})))},imageError:function(e){this.verifyInfo.item_array[e].img=this.defaultGoodsImage}}},f=c,u=(t("7349"),t("2877")),v=Object(u["a"])(f,r,n,!1,null,"1002ca66",null);i["default"]=v.exports}}]);
//# sourceMappingURL=chunk-40612c9a.ce08f2b0.js.map