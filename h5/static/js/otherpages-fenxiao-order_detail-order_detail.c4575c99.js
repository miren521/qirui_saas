(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["otherpages-fenxiao-order_detail-order_detail"],{1147:function(e,t,i){"use strict";var o=i("4ea4");Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var d=o(i("3744")),a={components:{},computed:{themeStyle:function(){return"theme-"+this.$store.state.themeStyle}},data:function(){return{isIphoneX:!1,orderId:0,orderData:{action:[]}}},onLoad:function(e){e.id?this.orderId=e.id:uni.navigateBack({delta:1})},mixins:[d.default],onShow:function(){this.$langConfig.refresh(),this.isIphoneX=this.$util.uniappIsIPhoneX(),uni.getStorageSync("token")?this.getOrderData():this.$util.redirectTo("/pages/login/login/login",{back:"/pages/order/detail/detail?order_id="+this.orderId})},methods:{getOrderData:function(){var e=this;this.$api.sendRequest({url:"/fenxiao/api/order/info",data:{fenxiao_order_id:this.orderId},success:function(t){if(-1==t.code&&"TOKEN_ERROR"==t.error_code){e.$util.showToast({title:"登录失败"});var i=getCurrentPages(),o=i[i.length-1].options,d=i[i.length-1].route;return o.back=d,void setTimeout((function(){e.$util.redirectTo("/pages/login/login/login",o)}),1500)}t.code>=0?(e.$refs.loadingCover&&e.$refs.loadingCover.hide(),e.orderData=t.data):e.$util.showToast({title:"未获取到订单信息!！",success:function(){setTimeout((function(){e.$util.redirectTo("/otherpages/fenxiao/order/order",{},"redirectTo")}),1500)}})},fail:function(t){e.$refs.loadingCover&&e.$refs.loadingCover.hide()}})},imageError:function(e){this.orderData.order_goods[e].sku_image=this.$util.getDefaultImage().default_goods_img,this.$forceUpdate()}}};t.default=a},"1ecf":function(e,t,i){"use strict";i.r(t);var o=i("28c2"),d=i("a514");for(var a in d)"default"!==a&&function(e){i.d(t,e,(function(){return d[e]}))}(a);i("66c9");var r,n=i("f0c5"),l=Object(n["a"])(d["default"],o["b"],o["c"],!1,null,"63dc9b54",null,!1,o["a"],r);t["default"]=l.exports},"28c2":function(e,t,i){"use strict";i.d(t,"b",(function(){return d})),i.d(t,"c",(function(){return a})),i.d(t,"a",(function(){return o}));var o={loadingCover:i("1ba8").default},d=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("v-uni-view",{class:e.themeStyle},[i("v-uni-view",[i("v-uni-view",{staticClass:"order-detail"},[i("v-uni-view",{staticClass:"order-detail-box"},[i("v-uni-view",{staticClass:"header"},[i("v-uni-view",{staticClass:"title ns-bg-before"},[i("v-uni-text",[e._v("订单明细")])],1),1==e.orderData.is_refund?i("v-uni-text",{staticClass:"ns-text-color"},[e._v("已退款")]):1==e.orderData.is_settlement?i("v-uni-text",{staticClass:"ns-text-color"},[e._v("已结算")]):i("v-uni-text",{staticClass:"ns-text-color"},[e._v("待结算")])],1),i("v-uni-view",{staticClass:"detail-body"},[i("v-uni-view",{staticClass:"detail-body-box"},[i("v-uni-view",{staticClass:"goods-image"},[i("v-uni-image",{attrs:{src:e.$util.img(e.orderData.sku_image,{size:"mid"}),mode:"aspectFill"},on:{error:function(t){arguments[0]=t=e.$handleEvent(t),e.imageError(e.goodsIndex)}}})],1),i("v-uni-view",{staticClass:"order-info"},[i("v-uni-view",{staticClass:"goods-name"},[e._v(e._s(e.orderData.sku_name))]),i("v-uni-view",{staticClass:"goods-sub-section ns-margin-top"},[i("v-uni-view",[i("v-uni-text",{staticClass:"goods-price"},[i("v-uni-text",{staticClass:"unit"},[e._v("￥")]),i("v-uni-text",[e._v(e._s(e.orderData.price))])],1)],1),i("v-uni-view",[i("v-uni-text",[i("v-uni-text",{staticClass:"iconfont iconclose"}),e._v(e._s(e.orderData.num))],1)],1)],1)],1)],1)],1),i("v-uni-view",{staticClass:"detail-content"},[i("v-uni-view",{staticClass:"order-info-item"},[i("v-uni-text",{staticClass:"tit"},[e._v("订单号：")]),i("v-uni-text",[e._v(e._s(e.orderData.order_no))])],1),i("v-uni-view",{staticClass:"order-info-item"},[i("v-uni-text",{staticClass:"tit"},[e._v("分佣层级：")]),i("v-uni-text",[e._v(e._s(e.orderData.commission_level))])],1),i("v-uni-view",{staticClass:"order-info-item"},[i("v-uni-text",{staticClass:"tit"},[e._v(e._s(e.fenxiaoWords.account)+"：")]),i("v-uni-text",{staticClass:"ns-text-color"},[e._v("￥"+e._s(e.orderData.commission))])],1)],1),i("v-uni-view",{staticClass:"detail-footer"},[i("v-uni-text",[i("v-uni-text",[e._v("共"+e._s(e.orderData.num)+"件商品")])],1),i("v-uni-text",[i("v-uni-text",[e._v("合计：")]),i("v-uni-text",{staticClass:"ns-text-color"},[e._v("￥"+e._s(e.orderData.real_goods_money))])],1)],1)],1)],1),i("loading-cover",{ref:"loadingCover"})],1)],1)},a=[]},3744:function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o={data:function(){return{fenxiaoWords:{}}},methods:{getFenxiaoWrods:function(){var e=this;this.$api.sendRequest({url:"/fenxiao/api/config/words",success:function(t){t.code>=0&&t.data&&(e.fenxiaoWords=t.data,uni.setStorageSync("fenxiaoWords",t.data))}})}},onShow:function(){uni.getStorageSync("fenxiaoWords")&&(this.fenxiaoWords=uni.getStorageSync("fenxiaoWords")),this.getFenxiaoWrods()}};t.default=o},"66c9":function(e,t,i){"use strict";var o=i("d439"),d=i.n(o);d.a},a514:function(e,t,i){"use strict";i.r(t);var o=i("1147"),d=i.n(o);for(var a in o)"default"!==a&&function(e){i.d(t,e,(function(){return o[e]}))}(a);t["default"]=d.a},d439:function(e,t,i){var o=i("e029");"string"===typeof o&&(o=[[e.i,o,""]]),o.locals&&(e.exports=o.locals);var d=i("4f06").default;d("42ab6ad6",o,!0,{sourceMap:!1,shadowMode:!1})},e029:function(e,t,i){var o=i("24fb");t=o(!1),t.push([e.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/**  -------------------------------------------------------------------默认主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------蓝色主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------绿色主题颜色配置-------------------------------------------------- */\n/* 文字基本颜色 */\n/* 文字尺寸 */.order-detail[data-v-63dc9b54]{width:100%;padding:0 %?20?%;box-sizing:border-box;margin-top:%?20?%}.order-detail .order-detail-box[data-v-63dc9b54]{width:100%;height:100%;padding-top:%?20?%;box-sizing:border-box;background:#fff;border-radius:4px}.order-detail .order-detail-box .header[data-v-63dc9b54]{width:100%;height:%?70?%;padding:0 %?20?%;border-bottom:%?1?% solid #e7e7e7;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;-webkit-box-align:center;-webkit-align-items:center;align-items:center;box-sizing:border-box}.order-detail .order-detail-box .header .title[data-v-63dc9b54]{padding-left:%?15?%;display:inline-block;position:relative;line-height:1;font-weight:600}.order-detail .order-detail-box .header .title[data-v-63dc9b54]::before{content:"";display:block;width:%?4?%;height:100%;position:absolute;left:0;top:0;border-radius:%?6?%}.order-detail .order-detail-box .detail-body[data-v-63dc9b54]{width:100%;padding:%?20?%;box-sizing:border-box;border-bottom:%?1?% solid #e7e7e7}.order-detail .order-detail-box .detail-body .detail-body-box[data-v-63dc9b54]{width:100%;height:100%;display:-webkit-box;display:-webkit-flex;display:flex}.order-detail .order-detail-box .detail-body .detail-body-box .goods-image[data-v-63dc9b54]{width:%?180?%;height:%?180?%;border-radius:4px}.order-detail .order-detail-box .detail-body .detail-body-box .goods-image uni-image[data-v-63dc9b54]{width:100%;height:100%;border:%?1?% solid #e7e7e7;border-radius:4px}.order-detail .order-detail-box .detail-body .detail-body-box .order-info[data-v-63dc9b54]{width:calc(100% - %?200?%);height:%?180?%;padding-left:%?20?%;box-sizing:border-box;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between}.order-detail .order-detail-box .detail-body .detail-body-box .order-info .goods-name[data-v-63dc9b54]{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden;line-height:1.5;font-size:%?28?%}.order-detail .order-detail-box .detail-body .detail-body-box .order-info .goods-sub-section[data-v-63dc9b54]{width:100%;line-height:1.3;display:-webkit-box;display:-webkit-flex;display:flex}.order-detail .order-detail-box .detail-body .detail-body-box .order-info .goods-sub-section .goods-price[data-v-63dc9b54]{font-weight:700;font-size:15px}.order-detail .order-detail-box .detail-body .detail-body-box .order-info .goods-sub-section .unit[data-v-63dc9b54]{font-weight:400;font-size:%?24?%;margin-right:%?2?%}.order-detail .order-detail-box .detail-body .detail-body-box .order-info .goods-sub-section uni-view[data-v-63dc9b54]{-webkit-box-flex:1;-webkit-flex:1;flex:1;line-height:1.3}.order-detail .order-detail-box .detail-body .detail-body-box .order-info .goods-sub-section uni-view[data-v-63dc9b54]:last-of-type{text-align:right}.order-detail .order-detail-box .detail-body .detail-body-box .order-info .goods-sub-section uni-view:last-of-type .iconfont[data-v-63dc9b54]{line-height:1;font-size:%?26?%}.order-detail .order-detail-box .detail-content[data-v-63dc9b54]{width:100%;padding:%?20?%;box-sizing:border-box;border-bottom:%?1?% solid #e7e7e7}.order-detail .order-detail-box .detail-content uni-text[data-v-63dc9b54]{font-size:%?24?%}.order-detail .order-detail-box .detail-content .order-info-item .tit[data-v-63dc9b54]{display:inline-block;width:%?200?%;color:#898989}.order-detail .order-detail-box .detail-footer[data-v-63dc9b54]{width:100%;height:%?100?%;padding:%?20?%;box-sizing:border-box;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.order-money-detail[data-v-63dc9b54]{width:100%;padding:0 %?20?%;box-sizing:border-box;margin-top:%?20?%}.order-money-detail .order-money-detail-box[data-v-63dc9b54]{width:100%;height:100%;padding-top:%?20?%;box-sizing:border-box;background:#fff;border-radius:4px}.order-money-detail .order-money-detail-box .header[data-v-63dc9b54]{width:100%;height:%?70?%;padding:0 %?20?%;border-bottom:%?1?% solid #e7e7e7;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;box-sizing:border-box}.order-money-detail .order-money-detail-box .header .title[data-v-63dc9b54]{padding-left:%?15?%;display:inline-block;position:relative;line-height:1;font-weight:600}.order-money-detail .order-money-detail-box .header .title[data-v-63dc9b54]::before{content:"";display:block;width:%?4?%;height:100%;position:absolute;left:0;top:0;border-radius:%?6?%}.order-money-detail .order-money-detail-box .money-detail-body[data-v-63dc9b54]{width:100%;padding:%?20?%;box-sizing:border-box}.order-money-detail .order-money-detail-box .money-detail-body .order-cell[data-v-63dc9b54]{display:-webkit-box;display:-webkit-flex;display:flex;margin:%?10?% 0;-webkit-box-align:center;-webkit-align-items:center;align-items:center;background:#fff;line-height:%?40?%}.order-money-detail .order-money-detail-box .money-detail-body .order-cell .tit[data-v-63dc9b54]{text-align:left;display:inline-block;width:%?200?%}.order-money-detail .order-money-detail-box .money-detail-body .order-cell .box[data-v-63dc9b54]{-webkit-box-flex:1;-webkit-flex:1;flex:1;line-height:inherit}.order-money-detail .order-money-detail-box .money-detail-body .order-cell .box .textarea[data-v-63dc9b54]{height:%?40?%}.order-money-detail .order-money-detail-box .money-detail-body .order-cell .iconfont[data-v-63dc9b54]{color:#bbb;font-size:%?28?%}.order-money-detail .order-money-detail-box .money-detail-body .order-cell .order-pay[data-v-63dc9b54]{padding:0}.order-money-detail .order-money-detail-box .money-detail-body .order-cell .order-pay uni-text[data-v-63dc9b54]{display:inline-block;margin-left:%?6?%}.order-money-detail .order-money-detail-box .money-detail-body .order-cell uni-text[data-v-63dc9b54]{color:#898989;font-size:%?24?%}',""]),e.exports=t}}]);