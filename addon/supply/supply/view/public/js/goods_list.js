var laytpl, form, element, repeat_flag, table;
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
                url: ns.url("supply://supply/goods/getGoodsSkuList"),
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

        form.render();
        refreshTable(0);

        //监听Tab切换，以改变地址hash值
        element.on('tab(goods_list_tab)', function () {
            var type = this.getAttribute('data-type');
            $("input[name='goods_state']").val("");
            $("input[name='verify_state']").val("");
            if (type) {
                var id = this.getAttribute('lay-id');
                $("input[name='" + type + "']").val(id);
            }
            var html = '<button class="layui-btn layui-btn-primary" lay-event="delete">批量删除</button>';
            if (type == "goods_state" && id == 1) {
                // 销售中状态：下架
                html += '<button class="layui-btn layui-btn-primary" lay-event="off_goods">下架</button>';
                $("input[name='verify_state']").val(1);
            } else if (type == "goods_state" && id == 0) {
                // 仓库中状态：上架
                html += '<button class="layui-btn layui-btn-primary" lay-event="on_goods">上架</button>';
                $("input[name='verify_state']").val(1);
            }
            $("#batchOperation").html(html);

            // 全部、销售中、仓库中、待审核
            if (type == null || type == "goods_state" || (type == "verify_state" && id == 0)) {
                refreshTable(0);
            } else if (type == "verify_state") {
                // 审核失败、违规下架
                refreshTable(1);
            }

        });

        // 监听工具栏操作
        table.tool(function (obj) {
            var data = obj.data;
            switch (obj.event) {

                case 'preview': //预览
                    goodsPreview(data);
                    break;
                case 'edit':
                    //编辑`
                    if (data.goods_class == 1) {
                        location.href = ns.url("supply://supply/goods/editgoods", {"goods_id": data.goods_id});
                    } else {
                        location.href = ns.url("supply://supply/virtualgoods/editgoods", {"goods_id": data.goods_id});
                    }
                    break;
                case 'delete':
                    //删除
                    deleteGoods(data.goods_id);
                    break;
                case 'off_goods':
                    //下架
                    offGoods(data.goods_id);
                    break;
                case 'on_goods':
                    //上架
                    onGoods(data.goods_id);
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
                case "delete":
                    deleteGoods(id_array.toString());
                    break;
                case 'off_goods':
                    //下架
                    offGoods(id_array.toString());
                    break;
                case 'on_goods':
                    //上架
                    onGoods(id_array.toString());
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
        })

    });
});

/**
 * 刷新表格列表
 * @param status 状态：0 在售，1 审核违规
 */
function refreshTable(status) {
    var cols = [
        [{
            type: 'checkbox',
            unresize: 'false',
            width: '3%'
        }, {
            title: '商品信息',
            unresize: 'false',
            width: '20%',
            templet: '#goods_info'
        }, {
            field: 'price',
            title: '<span style="padding-right: 15px;">价格(元)</span>',
            unresize: 'false',
            width: '10%',
            align: 'right',
            templet: function (data) {

                if (data.min_price == data.max_price) {
                    var price_str = '￥' + data.max_price;
                } else {
                    var price_str = '￥' + data.min_price + '~' + data.max_price;
                }
                return '<span style="padding-right: 15px;">' + price_str + '</span>';
            }
        }, {
            field: 'goods_stock',
            title: '库存',
            unresize: 'false',
            width: '10%'
        }, {
            field: 'sale_num',
            title: '销量',
            unresize: 'false',
            width: '10%'
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
            width: '17%',
            templet: function (data) {
                return ns.time_to_date(data.create_time);
            }
        }, {
            title: '操作',
            toolbar: '#operation',
            unresize: 'false',
            width: '15%'
        }]
    ];
    if (status === 1) {
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
                title: '审核状态',
                unresize: 'false',
                width: '12%',
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
                width: '25%',
                templet: function (data) {
                    return ns.time_to_date(data.create_time);
                }
            }, {
                title: '原因',
                unresize: 'false',
                width: '20%',
                templet: function (data) {
                    var str = '';
                    if (data.verify_state == 10) {
                        str = '<a href="javascript:getVerifyStateRemark(' + data.goods_id + ',10);" class="ns-text-color">查看</a>';
                    } else if (data.verify_state == -2) {
                        str = '<a href="javascript:getVerifyStateRemark(' + data.goods_id + ',-2);" class="ns-text-color">查看</a>';
                    }
                    return str;
                }
            }, {
                title: '操作',
                toolbar: '#operation',
                unresize: 'false',
                width: '15%'
            }]
        ];
    }

    table = new Table({
        elem: '#goods_list',
        url: ns.url("supply://supply/goods/lists"),
        cols: cols,
        bottomToolbar: "#batchOperation",
        where: {
            search_text: $("input[name='search_text']").val(),
            goods_state: $("input[name='goods_state']").val(),
            verify_state: $("input[name='verify_state']").val(),
            start_sale: $("input[name='start_sale']").val(),
            end_sale: $("input[name='end_sale']").val(),
            // start_price: 0,
            // end_price: 0,
            goods_shop_category_ids: $("select[name='goods_shop_category_ids'] option:checked").val(),
            category_id: $("input[name='category_id']").val(),
            goods_class: $("select[name='goods_class'] option:checked").val(),
        }
    });

    refreshVerifyStateCount();
}

function add() {
    location.href = ns.url('supply://supply/goods/addgoods');
    // var html = $("#selectAddGoods").html();
    // laytpl(html).render({}, function (html) {
    //     layer.open({
    //         type: 1,
    //         title: '选择商品类型',
    //         area: ['600px'],
    //         content: html
    //
    //     });
    // });
}

// 删除
function deleteGoods(goods_ids) {
    layer.confirm('删除后进入回收站，确定删除吗?', function () {
        if (repeat_flag) return;
        repeat_flag = true;

        $.ajax({
            url: ns.url("supply://supply/goods/deleteGoods"),
            data: {goods_ids: goods_ids.toString()},
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
}

//商品下架
function offGoods(goods_ids) {
    if (repeat_flag) return;
    repeat_flag = true;

    $.ajax({
        url: ns.url("supply://supply/goods/offGoods"),
        data: {goods_state: 0, goods_ids: goods_ids.toString()},
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

//商品上架
function onGoods(goods_ids) {

    if (repeat_flag) return;
    repeat_flag = true;

    $.ajax({
        url: ns.url("supply://supply/goods/onGoods"),
        data: {goods_state: 1, goods_ids: goods_ids.toString()},
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


/**
 * 获取违规下架原因
 * @param goods_id
 * @param verify_state
 */
function getVerifyStateRemark(goods_id, verify_state) {
    $.ajax({
        url: ns.url("supply://supply/goods/getVerifyStateRemark"),
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

/**
 * 刷新审核状态商品数量
 */
function refreshVerifyStateCount() {
    $.ajax({
        url: ns.url("supply://supply/goods/refreshVerifyStateCount"),
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

function closeStock() {
    layer.close(layer_stock);
}