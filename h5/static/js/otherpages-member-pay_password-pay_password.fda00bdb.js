(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["otherpages-member-pay_password-pay_password"],{"0378":function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@-webkit-keyframes twinkling-data-v-c51575fa{0%{opacity:.2}50%{opacity:.5}100%{opacity:.2}}@keyframes twinkling-data-v-c51575fa{0%{opacity:.2}50%{opacity:.5}100%{opacity:.2}}.code-box[data-v-c51575fa]{text-align:center}.flex-box[data-v-c51575fa]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-flex-wrap:wrap;flex-wrap:wrap;position:relative}.flex-box .hide-input[data-v-c51575fa]{position:absolute;top:0;left:-100%;width:200%;height:100%;text-align:left;z-index:9;opacity:1}.flex-box .item[data-v-c51575fa]{position:relative;-webkit-box-flex:1;-webkit-flex:1;flex:1;margin-right:%?18?%;font-size:%?70?%;font-weight:700;color:#333;line-height:%?100?%}.flex-box .item[data-v-c51575fa]::before{content:"";padding-top:100%;display:block}.flex-box .item[data-v-c51575fa]:last-child{margin-right:0}.flex-box .middle[data-v-c51575fa]{border:none}.flex-box .box[data-v-c51575fa]{box-sizing:border-box;border:%?2?% solid #ccc;border-width:%?2?% 0 %?2?% %?2?%;margin-right:0}.flex-box .box[data-v-c51575fa]:first-of-type{border-top-left-radius:%?8?%;border-bottom-left-radius:%?8?%}.flex-box .box[data-v-c51575fa]:last-child{border-right:%?2?% solid #ccc;border-top-right-radius:%?8?%;border-bottom-right-radius:%?8?%}.flex-box .bottom[data-v-c51575fa]{box-sizing:border-box;border-bottom:1px solid #ddd}.flex-box .active[data-v-c51575fa]{border-color:#ddd}.flex-box .active .line[data-v-c51575fa]{display:block}.flex-box .line[data-v-c51575fa]{display:none;position:absolute;left:50%;top:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);width:%?2?%;height:%?40?%;background:#333;-webkit-animation:twinkling-data-v-c51575fa 1s infinite ease;animation:twinkling-data-v-c51575fa 1s infinite ease}.flex-box .dot[data-v-c51575fa],\n.flex-box .number[data-v-c51575fa]{font-size:%?44?%;line-height:%?40?%;position:absolute;left:50%;top:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.flex-box .bottom-line[data-v-c51575fa]{height:4px;background:#000;width:80%;position:absolute;border-radius:2px;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}',""]),t.exports=e},"0433":function(t,e,i){"use strict";i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return a}));var a={mypOne:i("0c26").default},n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{class:t.themeStyle},[i("v-uni-view",{staticClass:"container"},[0!=t.step?i("v-uni-view",{staticClass:"tips"},[t._v("请输入6位支付密码，建议不要使用重复或连续数字")]):i("v-uni-view",{staticClass:"tips"},[t._v("验证码已发送至"+t._s(t._f("mobile")(t.memberInfo.mobile))+"请在下方输入4位数字验证码")]),i("v-uni-view",{staticClass:"password-wrap"},[i("myp-one",{ref:"input",attrs:{maxlength:0==t.step?4:6,"is-pwd":0!=t.step,"auto-focus":!0},on:{input:function(e){arguments[0]=e=t.$handleEvent(e),t.input.apply(void 0,arguments)}}}),i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:0==t.step,expression:"step == 0"}],staticClass:"dynacode",class:120==t.dynacodeData.seconds?"ns-text-color":"ns-text-color-gray",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.sendMobileCode.apply(void 0,arguments)}}},[t._v(t._s(t.dynacodeData.codeText))]),i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:0==t.step,expression:"step == 0"}],staticClass:"operation-tips"},[t._v("输入短信验证码")]),i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:1==t.step,expression:"step == 1"}],staticClass:"operation-tips"},[t._v("请设置支付密码")]),i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:2==t.step,expression:"step == 2"}],staticClass:"operation-tips"},[t._v("请再次输入")]),i("v-uni-view",{staticClass:"btn ns-bg-color ns-border-color",class:{disabled:!t.isClick},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.confirm.apply(void 0,arguments)}}},[t._v("确认")])],1)],1)],1)},o=[]},"0c26":function(t,e,i){"use strict";i.r(e);var a=i("ebbe"),n=i("2fb8");for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);i("cd77");var s,r=i("f0c5"),c=Object(r["a"])(n["default"],a["b"],a["c"],!1,null,"c51575fa",null,!1,a["a"],s);e["default"]=c.exports},"228b":function(t,e,i){var a=i("0378");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("3ebedbe0",a,!0,{sourceMap:!1,shadowMode:!1})},"2fb8":function(t,e,i){"use strict";i.r(e);var a=i("ac75"),n=i.n(a);for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);e["default"]=n.a},"393a":function(t,e,i){"use strict";i.r(e);var a=i("465c"),n=i.n(a);for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);e["default"]=n.a},4148:function(t,e,i){"use strict";i.r(e);var a=i("0433"),n=i("393a");for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);i("9fd1");var s,r=i("f0c5"),c=Object(r["a"])(n["default"],a["b"],a["c"],!1,null,"d4a01d4c",null,!1,a["a"],s);e["default"]=c.exports},"465c":function(t,e,i){"use strict";var a=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=a(i("0c26")),o={components:{mypOne:n.default},data:function(){return{isClick:!1,step:1,key:"",code:"",password:"",repassword:"",isSub:!1,back:"",memberInfo:{},dynacodeData:{seconds:120,timer:null,codeText:"获取验证码",isSend:!1}}},computed:{themeStyle:function(){return"theme-"+this.$store.state.themeStyle}},methods:{input:function(t){0==this.step?4==t.length?(this.isClick=!0,this.code=t):this.isClick=!1:1==this.step?6==t.length?(this.isClick=!0,this.password=t):this.isClick=!1:6==t.length?(this.isClick=!0,this.repassword=t):this.isClick=!1},confirm:function(){var t=this;if(this.isClick)if(0==this.step)this.$api.sendRequest({url:"/api/member/verifypaypwdcode",data:{code:this.code,key:this.key},success:function(e){0==e.code?(t.$refs.input.clear(),t.isClick=!1,t.step=1):t.$util.showToast({title:e.message})}});else if(1==this.step)this.$refs.input.clear(),this.isClick=!1,this.step=2;else if(this.password==this.repassword){if(this.isSub)return;this.isSub=!0,this.$api.sendRequest({url:"/api/member/modifypaypassword",data:{key:this.key,code:this.code,password:this.password},success:function(e){e.code>=0?t.back?t.$util.redirectTo(t.back,{},"redirectTo"):t.$util.redirectTo("/pages/member/index/index",{},"reLaunch"):(t.initInfo(),t.$util.showToast({title:e.message}))}})}else this.$util.showToast({title:"两次输入的密码不一致",success:function(e){t.initInfo()}})},initInfo:function(){this.isClick=!1,this.step=1,this.password="",this.repassword="",this.oldpassword="",this.isSub=!1,this.$refs.input.clear()},getMemberInfo:function(){var t=this;this.$api.sendRequest({url:"/api/member/info",success:function(e){if(-1==e.code&&"TOKEN_ERROR"==e.error_code){t.$util.showToast({title:"登录失败"});var i=getCurrentPages(),a=i[i.length-1].options,n=i[i.length-1].route;return a.back=n,void setTimeout((function(){t.$util.redirectTo("/pages/login/login/login",a)}),1500)}0==e.code&&(t.memberInfo=e.data,""==t.memberInfo.mobile?uni.showModal({title:"提示",content:"设置支付密码需要先绑定手机号,是否立即绑定?",success:function(e){e.confirm?t.$util.redirectTo("/otherpages/member/info/info",{action:"mobile",back:t.back},"redirectTo"):t.back?t.$util.redirectTo(t.back):t.$util.redirectTo("/pages/member/index/index",{},"redirectTo")}}):(t.step=0,t.sendMobileCode()))}})},sendMobileCode:function(){var t=this;120==this.dynacodeData.seconds&&(this.dynacodeData.isSend||(this.dynacodeData.isSend=!0,this.$api.sendRequest({url:"/api/member/paypwdcode",success:function(e){t.dynacodeData.isSend=!1,e.code>=0?(t.key=e.data.key,120==t.dynacodeData.seconds&&null==t.dynacodeData.timer&&(t.dynacodeData.timer=setInterval((function(){t.dynacodeData.seconds--,t.dynacodeData.codeText=t.dynacodeData.seconds+"s后可重新获取"}),1e3))):t.$util.showToast({title:e.message})},fail:function(){t.$util.showToast({title:"request:fail"}),t.dynacodeData.isSend=!1}})))}},onLoad:function(t){this.$langConfig.refresh(),t.back&&(this.back=t.back),uni.getStorageSync("token")?this.getMemberInfo():this.$util.redirectTo("/pages/login/login/login")},filters:{mobile:function(t){return t.substring(0,3)+"****"+t.substring(7)}},watch:{"dynacodeData.seconds":{handler:function(t,e){0==t&&(clearInterval(this.dynacodeData.timer),this.dynacodeData={seconds:120,timer:null,codeText:"获取动态码",isSend:!1})},immediate:!0,deep:!0}}};e.default=o},"60f4":function(t,e,i){var a=i("f0ec");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("7957e355",a,!0,{sourceMap:!1,shadowMode:!1})},"9fd1":function(t,e,i){"use strict";var a=i("60f4"),n=i.n(a);n.a},ac75:function(t,e,i){"use strict";i("a15b"),i("a9e3"),i("ac1f"),i("1276"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={name:"mypOneInput",props:{value:{type:String,default:""},maxlength:{type:Number,default:4},autoFocus:{type:Boolean,default:!1},isPwd:{type:Boolean,default:!1},type:{type:String,default:"bottom"}},watch:{maxlength:{immediate:!0,handler:function(t){this.ranges=6===t?[1,2,3,4,5,6]:[1,2,3,4]}},value:{immediate:!0,handler:function(t){t!==this.inputValue&&(this.inputValue=t,this.toMakeAndCheck(t))}}},data:function(){return{inputValue:"",codeIndex:1,codeArr:[],ranges:[1,2,3,4]}},methods:{getVal:function(t){var e=t.detail.value;this.inputValue=e,this.$emit("input",e),this.toMakeAndCheck(e)},toMakeAndCheck:function(t){var e=t.split("");this.codeIndex=e.length+1,this.codeArr=e,this.codeIndex>Number(this.maxlength)&&this.$emit("finish",this.codeArr.join(""))},set:function(t){this.inputValue=t,this.toMakeAndCheck(t)},clear:function(){this.inputValue="",this.codeArr=[],this.codeIndex=1}}};e.default=a},cd77:function(t,e,i){"use strict";var a=i("228b"),n=i.n(a);n.a},ebbe:function(t,e,i){"use strict";var a;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return a}));var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"code-box"},[i("v-uni-view",{staticClass:"flex-box"},[i("v-uni-input",{staticClass:"hide-input",attrs:{value:t.inputValue,type:"number",focus:t.autoFocus,maxlength:t.maxlength},on:{input:function(e){arguments[0]=e=t.$handleEvent(e),t.getVal.apply(void 0,arguments)}}}),t._l(t.ranges,(function(e,a){return[i("v-uni-view",{key:a+"_0",class:["item",{active:t.codeIndex===e,middle:"middle"===t.type,bottom:"bottom"===t.type,box:"box"===t.type}]},["middle"!==t.type?i("v-uni-view",{staticClass:"line"}):t._e(),"middle"===t.type&&t.codeIndex<=e?i("v-uni-view",{staticClass:"bottom-line"}):t._e(),t.isPwd&&t.codeArr.length>=e?[i("v-uni-text",{staticClass:"dot"},[t._v("●")])]:[i("v-uni-text",{staticClass:"number"},[t._v(t._s(t.codeArr[a]?t.codeArr[a]:""))])]],2)]}))],2)],1)},o=[]},f0ec:function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/**  -------------------------------------------------------------------默认主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------蓝色主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------绿色主题颜色配置-------------------------------------------------- */\n/* 文字基本颜色 */\n/* 文字尺寸 */.container[data-v-d4a01d4c]{width:100vw;height:100vh;background:#fff}.container .tips[data-v-d4a01d4c]{width:60%;margin:0 auto;text-align:center;padding-top:%?100?%}.container .password-wrap[data-v-d4a01d4c]{width:80%;margin:0 auto;margin-top:%?40?%}.container .password-wrap .operation-tips[data-v-d4a01d4c]{text-align:center;font-weight:600;margin-top:%?80?%}.container .password-wrap .dynacode[data-v-d4a01d4c]{line-height:1;margin-top:%?20?%;font-size:%?24?%}.container .password-wrap .btn[data-v-d4a01d4c]{margin:0 auto;margin-top:%?30?%;height:%?80?%;line-height:%?80?%;border-radius:%?80?%;color:#fff;text-align:center;border:1px solid}.container .password-wrap .btn.disabled[data-v-d4a01d4c]{background:#ccc!important;border-color:#ccc!important;color:#fff}',""]),t.exports=e}}]);