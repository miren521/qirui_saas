/**
 * 空的验证组件，后续如果增加业务，则更改组件
 */
var goodsListHtml = '<div class="goods-list-edit layui-form">';

		goodsListHtml += '<div class="layui-form-item">';
			goodsListHtml += '<label class="layui-form-label sm">商品来源</label>';
			goodsListHtml += '<div class="layui-input-block">';
				goodsListHtml += '<template v-for="(item,index) in goodsSources" v-bind:k="index">';
					goodsListHtml += '<div v-on:click="data.sources=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.sources==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.text}}</div></div>';
				goodsListHtml += '</template>';
			goodsListHtml += '</div>';
		goodsListHtml += '</div>';
		
		goodsListHtml += '<div class="layui-form-item" v-if="isLoad && data.sources == \'category\'">';
			goodsListHtml += '<label class="layui-form-label sm">商品分类</label>';
			goodsListHtml += '<div class="layui-input-block">';
				goodsListHtml += '<div v-bind:class="{  \'layui-unselect layui-form-select\' : true, \'layui-form-selected\' : isShow }" v-on:click="isShow=!isShow;">';
					goodsListHtml += '<div class="layui-select-title">';
						goodsListHtml += '<input type="text" placeholder="请选择" v-bind:value="categoryList[selectIndex].category_name" readonly="readonly" class="layui-input layui-unselect">';
						goodsListHtml += '<i class="layui-edge"></i>';
					goodsListHtml += '</div>';
					goodsListHtml += '<dl class="layui-anim layui-anim-upbit">';
						goodsListHtml += '<dd v-for="(item,index) in categoryList" v-bind:value="item.category_id" v-bind:class="{ \'layui-this\' : (data.categoryId==item.category_id) }" v-on:click.stop="data.categoryId=item.category_id;isShow=false;selectIndex=index;">{{item.category_name}}</dd>';
					goodsListHtml += '</dl>';
				goodsListHtml += '</div>';
			goodsListHtml += '</div>';
		goodsListHtml += '</div>';
		
		goodsListHtml += '<div class="layui-form-item" v-if="isLoad && data.sources == \'diy\'">';
			goodsListHtml += '<label class="layui-form-label sm">手动选择</label>';
			goodsListHtml += '<div class="layui-input-block">';
				goodsListHtml += '<a href="#" class="ns-input-text ns-text-color" v-on:click="addGoods">选择</a>';
			goodsListHtml += '</div>';
		goodsListHtml += '</div>';
		
		goodsListHtml += '<div class="layui-form-item" v-if="data.sources != \'diy\'">';
			goodsListHtml += '<label class="layui-form-label sm">商品数量</label>';
			goodsListHtml += '<div class="layui-input-block">';
				// goodsListHtml += '<input class="layui-input goods-account" v-model="data.goodsCount" />';
				goodsListHtml += '<input type="number" class="layui-input goods-account" v-on:keyup="shopNum" v-model="data.goodsCount"/>';
			goodsListHtml += '</div>';
		goodsListHtml += '</div>';
		
		goodsListHtml += '<div class="layui-form-item" v-if="data.sources != \'diy\'">';
			goodsListHtml += '<label class="layui-form-label sm"></label>';
			goodsListHtml += '<div class="layui-input-block">';
				goodsListHtml += '<template v-for="(item,index) in goodsCount" v-bind:k="index">';
					goodsListHtml += '<div v-on:click="data.goodsCount=item" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.goodsCount==item) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item}}</div></div>';
				goodsListHtml += '</template>';
			goodsListHtml += '</div>';
		goodsListHtml += '</div>';

		// goodsListHtml += '<p class="hint">商品数量选择 0 时，前台会自动上拉加载更多</p>';
		
	goodsListHtml += '</div>';
