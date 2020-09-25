var form, laytpl, layerIndex = -1, repeatFlag = false;

var selectedBrand = [];//已选品牌
var selectedBrandCopy = [];//已选品牌初始不变，用于防止已关联的品牌，进行误操作
var deleteBrand = [];//要删除的品牌

var attrValueList = [];// 属性值列表
var deleteAttrValueList = [];//要删除的属性值

$(function () {
	
	$(".ns-custom-panel .custom-panel-content .panel-content li.selected-brand-list span").each(function () {
		var brand_id = $(this).attr("data-brand-id");
		var id = $(this).attr("data-id");
		selectedBrand.push(brand_id);
		selectedBrandCopy.push({id: id, brand_id: brand_id});
	});
	
	layui.use(['form', 'laytpl'], function () {
		form = layui.form;
		laytpl = layui.laytpl;
		
		//编辑商品类型信息
		form.on('submit(save_attr)', function (data) {
			
			if (repeatFlag) return false;
			repeatFlag = true;
			
			$.ajax({
				url: ns.url("admin/goodsattr/editAttr"),
				data: data.field,
				dataType: 'json',
				type: 'post',
				success: function (data) {
					layer.msg(data.message);
					if (data.code == 0) {
						layer.close(layerIndex);
						location.reload();
					} else {
						repeatFlag = false;
					}
				}
			});
			return false;
		});
		
		//编辑关联品牌
		form.on('submit(save_brand)', function (data) {
			
			if (repeatFlag) return false;
			repeatFlag = true;
			
			var brand_id_arr = [];
			$(".brand-list ul.selected li").each(function () {
				var brand_id = $(this).attr("data-brand-id");
				var id = $(this).attr("data-id");
				//只添加未关联的品牌
				if (!id) brand_id_arr.push(brand_id);
			});
			
			if (deleteBrand.length > 0) {
				//删除已关联的品牌
				$.ajax({
					url: ns.url("admin/goodsattr/deleteAttrClassBrand"),
					data: {attr_class_id: attr_class_id, id_arr: deleteBrand.toString()},
					dataType: 'json',
					type: 'post',
					async: false,
					success: function (data) {
						
						if (brand_id_arr.length === 0) {
							layer.close(layerIndex);
							layer.msg(data.message);
							location.hash = "";
							location.reload();
						}
						
					}
				});
			}
			
			if (brand_id_arr.length > 0) {
				data.field.brand_id_arr = brand_id_arr.toString();
				
				//添加新关联的品牌
				$.ajax({
					url: ns.url("admin/goodsattr/addAttrClassBrand"),
					data: data.field,
					dataType: 'json',
					type: 'post',
					success: function (data) {
						layer.msg(data.message);
						if (data.code == 0) {
							layer.close(layerIndex);
							location.hash = "";
							location.reload();
						} else {
							repeatFlag = false;
						}
					}
				});
			}
			return false;
		});
		
		form.verify({
			attr_value: function (value) {
				if ($("select[name='attr_type']").val() != 3 && value.length == 0) {
					return "请输入属性值名称";
				}
			},
			num: function (value) {
				if (value == '') {
					return;
				}
				if (value%1 != 0) {
					return '排序数值必须为整数';
				}
				if (value < 0) {
					return '排序数值必须为大于0';
				}
			}
		});
		
		//添加属性、属性值
		form.on('submit(save_add_attribute)', function (data) {
			
			if (repeatFlag) return false;
			repeatFlag = true;
			
			if (data.field.attr_type != 3) {
				var value = [];
				for (var i = 0; i < attrValueList.length; i++) {
					value.push(attrValueList[i].attr_value_name);
				}
				data.field.attr_value_list = value.toString();
			} else {
				data.field.is_query = 0;
			}
			
			//开启规格属性后，不参与筛选
			if (data.field.is_spec == 1) {
				data.field.is_query = 0;
			}
			
			var attr_id = 0;
			
			// console.log(data.field);
			// return false;
			
			// 添加属性
			$.ajax({
				url: ns.url("admin/goodsattr/addattribute"),
				data: data.field,
				dataType: 'json',
				type: 'post',
				async: false,
				success: function (res) {
					attr_id = res.data;
					if (data.field.attr_type == 3) {
						layer.msg(res.message);
						if (res.code == 0) {
							layer.close(layerIndex);
							location.hash = "";
							location.reload();
						} else {
							repeatFlag = false;
						}
					}
				}
			});
			
			//输入类型不需要添加属性值
			if (data.field.attr_type != 3) {
				// 添加属性值
				addAttributeValue(attr_id, function (res) {
					layer.msg(res.message);
					layer.close(layerIndex);
					location.hash = "";
					location.reload();
				});
			}
			
			return false;
		});
		
		var table = new Table({
			elem: '#attribute_list',
			url: ns.url("admin/goodsattr/getAttributeList"),
			where: {attr_class_id: attr_class_id},
			page: false,
			parseData: function (data) {
				return {
					"code": data.code,
					"msg": data.message,
					"count": data.data.length,
					"data": data.data
				};
			},
			cols: [
				[
					{
						field: 'attr_name',
						title: '属性名称',
						width: '12%',
						unresize: 'false'
					},
					{
						title: '属性类型',
						width: '10%',
						unresize: 'false',
						templet: function (data) {
							var h = '';
							if (data.attr_type == 1) {
								h = '单选';
							} else if (data.attr_type == 2) {
								h = '多选';
							} else if (data.attr_type == 3) {
								h = '输入';
							}
							return h;
						}
					},
					{
						title: '是否参与筛选',
						width: '12%',
						unresize: 'false',
						templet: function (data) {
							var h = data.is_query == 1 ? "是" : "否";
							return h;
						}
					},
					{
						title: '是否规格属性',
						width: '13%',
						unresize: 'false',
						templet: function (data) {
							var h = data.is_spec == 1 ? "是" : "否";
							return h;
						}
					},
					{
						field: 'attr_value_list',
						title: '属性值',
						width: '20%',
						unresize: 'false'
					},
					{
						field: 'site_name',
						title: '店铺名称',
						width: '10%',
						unresize: 'false'
					},
					{
						unresize: 'false',
						field: 'sort',
						title: '排序',
						width: '10%',
						align: 'center',
					},
					{
						title: '操作',
						width: '13%',
						toolbar: '#attributeOperation',
						unresize: 'false'
					}
				]
			]
		});
		
		/**
		 * 监听工具栏操作
		 */
		table.tool(function (obj) {
			var data = obj.data;
			switch (obj.event) {
				case 'edit':
					editAttributePopup(data.attr_id);
					break;
				case 'delete':
					deleteAttribute(data.attr_id);
					break;
				case 'change':
					modifyAttributeSite(data.attr_id);
					break;
			}
		});
		
		//修改属性、属性值
		form.on('submit(save_edit_attribute)', function (data) {
			
			if (repeatFlag) return false;
			repeatFlag = true;
			
			if (data.field.attr_type != 3) {
				var value = [];
				for (var i = 0; i < attrValueList.length; i++) {
					value.push(attrValueList[i].attr_value_name);
				}
				data.field.attr_value_list = value.toString();
			} else {
				data.field.is_query = 0;
			}
			
			//开启规格属性后，不参与筛选
			if (data.field.is_spec == 1) {
				data.field.is_query = 0;
			}
			
			if (deleteAttrValueList.length > 0) {
				
				// 删除已存在的属性值
				$.ajax({
					url: ns.url("admin/goodsattr/deleteAttributeValue"),
					data: {attr_class_id: attr_class_id, attr_value_id_arr: deleteAttrValueList.toString()},
					dataType: 'json',
					type: 'post',
					async: false,
					success: function (data) {
					}
				});
			}
			
			// 修改已存在的属性值
			var isEditAttrValue = [];//已存在的属性值集合
			for (var i = 0; i < attrValueList.length; i++) {
				//只有已存在的属性值和修改过的属性值才进行push
				if (attrValueList[i].is_add && attrValueList[i].is_change) {
					attrValueList[i].attr_id = data.field.attr_id;
					attrValueList[i].attr_class_id = attr_class_id;
					isEditAttrValue.push(attrValueList[i]);
				}
			}
			if (isEditAttrValue.length > 0) {
				$.ajax({
					url: ns.url("admin/goodsattr/editAttributeValue"),
					data: {attr_class_id: attr_class_id, data: JSON.stringify(isEditAttrValue)},
					dataType: 'json',
					type: 'post',
					async: false,
					success: function (data) {
					}
				});
			}
			
			if (data.field.attr_type != 3) {
				// 添加新的属性值
				addAttributeValue(data.field.attr_id);
			}
			
			// 修改属性
			$.ajax({
				url: ns.url("admin/goodsattr/editAttribute"),
				data: data.field,
				dataType: 'json',
				type: 'post',
				async: false,
				success: function (data) {
					layer.msg(data.message);
					if (data.code == 0) {
						layer.close(layerIndex);
						location.hash = "";
						location.reload();
					} else {
						repeatFlag = false;
					}
				}
			});
			
			return false;
			
		});
		
	});
	
	// 已选品牌点击事件
	$("body").on("click", ".brand-list ul.selected li", function () {
		if ($(this).hasClass("selected")) return;
		var _self = this;
		var id = $(this).attr("data-id");//已关联品牌
		var brand_id = $(this).attr("data-brand-id");
		var index = selectedBrand.indexOf(brand_id);//匹配下标
		if (id) {
			//删除已关联品牌需要再次确认
			layerIndex = layer.confirm('当前品牌已关联商品分类，移除后数据会发生不可逆转的行为，请谨慎操作', function () {
				deleteBrand.push(id);
				$(".brand-list ul.unselected li[data-brand-id='" + brand_id + "']").removeClass("selected");
				$(_self).remove();
				selectedBrand.splice(index, 1);
				layer.close(layerIndex);
			});
		} else {
			$(".brand-list ul.unselected li[data-brand-id='" + brand_id + "']").removeClass("selected");
			$(this).remove();
			selectedBrand.splice(index, 1);
		}
	});
	
	// 未选品牌点击事件
	$("body").on("click", ".brand-list ul.unselected li", function () {
		if ($(this).hasClass("selected")) return;
		var brand_id = $(this).attr("data-brand-id");
		var brand_name = $(this).children().text();
		var id = 0;
		
		//检测当前操作的品牌是否属于已关联的品牌
		for (var i = 0; i < selectedBrandCopy.length; i++) {
			if (selectedBrandCopy[i].brand_id == brand_id) {
				id = selectedBrandCopy[i].id;
				break;
			}
		}
		
		if (id == 0) {
			var li = '<li data-brand-id="' + brand_id + '">';
		} else {
			var li = '<li data-brand-id="' + brand_id + '" data-id="' + id + '" class="ns-text-color-black">';
		}
		li += '<span>' + brand_name + '</span>';
		li += '<i class="layui-icon layui-icon-delete ns-bg-color"></i>';
		li += '</li>';
		$(".brand-list ul.selected").append(li);
		$(this).addClass("selected");
		selectedBrand.push(brand_id);
		
		//检测是否要删除的品牌，只有已关联的才能操作
		if (id > 0 && deleteBrand.indexOf(id) > -1) {
			deleteBrand.splice(deleteBrand.indexOf(id), 1);
		}
	});
	
	//监听属性值键盘输入事件
	$("body").on("keyup", ".attribute-value-list .table-wrap .layui-table input", function () {
		var name = $(this).attr("name");
		var index = $(this).attr("data-index");
		if (name == "attr_value_name") attrValueList[index].attr_value_name = $(this).val();
		if (name == "attr_value_sort") attrValueList[index].sort = $(this).val();
		attrValueList[index].is_change = true;//标记已修改
	});
	
});

