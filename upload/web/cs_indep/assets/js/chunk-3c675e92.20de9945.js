(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-3c675e92"],{"03f0":function(t,e,i){"use strict";var s=i("5560"),a=i.n(s);a.a},"498a":function(t,e,i){"use strict";var s=i("23e7"),a=i("58a8").trim,o=i("c8d2");s({target:"String",proto:!0,forced:o("trim")},{trim:function(){return a(this)}})},"54c8":function(t,e,i){},5560:function(t,e,i){},5899:function(t,e){t.exports="\t\n\v\f\r                　\u2028\u2029\ufeff"},"58a8":function(t,e,i){var s=i("1d80"),a=i("5899"),o="["+a+"]",n=RegExp("^"+o+o+"*"),r=RegExp(o+o+"*$"),d=function(t){return function(e){var i=String(s(e));return 1&t&&(i=i.replace(n,"")),2&t&&(i=i.replace(r,"")),i}};t.exports={start:d(1),end:d(2),trim:d(3)}},7631:function(t,e,i){"use strict";var s=i("54c8"),a=i.n(s);a.a},c8d2:function(t,e,i){var s=i("d039"),a=i("5899"),o="​᠎";t.exports=function(t){return s((function(){return!!a[t]()||o[t]()!=o||a[t].name!==t}))}},dea0:function(t,e,i){"use strict";i.d(e,"c",(function(){return a})),i.d(e,"e",(function(){return o})),i.d(e,"a",(function(){return n})),i.d(e,"f",(function(){return r})),i.d(e,"b",(function(){return d})),i.d(e,"d",(function(){return l})),i.d(e,"g",(function(){return c})),i.d(e,"h",(function(){return u}));var s=i("751a");function a(t){return Object(s["a"])({url:"/api/order/lists",data:t,forceLogin:!0})}function o(t){return Object(s["a"])({url:"/api/order/pay",data:t,forceLogin:!0})}function n(t){return Object(s["a"])({url:"/api/order/close",data:t,forceLogin:!0})}function r(t){return Object(s["a"])({url:"/api/order/takedelivery",data:t,forceLogin:!0})}function d(t){return Object(s["a"])({url:"/api/order/detail",data:t,forceLogin:!0})}function l(t){return Object(s["a"])({url:"/api/order/package",data:t,forceLogin:!0})}function c(t){return Object(s["a"])({url:"/api/order/evluateinfo",data:t,forceLogin:!0})}function u(t){var e="";return e=t.isEvaluate?"/api/goodsevaluate/again":"/api/goodsevaluate/add",Object(s["a"])({url:e,data:t,forceLogin:!0})}},f76d:function(t,e,i){"use strict";i.r(e);var s=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("el-form",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticClass:"ns-evalute"},[t._l(t.goodsList,(function(e,s){return i("div",{key:s,staticClass:"ns-eva-li"},[i("div",{staticClass:"ns-eva-good"},[i("el-image",{attrs:{fit:"scale-down",src:t.$img(e.sku_image,{size:"mid"})},on:{error:function(e){return t.imageError(s)},click:function(i){return t.toGoodsDetail(e.sku_id)}}}),i("p",{staticClass:"ns-eva-good-name",attrs:{title:e.sku_name},on:{click:function(i){return t.toGoodsDetail(e.sku_id)}}},[t._v(t._s(e.sku_name))]),i("p",[t._v("￥"+t._s(e.price))])],1),i("div",{staticClass:"ns-eva-form"},[t.isEvaluate?t._e():i("div",{staticClass:"block"},[i("span",{staticClass:"demonstration"},[t._v("描述相符：")]),i("el-rate",{on:{change:function(e){return t.setStar(s)}},model:{value:t.goodsEvalList[s].scores,callback:function(e){t.$set(t.goodsEvalList[s],"scores",e)},expression:"goodsEvalList[index].scores"}}),i("div",{staticClass:"level"},[i("i",{staticClass:"iconfont",class:"1"==t.goodsEvalList[s].explain_type?"iconhaoping1 ns-text-color":"2"==t.goodsEvalList[s].explain_type?"iconzhongchaping ns-text-color":"3"==t.goodsEvalList[s].explain_type?"iconzhongchaping":""}),i("span",[t._v(" "+t._s("1"==t.goodsEvalList[s].explain_type?"好评":"2"==t.goodsEvalList[s].explain_type?"中评":"3"==t.goodsEvalList[s].explain_type?"差评":"")+" ")])])],1),i("div",{staticClass:"ns-textarea"},[t.isEvaluate?i("el-input",{attrs:{type:"textarea",rows:5,placeholder:"请在此处输入您的追评",maxlength:"200","show-word-limit":""},model:{value:t.goodsEvalList[s].again_content,callback:function(e){t.$set(t.goodsEvalList[s],"again_content",e)},expression:"goodsEvalList[index].again_content"}}):i("el-input",{attrs:{type:"textarea",rows:5,placeholder:"请在此处输入您的评价",maxlength:"200","show-word-limit":""},model:{value:t.goodsEvalList[s].content,callback:function(e){t.$set(t.goodsEvalList[s],"content",e)},expression:"goodsEvalList[index].content"}})],1),i("el-upload",{ref:"upload",refInFor:!0,class:{ishide:t.hide[s]},attrs:{action:t.uploadActionUrl,"list-type":"picture-card","on-success":function(e,i){return t.handleSuccess(e,i,s)},"on-preview":t.handlePictureCardPreview,"on-remove":function(e,i){return t.handleRemove(e,i,s)},"on-exceed":t.handleExceed}},[i("i",{staticClass:"el-icon-plus"})]),i("el-dialog",{attrs:{visible:t.dialogVisible},on:{"update:visible":function(e){t.dialogVisible=e}}},[i("img",{attrs:{width:"100%",src:t.dialogImageUrl,alt:""}})]),i("span",[t._v("共6张，还能上传"+t._s(t.imgList[s].length?6-t.imgList[s].length:6)+"张")])],1)])})),t.isEvaluate?t._e():i("div",{staticClass:"ns-eva-public"},[i("div",{staticClass:"ns-eva-wrap"},[i("p",[t._v(t._s(t.siteName))]),i("div",{staticClass:"block"},[i("span",{staticClass:"demonstration"},[t._v("配送服务：")]),i("el-rate",{model:{value:t.shop_deliverycredit,callback:function(e){t.shop_deliverycredit=e},expression:"shop_deliverycredit"}})],1),i("div",{staticClass:"block"},[i("span",{staticClass:"demonstration"},[t._v("描述相符：")]),i("el-rate",{model:{value:t.shop_desccredit,callback:function(e){t.shop_desccredit=e},expression:"shop_desccredit"}})],1),i("div",{staticClass:"block"},[i("span",{staticClass:"demonstration"},[t._v("服务态度：")]),i("el-rate",{model:{value:t.shop_servicecredit,callback:function(e){t.shop_servicecredit=e},expression:"shop_servicecredit"}})],1),i("el-checkbox",{model:{value:t.isAnonymous,callback:function(e){t.isAnonymous=e},expression:"isAnonymous"}},[t._v("匿名")])],1)]),i("div",{staticClass:"save-btn-wrap"},[i("el-button",{attrs:{type:"primary"},on:{click:t.save}},[t._v("提交")])],1)],2)},a=[],o=(i("99af"),i("a434"),i("d3b7"),i("25f0"),i("498a"),i("5530")),n=i("2f62"),r=i("dea0"),d=i("01ea"),l={name:"evaluate",components:{},data:function(){return{loading:!0,value1:5,memberName:"",memberNeadimg:"",orderId:null,orderNo:"",isAnonymous:0,goodsList:[],goodsEvalList:[],imgList:[],isEvaluate:0,flag:!1,siteName:"",shop_deliverycredit:5,shop_desccredit:5,shop_servicecredit:5,uploadActionUrl:d["a"].baseUrl+"/api/upload/evaluateimg",dialogImageUrl:"",dialogVisible:!1,hide:[]}},created:function(){this.orderId=this.$route.query.order_id,this.getUserInfo(),this.orderId&&this.getOrderInfo()},computed:Object(o["a"])({},Object(n["b"])(["defaultGoodsImage"])),methods:{handleSuccess:function(t,e,i){var s=this.imgList[i];s=s.concat(t.data.pic_path),this.imgList[i]=[],this.$set(this.imgList,i,s),this.isEvaluate?this.goodsEvalList[i].again_images=this.imgList[i].toString():this.goodsEvalList[i].images=this.imgList[i].toString(),this.imgList[i].length>=6&&(this.hide[i]=!0)},handleRemove:function(t,e,i){var s=util.inArray(t.response.data.pic_path,this.imgList[i]);this.imgList[i].splice(s,1),this.isEvaluate?this.goodsEvalList[i].again_images=this.imgList[i].toString():this.goodsEvalList[i].images=this.imgList[i].toString(),this.imgList[i].length<6&&(this.hide[i]=!1)},handleExceed:function(t,e){this.$message.warning("上传图片最大数量为6张")},handlePictureCardPreview:function(t){this.dialogImageUrl=t.url,this.dialogVisible=!0},getUserInfo:function(){var t=this;this.$store.dispatch("member/member_detail",{refresh:1}).then((function(e){t.memberName=e.data.nickname,t.memberNeadimg=e.data.headimg})).catch((function(e){t.$message.error(e.message)}))},getOrderInfo:function(){var t=this;Object(r["g"])({order_id:this.orderId}).then((function(e){if(0==e.code)if(t.isEvaluate=e.data.evaluate_status,t.orderNo=e.data.list[0].order_no,t.goodsList=e.data.list,t.siteName=e.data.list[0].site_name,t.isEvaluate)for(var i=0;i<e.data.list.length;i++){var s=[];t.imgList.push(s),t.hide.push(!1),t.goodsEvalList.push({order_goods_id:e.data.list[i].order_goods_id,goods_id:e.data.list[i].goods_id,sku_id:e.data.list[i].sku_id,again_content:"",again_images:"",site_id:e.data.list[i].site_id})}else for(var a=0;a<e.data.list.length;a++){var o=[];t.imgList.push(o),t.goodsEvalList.push({content:"",images:"",scores:5,explain_type:1,order_goods_id:e.data.list[a].order_goods_id,goods_id:e.data.list[a].goods_id,sku_id:e.data.list[a].sku_id,sku_name:e.data.list[a].sku_name,sku_price:e.data.list[a].price,sku_image:e.data.list[a].sku_image,site_id:e.data.list[a].site_id})}t.loading=!1})).catch((function(e){t.$message.error(e.message),t.$router.push("/member/order_list"),t.loading=!1}))},setStar:function(t){this.goodsEvalList[t].scores>=4?this.goodsEvalList[t].explain_type=1:1<this.goodsEvalList[t].scores&&this.goodsEvalList[t].scores<4?this.goodsEvalList[t].explain_type=2:this.goodsEvalList[t].explain_type=3},imageError:function(t){this.goodsList[t].sku_image=this.defaultGoodsImage},save:function(){for(var t=this,e=0;e<this.goodsEvalList.length;e++)if(this.isEvaluate){if(!this.goodsEvalList[e].again_content.trim().length)return void this.$message({message:"商品的评价不能为空哦",type:"warning"})}else if(!this.goodsEvalList[e].content.trim().length)return void this.$message({message:"商品的评价不能为空哦",type:"warning"});var i=JSON.stringify(this.goodsEvalList),s={order_id:this.orderId,goods_evaluate:i,isEvaluate:this.isEvaluate};this.isEvaluate||(s.order_no=this.orderNo,s.member_name=this.memberName,s.member_headimg=this.memberNeadimg,s.is_anonymous=this.isAnonymous,s.shop_deliverycredit=this.shop_deliverycredit,s.shop_desccredit=this.shop_desccredit,s.shop_servicecredit=this.shop_servicecredit),this.flag||(this.flag=!0,Object(r["h"])(s).then((function(e){0==e.code?t.$message({message:"评价成功",type:"success",duration:2e3,onClose:function(){t.$router.push({path:"/member/order_list"})}}):(t.$message({message:e.message,type:"warning"}),t.flag=!1)})).catch((function(e){t.$message.error(e.message),t.flag=!1})))},toGoodsDetail:function(t){this.$router.pushToTab("sku-"+t)}}},c=l,u=(i("7631"),i("03f0"),i("2877")),g=Object(u["a"])(c,s,a,!1,null,"4de193c2",null);e["default"]=g.exports}}]);
//# sourceMappingURL=chunk-3c675e92.20de9945.js.map