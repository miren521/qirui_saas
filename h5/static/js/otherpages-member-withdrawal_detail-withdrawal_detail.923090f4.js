(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["otherpages-member-withdrawal_detail-withdrawal_detail"],{"18b9":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i={data:function(){return{id:0,detail:{}}},onLoad:function(t){this.id=t.id||0},onShow:function(){this.$langConfig.refresh(),uni.getStorageSync("token")?this.getDetail():this.$util.redirectTo("/pages/login/login/login",{back:"/otherpages/member/point/point"},"redirectTo")},computed:{themeStyle:function(){return"theme-"+this.$store.state.themeStyle}},methods:{getDetail:function(){var t=this;this.$api.sendRequest({url:"/api/memberwithdraw/detail",data:{id:this.id},success:function(e){if(-1==e.code&&"TOKEN_ERROR"==e.error_code){t.$util.showToast({title:"登录失败"});var a=getCurrentPages(),i=a[a.length-1].options,n=a[a.length-1].route;return i.back=n,void setTimeout((function(){t.$util.redirectTo("/pages/login/login/login",i)}),1500)}e.data&&(t.detail=e.data),t.$refs.loadingCover&&t.$refs.loadingCover.hide()},fail:function(e){t.$refs.loadingCover&&t.$refs.loadingCover.hide()}})}}};e.default=i},"23c2":function(t,e,a){"use strict";a.r(e);var i=a("d2d6"),n=a("449a");for(var s in n)"default"!==s&&function(t){a.d(e,t,(function(){return n[t]}))}(s);a("2f20");var l,u=a("f0c5"),r=Object(u["a"])(n["default"],i["b"],i["c"],!1,null,"62e10941",null,!1,i["a"],l);e["default"]=r.exports},"2f20":function(t,e,a){"use strict";var i=a("6e4a"),n=a.n(i);n.a},"449a":function(t,e,a){"use strict";a.r(e);var i=a("18b9"),n=a.n(i);for(var s in i)"default"!==s&&function(t){a.d(e,t,(function(){return i[t]}))}(s);e["default"]=n.a},"6e4a":function(t,e,a){var i=a("6f48");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=a("4f06").default;n("1e72e71f",i,!0,{sourceMap:!1,shadowMode:!1})},"6f48":function(t,e,a){var i=a("24fb");e=i(!1),e.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/**  -------------------------------------------------------------------默认主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------蓝色主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------绿色主题颜色配置-------------------------------------------------- */\n/* 文字基本颜色 */\n/* 文字尺寸 */.money-wrap[data-v-62e10941]{text-align:center;font-size:%?50?%;font-weight:700;margin:%?40?%;border-bottom:1px solid #e7e7e7;padding:%?40?%}.item[data-v-62e10941]{margin:%?40?%}.item .line-wrap[data-v-62e10941]{margin-bottom:%?20?%}.item .line-wrap .label[data-v-62e10941]{display:inline-block;width:%?200?%;color:#898989;font-size:%?28?%}.item .line-wrap .value[data-v-62e10941]{display:inline-block;font-size:%?28?%}',""]),t.exports=e},d2d6:function(t,e,a){"use strict";a.d(e,"b",(function(){return n})),a.d(e,"c",(function(){return s})),a.d(e,"a",(function(){return i}));var i={loadingCover:a("1ba8").default},n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{class:t.themeStyle},[a("v-uni-view",{staticClass:"money-wrap"},[a("v-uni-text",[t._v("-"+t._s(t.detail.apply_money))])],1),a("v-uni-view",{staticClass:"item"},[a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("当前状态")]),a("v-uni-text",{staticClass:"value"},[t._v(t._s(t.detail.status_name))])],1),a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("交易号")]),a("v-uni-text",{staticClass:"value"},[t._v(t._s(t.detail.withdraw_no))])],1),a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("手续费")]),a("v-uni-text",{staticClass:"value"},[t._v("￥"+t._s(t.detail.service_money))])],1),a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("申请时间")]),a("v-uni-text",{staticClass:"value"},[t._v(t._s(t.$util.timeStampTurnTime(t.detail.apply_time)))])],1),t.detail.status?a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("审核时间")]),a("v-uni-text",{staticClass:"value"},[t._v(t._s(t.$util.timeStampTurnTime(t.detail.audit_time)))])],1):t._e(),t.detail.bank_name?a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("银行名称")]),a("v-uni-text",{staticClass:"value"},[t._v(t._s(t.detail.bank_name))])],1):t._e(),a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("收款账号")]),a("v-uni-text",{staticClass:"value"},[t._v(t._s(t.detail.account_number))])],1),-1==t.detail.status&&t.detail.refuse_reason?a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("拒绝理由")]),a("v-uni-text",{staticClass:"value"},[t._v(t._s(t.detail.refuse_reason))])],1):t._e(),2==t.detail.status?a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("转账方式名称")]),a("v-uni-text",{staticClass:"value"},[t._v(t._s(t.detail.transfer_type_name))])],1):t._e(),2==t.detail.status?a("v-uni-view",{staticClass:"line-wrap"},[a("v-uni-text",{staticClass:"label"},[t._v("转账时间")]),a("v-uni-text",{staticClass:"value"},[t._v(t._s(t.$util.timeStampTurnTime(t.detail.payment_time)))])],1):t._e()],1),a("loading-cover",{ref:"loadingCover"})],1)},s=[]}}]);