/**
 * 打开编辑商品类型弹出框
 */
function editAttrClassPopup() {
	var edit_attr_class = $("#editAttrClass").html();
	laytpl(edit_attr_class).render({}, function (html) {
		layerIndex = layer.open({
			title: '编辑商品类型',
			skin: 'layer-tips-class',
			type: 1,
			area: ['550px'],
			content: html,
		});
	});
}

//获取品牌分页列表
function getBrandPageList(page) {
	page = page || 1;
	
	$.ajax({
		url: ns.url("admin/goodsattr/getBrandPageList"),
		data: {page: page, limit: 20},
		dataType: 'json',
		type: 'post',
		success: function (res) {
			var data = res.data;
			
			for (var i = 0; i < data.list.length; i++) {
				if (selectedBrand.indexOf(data.list[i].brand_id.toString()) > -1) {
					data.list[i].selected = true;
				} else {
					data.list[i].selected = false;
				}
			}
			
			var edit_brand = $("#brandList").html();
			laytpl(edit_brand).render(data.list, function (html) {
				$("#brand-wrap").html(html);
			});
			
			var page = new Page({
				elem: 'page',
				count: data.count,
				limit: 20,
				callback: function (res) {
					getBrandPageList(res.page);
				}
			});
		}
	});
}

/**
 * 编辑关联品牌弹出框
 */
