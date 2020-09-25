var floorForm, floorLayer, floorUpload, floorLaytpl, floorColorpicker, repeatFlag = false;
layui.use(['form', 'layer', 'upload', 'laytpl', 'colorpicker'], function () {
	floorForm = layui.form;
	floorLayer = layui.layer;
	floorUpload = layui.upload;
	floorLaytpl = layui.laytpl;
	floorColorpicker = layui.colorpicker;
	floorForm.render();

	if ($("#info").length > 0) {
		setTimeout(function () {
			vm.data = JSON.parse($("#info").val().toString());
			vm.blockId = parseInt($("#block_id").val().toString());
		}, 100);
	}

	floorForm.on('select(block_id)', function (data) {
		var value = $(data.elem).find("option:selected").attr("data-value");
		var blockId = $(data.elem).find("option:selected").attr("data-block-id");
		vm.blockId = blockId;
		if (value) {
			vm.data = JSON.parse(value);
		}
	});

	floorForm.verify({
		title: function (value) {
			if (value == '') {
				return '请输入楼层名称';
			}
			if (value.length > 100) {
				return '最多100个字符';
			}
		},
		block_id: function (value) {
			if (!value) return '请选择楼层模板';
		}
	});

	floorForm.on('submit(save)', function (data) {

		var value = JSON.parse(JSON.stringify(vm.data));
		for (var i in value) {
			if ($.inArray(value[i].type, ['goods', 'brand', 'category']) != -1) {
				value[i].value.list = [];
			}
		}

		data.field.value = JSON.stringify(value);

		if (repeatFlag) return;
		repeatFlag = true;

		$.ajax({
			url: ns.url("admin/pc/editFloor"),
			data: data.field,
			dataType: 'JSON',
			type: 'POST',
			success: function (res) {
				floorLayer.msg(res.message);
				if (res.code == 0) {
					location.href = ns.url("admin/pc/floor");
				}
				repeatFlag = false;
			}
		});
	});
});

var vm = new Vue({
	el: "#app",
	data: function () {
		return {
			data: null,
			blockId: 0
		};
	},
	created: function () {
	},
	methods: {
		img: function (url) {
			return url ? ns.img(url) : "";
		},
		/**
		 * 初始化链接下拉框
		 * @param select_tag
		 * @param link_tag
		 */
		initLink: function (select_tag, link_tag) {
			floorForm.on('select(' + select_tag + ')', function (data) {
				var title = $(data.elem).find("option:selected").text();
				if (data.value != 'diy') {
					$("input[name='" + link_tag + "']").val(JSON.stringify({
						"title": title,
						"url": data.value
					}));
				} else {
					floorLayer.prompt({
						formType: 2,
						value: $("input[name='" + link_tag + "']").val() ? JSON.parse($("input[name='" + link_tag + "']").val()).url : '',
						title: '自定义链接地址',
						area: ['450px', '100px'],
						cancel: function () {
							$("input[name='" + link_tag + "']").val("");
						}
					}, function (value, index, elem) {
						$("input[name='" + link_tag + "']").val(JSON.stringify({
							"title": title,
							"url": value
						}));
						floorLayer.close(index);
					});
				}
			});
		},
		/**
		 * 设置文本
		 * @param data 当前数据
		 * @param callback 回调
		 */
		setText: function (data, callback) {
			var self = this;
			var getTpl = $("#setTitleHtml").html();
			if (!data) data = {};
			floorLaytpl(getTpl).render(data, function (html) {
				var textLayer = floorLayer.open({
					type: 1,
					title: "编辑文本",
					content: html,
					area: ['400px', '300px'],
					success: function (layero, index) {
						floorForm.render();
						self.initLink("pc_link_text", "text_link");

						// 文字颜色
						floorColorpicker.render({
							elem: '#text_color',  //绑定元素
							color: data.color ? data.color : "",
							done: function (color) {
								$("#text_color_input").attr("value", color);
							}
						});

						floorForm.on('submit(save_text)', function (data) {
							if (data.field.text_link) data.field.text_link = JSON.parse(data.field.text_link);
							if (callback) callback({
								text: data.field.text,
								link: data.field.text_link,
								color: data.field.text_color
							});
							floorLayer.close(textLayer);
						});
					}
				});
				floorForm.render();
			});
		},
		/**
		 * 上传图片
		 * @param data 当前数据
		 * @param callback 回调
		 */
		uploadImg: function (data, callback) {
			var self = this;
			var getTpl = $("#uploadImg").html();
			if (!data) data = {};
			floorLaytpl(getTpl).render(data, function (html) {
				var textLayer = floorLayer.open({
					type: 1,
					title: "上传图片",
					content: html,
					area: ['450px', '300px'],
					success: function (layero, index) {
						floorForm.render();
						floorUpload.render({
							elem: "#upload_image",
							url: ns.url("admin/upload/upload"),
							done: function (res) {
								$("input[name='upload_image']").val(res.data.pic_path);
								$("#upload_image").html("<img src=" + ns.img(res.data.pic_path) + " >");
							}
						});
						self.initLink("pc_link_upload", "upload_link");
						floorForm.on('submit(save_upload)', function (data) {
							if (data.field.upload_link) data.field.upload_link = JSON.parse(data.field.upload_link);
							if (callback) callback({
								url: data.field.upload_image,
								link: data.field.upload_link
							});
							floorLayer.close(textLayer);
						});
					}
				});
				floorForm.render();
			});
		},
		/**
		 * 设置商品分类
		 * @param data 当前数据
		 * @param callback 回调
		 */
		setCategory: function (data, callback) {
			var self = this;
			var getTpl = $("#setCategoryHtml").html();
			if (!data) data = {};
			floorLaytpl(getTpl).render(data, function (html) {
				var textLayer = floorLayer.open({
					type: 1,
					title: "编辑商品分类",
					content: html,
					area: ['600px', '400px'],
					success: function (layero, index) {
						floorForm.render();
						floorForm.on('select(goods_category)', function (categoryData) {
							var category_name = $.trim($(categoryData.elem).find("option:selected").text());
							var category_id = $(categoryData.elem).val();
							var isAdd = true;
							for (var i = 0; i < data.list.length; i++) {
								if (data.list[i].category_id == category_id) {
									isAdd = false;
									break;
								}
							}
							if (isAdd) {
								data.list.push({
									category_id: category_id,
									category_name: category_name
								});
								floorLaytpl(getTpl).render(data, function (html) {
									$(".set-category").html(html);
									floorForm.render();
								});
							}
						});

						floorForm.on('submit(save_category)', function (data) {
							if (data.field.category_ids) data.field.category_ids = data.field.category_ids.replace(/\s+/g, "");
							if (callback) callback({
								category_ids: data.field.category_ids,
								list: data.field.category_list ? JSON.parse(data.field.category_list) : [],
							});
							floorLayer.close(textLayer);
						});
					}
				});
				floorForm.render();
			});
		},
	}
});

