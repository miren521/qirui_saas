/**
 * 系统内置属性组件
 */
var resourceHtml = '<div v-show="false"><slot></slot></div>';

//CSS·组件
Vue.component("css", {
	
	props: ["src"],
	template: resourceHtml,
	created: function () {
		var self = this;
		
		//内联样式
		if (this.$slots.default) {
			var css = "<style>" + this.$slots.default[0].text + '</style>';
			
			//防止重复加载资源
			if ($("head").html().indexOf(css) == -1) {
				$("head").append(css);
			}
			
			//延迟
			setTimeout(function () {
				self.$parent.data.lazyLoadCss = true;
			}, 10);
		}
		
		//外联样式
		if (this.src) {
			
			//防止重复加载资源
			if ($("head").html().indexOf(this.src) == -1) {
				var styleNode = createLink(this.src);
				styleOnload(styleNode, function () {
					self.$parent.data.lazyLoadCss = true;
				});
			} else {
				//延迟
				setTimeout(function () {
					self.$parent.data.lazyLoadCss = true;
				}, 10);
			}
		}
		
	}
});

//JavaScript脚本·组件
Vue.component("js", {
	
	props: ["src"],
	template: resourceHtml,
	created: function () {
		var self = this;
		
		//如果JS全部是内部代码，则延迟10毫秒
		//如果JS有内部代码、也有外部JS，则以外部JS加载完成时间为准，同时延迟10毫秒，让外部JS中的组件进行加载
		//如果JS全部是外部代码，则以外部JS加载完成时间为准，同时延迟10毫秒，让外部JS中的组件进行加载
		
		//内联js
		if (this.$slots.default) {
			var script = "<script>" + this.$slots.default[0].text + "</script>";
			$("body").append(script);
			//如果有外部JS，则以外部JS加载完成时间为准
			if (this.$parent.data.outerCountJs == 0) {
				setTimeout(function () {
					self.$parent.data.lazyLoad = true;
				}, 10);
			}
		}
		
		//外联js
		if (this.src) {
			$.getScript(this.src, function (res) {
				setTimeout(function () {
					self.$parent.data.lazyLoad = true;
				}, 10);
			});
		}
	}
});

//[对齐方式]属性组件
var textAlignHtml = '<div class="layui-form-item">';
		textAlignHtml += '<label class="layui-form-label sm">{{data.label}}</label>';
		textAlignHtml += '<div class="layui-input-block">';
			textAlignHtml += '<template v-for="(item,index) in list" v-bind:k="index">';
				textAlignHtml += '<div v-on:click="parent[data.field]=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (parent[data.field]==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.label}}</div></div>';
			textAlignHtml += '</template>';
		textAlignHtml += '</div>';
	textAlignHtml += '</div>';

Vue.component("text-align", {
	
	props: {
		data: {
			type: Object,
			default: function () {
				return {
					field: "textAlign",
					label: "对齐方式"
				};
			}
		}
	},
	created: function () {
		
		if (this.data.label == undefined) this.data.label = "对齐方式";
		
		if (this.data.field == undefined) this.data.field = "textAlign";
		
	},
	watch: {
		
		data: function (val, oldVal) {
			
			if (val.field == undefined) val.field = oldVal.field;
			
			if (val.label == undefined) val.label = "对齐方式";
			
		},
	},
	template: textAlignHtml,
	data: function () {
		return {
			list: [
				{label: "居左", value: "left"},
				{label: "居中", value: "center"},
				{label: "居右", value: "right"}
			],
			parent: this.$parent.data,
		};
	}
});

//[滑块]属性组件
var sliderHtml = '<div class="layui-form-item slide-component">';
sliderHtml += '<label class="layui-form-label sm">{{data.label}}</label>';
sliderHtml += '<div class="layui-input-block">';
sliderHtml += '<div v-bind:id="id" class="side-process"></div>';
sliderHtml += '<span class="slide-prompt-text">{{parent[data.field]}}</span>';
sliderHtml += '</div>';
sliderHtml += '</div>';

Vue.component("slide", {

	props: {
		data: {
			type: Object,
			default: function () {
				return {
					field: "height",
					label: "空白高度"
				};
			}
		}
	},
	created: function () {

		if (this.data.label == undefined) this.data.label = "空白高度";

		if (this.data.field == undefined) this.data.field = "height";

		var _self = this;
		setTimeout(function () {

			layui.use('slider', function(){
				var slider = layui.slider;
				var ins = slider.render({
					elem: '#'+_self.id,
					tips: false,
					theme: '#12b7f5',
					value : _self.parent[_self.data.field],
					change: function(value){
						_self.parent[_self.data.field] = value;
					}
				});

			});
		},10);
		
	},
	watch: {

		data: function (val, oldVal) {

			if (val.field == undefined) val.field = oldVal.field;

			if (val.label == undefined) val.label = "空白高度";

		},
	},
	template: sliderHtml,
	data: function () {
		return {
			id : "slide_" + ns.gen_non_duplicate(10),
			parent: this.$parent.data,
		};
	}
});

