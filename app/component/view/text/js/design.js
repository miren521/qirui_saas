/**
 * 空的验证组件，后续如果增加业务，则更改组件
 */

var emptyHtml = '<div class="layui-form-item"></div>';

Vue.component("text-empty", {
	template: emptyHtml,
	data: function () {
		return {
			textColor : this.$parent.data.textColor,
			padding : this.$parent.data.padding
		}
	},
	created:function() {
		this.$parent.data.verify = this.verify;//加载验证方法
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
		}
	}
});
var styleHtml = '<div class="layui-form-item">';
		styleHtml += '<label class="layui-form-label sm">选择风格</label>';
		styleHtml += '<div class="layui-input-block">';
			styleHtml += '<div class="ns-input-text ns-text-color selected-style" v-on:click="selectTestStyle">选择</div>';
		styleHtml += '</div>';
	styleHtml += '</div>';
Vue.component("text-style", {
	template: styleHtml,
	data: function() {
		return {
			data: this.$parent.data,
		}
	},
	created:function() {


		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		// this.$parent.data.verify.push(this.verify);//加载验证方法
		// console.log(this.verify)
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