/**
 * 商品选择器
 * @param callback 回调函数
 * @param selectId 已选商品id
 * @param params mode：模式(spu、sku), max_num：最大数量，min_num 最小数量, is_virtual 是否虚拟 0 1, disabled: 开启禁用已选 0 1
 */
function goodsSelect(callback, selectId, params) {
	if (selectId.length) {
		params.select_id = selectId.toString();
	}
	var url = ns.url("admin/pc/goodsselect", params);
	//iframe层-父子操作
	floorLayer.open({
			title: "商品选择",
			type: 2,
			area: ['1000px', '600px'],
			fixed: false, //不固定
			btn: ['保存', '返回'],
			content: url,
			yes: function (index, layero) {
				var iframeWin = window[layero.find('iframe')[0]['name']];//得到iframe页的窗口对象，执行iframe页的方法：
				iframeWin.selectGoods(function (obj) {
					if (typeof callback == "string") {
						try {
							eval(callback + '(obj)');
							floorLayer.close(index);
						} catch (e) {
							console.error('回调函数' + callback + '未定义');
						}
					} else if (typeof callback == "function") {
						callback(obj);
						floorLayer.close(index);
					}

				});
			}
		}
	);
}

/**
 * 品牌选择器
 * @param callback 回调函数
 * @param selectId 已选商品id
 * @param params mode：模式(spu、sku), max_num：最大数量，min_num 最小数量, disabled: 开启禁用已选 0 1
 */
function brandSelect(callback, selectId, params) {
	if (selectId.length) {
		params.select_id = selectId.toString();
	}
	var url = ns.url("admin/pc/brandselect", params);
	//iframe层-父子操作
	floorLayer.open({
			title: "品牌名称",
			type: 2,
			area: ['1000px', '600px'],
			fixed: false, //不固定
			btn: ['保存', '返回'],
			content: url,
			yes: function (index, layero) {
				var iframeWin = window[layero.find('iframe')[0]['name']];//得到iframe页的窗口对象，执行iframe页的方法：
				iframeWin.selectBrands(function (obj) {
					if (typeof callback == "string") {
						try {
							eval(callback + '(obj)');
							floorLayer.close(index);
						} catch (e) {
							console.error('回调函数' + callback + '未定义');
						}
					} else if (typeof callback == "function") {
						callback(obj);
						floorLayer.close(index);
					}

				});
			}
		}
	);
}