//[链接地址]属性组件
var linkHtml = '<div class="layui-form-item component-links">';
		linkHtml += '<label class="layui-form-label sm">{{myData[0].label}}</label>';
		linkHtml += '<div class="layui-input-block">';
			linkHtml += '<span style="margin-right: 10px;font-size: 12px;display: inline-block;height: 36px;line-height: 36px;vertical-align: top;white-space: nowrap;overflow: hidden;width: 100%;text-overflow: ellipsis;" v-if="myData[0].field.title" v-bind:title="myData[0].field.title">{{myData[0].field.title}}</span>';
			linkHtml += '<button v-for="(item,index) in myData[0].operation" class="layui-btn layui-btn-primary sm" v-on:click="selected(item.key,item.method)">{{item.label}}</button>';
		linkHtml += '</div>';
	linkHtml += '</div>';

/**
 * 链接组件：
 * 参数说明：data：当前链接对象, click：绑定事件，触发回调
 */
Vue.component("nc-link", {
	
	//data：链接对象，callback：回调，refresh：刷新filed
	props: {
		
		data: {
			type: Object,
			default: function () {
				return {
					
					//链接对象
					field: null,
					
					//文本
					label: "链接地址",
					
					//批量操作对象
					operation: [
						{key: "system", method: '', label: "选择"}
					],
					supportDiyView: ""
				};
			}
		},
		callback: null,
		refresh: null,
	},
	template: linkHtml,
	data: function () {
		return {
			myData: [this.data],//此处用数组的目的是触发变异方法，进行视图更新
		};
	},
	created: function () {
		
		if (this.data.supportDiyView == undefined) this.data.supportDiyView = "";
		
		if (this.data.label == undefined) this.data.label = "链接地址";
		
		if (this.data.operation == undefined) this.data.operation = [{ key : "system", method : '' , label: "选择" }];
		
	},
	watch: {
		
		data: function (val, oldVal) {
			if (val.field == undefined) val.field = oldVal.field;
			
			if (this.data.supportDiyView == undefined) this.data.supportDiyView = "";
			
			if (this.data.label == undefined) this.data.label = "链接地址";
			
			if (this.data.operation == undefined) this.data.operation = [{ key : "system", method : '' , label: "选择" }];
			
			// console.log("watch:", this.data.field);
		},
		refresh:function (val,oldVal) {
			this.myData[0].field = val;
			this.set(val);
		}
	},
	methods: {
		//设置链接地址
		set: function (link) {
			//由于Vue2.0是单向绑定的：子组件无法修改父组件，但是可以修改单个属性，循环遍历属性赋值
			if (this.data.field) {
				keys = Object.keys(this.data.field)
				for(let i=0;i<keys.length;i++){
					delete this.data.field[keys[i]];
				}
				Object.assign(this.data.field,link)
			}
			
			//触发变异方法，进行视图更新
			this.myData.push({});
			this.myData.pop();
		},
		selected: function (key,method) {
			
			var $self = this;
			if(key == "system") {
				//系统链接
				ns.select_link($self.myData[0].field,$self.myData[0].supportDiyView, function (data) {
					$self.set(data);
					console.log("data",data)
					if ($self.callback) $self.callback.call(this, data);
				}, link_url);
			}else {
				//插件自定义链接
				ns[method]($self.myData[0].field, $self.myData[0].supportDiyView, function (data) {
					$self.set(data);
					if ($self.callback) $self.callback.call(this, data);
				}, link_url);
			}
		}
	}
});

//[颜色]属性组件
var colorHtml = '<div class="layui-form-item">';
		colorHtml += '<label class="layui-form-label sm">{{d.label}}</label>';
		colorHtml += '<div class="layui-input-block">';
			colorHtml += '<div v-bind:class="class_name" class="colorSelector"><div v-bind:style="{ background : parent[d.field] }"></div></div>';
			colorHtml += '<button class="layui-btn layui-btn-primary sm" v-if="!reStartColor" v-on:click="reset()">{{text}}</button>';
		colorHtml += '</div>';
	colorHtml += '</div>';

