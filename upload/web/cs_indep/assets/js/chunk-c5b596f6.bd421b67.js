(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-c5b596f6"],{"498a":function(e,t,r){"use strict";var s=r("23e7"),i=r("58a8").trim,n=r("c8d2");s({target:"String",proto:!0,forced:n("trim")},{trim:function(){return i(this)}})},5899:function(e,t){e.exports="\t\n\v\f\r                　\u2028\u2029\ufeff"},"58a8":function(e,t,r){var s=r("1d80"),i=r("5899"),n="["+i+"]",a=RegExp("^"+n+n+"*"),o=RegExp(n+n+"*$"),c=function(e){return function(t){var r=String(s(t));return 1&e&&(r=r.replace(a,"")),2&e&&(r=r.replace(o,"")),r}};e.exports={start:c(1),end:c(2),trim:c(3)}},"6d75":function(e,t,r){"use strict";r.r(t);var s=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"register"},[r("div",{staticClass:"box-card"},[r("div",{staticClass:"register-title"},[e._v("用户注册")]),r("div",{staticClass:"register-account"},[r("el-form",{ref:"registerRef",attrs:{model:e.registerForm,rules:e.registerRules,"label-width":"80px","label-position":"right","show-message":""}},[r("el-form-item",{attrs:{label:"用户名",prop:"username"}},[r("el-input",{attrs:{placeholder:"请输入用户名"},model:{value:e.registerForm.username,callback:function(t){e.$set(e.registerForm,"username",t)},expression:"registerForm.username"}})],1),r("el-form-item",{attrs:{label:"密码",prop:"password"}},[r("el-input",{attrs:{placeholder:"请输入密码",type:"password"},model:{value:e.registerForm.password,callback:function(t){e.$set(e.registerForm,"password",t)},expression:"registerForm.password"}})],1),r("el-form-item",{attrs:{label:"确认密码",prop:"checkPass"}},[r("el-input",{attrs:{placeholder:"请输入确认密码",type:"password"},model:{value:e.registerForm.checkPass,callback:function(t){e.$set(e.registerForm,"checkPass",t)},expression:"registerForm.checkPass"}})],1),r("el-form-item",{attrs:{label:"验证码",prop:"code"}},[r("el-input",{attrs:{placeholder:"请输入验证码",maxlength:"4"},model:{value:e.registerForm.code,callback:function(t){e.$set(e.registerForm,"code",t)},expression:"registerForm.code"}},[r("template",{slot:"append"},[r("img",{staticClass:"captcha",attrs:{src:e.captcha.img,mode:""},on:{click:e.getCode}})])],2)],1)],1),r("div",{staticClass:"xy",on:{click:e.check}},[r("div",{staticClass:"xy-wrap"},[r("div",{staticClass:"iconfont",class:e.ischecked?"iconxuanze-duoxuan":"iconxuanze"}),r("div",{staticClass:"content"},[e._v(" 阅读并同意 "),r("b",{on:{click:function(t){return t.stopPropagation(),e.getAggrement(t)}}},[e._v("《服务协议》")])])]),r("div",{staticClass:"toLogin",on:{click:e.toLogin}},[e._v("已有账号，立即登录")])]),r("el-button",{on:{click:e.register}},[e._v("立即注册")])],1),r("el-dialog",{attrs:{title:e.agreement.title,visible:e.aggrementVisible,width:"60%","before-close":e.aggrementClose,"lock-scroll":!1,center:""},on:{"update:visible":function(t){e.aggrementVisible=t}}},[r("div",{staticClass:"xyContent",domProps:{innerHTML:e._s(e.agreement.content)}})])],1)])},i=[],n=(r("c975"),r("ac1f"),r("5319"),r("498a"),r("485b")),a=r("37cb"),o={name:"register",components:{},data:function(){var e=this,t=function(t,r,s){""===r?s(new Error("请再次输入密码")):r!==e.registerForm.password?s(new Error("两次输入密码不一致!")):s()},r=this,s=function(e,t,s){var i=r.registerConfig;if(!t)return s(new Error("请输入密码"));if(i.pwd_len>0){if(t.length<i.pwd_len)return s(new Error("密码长度不能小于"+i.pwd_len+"位"));s()}else if(""!=i.pwd_complexity){var n="密码需包含",a="";if(-1!=i.pwd_complexity.indexOf("number")?(a+="(?=.*?[0-9])",n+="数字"):-1!=i.pwd_complexity.indexOf("letter")?(a+="(?=.*?[a-z])",n+="、小写字母"):-1!=i.pwd_complexity.indexOf("upper_case")?(a+="(?=.*?[A-Z])",n+="、大写字母"):-1!=i.pwd_complexity.indexOf("symbol")?(a+="(?=.*?[#?!@$%^&*-])",n+="、特殊字符"):(a+="",n+=""),a.test(t))return s(new Error(n));s()}};return{registerForm:{username:"",password:"",checkPass:"",code:""},registerRules:{username:[{required:!0,message:"请输入用户名",trigger:"blur"}],password:[{required:!0,validator:s,trigger:"blur"}],checkPass:[{required:!0,validator:t,trigger:"blur"}],code:[{required:!0,message:"请输入验证码",trigger:"blur"}]},ischecked:!1,agreement:"",aggrementVisible:!1,captcha:{id:"",img:""},registerConfig:{}}},created:function(){this.getCode(),this.regisiterAggrement(),this.getRegisterConfig()},methods:{check:function(){this.ischecked=!this.ischecked},toLogin:function(){this.$router.push("/login")},getRegisterConfig:function(){var e=this;Object(n["c"])().then((function(t){t.code>=0&&(e.registerConfig=t.data.value,1!=e.registerConfig.is_enable&&e.$message({message:"平台未启用注册",type:"warning",duration:2e3,onClose:function(){e.$router.push("/")}}))})).catch((function(e){console.log(e.message)}))},register:function(){var e=this;this.$refs.registerRef.validate((function(t){if(!t)return!1;if(!e.ischecked)return e.$message({message:"请先阅读协议并勾选",type:"warning"});var r={username:e.registerForm.username.trim(),password:e.registerForm.password};""!=e.captcha.id&&(r.captcha_id=e.captcha.id,r.captcha_code=e.registerForm.code),e.$store.dispatch("member/register_token",r).then((function(t){t.code>=0&&e.$router.push("/member/index")})).catch((function(t){e.$message.error(t.message),e.getCode()}))}))},aggrementClose:function(){this.aggrementVisible=!1},regisiterAggrement:function(){var e=this;Object(n["a"])().then((function(t){t.code>=0&&(e.agreement=t.data)})).catch((function(e){console.log(e.message)}))},getAggrement:function(){this.aggrementVisible=!0},getCode:function(){var e=this;Object(a["b"])({captcha_id:"this.captcha.id"}).then((function(t){t.code>=0&&(e.captcha=t.data,e.captcha.img=e.captcha.img.replace(/\r\n/g,""))})).catch((function(t){e.$message.error(t.message)}))}}},c=o,l=(r("ebf2"),r("2877")),g=Object(l["a"])(c,s,i,!1,null,"6a109d56",null);t["default"]=g.exports},b049:function(e,t,r){},c8d2:function(e,t,r){var s=r("d039"),i=r("5899"),n="​᠎";e.exports=function(e){return s((function(){return!!i[e]()||n[e]()!=n||i[e].name!==e}))}},ebf2:function(e,t,r){"use strict";var s=r("b049"),i=r.n(s);i.a}}]);
//# sourceMappingURL=chunk-c5b596f6.bd421b67.js.map