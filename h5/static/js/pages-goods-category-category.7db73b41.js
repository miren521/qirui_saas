(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-goods-category-category"],{4451:function(t,e,i){"use strict";i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return s})),i.d(e,"a",(function(){return o}));var o={diyGoodsLevelCategory:i("0853").default,loadingCover:i("1ba8").default,diyBottomNav:i("b864").default},a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"page",class:t.themeStyle},[t._l(t.diyData.value,(function(e,o){return i("v-uni-view",{key:o},["GoodsCategory"==e.controller?[i("diy-goods-level-category",{attrs:{value:e,autoHeight:!1,bottom:t.windowHeight},on:{netFinish:function(e){arguments[0]=e=t.$handleEvent(e),t.netFinish.apply(void 0,arguments)}}})]:t._e()],2)})),i("loading-cover",{ref:"loadingCover"}),i("diy-bottom-nav")],2)},s=[]},5493:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var o={computed:{Development:function(){return this.$store.state.Development},themeStyleScore:function(){return this.$store.state.themeStyle},themeStyle:function(){return"theme-"+this.$store.state.themeStyle},addonIsExit:function(){return this.$store.state.addonIsExit},wholeSaleNumber:function(){return this.$store.state.wholeSaleNumber}}};e.default=o},ca71:function(t,e,i){"use strict";var o=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=o(i("0853")),s=o(i("b864")),r={components:{diyBottomNav:s.default,diyGoodsLevelCategory:a.default},data:function(){return{diyData:[],windowHeight:0}},computed:{themeStyle:function(){return"theme-"+this.$store.state.themeStyle}},onLoad:function(){this.getDiyInfo(),this.getHeight()},onShow:function(){this.$langConfig.refresh(),this.$refs.goodsLevelCategory&&this.$refs.goodsLevelCategory[0].getCartData()},methods:{getHeight:function(){var t=this;uni.getSystemInfo({success:function(e){t.windowHeight=e.windowHeight-57}})},netFinish:function(t){var e=this;t&&this.$refs.loadingCover&&this.$refs.loadingCover.hide(),setTimeout((function(){e.$refs.loadingCover&&e.$refs.loadingCover.hide()}),1e3)},getDiyInfo:function(){var t=this;this.$api.sendRequest({url:"/api/diyview/info",data:{name:"DIYVIEW_GOODS_CATEGORY"},success:function(e){if(0==e.code&&e.data){if(t.diyData=e.data,t.diyData.value){t.diyData=JSON.parse(t.diyData.value);for(var i=0;i<t.diyData.value.length;i++)if("PopWindow"==t.diyData.value[i].controller){setTimeout((function(){if(null!=uni.getStorageSync("index_wap_floating_layer")&&""!=uni.getStorageSync("index_wap_floating_layer")){var e=JSON.parse(uni.getStorageSync("index_wap_floating_layer"));e.closeNum<3&&t.$refs.uniPopup[0].open()}else t.$refs.uniPopup[0].open()}),500);break}}}else t.$refs.loadingCover&&t.$refs.loadingCover.hide()}})}}};e.default=r},ce20:function(t,e,i){"use strict";i.r(e);var o=i("4451"),a=i("ea51");for(var s in a)"default"!==s&&function(t){i.d(e,t,(function(){return a[t]}))}(s);var r,n=i("f0c5"),d=Object(n["a"])(a["default"],o["b"],o["c"],!1,null,"ad1ee38c",null,!1,o["a"],r);e["default"]=d.exports},ea51:function(t,e,i){"use strict";i.r(e);var o=i("ca71"),a=i.n(o);for(var s in o)"default"!==s&&function(t){i.d(e,t,(function(){return o[t]}))}(s);e["default"]=a.a},f8df:function(t,e,i){"use strict";var o=i("4ea4");i("99af"),i("a9e3"),i("b680"),i("e25e"),i("ac1f"),i("1276"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("96cf");var a=o(i("1da1")),s={data:function(){return{categoryAdvImage:"",cartList:[],cateList:[],twoCateList:[],threeCateList:[],join_cart:"join_cart",oneCategoryId:0,oneCategoryIndex:0,TwoCategoryId:0,TwoCategoryIndex:0,threeCategoryId:0,threeCategoryIndex:0,goodsList:[],isAll:!1,isToken:!1,size:10,num:1,isNetwork:1,isLoading:!1,newheight:0,goodsSkuDetail:{},currentRoute:"",token:"",isSafari:!1,cartFlag:!1}},props:{value:{type:Object},autoHeight:{type:Boolean},bottom:{type:[String,Number]},siteId:{type:[Number,String],default:0}},computed:{type:function(){return this.value.template},height:function(){return this.newheight+"px"},addonIsExit:function(){return this.$store.state.addonIsExit},themeStyle:function(){return"theme-"+this.$store.state.themeStyle}},created:function(){var t=this;return(0,a.default)(regeneratorRuntime.mark((function e(){var i;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return i=getCurrentPages()[getCurrentPages().length-1],t.currentRoute="/"+i.route,e.next=4,t.getCategoryList();case 4:t.token=uni.getStorageSync("token");case 5:case"end":return e.stop()}}),e)})))()},methods:{getToken:function(){this.token=uni.getStorageSync("token"),this.token||this.isToken||this.getCategoryList()},getCartData:function(){var t=this,e=this;uni.getStorageSync("token")?this.$api.sendRequest({url:"/api/cart/lists",success:function(i){i.code>=0&&(e.cartList=i.data,3!=t.value.level?e.modifyGoodsCartNum():e.modifyGoodsCartThree(),t.cartFlag=!1),t.$emit("netFinish",!0)},fail:function(t){}}):this.$emit("netFinish",!0)},modifyGoodsCartNum:function(t,e){var i=this,o=i.goodsList,a=i.cartList;for(var s in t=t||1,o){var r=o[s],n=r.sku_id,d=0,u="";if(1==t){for(var l in a){var g=a[l];n==g.sku_id&&(d+=g.num,u=g.cart_id)}r.num=d,r.cart_id=u}else if(2==t&&(d=parseInt(r.num||0),"undefined"!==typeof e&&!app.isEmptyObject(e)&&e.sku_id==n)){"add"==e.type?r.num+=parseInt(e.num||0):"minus"==e.type&&(r.num-=parseInt(e.num||0),r.num<0&&(r.num=0));break}"undefined"!==typeof r.num&&null!==r.num||(r.num=0)}this.goodsList=o,this.$forceUpdate()},modifyGoodsCartThree:function(){var t=this.cateList,e=this.cartList;for(var i in t){var o=t[i];o.sku_id;if(t[i].child_list&&t[i].child_list.length)for(var a in t[i].child_list){var s=t[i].child_list[a],r=s.sku_id,n=0,d="";for(var u in e){var l=e[u];r==s.sku_id&&(n=s.num,d=l.cart_id)}if(s.num=n,s.cart_id=d,s.child_list.length)for(var g in s.child_list){var c=s.child_list[g],h=c.sku_id,f=0,_="";for(var m in e){var v=e[m];h==v.sku_id&&(f=c.num,_=v.cart_id)}if(c.num=f,c.cart_id=_,c.goods_list.length>0)for(var p in c.goods_list){var y=c.goods_list[p],k=y.sku_id,C=0,w="";for(var S in e){var L=e[S];k==L.sku_id&&(C=L.num,w=L.cart_id)}y.num=C,y.cart_id=w}}}}this.cateList=t,this.$forceUpdate()},cartNumChange:function(t,e,i,o,a){var s=this;if(!this.token){var r="/pages/goods/category/category";return this.siteId&&(r="/otherpages/shop/category/category"),void this.$refs.login.open(r)}if(this.cartFlag)return!1;this.cartFlag=!0;var n,d={num:"",cart_id:i,sku_id:o,site_id:a},u={num:"",cart_id:i,sku_id:o},l="";""==i?(n="add",d.num=1,u.num=1,l="/api/cart/add"):"add"==t?(n="edit",d.num=e+1,u.num=e+1,l="/api/cart/edit"):"minus"==t&&(d.num=e-1?e-1:0,u.num=e-1?e-1:0,l=d.num>0?"/api/cart/edit":"/api/cart/delete",n=d.num>0?"edit":"delete"),this.shopDataChange(u),this.$api.sendRequest({url:l,data:d,success:function(t){if(t.code>=0&&t.data>0){if(s.getCartData(),"edit"==n)return!1;u.cart_id="add"==n?t.data:"",s.shopDataChange(u),s.$store.dispatch("getCartNumber")}else u.num=e,s.shopDataChange(u),s.cartFlag=!1}})},shopDataChange:function(t){if(3!=this.value.level)for(var e=0;e<this.goodsList.length;e++){var i=this.goodsList[e];t.sku_id==i.sku_id&&(i.num=t.num,i.cart_id=t.cart_id,this.$forceUpdate())}else for(e=0;e<this.threeCateList.length;e++){i=this.threeCateList[e];for(var o=0;o<i.goods_list.length;o++)i.goods_list[o].sku_id==t.sku_id&&(i.goods_list[o].num=t.num,i.goods_list[o].cart_id=t.cart_id,this.$forceUpdate())}},getCategoryList:function(){var t=this,e="/api/goodscategory/tree",i={level:this.value.level,template:this.value.template};this.siteId&&(i.site_id=this.siteId,e="/api/shopgoodscategory/tree"),this.$api.sendRequest({url:e,data:i,success:function(e){if(0==e.code){if(t.cateList=e.data,!t.cateList.length)return;t.categoryAdvImage=t.cateList[0].image_adv,e.data?t.oneCategoryId=e.data[0].category_id_1:t.$emit("netFinish",!0),1!=t.value.level&&e.data&&e.data[0].child_list&&e.data[0].child_list.length>0&&(t.twoCateList=e.data[0].child_list,t.TwoCategoryId=e.data[0].child_list[0].category_id_2,3==t.value.level&&e.data[0].child_list[0].child_list.length>0&&(t.threeCateList=e.data[0].child_list[0].child_list,t.threeCategoryId=e.data[0].child_list[0].child_list.category_id_3)),2==t.value.level&&3==t.type||1==t.value.level&&3==t.type?t.getGoodsList():3==t.value.level&&3==t.type?t.getCartData():t.$emit("netFinish",!0),setTimeout((function(){var e=uni.createSelectorQuery().in(t);e.select(".category-cate-top").boundingClientRect((function(e){t.newheight=e.height})).exec()}),100)}else t.$util.showToast({title:e.message})}})},getGoodsList:function(){var t=this,e=0,i=0;if(1==this.value.level?(e=this.oneCategoryId,i=1):2==this.value.level&&this.TwoCategoryId?(e=this.TwoCategoryId,i=2):2!=this.value.level||this.TwoCategoryId||(e=this.oneCategoryId,i=1),this.isNetwork&&!this.isAll){this.isNetwork=0,this.isLoading=!0;var o={page:this.num,page_size:this.size};this.siteId?o.shop_category_id=e:(o.category_id=e,o.category_level=i),this.$api.sendRequest({url:"/api/goodssku/page",data:o,success:function(e){if(0==e.code&&e.data){var i=[];0==e.code&&e.data&&(i=e.data.list),1==t.num&&(t.goodsList=[]),t.goodsList=t.goodsList.concat(i),t.goodsList.length==e.data.count&&(t.isAll=!0),t.getCartData()}t.$emit("netFinish",!0),t.num=t.num+1,t.isNetwork=1,t.isLoading=!1}})}},getGoodsSkuDetail:function(t){var e=this;return(0,a.default)(regeneratorRuntime.mark((function i(){var o,a,s,r,n;return regeneratorRuntime.wrap((function(i){while(1)switch(i.prev=i.next){case 0:if(uni.getStorageSync("token")){i.next=3;break}return e.$refs.login.open(e.currentRoute),i.abrupt("return");case 3:return i.next=5,e.$api.sendRequest({url:"/api/goodssku/detail",async:!1,data:{sku_id:t}});case 5:if(o=i.sent,a=o.data,null!=a.goods_sku_detail){if(e.goodsSkuDetail=a.goods_sku_detail,e.goodsSkuDetail.preview=e.preview,e.shopInfo=a.shop_info,0==e.skuId&&(e.skuId=e.goodsSkuDetail.sku_id),e.goodsSkuDetail.video_url&&(e.switchMedia="video"),e.goodsSkuDetail.sku_images=e.goodsSkuDetail.sku_images.split(","),e.goodsSkuDetail.unit=e.goodsSkuDetail.unit||"件",e.goodsSkuDetail.show_price=e.goodsSkuDetail.discount_price,e.goodsSkuDetail.sku_spec_format&&(e.goodsSkuDetail.sku_spec_format=JSON.parse(e.goodsSkuDetail.sku_spec_format)),e.goodsSkuDetail.goods_attr_format)for(s=JSON.parse(e.goodsSkuDetail.goods_attr_format),e.goodsSkuDetail.goods_attr_format=JSON.parse(e.goodsSkuDetail.goods_attr_format),e.goodsSkuDetail.goods_attr_format=e.$util.unique(e.goodsSkuDetail.goods_attr_format,"attr_id"),r=0;r<e.goodsSkuDetail.goods_attr_format.length;r++)for(n=0;n<s.length;n++)e.goodsSkuDetail.goods_attr_format[r].attr_id==s[n].attr_id&&e.goodsSkuDetail.goods_attr_format[r].attr_value_id!=s[n].attr_value_id&&(e.goodsSkuDetail.goods_attr_format[r].attr_value_name+="、"+s[n].attr_value_name);e.goodsSkuDetail.goods_spec_format&&(e.goodsSkuDetail.goods_spec_format=JSON.parse(e.goodsSkuDetail.goods_spec_format)),e.contactData={title:e.goodsSkuDetail.sku_name,path:"/pages/goods/detail/detail?sku_id="+e.skuId,img:e.$util.img(e.goodsSkuDetail.sku_image,{size:"big"})},e.$refs.goodSkuNew.show("join_cart",e.goodsSkuDetail,(function(){e.getCartCount()}))}else e.$util.redirectTo("/pages/index/index/index",{},"reLaunch");case 8:case"end":return i.stop()}}),i)})))()},refreshGoodsSkuDetail:function(t){Object.assign(this.goodsSkuDetail,t)},getCartCount:function(){var t=this;this.preview||this.$store.dispatch("getCartNumber").then((function(e){t.cartCount=e}))},toDetail:function(t){this.$util.redirectTo("/pages/goods/detail/detail",{sku_id:t})},toListDetail:function(t,e){var i={category_id:t,category_level:e},o="/pages/goods/list/list";this.siteId&&(o="/otherpages/shop/list/list",i.site_id=this.siteId),this.$util.redirectTo(o,i)},selectOneCategory:function(t,e,i){var o=this;this.oneCategoryId=t,this.oneCategoryIndex=e,this.categoryAdvImage=this.cateList[this.oneCategoryIndex].image_adv,this.value.level>1&&(this.twoCateList=this.cateList[this.oneCategoryIndex].child_list?this.cateList[this.oneCategoryIndex].child_list:[],this.twoCateList.length?(this.TwoCategoryId=this.twoCateList[0].category_id_2,this.TwoCategoryIndex=0):(this.TwoCategoryId=null,this.TwoCategoryIndex=null),3==this.value.level&&(this.twoCateList.length?this.threeCateList=this.twoCateList[this.TwoCategoryIndex].child_list:this.threeCateList=[])),2==this.value.level&&3==this.type&&(this.num=1,this.isAll=!1,this.isNetwork=!0,this.goodsList=[],this.getGoodsList()),1==this.value.level&&3==this.type&&(this.num=1,this.isAll=!1,this.isNetwork=!0,this.goodsList=[],this.getGoodsList()),setTimeout((function(){var t=uni.createSelectorQuery().in(o);t.select(".category-cate-top").boundingClientRect((function(t){o.newheight=t.height})).exec()}),100)},selectTwoCategory:function(t,e,i){this.TwoCategoryId=t,this.TwoCategoryIndex=e,2==this.value.level&&0==i&&(this.num=1,this.isAll=!1,this.isNetwork=!0,this.goodsList=[],this.getGoodsList()),3==this.value.level&&this.twoCateList.length&&(this.threeCateList=this.twoCateList[e].child_list),2==this.value.level&&3==this.type&&(this.num=1,this.isAll=!1,this.isNetwork=!0,this.goodsList=[],this.getGoodsList()),1==this.value.level&&3==this.type&&(this.num=1,this.isAll=!1,this.isNetwork=!0,this.goodsList=[],this.getGoodsList())},selectThreeCategory:function(){this.toListDetail()},goodsImageError:function(t,e){this.goodsList[t].sku_image=this.$util.getDefaultImage().default_goods_img,this.$forceUpdate()},cateImageError:function(t,e){1==e?(this.cateList[t].image=this.$util.getDefaultImage().default_goods_img,this.$forceUpdate()):2==e&&(this.twoCateList[t].image=this.$util.getDefaultImage().default_goods_img,this.$forceUpdate())},threeGoodsImageError:function(t,e){this.threeCateList[t].goods_list[e].sku_image=this.$util.getDefaultImage().default_goods_img,this.$forceUpdate()},threeCateImageError:function(t,e){this.twoCateList[t].child_list[e].image=this.$util.getDefaultImage().default_goods_img,this.$forceUpdate()},price:function(t){var e=Number(t.price),i=Number(t.member_price?t.member_price:1e20),o=1e20;return o=1==t.promotion_type?Number(t.discount_price?t.discount_price:1e20):1e20,e<=i&&e<=o?e.toFixed(2):i<=o&&i<=e?i.toFixed(2):o.toFixed(2)},priceLogo:function(t){var e=Number(t.price),i=Number(t.member_price?t.member_price:1e20),o=1e20;return o=1==t.promotion_type?Number(t.discount_price?t.discount_price:1e20):1e20,e<=i&&e<=o?"price":i<=o&&i<=e?"member_price":"discount_price"}}};e.default=s}}]);