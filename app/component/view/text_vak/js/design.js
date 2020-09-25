/**
 * 空的验证组件，后续如果增加业务，则更改组件
 */

var emptyHtml = '<div class="layui-form-item"></div>';

Vue.component("text-empty", {
	template: emptyHtml,
	data: function () {
		return {
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