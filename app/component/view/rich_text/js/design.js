var html = '<div class="rich-text-list">';
		html += '<color v-bind:data="{ field : \'backgroundColor\', \'label\' : \'背景颜色\' }"></color>';
		html += '<slide v-bind:data="{ field : \'padding\',  \'label\' : \'边距\' }"></slide>';
		html += '<div v-bind:id="id" style="width:100%;height:320px;"></div>';
	html += '</div>';

Vue.component("rich-text", {
	template: html,
	data: function () {
		return {
			// id: ns.gen_non_duplicate(10),
			// editor : null,
			// backgroundColor:this.$parent.data.backgroundColor,
			// padding:this.$parent.data.padding
			data : this.$parent.data,
			id: ns.gen_non_duplicate(10),
			editor : null,
			padding : this.$parent.data.padding,
			backgroundColor:this.$parent.data.backgroundColor,
		}
	},
	created: function () {
		
		this.$parent.data.verify = this.verify;//加载验证方法
		
		var self = this;
		setTimeout(function () {
			
			self.editor = UE.getEditor(self.id);
			
			self.editor.ready(function () {
				if(self.$parent.data.html) self.editor.setContent(self.$parent.data.html);
			});
			
			self.editor.addListener("contentChange",function(){
				self.$parent.data.html = self.editor.getContent();
			});
			
		}, 10);
		
	},
	methods:{
		
		verify : function () {
			var res = {code: true, message: ""};
			if (this.$parent.data.html == "") {
				res.code = false;
				res.message = "请输入富文本内容";
			}
			return res;
		}
	}
});