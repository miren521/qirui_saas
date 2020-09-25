var laytpl, form, element, repeat_flag = false, table;
$(function () {
	$("body").on("click", ".contraction", function () {
		var goods_id = $(this).attr("data-goods-id");
		var open = $(this).attr("data-open");
		var tr = $(this).parent().parent().parent().parent();
		var index = tr.attr("data-index");

		if (open == 1) {
			$(this).children("span").text("+");
			$(".js-sku-list-" + index).remove();
		} else {
			$(this).children("span").text("-");
			$.ajax({
				url: ns.url("city://city/goods/getGoodsSkuList"),
				data: {goods_id: goods_id},
				dataType: 'JSON',
				type: 'POST',
				async: false,
				success: function (res) {
					var list = res.data;
					var sku_list = $("#skuList").html();
					var data = {
						list: list,
						index: index
					};
					laytpl(sku_list).render(data, function (html) {
						tr.after(html);
					});
					layer.photos({
					  	photos: '.img-wrap',
						anim: 5
					});
				}
			});
		}
		$(this).attr("data-open", (open == 0 ? 1 : 0));
	});

	layui.use(['form', 'laytpl', 'element'], function () {
		form = layui.form;
		repeat_flag = false; //防重复标识
		element = layui.element;
		laytpl = layui.laytpl;

		//商品品牌
		goodsBrand();

		//商品类型
		// goodsSattr();

		refreshTable(0);
		
		form.render();

		//监听Tab切换，以改变地址hash值
		element.on('tab(goods_list_tab)', function () {
			var id = this.getAttribute('lay-id');
			var type = this.getAttribute('data-type');
			$("input[name='goods_state']").val("");
			$("input[name='verify_state']").val("");
			if (type) {
				$("input[name='" + type + "']").val(id);
			}

			$("#batchOperation").html("");
			if (type == "goods_state" && (id == 1 || id == 0)) {
				// 销售中、仓库中状态：违规下架
				$("#batchOperation").html('<button class="layui-btn layui-btn-primary" lay-event="lockup">违规下架</button>');
				$("input[name='verify_state']").val(1);
			} else if (type == "verify_state" && id == 0) {
				// 待审核状态：通过、拒绝
				$("#batchOperation").html('<button class="layui-btn layui-btn-primary" lay-event="verify_on">审核通过</button><button class="layui-btn layui-btn-primary" lay-event="verify_off">审核拒绝</button>');
			}

			// 全部
			if (type == null) {
				refreshTable(0);
			} else if (type == "goods_state") {
				// 销售中、仓库中
				refreshTable(1);
			} else if (type == "verify_state" && id == 0) {
				// 待审核
				refreshTable(2);
			} else if (type == "verify_state") {
				// 审核失败、违规下架
				refreshTable(3);
			}
		});

		// 监听工具栏操作
		table.tool(function (obj) {
			var data = obj.data;
			switch (obj.event) {
				case 'select': //推广
					goodsUrl(data);
					break;
				case 'preview': //预览
					goodsPreview(data);
					break;
				case 'lockup': //违规下架
					lockup(data.goods_id);
					break;
				case 'verify_on':
					//审核通过
					verifyOn(data.goods_id, 1);
					break;
				case 'verify_off':
					//审核失败
					verifyOn(data.goods_id, -2);
					break;
				case 'select_verify_remark':
					getVerifyStateRemark(data.goods_id, -2);
					break;
				case 'select_violations_remark':
					getVerifyStateRemark(data.goods_id, 10);
					break;
			}
		});

		// 批量操作
		table.bottomToolbar(function (obj) {
			if (obj.data.length < 1) {
				layer.msg('请选择要操作的数据');
				return;
			}

			var id_array = new Array();
			for (i in obj.data) id_array.push(obj.data[i].goods_id);
			switch (obj.event) {
				case 'lockup': //违规下架
					lockup(id_array.toString());
					break;
				case 'verify_on':
					//审核通过
					verifyOn(id_array.toString(), 1);
					break;
				case 'verify_off':
					//审核失败
					verifyOn(id_array.toString(), -2);
					break;
			}
		});

		// 搜索功能
		form.on('submit(search)', function (data) {
			table.reload({
				page: {
					curr: 1
				},
				where: data.field
			});
			refreshVerifyStateCount();
			return false;
		});
	});

});

