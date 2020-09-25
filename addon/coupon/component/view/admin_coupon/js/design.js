/**
 * 平台优惠券·组件
 */

var adminCouponHtml = '<div class="layui-form-item">';
		adminCouponHtml += '<label class="layui-form-label sm">选择模板</label>';
		adminCouponHtml += '<div class="layui-input-block">';
			adminCouponHtml += '<template v-for="(item,index) in selectedTemplateList" v-bind:k="index">';
				adminCouponHtml += '<div v-on:click="data.selectedTemplate=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.selectedTemplate==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.text}}</div></div>';
			adminCouponHtml += '</template>';
		adminCouponHtml += '</div>';
	adminCouponHtml += '</div>';

Vue.component("admin-coupon",{

	template : adminCouponHtml,
	data : function(){
		return {
			data : this.$parent.data,
			selectedTemplateList : [{
				text : '样式一',
				value : 'default'
			}]
		};
	}
});