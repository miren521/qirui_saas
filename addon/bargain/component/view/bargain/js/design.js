// 顶部内容组件
var bargainTopConHtml = '<div class="goods-head">';
	bargainTopConHtml +=	'<div class="title-wrap">';
	bargainTopConHtml +=		'<div class="left-icon" v-if="list.imageUrl"><img v-bind:src="$parent.$parent.changeImgUrl(list.imageUrl)" /></div>';
	bargainTopConHtml +=		'<span class="name" v-bind:style="{color: data.titleTextColor}">{{list.title}}</span>';
	bargainTopConHtml +=	'</div>';
	bargainTopConHtml +=	'<div class="more ns-red-color" v-if="listMore.title">';
	bargainTopConHtml +=		'<span v-bind:style="{color: data.moreTextColor}">{{listMore.title}}</span>';
	bargainTopConHtml +=		'<div class="right-icon" v-if="listMore.imageUrl"><img v-bind:src="$parent.$parent.changeImgUrl(listMore.imageUrl)" /></div>';
	bargainTopConHtml +=		'<i class="iconfont iconyoujiantou" v-else v-bind:style="{color: data.moreTextColor}"></i>';
	bargainTopConHtml +=	'</div>';
	bargainTopConHtml +='</div>';

Vue.component("bargain-top-content", {
	data: function () {
		return {
			data: this.$parent.data,
			list: this.$parent.data.list,
			listMore: this.$parent.data.listMore
		}
	},
	created: function () {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	methods: {
		verify : function () {
			var res = { code : true, message : "" };
			return res;
		},
	},
	template: bargainTopConHtml
});

/**
 * 空的验证组件，后续如果增加业务，则更改组件
 */
var bargainListHtml = '<div class="goods-list-edit layui-form">';

		bargainListHtml += '<div class="layui-form-item">';
			bargainListHtml += '<label class="layui-form-label sm">商品来源</label>';
			bargainListHtml += '<div class="layui-input-block">';
				bargainListHtml += '<template v-for="(item,index) in goodsSources" v-bind:k="index">';
					bargainListHtml += '<div v-on:click="data.sources=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.sources==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.text}}</div></div>';
				bargainListHtml += '</template>';
			bargainListHtml += '</div>';
		bargainListHtml += '</div>';
		
		bargainListHtml += '<div class="layui-form-item" v-if="data.sources == \'diy\'">';
			bargainListHtml += '<label class="layui-form-label sm">手动选择</label>';
			bargainListHtml += '<div class="layui-input-block">';
				bargainListHtml += '<a href="#" class="ns-input-text ns-text-color" v-on:click="addGoods">选择</a>';
			bargainListHtml += '</div>';
		bargainListHtml += '</div>';
		
		bargainListHtml += '<div class="layui-form-item" v-show="data.sources == \'default\'">';
			bargainListHtml += '<label class="layui-form-label sm">商品数量</label>';
			bargainListHtml += '<div class="layui-input-block">';
				// bargainListHtml += '<input class="layui-input goods-account" v-model="data.goodsCount" />';
				bargainListHtml += '<input type="number" class="layui-input goods-account" v-on:keyup="shopNum" v-model="data.goodsCount"/>';
			bargainListHtml += '</div>';
		bargainListHtml += '</div>';
		
		bargainListHtml += '<div class="layui-form-item" v-show="data.sources == \'default\'">';
			bargainListHtml += '<label class="layui-form-label sm"></label>';
			bargainListHtml += '<div class="layui-input-block">';
				bargainListHtml += '<template v-for="(item,index) in goodsCount" v-bind:k="index">';
					bargainListHtml += '<div v-on:click="data.goodsCount=item" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.goodsCount==item) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item}}</div></div>';
				bargainListHtml += '</template>';
			bargainListHtml += '</div>';
		bargainListHtml += '</div>';

		// bargainListHtml += '<p class="hint">商品数量选择 0 时，前台会自动上拉加载更多</p>';
		
	bargainListHtml += '</div>';

var select_goods_list = []; //配合商品选择器使用
Vue.component("bargain-list", {
	template: bargainListHtml,
	data: function () {
		return {
			data: this.$parent.data,
			goodsSources: [
				{
					text: "默认",
					value: "default"
				},
				{
					text : "手动选择",
					value : "diy"
				}
			],
			categoryList: [],
			isLoad: false,
			isShow: false,
			selectIndex: 0,//当前选中的下标
			goodsCount: [6, 12, 18, 24, 30],
		}
	},
	created:function() {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	methods: {
		shopNum: function(){
			if (this.$parent.data.goodsCount > 50){
				layer.msg("商品数量最多为50");
				this.$parent.data.goodsCount = 50;
			}
			if (this.$parent.data.goodsCount.length > 0 && this.$parent.data.goodsCount < 1) {
				layer.msg("商品数量不能小于0");
				this.$parent.data.goodsCount = 1;
			}
		},
		verify : function () {
			var res = { code : true, message : "" };
			if(this.$parent.data.goodsCount.length===0) {
				res.code = false;
				res.message = "请输入商品数量";
			}
			if (this.$parent.data.goodsCount < 0) {
				res.code = false;
				res.message = "商品数量不能小于0";
			}
			if(this.$parent.data.goodsCount > 50){
				res.message = "商品数量最多为50";
			}
			return res;
		},
		addGoods: function(){
			var self = this;

			goodsSelect(function (res) {
				// if (!res.length) return false;
				self.$parent.data.goodsId = [];
				for (var i=0; i<res.length; i++) {
					self.$parent.data.goodsId.push(res[i]);
				}

			}, self.$parent.data.goodsId, {mode: "spu", promotion: "bargain",disabled:0});
		}
	}
});

var bargainStyleHtml = '<div class="layui-form-item">';
		bargainStyleHtml += '<label class="layui-form-label sm">选择风格</label>';
		bargainStyleHtml += '<div class="layui-input-block">';
			bargainStyleHtml += '<div class="ns-input-text ns-text-color selected-style" v-on:click="selectGroupbuyStyle">选择</div>';
		bargainStyleHtml += '</div>';
	bargainStyleHtml += '</div>';

Vue.component("bargain-style", {
	template: bargainStyleHtml,
	data: function() {
		return {
			data: this.$parent.data,
		}
	},
	created:function() {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	methods: {
		verify: function () {
			var res = { code: true, message: "" };
			return res;
		},
		selectGroupbuyStyle: function() {
			var self = this;
			layer.open({
				type: 1,
				title: '风格选择',
				area:['930px','630px'],
				btn: ['确定', '返回'],
				content: $(".draggable-element[data-index='" + self.data.index + "'] .edit-attribute .bargain-list-style").html(),
				success: function(layero, index) {
					$(".layui-layer-content input[name='style']").val(self.data.style);
					$("body").on("click", ".layui-layer-content .style-list-con-bargain .style-li-bargain", function () {
						$(this).addClass("selected ns-border-color").siblings().removeClass("selected ns-border-color");
						$(".layui-layer-content input[name='style']").val($(this).index() + 1);
					});
				},
				yes: function (index, layero) {
					self.data.style = $(".layui-layer-content input[name='style']").val();
					layer.closeAll()
				}
			});
		},
	}
})

// 图片上传
var bargainTopHtml = '<ul class="fenxiao-addon-title">';
		bargainTopHtml += '<li>';
		
			bargainTopHtml += '<div class="layui-form-item">';
				bargainTopHtml += '<label class="layui-form-label sm">左侧图标</label>';
				bargainTopHtml += '<div class="layui-input-block">';
					bargainTopHtml += '<img-upload v-bind:data="{ data : list }"></img-upload>';
				bargainTopHtml += '</div>';
			bargainTopHtml += '</div>';
			
			// bargainTopHtml += '<img-upload v-bind:data="{ data : list }"></img-upload>';
			bargainTopHtml += '<div class="content-block">';
				bargainTopHtml += '<div class="layui-form-item">';
					bargainTopHtml += '<label class="layui-form-label sm">标题</label>';
					bargainTopHtml += '<div class="layui-input-block">';
						bargainTopHtml += '<input type="text" name=\'title\' v-model="list.title" class="layui-input" />';
					bargainTopHtml += '</div>';
				bargainTopHtml += '</div>';
			bargainTopHtml += '</div>';
			
			bargainTopHtml += '<color v-bind:data="{ field : \'titleTextColor\', label : \'标题颜色\', defaultcolor: \'defaultTitleTextColor\' }"></color>';
		bargainTopHtml += '</li>';
		
		bargainTopHtml += '<li>';
			// bargainTopHtml += '<div class="layui-form-item">';
			// 	bargainTopHtml += '<label class="layui-form-label sm">右侧图标</label>';
			// 	bargainTopHtml += '<div class="layui-input-block">';
			// 		bargainTopHtml += '<img-upload v-bind:data="{ data : item }"></img-upload>';
			// 	bargainTopHtml += '</div>';
			// bargainTopHtml += '</div>';
			
			bargainTopHtml += '<div class="content-block">';
				bargainTopHtml += '<div class="layui-form-item">';
					bargainTopHtml += '<label class="layui-form-label sm">文本内容</label>';
					bargainTopHtml += '<div class="layui-input-block">';
						bargainTopHtml += '<input type="text" name=\'title\' v-model="listMore.title" class="layui-input" />';
					bargainTopHtml += '</div>';
				bargainTopHtml += '</div>';
				bargainTopHtml += '<color v-bind:data="{ field : \'moreTextColor\', defaultcolor: \'defaultMoreTextColor\' }"></color>';
				
				// bargainTopHtml += '<nc-link v-bind:data="{ field : $parent.data.list[index].link }"></nc-link>';
				
			bargainTopHtml += '</div>';
		bargainTopHtml += '</li>';
	bargainTopHtml += '</ul>';

Vue.component("bargain-top-list",{
	template : bargainTopHtml,
	data : function(){
		return {
            data : this.$parent.data,
			list : this.$parent.data.list,
			listMore: this.$parent.data.listMore
		};
	},
	created : function(){
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	watch : {

	},
	methods : {
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
	}
});

