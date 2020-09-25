/**
 * 空的验证组件，后续如果增加业务，则更改组件
 */
var emptyHtml = '<div class="text-slide">';
		emptyHtml += '<slide v-bind:data="{ field : \'padding\', label : \'上下边距\' }"></slide>';
	emptyHtml += '</div>';


var styleHtml = '<div class="layui-form-item">';
		styleHtml += '<label class="layui-form-label sm">选择风格</label>';
		styleHtml += '<div class="layui-input-block">';
			styleHtml += '<div class="ns-input-text ns-text-color selected-style" v-on:click="selectTestStyle">选择</div>';
		styleHtml += '</div>';
	styleHtml += '</div>';

Vue.component("text-empty", {
	template: emptyHtml,
	data: function () {
		return {
			data : this.$parent.data,
			padding: this.$parent.data.padding,
		}
	},
	created:function() {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	methods: {

		verify : function () {
			var res = { code : true, message : "" };
			var _self = this;
			if(this.$parent.data.title.length == 0){
				res.code = false;
				res.message = "文本不能为空";
				setTimeout(function(){
					$("#title_" + _self.$parent.data.index).focus();
				},10);
			}
			return res;
		},
	}
});

Vue.component("text-style", {
	template: styleHtml,
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
				content: $(".draggable-element[data-index='" + self.data.index + "'] .edit-attribute .style-list-box").html(),
				success: function(layero, index) {
					$(".layui-layer-content input[name='style']").val(self.data.style);
					$(".layui-layer-content input[name='sub']").val(self.data.sub);
					$("body").on("click", ".layui-layer-content .style-list-con .style-li", function () {
						$(this).addClass("selected ns-border-color").siblings().removeClass("selected ns-border-color ns-bg-color-after");
						$(".layui-layer-content input[name='style']").val($(this).index() + 1);
						$(".layui-layer-content input[name='sub']").val($(this).find("input").val());
					});
				},
				yes: function (index, layero) {
					self.data.style = $(".layui-layer-content input[name='style']").val();
					self.data.sub = $(".layui-layer-content input[name='sub']").val();
					layer.closeAll()
				}
			});
		},
		/* fontWeight: function(data, obj) {
			var self = this;
			self.data.fontWeight = data;
			$(obj).attr("checked", true);
			$(obj).siblings().attr("checked", false);
			layui.use('form', function(){
				form = layui.form;
				form.render();
			})
		} */
	}
});


// 主标题文字粗细
var fontWeightHtml = '<div class="layui-form-item">';
	fontWeightHtml +=	 '<label class="layui-form-label sm">{{data.label}}</label>';
	fontWeightHtml +=	 '<div class="layui-input-block">';
	fontWeightHtml +=		 '<template v-for="(item,index) in list" v-bind:k="index">';
	fontWeightHtml +=			 '<div v-on:click="parent[data.field]=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (parent[data.field]==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.label}}</div></div>';
	fontWeightHtml +=		 '</template>';
	fontWeightHtml +=	 '</div>';
	fontWeightHtml += '</div>';

Vue.component("font-weight", {
	props: {
		data: {
			type: Object,
			default: function () {
				return {
					field: "fontWeight",
					label: "文字粗细"
				};
			}
		}
	},
	created: function () {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
		if (this.data.label == undefined) this.data.label = "文字粗细";
		if (this.data.field == undefined) this.data.field = "fontWeight";
	},
	watch: {
		data: function (val, oldVal) {
			if (val.field == undefined) val.field = oldVal.field;
			if (val.label == undefined) val.label = "文字粗细";
		},
	},
	template: fontWeightHtml,
	data: function () {
		return {
			list: [
				{label: "粗", value: 600},
				{label: "细", value: 500},
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


// 是否启用更多按钮设置
var moreBtnHtml = '<div class="layui-form-item">';
	moreBtnHtml +=	 '<label class="layui-form-label sm">{{data.label}}</label>';
	moreBtnHtml +=	 '<div class="layui-input-block">';
	moreBtnHtml +=		 '<template v-for="(item,index) in list" v-bind:k="index">';
	moreBtnHtml +=			 '<div v-on:click="parent[data.field]=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (parent[data.field]==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.label}}</div></div>';
	moreBtnHtml +=		 '</template>';
	moreBtnHtml +=	 '</div>';
	moreBtnHtml += '</div>';

Vue.component("text-more-btn", {
	props: {
		data: {
			type: Object,
			default: function () {
				return {
					field: "isShowMore",
					label: "是否启用"
				};
			}
		}
	},
	created: function () {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
		if (this.data.label == undefined) this.data.label = "启用";
		if (this.data.field == undefined) this.data.field = "isShowMore";
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