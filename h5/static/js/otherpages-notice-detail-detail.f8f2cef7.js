(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["otherpages-notice-detail-detail"],{"1f70":function(t,e,n){"use strict";var i=n("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=i(n("5ca6")),r={data:function(){return{noticeId:0,content:"",detail:{}}},onLoad:function(t){var e=this;t.notice_id?this.noticeId=t.notice_id:this.$util.redirectTo("/otherpages/notice/list/list",{},"redirectTo"),this.$api.sendRequest({url:"/api/notice/info",data:{id:this.noticeId},success:function(t){0==t.code?t.data?(e.detail=t.data,e.content=(0,a.default)(t.data.content),e.$refs.loadingCover&&e.$refs.loadingCover.hide()):e.$util.redirectTo("/otherpages/notice/list/list",{},"redirectTo"):(e.$util.showToast({title:t.message}),setTimeout((function(){e.$util.redirectTo("/otherpages/notice/list/list",{},"redirectTo")}),2e3))},fail:function(t){e.$util.redirectTo("/otherpages/notice/list/list",{},"redirectTo"),e.$refs.loadingCover&&e.$refs.loadingCover.hide()}})},onShow:function(){this.$langConfig.refresh()},computed:{themeStyle:function(){return"theme-"+this.$store.state.themeStyle}},methods:{},onShareAppMessage:function(t){var e="[公告]"+this.detail.title,n="/otherpages/notice/detail/detail?notice_id="+this.noticeId;return{title:e,path:n,success:function(t){},fail:function(t){}}}};e.default=r},"2e50":function(t,e,n){var i=n("24fb");e=i(!1),e.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/**  -------------------------------------------------------------------默认主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------蓝色主题颜色配置-------------------------------------------------- */\n/**  -------------------------------------------------------------------绿色主题颜色配置-------------------------------------------------- */\n/* 文字基本颜色 */\n/* 文字尺寸 */.page[data-v-883b4b7a]{width:100%;height:100%;padding:%?20?%;box-sizing:border-box;background-color:#fff}.notice-title[data-v-883b4b7a]{font-size:%?32?%;font-weight:700;text-align:center}.notice-content[data-v-883b4b7a]{margin-top:%?20?%}.notice-meta[data-v-883b4b7a]{text-align:right;margin-top:%?20?%;color:#999}.empty[data-v-883b4b7a]{width:100%;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-align:center;-webkit-align-items:center;align-items:center;padding:%?20?%;box-sizing:border-box;margin-top:%?150?%}.empty .iconfont[data-v-883b4b7a]{font-size:%?190?%;color:#898989;line-height:1.2}.page[data-v-883b4b7a]{width:100%;height:100%;padding:%?20?%;box-sizing:border-box;background-color:#fff}.notice-title[data-v-883b4b7a]{font-size:%?32?%;font-weight:700;text-align:center}.notice-content[data-v-883b4b7a]{margin-top:%?20?%}.notice-meta[data-v-883b4b7a]{text-align:right;margin-top:%?20?%;color:#999}',""]),t.exports=e},"366a":function(t,e,n){"use strict";n.r(e);var i=n("1f70"),a=n.n(i);for(var r in i)"default"!==r&&function(t){n.d(e,t,(function(){return i[t]}))}(r);e["default"]=a.a},"42c5":function(t,e,n){var i=n("2e50");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var a=n("4f06").default;a("1f64b5ac",i,!0,{sourceMap:!1,shadowMode:!1})},"5ca6":function(t,e,n){"use strict";var i=n("4ea4");n("c975"),n("13d5"),n("4d63"),n("ac1f"),n("25f0"),n("466d"),n("5319"),n("1276"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=i(n("f2cb")),r=/^<([-A-Za-z0-9_]+)((?:\s+[a-zA-Z_:][-a-zA-Z0-9_:.]*(?:\s*=\s*(?:(?:"[^"]*")|(?:'[^']*')|[^>\s]+))?)*)\s*(\/?)>/,o=/^<\/([-A-Za-z0-9_]+)[^>]*>/,c=/([a-zA-Z_:][-a-zA-Z0-9_:.]*)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/g,s=g("area,base,basefont,br,col,frame,hr,img,input,link,meta,param,embed,command,keygen,source,track,wbr"),l=g("a,address,article,applet,aside,audio,blockquote,button,canvas,center,dd,del,dir,div,dl,dt,fieldset,figcaption,figure,footer,form,frameset,h1,h2,h3,h4,h5,h6,header,hgroup,hr,iframe,isindex,li,map,menu,noframes,noscript,object,ol,output,p,pre,section,script,table,tbody,td,tfoot,th,thead,tr,ul,video"),d=g("abbr,acronym,applet,b,basefont,bdo,big,br,button,cite,code,del,dfn,em,font,i,iframe,img,input,ins,kbd,label,map,object,q,s,samp,script,select,small,span,strike,strong,sub,sup,textarea,tt,u,var"),u=g("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr"),f=g("checked,compact,declare,defer,disabled,ismap,multiple,nohref,noresize,noshade,nowrap,readonly,selected"),h=g("script,style");function p(t,e){var n,i,a,p=[],g=t;p.last=function(){return this[this.length-1]};while(t){if(i=!0,p.last()&&h[p.last()])t=t.replace(new RegExp("([\\s\\S]*?)</"+p.last()+"[^>]*>"),(function(t,n){return n=n.replace(/<!--([\s\S]*?)-->|<!\[CDATA\[([\s\S]*?)]]>/g,"$1$2"),e.chars&&e.chars(n),""})),v("",p.last());else if(0==t.indexOf("\x3c!--")?(n=t.indexOf("--\x3e"),n>=0&&(e.comment&&e.comment(t.substring(4,n)),t=t.substring(n+3),i=!1)):0==t.indexOf("</")?(a=t.match(o),a&&(t=t.substring(a[0].length),a[0].replace(o,v),i=!1)):0==t.indexOf("<")&&(a=t.match(r),a&&(t=t.substring(a[0].length),a[0].replace(r,m),i=!1)),i){n=t.indexOf("<");var b=n<0?t:t.substring(0,n);t=n<0?"":t.substring(n),e.chars&&e.chars(b)}if(t==g)throw"Parse Error: "+t;g=t}function m(t,n,i,a){if(n=n.toLowerCase(),l[n])while(p.last()&&d[p.last()])v("",p.last());if(u[n]&&p.last()==n&&v("",n),a=s[n]||!!a,a||p.push(n),e.start){var r=[];i.replace(c,(function(t,e){var n=arguments[2]?arguments[2]:arguments[3]?arguments[3]:arguments[4]?arguments[4]:f[e]?e:"";r.push({name:e,value:n,escaped:n.replace(/(^|[^\\])"/g,'$1\\"')})})),e.start&&e.start(n,r,a)}}function v(t,n){if(n){for(i=p.length-1;i>=0;i--)if(p[i]==n)break}else var i=0;if(i>=0){for(var a=p.length-1;a>=i;a--)e.end&&e.end(p[a]);p.length=i}}v()}function g(t){for(var e={},n=t.split(","),i=0;i<n.length;i++)e[n[i]]=!0;return e}function b(t){return t.replace(/<\?xml.*\?>\n/,"").replace(/<!doctype.*>\n/,"").replace(/<!DOCTYPE.*>\n/,"")}function m(t){t=t.replace(/<!--[\s\S]*-->/gi,"");return t}function v(t){t=t.replace(/\\/g,"").replace(/<img/g,'<img style="width:100% !important;display:block;"');return t=t.replace(/<img [^>]*src=['"]([^'"]+)[^>]*>/gi,(function(t,e){return'<img style="width:100% !important;display:block;" src="'+a.default.img(e)+'"/>'})),t}function x(t){t=t.replace(/style\s*=\s*["][^>]*;[^"]?/gi,(function(t,e){return t=t.replace(/[:](\s?)[\s\S]*/gi,(function(t,e){return t.replace(/"/g,"'")})),t}));return t}function w(t){return t.reduce((function(t,e){var n=e.value,i=e.name;return t[i]?t[i]=t[i]+" "+n:t[i]=n,t}),{})}function y(t){t=b(t),t=m(t),t=v(t),t=x(t);var e=[],n={node:"root",children:[]};return p(t,{start:function(t,i,a){var r={name:t};if(0!==i.length&&(r.attrs=w(i)),a){var o=e[0]||n;o.children||(o.children=[]),o.children.push(r)}else e.unshift(r)},end:function(t){var i=e.shift();if(i.name!==t&&console.error("invalid state: mismatch end tag"),0===e.length)n.children.push(i);else{var a=e[0];a.children||(a.children=[]),a.children.push(i)}},chars:function(t){var i={type:"text",text:t};if(0===e.length)n.children.push(i);else{var a=e[0];a.children||(a.children=[]),a.children.push(i)}},comment:function(t){var n={node:"comment",text:t},i=e[0];i.children||(i.children=[]),i.children.push(n)}}),n.children}var k=y;e.default=k},7090:function(t,e,n){"use strict";var i=n("42c5"),a=n.n(i);a.a},a2b6:function(t,e,n){"use strict";n.d(e,"b",(function(){return a})),n.d(e,"c",(function(){return r})),n.d(e,"a",(function(){return i}));var i={loadingCover:n("1ba8").default},a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-uni-view",{staticClass:"page",class:t.themeStyle},[n("v-uni-view",{staticClass:"notice-title"},[t._v(t._s(t.detail.title))]),n("v-uni-view",{staticClass:"notice-content"},[n("v-uni-rich-text",{attrs:{nodes:t.content}})],1),n("v-uni-view",{staticClass:"notice-meta"},[n("v-uni-text",{staticClass:"notice-time"},[t._v(t._s(t.$lang("time"))+": "+t._s(t.$util.timeStampTurnTime(t.detail.create_time)))])],1),n("loading-cover",{ref:"loadingCover"})],1)},r=[]},d0a8:function(t,e,n){"use strict";n.r(e);var i=n("a2b6"),a=n("366a");for(var r in a)"default"!==r&&function(t){n.d(e,t,(function(){return a[t]}))}(r);n("7090");var o,c=n("f0c5"),s=Object(c["a"])(a["default"],i["b"],i["c"],!1,null,"883b4b7a",null,!1,i["a"],o);e["default"]=s.exports}}]);