/**
 * 颜色组件：
 * 参数说明：
 * data：{ field : 字段名, value : 值(默认:#333333), 'label' : 文本标签(默认:文字颜色) }
 */
Vue.component("color", {
	props: {
		data: {
			type: Object,
			default: function () {
				return {
					field: "textColor",
					label: "文字颜色",
					defaultcolor: ""
				};
			}
		},
		text:{
			type: String,
			default: '重置'
		},
		reStartColor:{
			type: [Boolean,String],
		}
	},
	
	data: function () {
		return {
			d: this.data,
			// class_name: "colorSelector_" + (this.data.field ? this.data.field : "textColor") + ns.gen_non_duplicate(10),
			class_name: "",
			parent: (Object.keys(this.$parent.data).length) ? this.$parent.data : this.$parent.global,
		};
	},
	created: function () {
		this.bindColor();
	},
	watch:{
		// parent : function(val, oldVal) {
			// console.log("watch p",val,oldVal);
		// },
	},
	methods: {
		init:function(){

			// console.log("datadatadata",this.data,this.parent);
			if (this.data.field == undefined) this.data.field = "textColor";
			if (this.data.label == undefined) this.data.label = "文字颜色";
			if (this.data.value == undefined) this.data.value = "#333333";
			if (this.data.defaultcolor == undefined) this.data.defaultcolor = "";
			if (this.data.defaultvalue == undefined) this.data.defaultvalue = "";

			//如果当前字段没有值数据，则给予默认值，反之用该字段的值，用于优化调用该组件
			if (this.parent[this.data.field] == undefined) this.$set(this.parent, this.data.field, this.data.value);
			else this.data.value = this.parent[this.data.field];
			this.parent[this.data.field] = this.data.value;

			if (this.parent[this.data.defaultcolor] == undefined) this.$set(this.parent, this.data.defaultcolor, this.data.defaultvalue);
			else this.data.defaultvalue = this.parent[this.data.defaultcolor];
			this.parent[this.data.defaultcolor] = this.data.defaultvalue;
			this.d = this.data;
		},
		reset: function () {
			try {
				this.parent[this.d.field] = this.d.defaultvalue;
			} catch (e) {
				console.log("color reset() ERROR:" + e.message);
			}
		},
		bindColor: function () {
			this.init();
			this.class_name = "colorSelector_" + (this.data.field ? this.data.field : "textColor") + ns.gen_non_duplicate(10);
			var class_name = "." + this.class_name;
			var $self = this;

			setColorPicker($self.data.value, class_name, function (hex) {
				try {
					// data数据可能不全，所以要用d，通过对象的引用关系绑定数据
					// 这里要更新$self，不然如果删除了，就会出问题
					// console.log("getThis",$self.refreshData());
					$self.parent[$self.d.field] = "#" + hex;
					// console.log("$self.parent", $self.parent, vue.currentIndex);
				} catch (e) {
					console.log("color ERROR:" + e.message);
				}
			});
		},
		refreshData: function (){
			// 刷新parent、data
			// console.log("this.parent",this.parent);
			if(this.parent.controller && this.parent.controller != vue.data[vue.currentIndex].controller){
				// 数据发送变动
				this.parent = vue.data[vue.currentIndex];
				this.init();
				// console.log("数据发送变动",this.d);
			}
			return this.parent;
		}
	},
	
	template: colorHtml
});

/**
 * 生成颜色选择器
 * @param defaultColor
 * @param obj
 * @param callBack
 */
