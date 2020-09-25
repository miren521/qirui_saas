/**
 * 空的验证组件，后续如果增加业务，则更改组件
 */
var goodsListHtml = '<div class="goods-list-edit layui-form">';

		goodsListHtml += '<div class="layui-form-item">';
			goodsListHtml += '<label class="layui-form-label sm">商品来源</label>';
			goodsListHtml += '<div class="layui-input-block">';
				goodsListHtml += '<template v-for="(item,index) in goodsSources" v-bind:k="index">';
					goodsListHtml += '<div v-on:click="changeSources(item.value)" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.sources==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.text}}</div></div>';
				goodsListHtml += '</template>';
			goodsListHtml += '</div>';
		goodsListHtml += '</div>';
		
		goodsListHtml += '<div class="layui-form-item" v-if="data.sources == \'category\' && isLoad">';
			goodsListHtml += '<label class="layui-form-label sm">商品分类</label>';
			goodsListHtml += '<div class="layui-input-block">';
				goodsListHtml += '<div v-bind:class="{  \'layui-unselect layui-form-select\' : true, \'layui-form-selected\' : isShowCategory }" v-on:click="isShowCategory=!isShowCategory;">';
					goodsListHtml += '<div class="layui-select-title">';
						goodsListHtml += '<input type="text" placeholder="请选择" v-bind:value="categoryList[selectIndex].category_name" readonly="readonly" class="layui-input layui-unselect">';
						goodsListHtml += '<i class="layui-edge"></i>';
					goodsListHtml += '</div>';
					goodsListHtml += '<dl class="layui-anim layui-anim-upbit">';
						goodsListHtml += '<dd v-for="(item,index) in categoryList" v-bind:value="item.category_id" v-bind:class="{ \'layui-this\' : (data.categoryId==item.category_id) }" v-on:click.stop="data.categoryId=item.category_id;isShowCategory=false;selectIndex=index;">{{item.category_name}}</dd>';
					goodsListHtml += '</dl>';
				goodsListHtml += '</div>';
			goodsListHtml += '</div>';
		goodsListHtml += '</div>';
		
		goodsListHtml += '<div class="layui-form-item" v-if="data.sources == \'category\'">';
			goodsListHtml += '<label class="layui-form-label sm">商品数量</label>';
			goodsListHtml += '<div class="layui-input-block">';
				goodsListHtml += '<template v-for="(item,index) in goodsCount" v-bind:k="index">';
					goodsListHtml += '<div v-on:click="data.goodsCount=item" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.goodsCount==item) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item}}</div></div>';
				goodsListHtml += '</template>';
			goodsListHtml += '</div>';
		goodsListHtml += '</div>';

		goodsListHtml += '<div class="layui-form-item" v-if="data.sources == \'diy\'">';
			goodsListHtml += '<label class="layui-form-label sm">选择商品</label>';
			goodsListHtml += '<div class="layui-input-block">';
				goodsListHtml += '<span style="margin-right: 10px;font-size: 12px;" v-if="data.goodsId">已选{{data.goodsId.length}}个商品</span>';
				goodsListHtml += '<button class="layui-btn layui-btn-primary sm" v-on:click="openSelectedGoodsPopup()">选择</button>';
			goodsListHtml += '</div>';
		goodsListHtml += '</div>';
		
	goodsListHtml += '</div>';

Vue.component("rank-list", {
	template: goodsListHtml,
	data: function () {
		return {
			data: this.$parent.data,
			goodsSources :[
				{
					text : "商品分类",
					value : "category"
				},
				{
					text : "自定义",
					value : "diy"
				},
			],
			categoryList : [],
			isLoad : false,
			isShowCategory: false,
			selectIndex: 0,//当前选中的下标
			goodsCount : [6,12,18,24],
		}
	},
	created:function() {
		this.$parent.data.verify = this.verify;//加载验证方法
		this.getGoodsCategory();
		console.log(this.data);
	},
	methods: {
		
		verify : function () {
			var res = { code : true, message : "" };
			return res;
		},
		getGoodsCategory :function(){
			var self = this;
			$.ajax({
				url : ns.url("admin/goodscategory/getCategoryList"),
				dataType: 'JSON',
				type: 'post',
				data: {'level':1, 'pid':0},
				success: function(res) {
					if(res.data){
						self.categoryList = res.data;
						self.categoryList.splice(0,0,{ category_name : "请选择", category_id : 0 });
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
		changeSources:function(value){
			this.data.sources = value;
		},
		openSelectedGoodsPopup :function () {
			goodsSelect(this.goodsSelectCallback, this.data.goodsId, {mode: "spu"});
		},
		goodsSelectCallback:function(res){
			var goods_ids = [];
			for(var i=0;i<res.length;i++) {
				goods_ids.push(res[i].goods_id);
			}
			this.data.goodsId = goods_ids;
		}
	}
});