var laytpl, form, element, repeat_flag, table, layer_pass;

$(function () {
	layui.use(['form', 'laytpl', 'element'], function () {
		form = layui.form;
		repeat_flag = false; //防重复标识
		element = layui.element;
		laytpl = layui.laytpl;
		form.render();
		refreshTable();
		// 监听工具栏操作
		table.tool(function (obj) {
			var data = obj.data;
			switch (obj.event) {
				case 'stock':
                    $.ajax({
                        url: ns.url("store://store/goods/getGoodsSkuList"),
                        data: {goods_id: data.goods_id},
                        dataType: 'JSON',
                        type: 'POST',
                        async: false,
                        success: function (res) {
                            var data = res.data;

							laytpl($("#skuList").html()).render(data, function(html) {
								layer_pass = layer.open({
									title: '商品库存',
									skin: 'layer-tips-class',
									type: 1,
									area: ['600px'],
									content: html
								});
							});
                        }
                    });
					break;
			}
		});

		// 提交修改后的库存
		form.on('submit(save_stock)', function (obj) {
			var field = obj.field;
			if (repeat_flag) return false;
			repeat_flag = true;

			$.ajax({
				type: "POST",
				url: ns.url("store://store/goods/saveStock"),
				data: field,
				dataType: 'JSON',
				success: function (res) {
					layer.msg(res.message);
					repeat_flag = false;
					if (res.code == 0) {
						location.reload();
					}
				}
			});
		});


		// 搜索功能
		form.on('submit(search)', function (data) {
			table.reload({
				page: {
					curr: 1
				},
				where: data.field
			});
			return false;
		});

		// 验证
		form.verify({
			int: function (value) {
				if (value < 0) {
					return '销量不能小于0!'
				}
				if (value % 1 != 0) {
					return '销量不能为小数!'
				}
			},
			stock: function(value, item){ //value：表单的值、item：表单的DOM对象
				var now_stock = $(item).attr("now_stock");
				if((parseInt(now_stock) + parseInt(value)) < 0){
					$(item).val(0);
					return '库存不能小于0!'
				}
			}
		})


	});
});

function cancelLayer() {
	layer.close(layer_pass);
}

/**
 * 刷新表格列表
 * @param status 状态：0 在售
 */
function refreshTable() {
	table = new Table({
		elem: '#goods_list',
		url: ns.url("store://store/goods/index"),
		cols: [
			[{
				title: '商品信息',
				unresize: 'false',
				width: '25%',
				templet: '#goods_info'
			}, {
				field: 'price',
				title: '<span style="padding-right: 15px;">价格(元)</span>',
				unresize: 'false',
				width: '10%',
				align: 'right',
				templet: function (data) {
					return '<span style="padding-right: 15px;">￥' + data.price + '</span>';
				}
			}, {
				field: 'store_goods_stock',
				title: '库存',
				unresize: 'false',
				width: '10%',
				templet: function (data) {
					return data.store_goods_stock == null ? 0 : data.store_goods_stock;
				}
			}, {
				field: 'store_sale_num',
				title: '销量',
				unresize: 'false',
				width: '10%',
				templet: function (data) {
					return data.store_sale_num == null ? 0 : data.store_sale_num;
				}
			}, {
				title: '商品状态',
				unresize: 'false',
				width: '12%',
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
				width: '15%',
				templet: function (data) {
					return ns.time_to_date(data.create_time);
				}
			}, {
				title: '操作',
				toolbar: '#action',
				unresize: 'false',
				width: '15%'
			}]
		],
		where: {
			search_text: $("input[name='search_text']").val(),
			start_sale: $("input[name='start_sale']").val(),
            end_sale: $("input[name='end_sale']").val(),
			goods_shop_category_ids: $("select[name=goods_shop_category_ids] option:checked").val(),
		}
	});
}