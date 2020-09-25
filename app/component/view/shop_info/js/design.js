/**
 * 空的验证组件，后续如果增加业务，则更改组件
 */
var shopInfoHtml = '<div class="goods-list-edit layui-form">';
	shopInfoHtml += '</div>';

Vue.component("shop-info-empty", {
	template: shopInfoHtml,
	data: function () {
		return {
			data: this.$parent.data,
		}
	},
	created:function() {
	},
	methods: {
	}
});