/**
 * 刷新表格列表
 * @param status 状态：0 全部，1 销售中、仓库中，2 待审核，3 审核失败、违规下架
 */
function refreshTable(status) {

	// 全部
	var cols = [
		[{
			type: 'checkbox',
			unresize: 'false',
			width: '3%'
		}, {
			title: '商品信息',
			unresize: 'false',
			width: '25%',
			templet: '#goods_info'
		}, {
			field: 'site_name',
			title: '店铺名称',
			unresize: 'false',
			width: '10%',
			templet: function(data) {
				return '<span title="'+ data.site_name +'">'+ data.site_name +'</span>';
			}
		}, {
			field: 'price',
			title: '<span style="padding-right: 15px;" class="ns-line-hiding" title="价格">价格</span>',
			align: 'right',
			unresize: 'false',
			width: '9%',
			templet: function(data) {
				return '<span style="padding-right: 15px;"  class="ns-line-hiding" title="￥'+ data.price +'">￥' + data.price +'<span>';
			}
		}, {
			field: 'goods_stock',
			title: '库存',
			unresize: 'false',
			width: '8%'
		}, {
			field: 'sale_num',
			title: '销量',
			unresize: 'false',
			width: '7%'
		}, {
			title: '商品状态',
			unresize: 'false',
			width: '8%',
			templet: function (data) {
				var str = '';
				if (data.goods_state == 1) {
					str = '销售中';
				} else if (data.goods_state == 0) {
					str = '仓库中';
				}
				return str;
			}
		}, {
			title: '审核状态',
			unresize: 'false',
			width: '8%',
			templet: function (data) {
				var str = '';
				if (data.verify_state == 1) {
					str = '已审核';
				} else if (data.verify_state == 0) {
					str = '待审核';
				} else if (data.verify_state == 10) {
					str = '违规下架';
				} else if (data.verify_state == -1) {
					str = '审核中';
				} else if (data.verify_state == -2) {
					str = '审核失败';
				}
				return str;
			}
		}, {
			title: '创建时间',
			unresize: 'false',
			width: '12%',
			templet: function (data) {
				return '<span title="'+ ns.time_to_date(data.create_time) +'">'+ ns.time_to_date(data.create_time) +'</span>';
			}
		}, {
			title: '操作',
			toolbar: '#action',
			unresize: 'false',
			width: '10%'
		}]
	];
	if (status === 1) {
		// 销售中、仓库中
		cols = [
			[{
				type: 'checkbox',
				unresize: 'false',
				width: '3%'
			}, {
				title: '商品信息',
				unresize: 'false',
				width: '25%',
				templet: '#goods_info'
			}, {
				field: 'site_name',
				title: '店铺名称',
				unresize: 'false',
				width: '10%',
			}, {
				field: 'price',
				title: '价格(元)',
				unresize: 'false',
				width: '12%'
			}, {
				field: 'goods_stock',
				title: '库存',
				unresize: 'false',
				width: '10%'
			}, {
				field: 'sale_num',
				title: '销量',
				unresize: 'false',
				width: '8%'
			}, {
				title: '商品状态',
				unresize: 'false',
				width: '10%',
				templet: function (data) {
					var str = '';
					if (data.goods_state == 1) {
						str = '销售中';
					} else if (data.goods_state == 0) {
						str = '仓库中';
					}
					return str;
				}
			}, {
				title: '创建时间',
				unresize: 'false',
				width: '12%',
				templet: function (data) {
					return ns.time_to_date(data.create_time);
				}
			}, {
				title: '操作',
				toolbar: '#action',
				unresize: 'false',
				width: '10%'
			}]
		];
	} else if (status === 2) {
		// 待审核，跟全部展示一样
	}
	else if (status === 3) {
		// 审核失败、违规下架
		cols = [
			[{
				type: 'checkbox',
				unresize: 'false',
				width: '3%'
			}, {
				title: '商品信息',
				unresize: 'false',
				width: '37%',
				templet: '#goods_info'
			}, {
				field: 'site_name',
				title: '店铺名称',
				unresize: 'false',
				width: '25%',
			}, {
				title: '审核状态',
				unresize: 'false',
				width: '10%',
				templet: function (data) {
					var str = '';
					if (data.verify_state == 1) {
						str = '已审核';
					} else if (data.verify_state == 0) {
						str = '待审核';
					} else if (data.verify_state == 10) {
						str = '违规下架';
					} else if (data.verify_state == -1) {
						str = '审核中';
					} else if (data.verify_state == -2) {
						str = '审核失败';
					}
					return str;
				}
			}, {
				title: '创建时间',
				unresize: 'false',
				width: '15%',
				templet: function (data) {
					return ns.time_to_date(data.create_time);
				}
			}, {
				title: '原因',
				unresize: 'false',
				width: '10%',
				templet: function (data) {
					var str = '';
					if (data.verify_state == 10) {
						str = '<a href="javascript:getVerifyStateRemark(' + data.goods_id + ',10);" class="ns-text-color">查看</a>';
					} else if (data.verify_state == -2) {
						str = '<a href="javascript:getVerifyStateRemark(' + data.goods_id + ',-2);" class="ns-text-color">查看</a>';
					}
					return str;
				}
			}]
		];
	}

	table = new Table({
		elem: '#goods_list',
		url: ns.url("city://city/goods/lists"),
		cols: cols,
		bottomToolbar: "#batchOperation",
		where: {
			search_text: $("input[name='search_text']").val(),
			search_text_type: $("select[name='search_text_type'] option:checked").val(),
			goods_state: $("input[name='goods_state']").val(),
			verify_state: $("input[name='verify_state']").val(),
			category_id: $("input[name='category_id']").val(),
			goods_brand: $("select[name='goods_brand'] option:checked").val(),
			goods_class: $("select[name='goods_class'] option:checked").val(),
			goods_attr_class: $("select[name='goods_attr_class'] option:checked").val()
		}
	});

	refreshVerifyStateCount();
}

