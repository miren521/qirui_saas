(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["otherpages-member-apply_withdrawal-apply_withdrawal"],{"11d6":function(t,a,e){"use strict";e("b680"),e("acd8"),Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var i={data:function(){return{withdrawInfo:{config:{is_use:0,min:1,rate:0},member_info:{balance_money:0,balance_withdraw:0,balance_withdraw_apply:0}},bankAccountInfo:{},withdrawMoney:"",isSub:!1}},onShow:function(){this.$langConfig.refresh(),uni.getStorageSync("token")?(this.getWithdrawInfo(),this.getBankAccountInfo()):this.$util.redirectTo("/pages/login/login/login",{back:"/otherpages/member/apply_withdrawal/apply_withdrawal"})},computed:{themeStyle:function(){return"theme-"+this.$store.state.themeStyle}},methods:{allTx:function(){this.withdrawMoney=this.withdrawInfo.member_info.balance_money},remove:function(){this.withdrawMoney=""},getWithdrawInfo:function(){var t=this;this.$api.sendRequest({url:"/api/memberwithdraw/info",success:function(a){if(-1==a.code&&"TOKEN_ERROR"==a.error_code){t.$util.showToast({title:"登录失败"});var e=getCurrentPages(),i=e[e.length-1].options,n=e[e.length-1].route;return i.back=n,void setTimeout((function(){t.$util.redirectTo("/pages/login/login/login",i)}),1500)}a.code>=0&&a.data&&(t.withdrawInfo=a.data,0==t.withdrawInfo.config.is_use&&t.$util.redirectTo("/pages/member/index/index",{},"reLaunch")),t.$refs.loadingCover&&t.$refs.loadingCover.hide()},fail:function(a){t.$refs.loadingCover&&t.$refs.loadingCover.hide()}})},getBankAccountInfo:function(){var t=this;this.$api.sendRequest({url:"/api/memberbankaccount/defaultinfo",success:function(a){a.code>=0&&a.data&&(t.bankAccountInfo=a.data)}})},verify:function(){return""==this.withdrawMoney||0==this.withdrawMoney||isNaN(parseFloat(this.withdrawMoney))?(this.$util.showToast({title:"请输入提现金额"}),!1):parseFloat(this.withdrawMoney)>parseFloat(this.withdrawInfo.member_info.balance_money)?(this.$util.showToast({title:"提现金额超出可提现金额"}),!1):!(parseFloat(this.withdrawMoney)<parseFloat(this.withdrawInfo.config.min))||(this.$util.showToast({title:"提现金额小于最低提现金额"}),!1)},withdraw:function(){var t=this;if(this.bankAccountInfo.withdraw_type){if(this.verify()){if(this.isSub)return;this.isSub=!0,this.$api.sendRequest({url:"/api/memberwithdraw/apply",data:{apply_money:this.withdrawMoney,transfer_type:this.bankAccountInfo.withdraw_type,realname:this.bankAccountInfo.realname,mobile:this.bankAccountInfo.mobile,bank_name:this.bankAccountInfo.branch_bank_name,account_number:this.bankAccountInfo.bank_account},success:function(a){if(-1==a.code&&"TOKEN_ERROR"==a.error_code){t.$util.showToast({title:"登录失败"});var e=getCurrentPages(),i=e[e.length-1].options,n=e[e.length-1].route;return i.back=n,void setTimeout((function(){t.$util.redirectTo("/pages/login/login/login",i)}),1500)}a.code>=0?t.$util.showToast({title:"提现申请成功",success:function(a){setTimeout((function(){t.$util.redirectTo("/otherpages/member/withdrawal/withdrawal",{},"redirectTo")}),1500)}}):(t.isSub=!1,t.$util.showToast({title:a.message}))},fail:function(a){t.isSub=!1}})}}else this.$util.showToast({title:"请先添加提现方式"})},goAccount:function(){this.$util.redirectTo("/otherpages/member/account/account",{back:"/otherpages/member/apply_withdrawal/apply_withdrawal"},"redirectTo")}},filters:{moneyFormat:function(t){return parseFloat(t).toFixed(2)}}};a.default=i},"24a2":function(t,a,e){"use strict";e.r(a);var i=e("11d6"),n=e.n(i);for(var o in i)"default"!==o&&function(t){e.d(a,t,(function(){return i[t]}))}(o);a["default"]=n.a},"4c8d":function(t,a,e){"use strict";e.d(a,"b",(function(){return n})),e.d(a,"c",(function(){return o})),e.d(a,"a",(function(){return i}));var i={loadingCover:e("1ba8").default},n=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("v-uni-view",{class:t.themeStyle},[e("v-uni-view",{staticClass:"container"},[e("v-uni-view",{staticClass:"bank-account-wrap",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.goAccount()}}},[t.bankAccountInfo.withdraw_type?e("v-uni-view",{staticClass:"tx-wrap"},[e("v-uni-text",{staticClass:"tx-to"},[t._v(t._s(t.$lang("withdrawTo")))]),"wechatpay"==t.bankAccountInfo.withdraw_type?e("v-uni-view",{staticClass:"tx-bank"},[t._v(t._s(t.$lang("defaultWithdraw")))]):e("v-uni-view",{staticClass:"tx-bank"},[t._v(t._s(t.bankAccountInfo.bank_account))]),"alipay"==t.bankAccountInfo.withdraw_type?e("v-uni-view",{staticClass:"tx-img"},[e("v-uni-image",{attrs:{src:t.$util.img("upload/uniapp/member/apply_withdrawal/alipay.png"),mode:"widthFix"}})],1):"bank"==t.bankAccountInfo.withdraw_type?e("v-uni-view",{staticClass:"tx-img"},[e("v-uni-image",{attrs:{src:t.$util.img("upload/uniapp/member/apply_withdrawal/bank.png"),mode:"widthFix"}})],1):"wechatpay"==t.bankAccountInfo.withdraw_type?e("v-uni-view",{staticClass:"tx-img"},[e("v-uni-image",{attrs:{src:t.$util.img("upload/uniapp/member/apply_withdrawal/wechatpay.png"),mode:"widthFix"}})],1):t._e()],1):e("v-uni-text",{staticClass:"tx-to"},[t._v(t._s(t.$lang("withdrawType")))]),e("v-uni-view",{staticClass:"iconfont iconright"})],1),e("v-uni-view",{staticClass:"empty-box"}),e("v-uni-view",{staticClass:"withdraw-wrap"},[e("v-uni-view",{staticClass:"withdraw-wrap-title"},[t._v(t._s(t.$lang("withdrawMoney"))+"：")]),e("v-uni-view",{staticClass:"money-wrap"},[e("v-uni-text",{staticClass:"unit"},[t._v(t._s(t.$lang("common.currencySymbol")))]),e("v-uni-input",{staticClass:"withdraw-money",attrs:{type:"digit",value:""},model:{value:t.withdrawMoney,callback:function(a){t.withdrawMoney=a},expression:"withdrawMoney"}}),t.withdrawMoney?e("v-uni-view",{staticClass:"delete",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.remove.apply(void 0,arguments)}}},[e("v-uni-image",{attrs:{src:t.$util.img("upload/uniapp/member/apply_withdrawal/close.png"),mode:"widthFix"},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.remove.apply(void 0,arguments)}}})],1):t._e()],1),e("v-uni-view",{staticClass:"bootom"},[e("v-uni-view",[e("v-uni-text",{staticClass:"ns-text-color-gray"},[t._v(t._s(t.$lang("ableAccountBalance"))+"："+t._s(t.$lang("common.currencySymbol"))+t._s(t._f("moneyFormat")(t.withdrawInfo.member_info.balance_money)))]),e("v-uni-text",{staticClass:"all-tx ns-text-color",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.allTx.apply(void 0,arguments)}}},[t._v(t._s(t.$lang("withdrawAll")))])],1)],1),e("v-uni-view",{staticClass:"desc"},[e("v-uni-text",[t._v(t._s(t.$lang("minWithdraw"))+" "+t._s(t.$lang("common.currencySymbol"))+t._s(t._f("moneyFormat")(t.withdrawInfo.config.min)))]),e("v-uni-text",[t._v("，"+t._s(t.$lang("formalities"))+t._s(t.withdrawInfo.config.rate+"%"))])],1)],1),e("v-uni-view",{staticClass:"btn ns-gradient-otherpages-member-widthdrawal-withdrawal ns-bg-color ns-border-color",class:{themeStyle:t.themeStyle,disabled:""==t.withdrawMoney||0==t.withdrawMoney},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.withdraw.apply(void 0,arguments)}}},[t._v(t._s(t.$lang("withdraw")))]),e("v-uni-view",{staticClass:"recoend"},[e("v-uni-navigator",{staticClass:"recoend-con",attrs:{url:"/otherpages/member/withdrawal/withdrawal"}},[t._v(t._s(t.$lang("withdrawRecord")))])],1),e("loading-cover",{ref:"loadingCover"})],1)],1)},o=[]},9457:function(t,a,e){"use strict";e.r(a);var i=e("4c8d"),n=e("24a2");for(var o in n)"default"!==o&&function(t){e.d(a,t,(function(){return n[t]}))}(o);e("ca11");var r,s=e("f0c5"),c=Object(s["a"])(n["default"],i["b"],i["c"],!1,null,"1593e54e",null,!1,i["a"],r);a["default"]=c.exports},c6e9:function(t,a,e){var i=e("24fb");a=i(!1),a.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/**  -------------------------------------------------------------------默认主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------蓝色主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------绿色主题颜色配置-------------------------------------------------- */\n/* 文字基本颜色 */\n/* 文字尺寸 */.container[data-v-1593e54e]{width:100vw;height:100vh;background:#fff}.empty-box[data-v-1593e54e]{height:%?20?%}.bank-account-wrap[data-v-1593e54e]{margin:0 %?20?%;padding:%?10?% %?30?%;border-bottom:1px solid #f7f7f7;position:relative}.bank-account-wrap .tx-wrap[data-v-1593e54e]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;margin-right:%?60?%}.bank-account-wrap .tx-wrap .tx-bank[data-v-1593e54e]{margin-right:%?60?%}.bank-account-wrap .tx-wrap .tx-img[data-v-1593e54e]{position:absolute;right:%?100?%;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);width:%?40?%;height:%?40?%}.bank-account-wrap .tx-wrap .tx-img uni-image[data-v-1593e54e]{width:100%;height:100%}.bank-account-wrap .iconfont[data-v-1593e54e]{position:absolute;right:%?40?%;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%)}.withdraw-wrap[data-v-1593e54e]{margin:0 %?20?%;padding:%?30?%;border-radius:%?16?%;box-shadow:hsla(0,0%,43.1%,.09) 0 0 %?20?% 0}.withdraw-wrap .money-wrap[data-v-1593e54e]{padding:%?20?% 0;border-bottom:1px solid #eee;display:-webkit-box;display:-webkit-flex;display:flex}.withdraw-wrap .money-wrap .unit[data-v-1593e54e]{font-size:%?60?%;line-height:1}.withdraw-wrap .money-wrap .withdraw-money[data-v-1593e54e]{height:%?60?%;line-height:1;min-height:%?60?%;padding-left:%?20?%;font-size:%?60?%;-webkit-box-flex:1;-webkit-flex:1;flex:1;font-weight:bolder}.withdraw-wrap .money-wrap .delete[data-v-1593e54e]{width:%?40?%;height:%?40?%}.withdraw-wrap .money-wrap .delete uni-image[data-v-1593e54e]{width:100%;height:100%}.withdraw-wrap .bootom[data-v-1593e54e]{display:-webkit-box;display:-webkit-flex;display:flex;padding-top:%?20?%}.withdraw-wrap .bootom uni-text[data-v-1593e54e]{line-height:1;-webkit-box-flex:2;-webkit-flex:2;flex:2}.withdraw-wrap .bootom .all-tx[data-v-1593e54e]{padding-left:%?10?%}.btn[data-v-1593e54e]{margin:0 %?30?%;margin-top:%?60?%;height:%?80?%;line-height:%?80?%;border-radius:%?80?%;color:#fff;text-align:center;border:1px solid}.btn.disabled[data-v-1593e54e]{background:#ccc!important;border-color:#ccc!important;color:#fff}.recoend[data-v-1593e54e]{margin-top:%?40?%}.recoend .recoend-con[data-v-1593e54e]{text-align:center}.desc[data-v-1593e54e]{font-size:%?24?%;color:#999}',""]),t.exports=a},ca11:function(t,a,e){"use strict";var i=e("d2f4"),n=e.n(i);n.a},d2f4:function(t,a,e){var i=e("c6e9");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=e("4f06").default;n("25972b0b",i,!0,{sourceMap:!1,shadowMode:!1})}}]);