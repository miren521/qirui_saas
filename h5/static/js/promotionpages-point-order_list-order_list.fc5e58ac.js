(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["promotionpages-point-order_list-order_list"],{"29e4":function(e,t,i){"use strict";var o=i("9f3f"),a=i.n(o);a.a},"31c3":function(e,t,i){"use strict";i.r(t);var o=i("f69a"),a=i.n(o);for(var r in o)"default"!==r&&function(e){i.d(t,e,(function(){return o[e]}))}(r);t["default"]=a.a},5888:function(e,t,i){"use strict";i.d(t,"b",(function(){return a})),i.d(t,"c",(function(){return r})),i.d(t,"a",(function(){return o}));var o={nsEmpty:i("916e").default,loadingCover:i("1ba8").default},a=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("v-uni-view",{staticClass:"order-container",class:e.themeStyle},[i("mescroll-uni",{ref:"mescroll",on:{getData:function(t){arguments[0]=t=e.$handleEvent(t),e.getListData.apply(void 0,arguments)}}},[i("template",{attrs:{slot:"list"},slot:"list"},[e.orderList.length?e._l(e.orderList,(function(t,o){return i("v-uni-view",{key:o,staticClass:"order-item",on:{click:function(i){arguments[0]=i=e.$handleEvent(i),e.detail(t)}}},[i("v-uni-view",{staticClass:"order-header"},[i("v-uni-view",[i("v-uni-text",{staticClass:"ns-text-color-gray ns-font-size-sm"},[e._v(e._s(t.order_no))])],1),i("v-uni-view",{staticClass:"align-right"},[i("v-uni-text",{staticClass:"ns-text-color-gray ns-font-size-sm"},[e._v(e._s(e.$util.timeStampTurnTime(t.create_time)))])],1)],1),i("v-uni-view",{staticClass:"order-body"},[i("v-uni-view",{staticClass:"goods-wrap"},[1==t.type?[t.exchange_image?i("v-uni-view",{staticClass:"goods-img"},[i("v-uni-image",{attrs:{src:e.$util.img(t.exchange_image),mode:"aspectFill","lazy-load":!0},on:{error:function(i){arguments[0]=i=e.$handleEvent(i),e.imageError(o,t.type)}}})],1):i("v-uni-view",{staticClass:"goods-img"},[i("v-uni-image",{attrs:{src:e.$util.img("upload/uniapp/point/gift.png"),mode:"aspectFill","lazy-load":!0}})],1)]:e._e(),2==t.type?[t.exchange_image?i("v-uni-view",{staticClass:"goods-img"},[i("v-uni-image",{attrs:{src:e.$util.img(t.exchange_image),mode:"aspectFill","lazy-load":!0},on:{error:function(i){arguments[0]=i=e.$handleEvent(i),e.imageError(o,t.type)}}})],1):i("v-uni-view",{staticClass:"goods-img"},[i("v-uni-image",{attrs:{src:e.$util.img("upload/uniapp/point/coupon.png"),mode:"aspectFill","lazy-load":!0}})],1)]:e._e(),3==t.type?[t.exchange_image?i("v-uni-view",{staticClass:"goods-img"},[i("v-uni-image",{attrs:{src:e.$util.img(t.exchange_image),mode:"aspectFill","lazy-load":!0},on:{error:function(i){arguments[0]=i=e.$handleEvent(i),e.imageError(o,t.type)}}})],1):i("v-uni-view",{staticClass:"goods-img"},[i("v-uni-image",{attrs:{src:e.$util.img("upload/uniapp/point/hongbao.png"),mode:"aspectFill","lazy-load":!0}})],1)]:e._e(),i("v-uni-view",{staticClass:"goods-info"},[i("v-uni-view",{staticClass:"goods-name"},[e._v(e._s(t.exchange_name))]),i("v-uni-view",{staticClass:"goods-sub-section"},[i("v-uni-view",[i("v-uni-text",[i("v-uni-text",{staticClass:"iconfont iconclose"}),e._v(e._s(t.num))],1)],1)],1)],1)],2)],1),i("v-uni-view",{staticClass:"order-footer"},[i("v-uni-view",{staticClass:"order-base-info"},[i("v-uni-view",{staticClass:"total"},[i("v-uni-text",[e._v(e._s(t.point)),i("v-uni-text",{staticClass:"ns-font-size-sm"},[e._v(e._s(e.$lang("point")))])],1)],1)],1)],1)],1)})):e._e(),e.showEmpty&&!e.orderList.length?[i("v-uni-view",[i("ns-empty",{attrs:{isIndex:!1,text:e.$lang("emptyTips")}})],1)]:e._e()],2)],2),i("loading-cover",{ref:"loadingCover"})],1)},r=[]},"848f":function(e,t,i){var o=i("24fb");t=o(!1),t.push([e.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/**  -------------------------------------------------------------------默认主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------蓝色主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------绿色主题颜色配置-------------------------------------------------- */\n/* 文字基本颜色 */\n/* 文字尺寸 */.order-container[data-v-272c1cc4]{width:100vw;height:100vh}.align-right[data-v-272c1cc4]{text-align:right}.order-item[data-v-272c1cc4]{margin:%?20?%;padding:%?20?%;border-radius:4px;background:#fff;position:relative}.order-item .order-header[data-v-272c1cc4]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;position:relative}.order-item .order-header > uni-view[data-v-272c1cc4]{-webkit-box-flex:1;-webkit-flex:1;flex:1}.order-item .order-body .goods-wrap[data-v-272c1cc4]{margin-bottom:%?20?%;display:-webkit-box;display:-webkit-flex;display:flex;position:relative}.order-item .order-body .goods-wrap[data-v-272c1cc4]:last-of-type{margin-bottom:0}.order-item .order-body .goods-wrap .goods-img[data-v-272c1cc4]{width:%?120?%;height:%?120?%;padding:%?20?% 0 0 0;margin-right:%?20?%}.order-item .order-body .goods-wrap .goods-img uni-image[data-v-272c1cc4]{width:100%;height:100%;border-radius:4px}.order-item .order-body .goods-wrap .goods-info[data-v-272c1cc4]{-webkit-box-flex:1;-webkit-flex:1;flex:1;position:relative;padding:%?20?% 0 0 0;max-width:calc(100% - %?140?%)}.order-item .order-body .goods-wrap .goods-info .goods-name[data-v-272c1cc4]{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden;line-height:1.5;font-size:%?28?%}.order-item .order-body .goods-wrap .goods-info .goods-sub-section[data-v-272c1cc4]{width:100%;line-height:1.3;display:-webkit-box;display:-webkit-flex;display:flex}.order-item .order-body .goods-wrap .goods-info .goods-sub-section .goods-price[data-v-272c1cc4]{font-weight:700;font-size:15px}.order-item .order-body .goods-wrap .goods-info .goods-sub-section .unit[data-v-272c1cc4]{font-weight:400;font-size:%?24?%;margin-right:%?2?%}.order-item .order-body .goods-wrap .goods-info .goods-sub-section uni-view[data-v-272c1cc4]{-webkit-box-flex:1;-webkit-flex:1;flex:1;line-height:1.3}.order-item .order-body .goods-wrap .goods-info .goods-sub-section uni-view[data-v-272c1cc4]:last-of-type{text-align:right}.order-item .order-body .goods-wrap .goods-info .goods-sub-section uni-view:last-of-type .iconfont[data-v-272c1cc4]{line-height:1;font-size:%?26?%}.order-item .order-body .goods-wrap .goods-info .goods-operation[data-v-272c1cc4]{text-align:right;padding-top:%?20?%}.order-item .order-body .goods-wrap .goods-info .goods-operation .operation-btn[data-v-272c1cc4]{line-height:1;padding:%?14?% %?20?%;color:#333;display:inline-block;border-radius:%?28?%;background:#fff;border:.5px solid #999;font-size:%?24?%;margin-left:%?10?%}.order-item .order-footer .order-base-info[data-v-272c1cc4]{display:-webkit-box;display:-webkit-flex;display:flex}.order-item .order-footer .order-base-info .total[data-v-272c1cc4]{text-align:right;padding-top:%?20?%;-webkit-box-flex:1;-webkit-flex:1;flex:1}.order-item .order-footer .order-base-info .total > uni-text[data-v-272c1cc4]{line-height:1;margin-left:%?10?%}.order-item .order-footer .order-operation[data-v-272c1cc4]{text-align:right;padding-top:%?20?%}.order-item .order-footer .order-operation .operation-btn[data-v-272c1cc4]{line-height:1;padding:%?20?% %?26?%;color:#333;display:inline-block;border-radius:%?32?%;background:#fff;border:.5px solid #999;font-size:%?24?%;margin-left:%?10?%}.empty[data-v-272c1cc4]{padding-top:%?200?%;text-align:center}.empty .empty-image[data-v-272c1cc4]{width:%?180?%;height:%?180?%}',""]),e.exports=t},"9f3f":function(e,t,i){var o=i("848f");"string"===typeof o&&(o=[[e.i,o,""]]),o.locals&&(e.exports=o.locals);var a=i("4f06").default;a("4bfc4ecd",o,!0,{sourceMap:!1,shadowMode:!1})},ae0f:function(e,t,i){"use strict";i.r(t);var o=i("5888"),a=i("31c3");for(var r in a)"default"!==r&&function(e){i.d(t,e,(function(){return a[e]}))}(r);i("29e4");var n,s=i("f0c5"),d=Object(s["a"])(a["default"],o["b"],o["c"],!1,null,"272c1cc4",null,!1,o["a"],n);t["default"]=d.exports},f69a:function(e,t,i){"use strict";i("99af"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o={data:function(){return{orderList:[],showEmpty:!1}},onLoad:function(){},onShow:function(){this.$langConfig.refresh(),this.$refs.mescroll&&this.$refs.mescroll.refresh(),uni.getStorageSync("token")||this.$util.redirectTo("/pages/login/login/login",{back:"/promotionpages/point/order_list/order_list"})},computed:{themeStyle:function(){return"theme-"+this.$store.state.themeStyle}},methods:{getListData:function(e){var t=this;this.showEmpty=!1,this.$api.sendRequest({url:"/pointexchange/api/order/page",data:{page:e.num,page_size:e.size},success:function(i){if(-1==i.code&&"TOKEN_ERROR"==i.error_code){t.$util.showToast({title:"登录失败"});var o=getCurrentPages(),a=o[o.length-1].options,r=o[o.length-1].route;return a.back=r,void setTimeout((function(){t.$util.redirectTo("/pages/login/login/login",a)}),1500)}t.showEmpty=!0;var n=[],s=i.message;0==i.code&&i.data?n=i.data.list:t.$util.showToast({title:s}),e.endSuccess(n.length),1==e.num&&(t.orderList=[]),t.orderList=t.orderList.concat(n),t.$refs.loadingCover&&t.$refs.loadingCover.hide()},fail:function(i){e.endErr(),t.$refs.loadingCover&&t.$refs.loadingCover.hide()}})},detail:function(e){e.type},imageError:function(e,t){var i="";1==t?i=this.$util.img("upload/uniapp/point/gift.png"):2==t?i=this.$util.img("upload/uniapp/point/coupon.png"):3==t&&(i=this.$util.img("upload/uniapp/point/hongbao.png")),this.orderList[e].exchange_image=i,this.$forceUpdate()}}};t.default=o}}]);