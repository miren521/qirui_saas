/**
 * 图文导航·组件
 * 开发时间：2018年7月25日17:19:37
 * 1、该组件不会对图片大小进行限制，会自适应图片比例
 * 2、上传图片的比例，最好统一，效果最佳
 */

var graphicNavPreviewHtml = '<div v-bind:id="id" class="graphic-nav">';
		graphicNavPreviewHtml += '<div class="wrap" v-bind:style="{ margin:\'0 \'+data.marginLeftRight+\'px\',\'border-radius\' : data.radius+\'px\' }">';
			graphicNavPreviewHtml += '<div v-for="(item,index) in list" class="item" v-bind:style="{ backgroundColor : data.backgroundColor,padding : (data.padding + \'px 0\'),width : ( data.scrollSetting == \'fixed\' ? ((318 - data.marginLeftRight*2)/list.length) : (318/3)) + \'px\' }">';
				graphicNavPreviewHtml += '<a href="javascript:;">';
					graphicNavPreviewHtml += '<img v-show="data.selectedTemplate ==\'imageNavigation\'" v-bind:src="item.imageUrl? $parent.$parent.changeImgUrl(item.imageUrl) : \'' + STATICEXT_IMG + "/crack_figure.png" + '\'" v-bind:style="{ width : data.imageScale+\'%\' }" />';
					graphicNavPreviewHtml += '<span v-show="item.title" v-bind:style="{ color: data.textColor }">{{item.title}}</span>';
					graphicNavPreviewHtml += '</a>';
			graphicNavPreviewHtml += '</div>';
		graphicNavPreviewHtml += '</div>';
		
	graphicNavPreviewHtml += '</div>';

Vue.component("graphic-nav", {
	data: function () {
		return {
			id: "graphic_nav_" + ns.gen_non_duplicate(10),
			data: this.$parent.data,
			list: this.$parent.data.list,
			selectedTemplate : this.$parent.data.selectedTemplate
		}
	},
	created: function () {
	},
	methods: {
	},
	template: graphicNavPreviewHtml
});


/**
 * [图片导航的图片]·组件
 */
var graphicNavListHtml = '<div class="graphic-nav-list">';

		graphicNavListHtml += '<div class="layui-form-item">';
			graphicNavListHtml += '<label class="layui-form-label sm">选择模板</label>';
			graphicNavListHtml += '<div class="layui-input-block">';
				graphicNavListHtml += '<div class="template-list">';
				
					graphicNavListHtml += '<div v-for="(item,i) in selectedTemplateList" v-bind:class="[\'template-item\',selectedTemplate == item.value ? \'ns-border-color ns-text-color\' : \'\']" v-on:click="selectedTemplate =item.value">';
						graphicNavListHtml += '<img v-bind:src="item.src" width="90px" height="64px">';
						graphicNavListHtml += '<span>{{item.name}}</span>';
					graphicNavListHtml += '</div>';
					
				graphicNavListHtml += '</div>';
			graphicNavListHtml += '</div>';
		graphicNavListHtml += '</div>';

		graphicNavListHtml += '<color></color>';
		graphicNavListHtml += '<color v-bind:data="{ field : \'backgroundColor\', label : \'背景颜色\' }"></color>';

		graphicNavListHtml += '<slide v-bind:data="{ field : \'padding\', label : \'上下边距\' }"></slide>';
		graphicNavListHtml += '<slide v-bind:data="{ field : \'marginLeftRight\', label : \'左右边距\' }"></slide>';
		graphicNavListHtml += '<slide v-bind:data="{ field : \'radius\', label : \'四边圆角\' }"></slide>';
		graphicNavListHtml += '<slide v-bind:data="{ field : \'imageScale\', label : \'图片比例\' }"></slide>';

		graphicNavListHtml += '<div class="layui-form-item">';
			graphicNavListHtml += '<label class="layui-form-label sm">滚动设置</label>';
			graphicNavListHtml += '<div class="layui-input-block sm">';
				graphicNavListHtml += '<div v-for="(item,i) in scrollSettingList" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (scrollSetting==item.value) }" v-on:click="scrollSetting=item.value">';
					graphicNavListHtml += '<i class="layui-anim layui-icon">&#xe643;</i>';
					graphicNavListHtml += '<div>{{item.name}}</div>';
				graphicNavListHtml += '</div>';

			graphicNavListHtml += '</div>';
		graphicNavListHtml += '</div>';
	
		graphicNavListHtml += '<ul>';
			graphicNavListHtml += '<li v-for="(item,index) in list" v-bind:key="index">';

				graphicNavListHtml += '<img-upload v-bind:data="{ data : item }" v-bind:condition="$parent.data.selectedTemplate == \'imageNavigation\'"></img-upload>';

				graphicNavListHtml += '<div class="content-block" v-bind:class="$parent.data.selectedTemplate">';
					graphicNavListHtml += '<div class="layui-form-item">';
						graphicNavListHtml += '<label class="layui-form-label sm">标题</label>';
						graphicNavListHtml += '<div class="layui-input-block">';
							graphicNavListHtml += '<input type="text" name=\'title\' v-model="item.title" class="layui-input" />';
						graphicNavListHtml += '</div>';
					graphicNavListHtml += '</div>';
				
					graphicNavListHtml += '<nc-link v-bind:data="{ field : $parent.data.list[index].link }"></nc-link>';
				graphicNavListHtml += '</div>';
				
				graphicNavListHtml += '<i class="del" v-on:click="list.splice(index,1)" data-disabled="1">x</i>';
				
				graphicNavListHtml += '<div class="error-msg"></div>';
				
			graphicNavListHtml += '</li>';
			
		graphicNavListHtml += '</ul>';
		
		graphicNavListHtml += '<div class="add-item ns-text-color" v-if="showAddItem" v-on:click="list.push({ imageUrl : \'\', title : \'\', link : {} })">';
		
			graphicNavListHtml += '<i>+</i>';
		
			graphicNavListHtml += '<span>添加一个图文导航</span>';
		
			graphicNavListHtml += '</div>';
		
		graphicNavListHtml += '<p class="hint">建议上传比例相同的图片，最多添加 {{maxTip}} 个导航</p>';//，拖动选中的导航可对其排序
		
	graphicNavListHtml += '</div>';
	
