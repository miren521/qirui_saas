/**
 * 公告·组件
 */

var noticePreviewHtml = '<div class="notice-view" v-bind:style="{ padding :data.padding + \'px 0\',margin:\'0 \' + data.marginLeftRight + \'px\'}">';
	noticePreviewHtml += '<div class="tips ns-border-color ns-text-color">公告</div>';
	noticePreviewHtml += '<div class="scroll-list" v-bind:style="{ width :((316 - 90 - data.marginLeftRight*2) + \'px\')}">';
		noticePreviewHtml += '<div v-for="(item,index) in list" class="item">';
			noticePreviewHtml += '<a href="javascript:;" :style="{ color : data.textColor,fontSize : data.fontSize + \'px\' }">{{item.title}}</a>';
		noticePreviewHtml += '</div>';
	noticePreviewHtml += '</div>';
noticePreviewHtml += '</div>';

Vue.component("notice",{
	
	template : noticePreviewHtml,
	data : function(){
		
		return {
			id: "notice_" + ns.gen_non_duplicate(10),
			data : this.$parent.data,
			list : this.$parent.data.list,
			marginLeftRight : this.$parent.data.marginLeftRight,
			padding : this.$parent.data.padding
		};
		
	},
});

var noticeEditHtml = '<div class="notice-config">';

	noticeEditHtml += '<font-size v-bind:data="{ value : $parent.data.fontSize }"></font-size>';
	noticeEditHtml += '<color></color>';
	noticeEditHtml += '<color v-bind:data="{ field : \'backgroundColor\', label : \'背景颜色\' }"></color>';
	noticeEditHtml += '<slide v-bind:data="{ field : \'padding\', label : \'上下边距\' }"></slide>'
	noticeEditHtml += '<slide v-bind:data="{ field : \'marginLeftRight\', label : \'左右边距\' }"></slide>'
	
	// noticeEditHtml += '<div class="layui-form-item" >';
	// 	noticeEditHtml += '<label class="layui-form-label sm">左侧图片</label>';
	// 	noticeEditHtml += '<div class="layui-input-block">';
	// 		noticeEditHtml += '<img-upload v-bind:data="{ data : $parent.data, field : \'image_url\' }"></img-upload>';
	// 	noticeEditHtml += '</div>';
	// noticeEditHtml += '</div>';
	
	noticeEditHtml += '<ul>';
		noticeEditHtml += '<li v-for="(item,index) in list" v-bind:key="index">';
			noticeEditHtml += '<div class="content-block">';
				noticeEditHtml += '<div class="layui-form-item" >';
					noticeEditHtml += '<label class="layui-form-label sm">公告内容</label>';
					noticeEditHtml += '<div class="layui-input-block">';
						noticeEditHtml += '<input type="text" name=\'title\' v-model="item.title" class="layui-input" />';
					noticeEditHtml += '</div>';
				noticeEditHtml += '</div>';
			
				noticeEditHtml += '<nc-link v-bind:data="{ field : $parent.data.list[index].link }"></nc-link>';
			noticeEditHtml += '</div>';
			
			noticeEditHtml += '<i class="del" v-on:click="list.splice(index,1)" data-disabled="1">x</i>';
			
			noticeEditHtml += '<div class="error-msg"></div>';
			
		noticeEditHtml += '</li>';
	
	noticeEditHtml += '</ul>';
	
	noticeEditHtml += '<div class="add-item ns-text-color" v-on:click="list.push({ title:\'公告\',link:{} })">';
	
		noticeEditHtml += '<i>+</i>';
		
		noticeEditHtml += '<span>添加一条公告</span>';
	
	noticeEditHtml += '</div>';
	
	noticeEditHtml += '</div>';
	
Vue.component("notice-edit",{

	template : noticeEditHtml,
	data : function(){

		return {
			data : this.$parent.data,
			list : this.$parent.data.list,
			marginLeftRight : this.$parent.data.marginLeftRight,
			padding : this.$parent.data.padding
		};

	},
	created : function(){
		this.$parent.data.verify = this.verify;//加载验证方法
	},
	methods : {
		verify :function () {
			var res = { code : true, message : "" };
			if(this.$parent.data.list.length >0){
				for(var i=0;i < this.$parent.data.list.length;i++){
					if(this.$parent.data.list[i].title == ""){
						res.code = false;
						res.message = "公告内容不能为空";
						break;
					}
				}
			}else{
				res.code = false;
				res.message = "请添加一条公告";
			}
			return res;
		}
	}
});