var laytpl, form, layerIndex;
var file = "";//图片路径
$(function () {
	
	//编辑时赋值组装名称
	if ($("input[name='category_full_name']").length > 0) {
		var category_full_name = $("input[name='category_full_name']").val().split("/").slice(0, $("input[name='category_full_name']").val().split("/").length - 1);
		$("input[name='category_parent_name']").val(category_full_name);
	}
	
	if ($("input[name='image']").length > 0) {
		file = $("input[name='image']").val();
	}
	
	layui.use(['form', 'upload', 'laytpl'], function () {
		var upload = layui.upload,
			repeat_flag = false;//防重复标识
		laytpl = layui.laytpl;
		form = layui.form;
		form.render();
		
		/**
		 * 表单验证
		 */
		form.verify({
			num: function (value) {
				if (value == '') {
					return;
				}
				if (value % 1 != 0) {
					return '排序数值必须为整数';
				}
				if (value < 0) {
					return '排序数值必须为大于0';
				}
			},
			int: function (value) {
				if (value == '') {
					return;
				}
				if (value < 0) {
					return '库存数量不能小于0';
				}
				if (value % 1 != 0) {
					return '库存值不能为小数'
				}
			},
			flo: function (value, item) {
				var _index = $(item).parent().index();
				var _th = $(item).parents("table").find("thead th").eq(_index).text();
				
				if (value == '') {
					return;
				}
				if (value < 0) {
					return _th + '的值不能小于0';
				}
				if (value) {
					var arrMen = value.split(".");
					var val = 0;
					if (arrMen.length == 2) {
						val = arrMen[1];
					}
					if (val.length > 2) {
						return _th + '最多可保留两位小数'
					}
				}
			}
		});
		
		//普通图片上传
		var uploadInst = upload.render({
			elem: '#imgUpload'
			, url: ns.url("shop/upload/image")
			, before: function (obj) {
				//预读本地文件示例，不支持ie8
				obj.preview(function (index, file, result) {
					$('#imgUpload').html("<img src=" + result + " >"); //图片链接（base64）
				});
			}
			, done: function (res) {
				if (res.code >= 0) {
					file = res.data.pic_path;
				}
				return layer.msg(res.message);
			}
		});
		
		form.on('submit(save)', function (data) {
			
			data.field.image = file;
			data.field.category_full_name = data.field.category_name;
			if (data.field.category_parent_name) data.field.category_full_name = data.field.category_parent_name + "/" + data.field.category_name;
			
			data.field.attr_class_name = $("select[name='attr_class_id'] option:checked").text();
			
			if (repeat_flag) return false;
			repeat_flag = true;
			
			var url = ns.url("shop/goodscategory/addCategory");
			if (data.field.category_id) {
				url = ns.url("shop/goodscategory/editCategory");
			}
			$.ajax({
				url: url,
				data: data.field,
				dataType: 'json',
				type: 'post',
				success: function (data) {
					layer.msg(data.message);
					if (data.code == 0) {
						location.href = ns.url("shop/goodscategory/lists");
					} else {
						repeat_flag = false;
					}
				}
			});
			return false;
		});
		
		//保存上级分类
		form.on('submit(save_pid)', function (data) {
			
			var option_category_id_1 = $("select[name='category_id_1'] option:checked");
			
			var level, category_name, pid;
			if (option_category_id_1.length) {
				level = parseInt(option_category_id_1.attr("data-level"));
				category_name = option_category_id_1.text();
				pid = option_category_id_1.val();//上级分类id
				var category_id_1 = option_category_id_1.val();//一级分类id
				$("input[name='category_id_1']").val(category_id_1);
				$("input[name='category_parent_name']").val(category_name);
			}
			
			$(".js-pid span").text(category_name);
			$("input[name='pid']").val(pid);
			$("input[name='level']").val(level + 1);//当前添加的层级+1
			
			layer.close(layerIndex);
			return false;
			
		});
		
	});
	
});

//选择商品分类弹出框
function selectedCategoryPopup() {
	
	if ($("input[name='category_id']").length) {
		
		// 修改
		editSelectedPid();
		
	} else {
		
		//添加
		addSelectedPid();
		
	}
	
}

/**
 * 获取商品分类列表
 * @param data
 * @param callback
 */
function getCategoryList(data, callback) {
	$.ajax({
		url: ns.url("shop/goodscategory/getCategoryList"),
		data: data,
		dataType: 'json',
		type: 'post',
		async: false,
		success: function (res) {
			var data = res.data;
			if (callback) callback(data);
		}
	});
}

/**
 * 添加时，选择上级分类
 */
function addSelectedPid() {
	
	//查询一级商品分类
	getCategoryList({pid: 0}, function (list) {
		
		var html = $("#selectedCategory").html();
		var data = {
			category_id_1: $("input[name='category_id_1']").val(),
			category_list_1: list
		};
		laytpl(html).render(data, function (html) {
			layerIndex = layer.open({
				title: '选择商品分类',
				skin: 'layer-tips-class',
				type: 1,
				area: ['560px'],
				content: html,
				success: function () {
					form.render();
				}
			});
		});
	});
}

/**
 * 编辑时，选择上级分类
 */
function editSelectedPid() {
	
	var level = parseInt($("input[name='level']").val());
	
	if (level == 2) {
		
		//查询一级商品分类
		getCategoryList({pid: 0}, function (list) {
			
			var html = $("#selectedCategory").html();
			var data = {
				category_id_1: $("input[name='category_id_1']").val(),
				category_list_1: list
			};
			laytpl(html).render(data, function (html) {
				layerIndex = layer.open({
					title: '选择商品分类',
					skin: 'layer-tips-class',
					type: 1,
					area: ['450px'],
					content: html,
					success: function () {
						form.render();
					}
				});
			});
		});
		
	}
	
}

/**
 * 获取商品分类信息
 * @param category_id
 * @param callback
 */
function getCategoryInfo(category_id, callback) {
	$.ajax({
		url: ns.url("shop/goodscategory/getCategoryInfo"),
		data: {category_id: category_id},
		dataType: 'json',
		type: 'post',
		async: false,
		success: function (res) {
			var data = res.data;
			if (callback) callback(data);
		}
	});
}

function back() {
	location.href = ns.url("shop/goodscategory/lists")
}