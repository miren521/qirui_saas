(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["otherpages-goods-coupon-coupon"],{5421:function(t,i,e){"use strict";e("99af"),e("4160"),e("159b"),Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0;var o={data:function(){return{couponTab:0,list:[]}},onShow:function(){var t=this;if(!this.addonIsExit.coupon)return this.$util.showToast({title:"优惠券插件未安装",mask:!0,duration:2e3}),void setTimeout((function(){t.$util.redirectTo("/pages/index/index/index",{},"redirectTo")}),2e3);this.$langConfig.refresh()},computed:{themeStyle:function(){return"theme-"+this.$store.state.themeStyle},addonIsExit:function(){return this.$store.state.addonIsExit}},methods:{couponTabOn:function(t){this.couponTab=t,this.list=[],this.$refs.mescroll.refresh(!1)},receiveCoupon:function(t,i){var e=this;if(!this.couponBtnSwitch){this.couponBtnSwitch=!0;var o=uni.getStorageSync("token");if(""!=o){var n,s={site_id:t.site_id,get_type:2};0==this.couponTab?(n="/coupon/api/coupon/receive",s.coupon_type_id=t.coupon_type_id):(n="/platformcoupon/api/platformcoupon/receive",s.platformcoupon_type_id=t.platformcoupon_type_id),this.$api.sendRequest({url:n,data:s,success:function(i){if(-1==i.code&&"TOKEN_ERROR"==i.error_code){e.$util.showToast({title:"登录失败"});var o=getCurrentPages(),n=o[o.length-1].options,s=o[o.length-1].route;return n.back=s,void setTimeout((function(){e.$util.redirectTo("/pages/login/login/login",n)}),1500)}i.data;var a=i.message;0==i.code&&(a="领取成功");var c=e.list;if(0==e.couponTab)if(1==i.data.is_exist)for(var l=0;l<c.length;l++)c[l].coupon_type_id==t.coupon_type_id&&(c[l].useState=1);else for(var u=0;u<c.length;u++)c[u].coupon_type_id==t.coupon_type_id&&(c[u].useState=2);else if(1==i.data.is_exist)for(var p=0;p<c.length;p++)c[p].platformcoupon_type_id==t.platformcoupon_type_id&&(c[p].useState=1);else for(var r=0;r<c.length;r++)c[r].platformcoupon_type_id==t.platformcoupon_type_id&&(c[r].useState=2);e.$util.showToast({title:a}),e.couponBtnSwitch=!1},fail:function(t){e.couponBtnSwitch=!1}})}else this.$util.redirectTo("/pages/login/login/login")}},getMemberCounponList:function(t){var i,e=this;i=0==this.couponTab?"/coupon/api/coupon/typepagelists":"/platformcoupon/api/platformcoupon/typepagelists",this.$api.sendRequest({url:i,data:{page:t.num,page_size:t.size},success:function(i){var o=[],n=i.message;0==i.code&&i.data?o=i.data.list:e.$util.showToast({title:n}),o.length&&o.forEach((function(t){t.useState=0})),t.endSuccess(o.length),1==t.num&&(e.list=[]),e.list=e.list.concat(o),e.$refs.loadingCover&&e.$refs.loadingCover.hide()},fail:function(){t.endErr(),this.$refs.loadingCover&&this.$refs.loadingCover.hide()}})},toGoodList:function(t){0==this.couponTab?1!=t.goods_type?this.$util.redirectTo("/otherpages/shop/list/list",{couponId:t.coupon_type_id,site_id:t.site_id}):this.$util.redirectTo("/otherpages/shop/list/list",{site_id:t.site_id}):1!=t.use_scenario?this.$util.redirectTo("/pages/goods/list/list",{platformcouponTypeId:t.platformcoupon_type_id}):this.$util.redirectTo("/pages/goods/list/list",{})},imageError:function(t){this.list[t].logo=this.$util.getDefaultImage().default_shop_img,this.$forceUpdate()},couponImageError:function(t){this.list[t].image=this.$util.img("upload/uniapp/goods/coupon.png"),this.$forceUpdate()}},onShareAppMessage:function(t){var i="送您一张优惠券,快来领取吧",e="/otherpages/goods/coupon/coupon";return{title:i,path:e,success:function(t){},fail:function(t){}}}};i.default=o},"5f77":function(t,i,e){"use strict";e.r(i);var o=e("5421"),n=e.n(o);for(var s in o)"default"!==s&&function(t){e.d(i,t,(function(){return o[t]}))}(s);i["default"]=n.a},a130:function(t,i,e){var o=e("24fb");i=o(!1),i.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/**  -------------------------------------------------------------------默认主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------蓝色主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------绿色主题颜色配置-------------------------------------------------- */\n/* 文字基本颜色 */\n/* 文字尺寸 */.coupon-tab[data-v-173bbd73]{width:100%;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;padding-top:%?10?%}.coupon-tab .tab-item[data-v-173bbd73]{height:%?70?%;color:#333;line-height:%?70?%;box-sizing:border-box}.coupon-tab .tab-item.active[data-v-173bbd73]{position:relative}.coupon-tab .tab-item.active[data-v-173bbd73]::after{content:"";display:inline-block;width:100%;height:%?6?%;position:absolute;left:0;bottom:0;background:#fff;border-radius:%?3?%}.coupon-tab .tab-item[data-v-173bbd73]:nth-child(1){margin-right:25%}.coupon-list[data-v-173bbd73]{width:100%;height:100%;padding:%?20?%;box-sizing:border-box;background:#f5f5f5}.coupon-list .coupon-li[data-v-173bbd73]{width:%?702?%;height:%?206?%;margin:0 auto;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;margin-bottom:%?20?%;position:relative}.coupon-list .coupon-li uni-image[data-v-173bbd73]{width:100%;height:100%;position:absolute;left:0;top:0}.coupon-list .coupon-li .li-top[data-v-173bbd73]{width:%?652?%;margin:0 auto;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;-webkit-box-align:end;-webkit-align-items:flex-end;align-items:flex-end;border-bottom:1px dashed #e1e1e1;position:relative;margin-top:%?39?%;line-height:1;padding:0 %?10?%;box-sizing:border-box;padding-bottom:%?26?%}.coupon-list .coupon-li .li-top .top-content-left[data-v-173bbd73]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;line-height:1;-webkit-box-flex:1;-webkit-flex:1;flex:1;overflow:hidden}.coupon-list .coupon-li .li-top .top-content-left .coupon-name[data-v-173bbd73]{width:100%;line-height:1;color:#000;font-size:%?28?%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.coupon-list .coupon-li .li-top .top-content-left .coupon-desc[data-v-173bbd73]{line-height:1;color:#ababab;font-size:%?20?%;margin-top:%?23?%}.coupon-list .coupon-li .li-top .top-content-right[data-v-173bbd73]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;line-height:1}.coupon-list .coupon-li .li-top .top-content-right .font-sm[data-v-173bbd73]{line-height:1;margin-top:%?20?%;font-size:%?32?%;margin-right:%?6?%}.coupon-list .coupon-li .li-top .top-content-right .font-big[data-v-173bbd73]{line-height:1;font-size:%?70?%}.coupon-list .coupon-li .li-bottom[data-v-173bbd73]{-webkit-box-flex:1;-webkit-flex:1;flex:1;width:%?652?%;margin:0 auto;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;-webkit-box-align:center;-webkit-align-items:center;align-items:center;z-index:5;padding:0 %?10?%;box-sizing:border-box}.coupon-list .coupon-li .li-bottom .fonts-sm[data-v-173bbd73]{color:#ababab;font-size:%?24?%}.coupon-list .coupon-li .li-bottom .li-bottom-right[data-v-173bbd73]{display:-webkit-box;display:-webkit-flex;display:flex}.coupon-list .coupon-li .li-bottom .li-bottom-right .getCoupon[data-v-173bbd73]{font-size:%?24?%}.coupon-list .coupon-li .li-bottom .li-bottom-right .iconright[data-v-173bbd73]{margin-left:%?10?%;font-size:%?24?%}.empty[data-v-173bbd73]{margin-top:%?200?%}',""]),t.exports=i},beea:function(t,i,e){"use strict";e.d(i,"b",(function(){return n})),e.d(i,"c",(function(){return s})),e.d(i,"a",(function(){return o}));var o={nsEmpty:e("916e").default,loadingCover:e("1ba8").default},n=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("v-uni-view",{class:t.themeStyle},[e("v-uni-view",{staticClass:"coupon-tab"},[e("v-uni-view",{staticClass:"tab-item",class:0==t.couponTab?"active ns-text-color ns-bg-before":"",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.couponTabOn(0)}}},[t._v("店铺优惠劵")]),e("v-uni-view",{staticClass:"tab-item",class:1==t.couponTab?"active ns-text-color ns-bg-before":"",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.couponTabOn(1)}}},[t._v("平台优惠劵")])],1),e("v-uni-view",[e("v-uni-view",[t.addonIsExit.coupon?e("mescroll-uni",{ref:"mescroll",attrs:{top:80},on:{getData:function(i){arguments[0]=i=t.$handleEvent(i),t.getMemberCounponList.apply(void 0,arguments)}}},[e("template",{attrs:{slot:"list"},slot:"list"},[e("v-uni-view",{staticClass:"coupon-list"},t._l(t.list,(function(i,o){return e("v-uni-view",{key:o,staticClass:"coupon-li"},[e("v-uni-image",{attrs:{src:t.$util.img("upload/uniapp/coupon/coupon_ysy.png"),mode:"widthFix"}}),e("v-uni-view",{staticClass:"li-top",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.toGoodList(i)}}},[e("v-uni-view",{staticClass:"top-content-left"},[e("v-uni-view",{staticClass:"coupon-name"},[t._v(t._s(0==t.couponTab?i.coupon_name:i.platformcoupon_name)),0==t.couponTab&&i.site_name?e("v-uni-text",{staticClass:"ns-text-color-gray"},[t._v("("+t._s(i.site_name)+")")]):t._e()],1),e("v-uni-view",{staticClass:"coupon-desc"},[i.validity_type?e("v-uni-text",[t._v("领取之日起"+t._s(i.fixed_term)+"日内有效")]):e("v-uni-text",[t._v("有效日期至"+t._s(t.$util.timeStampTurnTime(i.end_time)))])],1)],1),e("v-uni-view",{staticClass:"top-content-right"},[i.discount&&"0.00"!=i.discount?[e("v-uni-text",{staticClass:"font-big ns-text-color"},[t._v(t._s(parseInt(i.discount)))]),e("v-uni-text",{staticClass:"font-big ns-text-color"},[t._v("折")])]:[e("v-uni-text",{staticClass:"font-big ns-text-color"},[t._v("￥")]),e("v-uni-text",{staticClass:"font-big ns-text-color"},[t._v(t._s(parseInt(i.money)))])]],2)],1),e("v-uni-view",{staticClass:"li-bottom"},[e("v-uni-view",{staticClass:"fonts-sm"},[0==t.couponTab?[1==i.goods_type?e("v-uni-text",[t._v("全场商品")]):e("v-uni-text",[t._v("指定商品")])]:t._e(),1==t.couponTab?[1==i.use_scenario?e("v-uni-text",[t._v("全场店铺")]):e("v-uni-text",[t._v("指定店铺")])]:t._e(),i.at_least>0?e("v-uni-text",[t._v("满"+t._s(i.at_least)+"元可用")]):e("v-uni-text",[t._v("无门槛优惠券")]),i.discount_limit&&"0.00"!=i.discount_limit?e("v-uni-text",{staticClass:"ns-text-color"},[t._v("(最大优惠"+t._s(i.discount_limit)+"元)")]):t._e()],2),e("v-uni-view",{staticClass:"li-bottom-right"},[0==i.useState?e("v-uni-view",{staticClass:"getCoupon ns-text-color",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.receiveCoupon(i,o)}}},[t._v("立即领取")]):t._e(),1==i.useState?e("v-uni-view",{staticClass:"getCoupon ns-text-color",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.toGoodList(i)}}},[t._v("去使用")]):t._e(),2!=i.useState?e("v-uni-view",{staticClass:"iconfont iconright ns-text-color"}):t._e(),2==i.useState?e("v-uni-view",{staticClass:"getCoupon ns-text-color-gary"},[t._v("去使用")]):t._e(),2==i.useState?e("v-uni-view",{staticClass:"iconfont iconright ns-text-color-gary"}):t._e()],1)],1)],1)})),1),0==t.list.length?e("v-uni-view",[e("ns-empty",{attrs:{text:"当前没有可以领取的优惠券哦!",isIndex:!1}})],1):t._e()],1)],2):t._e(),e("loading-cover",{ref:"loadingCover"})],1)],1)],1)},s=[]},c72c:function(t,i,e){"use strict";var o=e("fa62"),n=e.n(o);n.a},fa62:function(t,i,e){var o=e("a130");"string"===typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);var n=e("4f06").default;n("5fb95dae",o,!0,{sourceMap:!1,shadowMode:!1})},fb4d:function(t,i,e){"use strict";e.r(i);var o=e("beea"),n=e("5f77");for(var s in n)"default"!==s&&function(t){e.d(i,t,(function(){return n[t]}))}(s);e("c72c");var a,c=e("f0c5"),l=Object(c["a"])(n["default"],o["b"],o["c"],!1,null,"173bbd73",null,!1,o["a"],a);i["default"]=l.exports}}]);