function editBrandPopup() {
	var edit_brand = $("#editBrand").html();
	laytpl(edit_brand).render({}, function (html) {
		layerIndex = layer.open({
			title: '编辑关联品牌',
			skin: 'layer-tips-class',
			type: 1,
			area: ['750px', '600px'],
			content: html,
			success: function () {
				getBrandPageList();
			}
		});
	});
	
}

/**
 * 打开添加属性弹出框
 */
function addAttributePopup() {
	var add_attr = $("#addAttribute").html();
	laytpl(add_attr).render({}, function (html) {
		layerIndex = layer.open({
			title: '添加属性',
			skin: 'layer-tips-class',
			type: 1,
			area: ['800px', '500px'],
			content: html,
			success: function () {
				form.render();
				
				form.on('select(attr_type)', function (data) {
					if (data.value == 3) {
						$(".js-is-query").hide();
						$(".attribute-value-list").hide();
					} else {
						$(".js-is-query").show();
						$(".attribute-value-list").show();
					}
					
					//检测是否开启规格属性
					if($("input[name='is_spec']").is(":checked")) {
						if (data.value == 1) {
							$(".js-is-query").hide();
							$(".js-is-spec").show();
						} else {
							$(".js-is-spec").hide();
						}
					}
				});
				
				form.on('switch(is_spec)', function (data) {
					var h = '';
					if (this.checked) {
						h += '<option value="1">单选</option>';
						$(".js-is-query").hide();
					} else {
						h += '<option value="1">单选</option>';
						h += '<option value="2">多选</option>';
						h += '<option value="3">输入</option>';
						$(".js-is-query").show();
					}
					
					$("select[name='attr_type']").html(h);
					form.render("select");
				});
				
				attrValueList = [];
				addAttrValue();
			}
		});
	});
}

