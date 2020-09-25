/**
 * 空的验证组件，后续如果增加业务，则更改组件
 */
var shopStoreHtml = '<div class="shop-store-edit layui-form">';
shopStoreHtml += '</div>';

Vue.component("shop-store-empty", {
	template: shopStoreHtml,
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