var select_goods_list = []; //配合商品选择器使用
Vue.component("goods-list", {
	template: goodsListHtml,
	data: function () {
		return {
			data: this.$parent.data,
			goodsSources: [
				{
					text: "默认",
					value: "default"
				},
				{
					text: "商品分类",
					value: "category"
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
			goodsCount: [6, 12, 18, 24, 30]
		}
	},
	created:function() {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
		this.getGoodsCategory();
	},
	methods: {
		shopNum: function(){
			if (this.$parent.data.goodsCount.length > 0 && this.$parent.data.goodsCount < 1) {
				layer.msg("商品数量不能小于0");
				this.$parent.data.goodsCount = 0;
			}
			if (this.$parent.data.goodsCount > 50){
				layer.msg("商品数量最多为50");
				this.$parent.data.goodsCount = 50;
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
			if (this.$parent.data.goodsCount > 50){
				res.code = false;
				res.message = "商品数量最多为50";
			}
			return res;
		},
		getGoodsCategory(){
			var self = this;
			$.ajax({
				url : ns.url(module+"/goodscategory/getCategoryList"),
				dataType: 'JSON',
				type: 'post',
				data: {'level':1, 'pid':0},
				success: function(res) {
					if(res.data){
						self.categoryList = res.data;
						self.categoryList.splice(0,0,{ category_name : "请选择", category_id : "" });
						self.categoryList.push({});
						self.categoryList.pop();
						for(var i=0;i<self.categoryList.length;i++){
							if(self.categoryList[i].category_id == self.data.categoryId){
								self.selectIndex = i;
								break;
							}
						}
						self.isLoad = true;
					}
				}
			})
		},
		addGoods: function(){
			var self = this;
			goodsSelect(function (res) {
				// if (!res.length) return false;
				self.$parent.data.goodsId = [];
				for (var i=0; i<res.length; i++) {
					self.$parent.data.goodsId.push(res[i]);
				}
				
			}, self.$parent.data.goodsId, {mode: "spu",disabled:0,promotion: "all"});
		}
	}
});

var goodsListStyleHtml = '<div class="layui-form-item">';
		goodsListStyleHtml += '<label class="layui-form-label sm">选择风格</label>';
		goodsListStyleHtml += '<div class="layui-input-block">';
			goodsListStyleHtml += '<div class="ns-input-text ns-text-color selected-style" v-on:click="selectGoodsStyle">选择</div>';
		goodsListStyleHtml += '</div>';
	goodsListStyleHtml += '</div>';

Vue.component("goods-list-style", {
	template: goodsListStyleHtml,
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
		selectGoodsStyle: function() {
			var self = this;
			layer.open({
				type: 1,
				title: '风格选择',
				area:['930px','630px'],
				btn: ['确定', '返回'],
				content: $(".draggable-element[data-index='" + self.data.index + "'] .edit-attribute .goods-list-style").html(),
				success: function(layero, index) {
					$(".layui-layer-content input[name='style']").val(self.data.style);
					$("body").on("click", ".layui-layer-content .style-list-con-goods .style-li-goods", function () {
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
});

// 是否启用更多按钮设置
var moreBtnHtml = '<div class="layui-form-item">';
	moreBtnHtml +=	 '<label class="layui-form-label sm">{{data.label}}</label>';
	moreBtnHtml +=	 '<div class="layui-input-block">';
	moreBtnHtml +=		 '<template v-for="(item,index) in list" v-bind:k="index">';
	moreBtnHtml +=			 '<div v-on:click="parent[data.field]=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (parent[data.field]==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.label}}</div></div>';
	moreBtnHtml +=		 '</template>';
	moreBtnHtml +=	 '</div>';
	moreBtnHtml += '</div>';

Vue.component("goods-list-more-btn", {
	props: {
		data: {
			type: Object,
			default: function () {
				return {
					field: "isShowCart",
					label: "是否启用"
				};
			}
		}
	},
	created: function () {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
		if (this.data.label == undefined) this.data.label = "启用";
		if (this.data.field == undefined) this.data.field = "isShowCart";
	},
	watch: {
		data: function (val, oldVal) {
			if (val.field == undefined) val.field = oldVal.field;
			if (val.label == undefined) val.label = "启用";
		},
	},
	template: moreBtnHtml,
	data: function () {
		return {
			list: [
				{label: "是", value: 1},
				{label: "否", value: 0},
			],
			parent: this.$parent.data,
		};
	},
	methods: {
		verify : function () {
			var res = { code : true, message : "" };
			return res;
		},
	},
});

// 购物车按钮
var cartStyleHtml = '<div class="layui-form-item">';
		cartStyleHtml += '<label class="layui-form-label sm">选择风格</label>';
		cartStyleHtml += '<div class="layui-input-block">';
			cartStyleHtml += '<div class="ns-input-text ns-text-color selected-style" v-on:click="selectTestStyle">选择</div>';
		cartStyleHtml += '</div>';
	cartStyleHtml += '</div>';

Vue.component("cart-style", {
	template: cartStyleHtml,
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
		selectTestStyle: function() {
			var self = this;
			layer.open({
				type: 1,
				title: '风格选择',
				area:['930px','630px'],
				btn: ['确定', '返回'],
				content: $(".draggable-element[data-index='" + self.data.index + "'] .edit-attribute .cart-list-style").html(),
				success: function(layero, index) {
					$(".layui-layer-content input[name='cart_style']").val(self.data.style);
					$("body").on("click", ".layui-layer-content .cart-list-con .cart-li", function () {
						$(this).addClass("selected ns-border-color").siblings().removeClass("selected ns-border-color");
						$(".layui-layer-content input[name='cart_style']").val($(this).index() + 1);
					});
				},
				yes: function (index, layero) {
					self.data.cartStyle = $(".layui-layer-content input[name='cart_style']").val();
					layer.closeAll()
				}
			});
		},
	}
});

// 多选
var showContentHtml = '<div class="layui-form-item goods-show-box">';
	showContentHtml +=	'<div class="layui-input-inline">';
		showContentHtml +=		'<div v-on:click="changeStatus(\'isShowGoodName\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.isShowGoodName == 1)}" lay-skin="primary"><span>商品名称</span><i class="layui-icon layui-icon-ok"></i></div>';
		showContentHtml +=		'<div v-on:click="changeStatus(\'isShowGoodSubTitle\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.isShowGoodSubTitle == 1)}" lay-skin="primary"><span>副标题</span><i class="layui-icon layui-icon-ok"></i></div>';
		showContentHtml +=		'<div v-on:click="changeStatus(\'isShowMarketPrice\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.isShowMarketPrice == 1)}" lay-skin="primary"><span>划线市场价</span><i class="layui-icon layui-icon-ok"></i></div>';
		showContentHtml +=		'<div v-on:click="changeStatus(\'isShowGoodSaleNum\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.isShowGoodSaleNum == 1)}" lay-skin="primary"><span>商品销量</span><i class="layui-icon layui-icon-ok"></i></div>';
	showContentHtml +=	'</div>';
	showContentHtml += '</div>';

Vue.component("show-content", {
	template: showContentHtml,
	data: function () {
		return {
			data: this.$parent.data,
			isShowGoodName: this.$parent.data.isShowGoodName,
			isShowMarketPrice: this.$parent.data.isShowMarketPrice,
		};
	},
	created: function () {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	methods: {
		verify :function () {
			var res = { code: true, message: "" };
			return res;
		},
		changeStatus: function(field) {
			this.$parent.data[field] = this.$parent.data[field] ? 0 : 1;
		}
	}
});