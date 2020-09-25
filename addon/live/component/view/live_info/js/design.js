// 显示内容
var showContentHtml = '<div class="layui-form-item goods-show-box">';
	showContentHtml += '<div class="layui-input-inline">';
		showContentHtml += '<div v-on:click="changeStatus(\'isShowAnchorInfo\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.isShowAnchorInfo == 1)}" lay-skin="primary"><span>主播信息</span><i class="layui-icon layui-icon-ok"></i></div>';
		showContentHtml += '<div v-on:click="changeStatus(\'isShowLiveGood\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.isShowLiveGood == 1)}" lay-skin="primary"><span>直播商品</span><i class="layui-icon layui-icon-ok"></i></div>';
	showContentHtml += '</div>';
	showContentHtml += '</div>';

Vue.component("live-show-content", {
	template: showContentHtml,
	data: function () {
		return {
			data: this.$parent.data,
			isShowAnchorInfo: this.$parent.data.isShowAnchorInfo,
			isShowLiveGood: this.$parent.data.isShowLiveGood,
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