Vue.component("graphic-nav-list",{
	
	data : function(){
		return {
            data : this.$parent.data,
			showAddItem : true,
			list : this.$parent.data.list,
			scrollSettingList : [{
				name : "固定",
				value : "fixed",
				max : 5
			},{
				name : "横向滚动",
				value : "horizontal-scroll",
				max : 20
			}],
			scrollSetting : this.$parent.data.scrollSetting,
			imageScale : this.$parent.data.imageScale,
			padding : this.$parent.data.padding,
			marginLeftRight : this.$parent.data.marginLeftRight,
			radius: this.$parent.data.radius,
			selectedTemplate : this.$parent.data.selectedTemplate,
			maxTip : 5,//最大上传数量提示
			selectedTemplateList : [{
				name : '图片导航',
				value : 'imageNavigation',
				src : RESOURCEPATH + "/graphic_nav/img/image_navigation.png"
			},{
				name : '文字导航',
				value : 'textNavigation',
				src : RESOURCEPATH + "/graphic_nav/img/text_navigation.png"
			}]
			
		};
	},
	
	watch : {
		list : function(){
			this.changeShowAddItem();
		},
		scrollSetting : function(){
			//更新父级对象
			this.$parent.data.scrollSetting = this.scrollSetting;
			
			//当前滚动方式切换到固定时，要检测当前集合是否超过最多限制max
			if(this.scrollSetting == this.scrollSettingList[0].value && this.list.length>this.scrollSettingList[0].max){
				this.list.splice(5,this.scrollSettingList[0].max);
			}
			this.changeShowAddItem();
			
		},
		selectedTemplate : function(){
			this.$parent.data.selectedTemplate = this.selectedTemplate;
			if(this.selectedTemplate == "imageNavigation"){
				$(".draggable-element[data-index='" + vue.currentIndex + "'] .graphic-navigation .graphic-nav-list>ul>li input[name='title']").removeAttr("style");
			}else{
				$(".draggable-element[data-index='" + this.data.index + "'] .graphic-navigation .graphic-nav-list>ul>li .error-msg").text("").hide();
			}
		}
	},
	
	methods : {
		
		//改变图文导航按钮的显示隐藏
		changeShowAddItem : function(){

			if(this.scrollSetting == this.scrollSettingList[0].value){
				
				if(this.list.length >= this.scrollSettingList[0].max) this.showAddItem = false;
				else this.showAddItem = true;
				
				this.maxTip =  this.scrollSettingList[0].max;
				
			}else if(this.scrollSetting == this.scrollSettingList[1].value){
				
				if(this.list.length >= this.scrollSettingList[1].max) this.showAddItem = false;
				else this.showAddItem = true;

				this.maxTip =  this.scrollSettingList[1].max;
				
			}
		},
		verify:function () {
			
			var res = { code : true, message : "" };
			var _self = this;
			$(".draggable-element[data-index='" + this.data.index + "'] .graphic-navigation .graphic-nav-list>ul>li").each(function(index){
				
				if(_self.selectedTemplate == "imageNavigation"){
					$(this).find("input[name='title']").removeAttr("style");//清空输入框的样式
					//检测是否有未上传的图片
					if(_self.list[index].imageUrl == ""){
						res.code = false;
						res.message = "请选择一张图片";
						$(this).find(".error-msg").text("请选择一张图片").show();
						return res;
					}else{
						$(this).find(".error-msg").text("").hide();
					}
				}else{
					if(_self.list[index].title == ""){
						res.code = false;
						res.message = "请输入标题";
						$(this).find("input[name='title']").attr("style","border-color:red !important;").focus();
						$(this).find(".error-msg").text("请输入标题").show();
						return res;
					}else{
						$(this).find("input[name='title']").removeAttr("style");
						$(this).find(".error-msg").text("").hide();
					}
				}
			});
			
			return res;
		}
		
	},
	
	created : function(){
		this.changeShowAddItem();
		this.$parent.data.verify = this.verify;//加载验证方法
	},
	
	template : graphicNavListHtml
});

// $('.graphic-navigation .graphic-nav-list>ul').DDSort({
//
// 	//拖拽数据源
// 	target: 'li',
//
// 	//拖拽时显示的样式
// 	floatStyle: {
// 		'border': '1px solid #0d73f9',
// 		'background-color': '#ffffff'
// 	},
//
// 	//设置可拖拽区域
// //	draggableArea : "preview-draggable",
//
// 	//拖拽中，将右侧编辑属性栏右侧，并且显示拖拽中提示信息
// 	move : function(){
// //		$(".edit-attribute-list").hide();
// 		$(".drag-in").show();
// 	},
//
// 	//拖拽结束后，还原右侧编辑属性栏，并且刷新数据
// 	up : function(){
// //		console.log($(".edit-attribute-list"));
// //		$(".edit-attribute-list").show();
// 		$(".drag-in").hide();
// //		vue.refresh();
// 	}
// });