//审核商品
function verifyOn(goods_ids, verify_state) {
	if (verify_state === -2) {
		// 拒绝
		layer.prompt({
			formType: 2,
			title: '审核拒绝原因',
			cancel: function (index, layero) {
				repeat_flag = false;
			},
			end: function () {
				repeat_flag = false;
			}
		}, function (value, index, elem) {

			if (repeat_flag) return;
			repeat_flag = true;
			layer.close(index);

			$.ajax({
				url: ns.url("city://city/goods/verifyOn"),
				data: {
					goods_ids: goods_ids.toString(),
					verify_state: verify_state,
					verify_state_remark: value
				},
				dataType: 'JSON',
				type: 'POST',
				success: function (res) {
					layer.msg(res.message);
					repeat_flag = false;
					if (res.code == 0) {
						table.reload();
						refreshVerifyStateCount();
					}
				}
			});
		});
	} else {
		if (repeat_flag) return;
		repeat_flag = true;
		layer.close(index);

		$.ajax({
			url: ns.url("city://city/goods/verifyOn"),
			data: {
				goods_ids: goods_ids.toString(),
				verify_state: verify_state
			},
			dataType: 'JSON',
			type: 'POST',
			success: function (res) {
				layer.msg(res.message);
				repeat_flag = false;
				if (res.code == 0) {
					table.reload();
					refreshVerifyStateCount();
				}
			}
		});
	}

}

//商品违规下架
function lockup(goods_ids) {
	layer.prompt({
		formType: 2,
		title: '违规下架原因',
		cancel: function (index, layero) {
			repeat_flag = false;
		},
		end: function () {
			repeat_flag = false;
		},
		yes: function (index, layero) {
			var value = layero.find(".layui-layer-input").val();
			
			if (repeat_flag) return;
			repeat_flag = true;
			if (value) {
				$.ajax({
					url: ns.url("city://city/goods/lockup"),
					data: {
						"verify_state_remark": value,
						"goods_ids": goods_ids.toString()
					},
					dataType: 'JSON',
					type: 'POST',
					success: function (res) {
						layer.msg(res.message);
						repeat_flag = false;
						if (res.code == 0) {
							table.reload();
							refreshVerifyStateCount();
						}
					}
				});
				layer.close(index);
			} else {
				repeat_flag = false;
				layer.msg('请输入违规下架原因!', {icon: 5, anim: 6});
				return;
			}
		}
	});
}