function setColorPicker(defaultColor, obj, callBack) {

	setTimeout(function () {
		$(obj).ColorPicker({
			
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			
			onSubmit: function (hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			
			onChange: function (hsb, hex, rgb) {
				$(obj).find("div").css('background', '#' + hex);
				if (callBack) callBack(hex);
			}
			
		});
		if(defaultColor) $(obj).ColorPickerSetColor(defaultColor);
	}, 500);
}

//[文字大小]属性组件
var fontSizeHtml = '<div class="layui-form-item">';
		fontSizeHtml += '<label class="layui-form-label sm">{{d.label}}</label>';
		fontSizeHtml += '<div class="layui-input-block">';
			fontSizeHtml += '<div v-bind:class="{  \'layui-unselect layui-form-select\' : true, \'layui-form-selected\' : isShowFontSize }" v-on:click="isShowFontSize=!isShowFontSize;">';
				fontSizeHtml += '<div class="layui-select-title">';
					fontSizeHtml += '<input type="text" placeholder="请选择" v-bind:value="list[selectIndex].text" readonly="readonly" class="layui-input layui-unselect">';
					fontSizeHtml += '<i class="layui-edge"></i>';
				fontSizeHtml += '</div>';
				fontSizeHtml += '<dl class="layui-anim layui-anim-upbit">';
					fontSizeHtml += '<dd v-for="(item,index) in list" v-bind:value="item.value" v-bind:class="{ \'layui-this\' : (parent[d.field]==item.value) }" v-on:click.stop="parent[d.field]=item.value;isShowFontSize=false;selectIndex=index;">{{item.text}}</dd>';
				fontSizeHtml += '</dl>';
			fontSizeHtml += '</div>';
		fontSizeHtml += '</div>';
	fontSizeHtml += '</div>';

/**
 * 文字大小
 * 参数说明：
 * data：{ field : 字段名, value : 值(默认:14), 'label' : 文本标签(默认:文字大小) }
 */
Vue.component("font-size", {
	
	template: fontSizeHtml,
	props: {
		data: {
			type: Object,
			default: function () {
				return {
					field: "fontSize",
					label: "文字大小",
					value: 14
				};
			}
		},
	},
	data: function () {
		return {
			d: this.data,
			isShowFontSize: false,
			selectIndex: 2,		//当前选中的下标
			list: [],
			parent: (Object.keys(this.$parent.data).length) ? this.$parent.data : this.$parent.global,
		};
	},
	created: function () {
		
		if (this.data.field == undefined) this.data.field = "fontSize";
		
		if (this.data.label == undefined) this.data.label = "文字大小";
		
		if (this.data.value == undefined) this.data.value = 14;
		
		if (this.parent[this.data.field] == undefined) this.$set(this.parent, this.data.field, this.data.value);
		
		this.parent[this.data.field] = this.data.value;
		
		for (var i = 12; i <= 30; i++) this.list.push({value: i, text: i + "px"});
		for (var i = 0; i < this.list.length; i++) {
			if (this.list[i].value == this.data.value) {
				this.selectIndex = i;
				break;
			}
		}
		
	},
});

//[图片上传]组件
var imageHtml = '<div v-show="condition" class="img-block layui-form ns-text-color" :id="id" v-bind:class="{ \'has-choose-image\' : (myData.data[myData.field]) }">';
			imageHtml += '<div>';
				imageHtml += '<template v-if="myData.data[myData.field]">';
					imageHtml += '<img v-bind:src="changeImgUrl(myData.data[myData.field])"/>';
					imageHtml += '<span>更换图片</span>';
					imageHtml += '<i class="del" v-on:click.stop="del()" data-disabled="1">x</i>';
				imageHtml += '</template>';
				
				imageHtml += '<template v-else>';
					imageHtml += '<i class="add">+</i>';
					imageHtml += '<span>{{myData.text}}</span>';
				imageHtml += '</template>';
				
			imageHtml += '</div>';
	imageHtml += '</div>';

/**
 * 图片上传
 * 参数说明：
 * data：{ field : 字段名, value : 值(默认:14), 'label' : 文本标签(默认:文字大小) }
 */
Vue.component("img-upload", {
	
	template: imageHtml,
	props: {
		data: {
			type: Object,
			default: function () {
				return {
					data: {},
					field: "imageUrl",
					callback: null,
					text: "添加图片"
				};
			}
		},
		condition: {
			type: Boolean,
			default: true
		}
	},
	data: function () {
		return {
			myData: this.data,
			upload : null,
			id : ns.gen_non_duplicate(10)
		};
	},
	watch: {
		data: function (val, oldVal) {
			if (val.field == undefined) val.field = oldVal.field;
			
			if (val.text == undefined) val.text = "添加图片";
			
			this.myData = val;
			
		}
	},
	created: function () {
		
		if (this.data.field == undefined) this.data.field = "imageUrl";
		
		if (this.data.data[this.data.field] == undefined) this.$set(this.data.data, this.data.field, "");
		
		if (this.data.text == undefined) this.data.text = "添加图片";
		
		this.id = ns.gen_non_duplicate(10);
		
		var self = this;
		setTimeout(function () {
			layui.use(['upload'], function() {
				self.upload = layui.upload;
				//上传logo
				var uploadInst = this.upload.render({
					elem: '#'+self.id,
					url: ns.url("shop/upload/upload"),
					done: function(res) {
						self.data.data[self.data.field] = res.data.pic_path;
						if (self.callback) self.data.callback.call(this);
						return layer.msg(res.message);
					}
				});
			});
		},20);
		
	},
	methods: {
		del: function () {
			this.data.data[this.data.field] = "";
		},
		
		//转换图片路径
		changeImgUrl: function (url) {
			if (url == null || url == "") return '';
			else return ns.img(url);
		}
	}
});