//添加属性值
function addAttrValue() {
	
	attrValueList.push({
		attr_value_name: "",
		sort: 0
	});
	refreshAttrValueList();
	var scrollHeight = $(".attribute-value-list .table-wrap").prop("scrollHeight");
	$(".attribute-value-list .table-wrap").scrollTop(scrollHeight)
	
}

//刷新属性值列表
function refreshAttrValueList() {
	var h = '';
	for (var i = 0; i < attrValueList.length; i++) {
		var item = attrValueList[i];
		h += '<tr>';
		h += '<td><input name="attr_value_name" type="text" value="' + item.attr_value_name + '" data-index="' + i + '" placeholder="请输入属性值名称" lay-verify="attr_value" class="layui-input ns-len-mid" autocomplete="off"></td>';
		h += '<td><input name="attr_value_sort" type="text" value="' + item.sort + '" data-index="' + i + '" placeholder="请输入排序" lay-verify="num" class="layui-input ns-len-small" autocomplete="off"></td>';
		h += '<td><a class="ns-text-color" href="javascript:deleteAttrValue(' + i + ');">删除</a></td>';
		h += '</tr>';
	}
	
	$(".attribute-value-list .layui-table tbody").html(h);
}

//删除属性值
function deleteAttrValue(index) {
	if (attrValueList[index].is_add) {
		
		//删除已添加的属性值需要再次确认
		layerIndex = layer.confirm('属性值已使用，请谨慎操作', function () {
			
			deleteAttrValueList.push(attrValueList[index].attr_value_id);
			attrValueList.splice(index, 1);
			refreshAttrValueList();
			
			layer.close(layerIndex);
		});
	} else {
		attrValueList.splice(index, 1);
		refreshAttrValueList();
	}
}

