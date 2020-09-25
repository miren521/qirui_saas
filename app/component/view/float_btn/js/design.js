var floatBtnListHtml = '<div class="float-btn-list">';
		floatBtnListHtml += '<ul>';
			floatBtnListHtml += '<li v-for="(item,index) in list" v-bind:key="index">';
				floatBtnListHtml += '<img-upload v-bind:data="{data : item}"></img-upload>';
				floatBtnListHtml += '<div class="content-block">';
					// floatBtnListHtml += '<div class="layui-form-item">';
					// 	floatBtnListHtml += '<label class="layui-form-label sm">标题</label>';
					// 	floatBtnListHtml += '<div class="layui-input-block">';
					// 		floatBtnListHtml += '<input type="text" name="title" v-model="item.title" class="layui-input">';
					// 	floatBtnListHtml += '</div>';
					// floatBtnListHtml += '</div>';
					floatBtnListHtml += '<nc-link v-bind:data="{field: $parent.data.list[index].link}"></nc-link>';
				floatBtnListHtml += '</div>';
				floatBtnListHtml += '<i class="del" v-on:click="list.splice(index,1)" data-disabled="1">x</i>';
				floatBtnListHtml += '<div class="error-msg"></div>';
			floatBtnListHtml += '</li>';
		floatBtnListHtml += '</ul>';
		floatBtnListHtml += '<div class="add-item ns-text-color" v-if="showAddItem" v-on:click="list.push({ imageUrl : \'\', title : \'\', link : {} })">';
			floatBtnListHtml += '<i>+</i>';
			floatBtnListHtml += '<span>添加一个浮动按钮</span>';
		floatBtnListHtml += '</div>';
	floatBtnListHtml += '</div>';

Vue.component("float-btn-list",{
	data: function () {
		return {
			list: this.$parent.data.list,
			maxTip : 3,//最大上传数量提示
			showAddItem : true,
		};
	},
	created : function(){
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	watch : {
		list : function(){
			this.changeShowAddItem();
		}
	},
	methods: {
		verify :function () {
			var res = { code: true, message: "" };
			if(this.list.length >0){
				for(var i=0;i < this.list.length;i++){
					if(this.$parent.data.list[i].imageUrl == ""){
						res.code = false;
						res.message = "请添加图片";
						break;
					}
				}
			}else{
				res.code = false;
				res.message = "请添加一个浮动按钮";
			}
			return res;
		},
		//改变添加浮动按钮
		changeShowAddItem(){
			if(this.list.length >= this.maxTip) this.showAddItem = false;
			else this.showAddItem = true;
		},
	},
	template: floatBtnListHtml
});