// 商品推广
function goodsUrl(data) {
	$(".operation-wrap[data-goods-id='" + data.goods_id + "'] .popup-qrcode-wrap").show();
	$('#goods_name').html(data.goods_name);
	$.ajax({
		type: "POST",
		url: ns.url("city://city/goods/goodsUrl"),
		data: {
			'goods_id': data.goods_id
		},
		dataType: 'JSON',
		success: function (res) {
			if (res.data.path.h5.status == 1) {
				res.data.goods_id = data.goods_id;
				laytpl($("#goods_url").html()).render(res.data, function (html) {
					$(".operation-wrap[data-goods-id='" + data.goods_id + "'] .popup-qrcode-wrap").html(html).show();

					$("body").click(function (e) {
						if (!$(e.target).closest(".popup-qrcode-wrap").length) {
							$(".operation-wrap[data-goods-id='" + data.goods_id + "'] .popup-qrcode-wrap").hide();
						}
					});
				});
			} else {
				layer.msg(res.data.path.h5.message);
				$(".operation-wrap[data-goods-id='" + data.goods_id + "'] .popup-qrcode-wrap").hide();
			}
		}
	});
}

// 商品预览
var isOpenGoodsPreviewPopup = false;//防止重复弹出商品预览框
function goodsPreview(data) {
	if (isOpenGoodsPreviewPopup) return;
	isOpenGoodsPreviewPopup = true;
	$.ajax({
		type: "POST",
		url: ns.url("city://city/goods/goodsPreview"),
		data: {
			'goods_id': data.goods_id
		},
		dataType: 'JSON',
		success: function (res) {
			console.log(res)
			if (res.data.path.h5.status == 1) {
				res.data.goods_id = data.goods_id;

				laytpl($("#goods_preview").html()).render(res.data, function (html) {
					var layerIndex = layer.open({
						title: '商品预览',
						skin: 'layer-tips-class',
						type: 1,
						area: ['600px', '600px'],
						content: html,
						success: function () {
							isOpenGoodsPreviewPopup = false;
						}
					});
				});
			} else {
				layer.msg(res.data.path.h5.message);
			}
		}
	});
}

/**
 * 获取商品违规或审核失败说明
 * @param goods_id
 * @param verify_state
 */
function getVerifyStateRemark(goods_id, verify_state) {
	if (repeat_flag) return;
	repeat_flag = true;
	$.ajax({
		url: ns.url("city://city/goods/getVerifyStateRemark"),
		data: {goods_id: goods_id},
		dataType: 'JSON',
		type: 'POST',
		success: function (res) {
			var data = res.data;
			if (data) {
				var title = '';
				if (verify_state == -2) title = '审核失败原因';
				else title = '违规下架原因';
				layer.open({
					title: title,
					content: data.verify_state_remark,
					cancel: function (index, layero) {
						repeat_flag = false;
					},
					end: function () {
						repeat_flag = false;
					},
					yes: function (index, layero) {
						repeat_flag = false;
						layer.close(index);
					}
				});
			}
		}
	});
}

//商品品牌
function goodsBrand() {
	var brandHtml = "";
	$.ajax({
		url: ns.url("city://city/goodsbrand/lists"),
		type: 'POST',
		dataType: 'JSON',
		success: function (res) {
			brandHtml += '<option value="">全部</option>';
			$.each(res.data.list, function (key, val) {
				brandHtml += '<option value="${val.brand_id}">${val.brand_name}</option>';
			});
			$("select[name='goods_brand']").html(brandHtml);
			form.render('select');
		}
	});
}

//商品类型
function goodsSattr() {
	var sattrHtml = "";
	$.ajax({
		url: ns.url("city://city/goodsattr/lists"),
		type: 'POST',
		dataType: 'JSON',
		success: function (res) {
			sattrHtml += '<option value="">全部</option>';
			$.each(res.data.list, function (key, val) {
				sattrHtml += '<option value="${val.class_id}">${val.class_name}</option>';
			});
			$("select[name='goods_attr_class']").html(sattrHtml);
			form.render('select');
		}
	});
}

// 刷新审核状态商品数量
function refreshVerifyStateCount() {
	$.ajax({
		url: ns.url("city://city/goods/refreshVerifyStateCount"),
		type: 'POST',
		dataType: 'JSON',
		success: function (res) {
			for (var i = 0; i < res.length; i++) {
				if (res[i].count) $('div[lay-filter="goods_list_tab"] li[data-type="verify_state"][lay-id="' + res[i].state + '"] span.count').text(res[i].count).show();
				else $('div[lay-filter="goods_list_tab"] li[data-type="verify_state"][lay-id="' + res[i].state + '"] span').hide();
			}
		}
	});
}