//删除属性
function deleteAttribute(attr_id) {
	
	//删除属性需要再次确认
	layerIndex = layer.confirm('确定要删除吗？', function () {
		
		if (repeatFlag) return false;
		repeatFlag = true;
		
		$.ajax({
			url: ns.url("admin/goodsattr/deleteAttribute"),
			data: {attr_class_id: attr_class_id, attr_id: attr_id},
			dataType: 'json',
			type: 'post',
			success: function (data) {
				layer.msg(data.message);
				if (data.code == 0) {
					layer.close(layerIndex);
					location.hash = "";
					location.reload();
				} else {
					repeatFlag = false;
				}
			}
		});
		
		layer.close(layerIndex);
	});
}

function modifyAttributeSite(attr_id) {
	layer.confirm('确定要转移到平台吗？', function() {
		$.ajax({
			url: ns.url("admin/goodsattr/modifyattributesite"),
			data: {attr_class_id: attr_class_id, attr_id: attr_id},
			dataType: 'JSON',
			type: 'POST',
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					location.hash = "";
					location.reload();
				}
			}
		});
	});
}

/**
 * 打开编辑属性弹出框
 */
function editAttributePopup(attr_id) {
	
	$.ajax({
		url: ns.url("admin/goodsattr/getAttributeDetail"),
		data: {attr_class_id: attr_class_id, attr_id: attr_id},
		dataType: 'json',
		type: 'post',
		success: function (res) {
			if (res.code == 0) {
				var data = res.data;
				var edit_attr = $("#editAttribute").html();
				laytpl(edit_attr).render(data, function (html) {
					var area = ['800px', '500px'];
					if (data.attr_type == 3) {
						area = ['800px', '350px'];
					}
					layerIndex = layer.open({
						title: '编辑属性',
						skin: 'layer-tips-class',
						type: 1,
						area: area,
						content: html,
						success: function () {
							form.render();
							
							if (data.is_spec == 1) {
								$(".js-is-query").hide();
							}
							
							if (data.attr_type == 3) {
								$(".js-is-query").hide();
								$(".attribute-value-list").hide();
							}
							
							form.on('switch(is_spec)', function (data) {
								//检测是否开启规格属性
								if (data.elem.checked) {
									$(".js-is-query").hide();
									$("input[name=is_spec]").val(1);
								} else {
									$(".js-is-query").show();
									$("input[name=is_spec]").val(0);
								}
							});
							
							attrValueList = [];//每次编辑时清空属性值集合
							if (data.attr_type != 3 && data.value) {
								for (var i = 0; i < data.value.length; i++) {
									attrValueList.push({
										attr_value_name: data.value[i].attr_value_name,
										sort: data.value[i].sort,
										is_add: true,// 已添加属性值进行标识
										attr_value_id: data.value[i].attr_value_id
									});
								}
								refreshAttrValueList();
							}
							
						}
					});
				});
			}
			
		}
	});
	
}

//添加属性值
function addAttributeValue(attr_id, callback) {
	
	attr_id = attr_id || 0;
	
	// 添加属性值
	var addAttrValue = [];
	for (var i = 0; i < attrValueList.length; i++) {
		if (!attrValueList[i].is_add) {
			attrValueList[i].attr_id = attr_id;
			attrValueList[i].attr_class_id = attr_class_id;
			addAttrValue.push(attrValueList[i]);
		}
	}
	
	if (addAttrValue.length > 0) {
		
		$.ajax({
			url: ns.url("admin/goodsattr/addAttributeValue"),
			data: {attr_class_id: attr_class_id, value: JSON.stringify(addAttrValue)},
			dataType: 'json',
			type: 'post',
			async: false,
			success: function (data) {
				if (callback) callback(data);
			}
		});
	}
}