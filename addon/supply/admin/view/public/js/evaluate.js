Evaluate = function (limit = 0, limits = []) {
    var _this = this;
    _this.listCount = 0;
    _this.page = 1;
    _this.limit = !limit ? 10 : limit;
    _this.limits = limits;
};

// var n = 1;
Evaluate.prototype.getList = function (d) {
    var _this = d._this,
        page = _this.page,
        limit = _this.limit,
        explain_type = d.explain_type == null ? '' : d.explain_type,
        search_type = d.search_type == null ? '' : d.search_type,
        search_text = d.search_text == null ? '' : d.search_text;

    $.ajax({
        url: ns.url("supply://admin/goods/evaluateList"),
        async: false,
        data: {
            "page": page,
            "limit": limit,
            "explain_type": explain_type,
            "search_type": search_type,
            "search_text": search_text,
        },
        type: "POST",
        dataType: "JSON",
        async: false,
        success: function (res) {
            _this.listCount = res.data.count;
            $(".ns-evaluate-table").find("tbody").empty();
            var d = res.data.list;

            if (d.length == 0) {
                var html = '<tr><td colspan="3" align="center">无数据</td></tr>';
                $(".ns-evaluate-table").find("tbody").append(html);
            }

            for (var i in d) {
                var html = '';
                html += '<tr>' +
                    '<input class="evaluate_id" type="hidden" value=' + d[i].evaluate_id + ' />' +
                    '<td colspan="3">' +
                    '<div class="ns-evaluate-title">' +
                    '<p>评论时间：' + ns.time_to_date(d[i].create_time) + '</p>' +
                    '<p>客户：' + d[i].shop_name + '</p>';

                if (d[i].explain_type == 1) {
                    html += `<p class="evaluate-level-good"><img src= "${ADMINIMG}/good_evaluate.png" /><span>好评</span></p>`;
                } else if (d[i].explain_type == 2) {
                    html += `<p class="evaluate-level-middel"><img src= "${ADMINIMG}/middel_evaluate.png" /><span>中评</span></p>`;
                } else {
                    html += `<p class="evaluate-level-bad"><img src= "${ADMINIMG}/bad_evaluate.png" /><span>差评</span></p>`;
                }

                html += '</div>' +
                    '</td>' +
                    '</tr>';

                html += '<tr id="eva_' + i + '">';
                html += '<td>';
                html += '<div class="ns-evaluate">' +
                    '<span class="again-evaluate required">[用户评论]</span>' +
                    '<p>' + d[i].content + '</p>' +
                    '</div>';

                if (d[i].images) {
                    html += '<div class="ns-evaluate-img">';

                    var images = d[i].images.split(",");
                    for (var j = 0; j < images.length; j++) {
                        html += '<div class="ns-img-box" id="eva_img_' + i + '_' + j + '">';
                        html += '<img layer-src src="' + ns.img(images[j]) + '"' +
                            ' onerror=src="' + ns.img('public/static/img/null.png') + '"' +
                            ' id="' + d[i].evaluate_id + '1' + j + '">';
                        html += '</div>';
                    }

                    html += '</div>';
                }

                if (d[i].explain_first) {
                    html += '<div class="ns-evaluate-explain">' +
                        '<span class="again-evaluate required">[商家回复]</span>' +
                        '<p>' + d[i].explain_first + '</p>' +
                        '</div>';
                }


                if (d[i].again_content) {
                    html += '<hr />';
                    html += '<div class="ns-evaluate-again">' +
                        '<span class="again-evaluate required">[追加评论]</span>' +
                        '<p>' + d[i].again_content + '</p>' +
                        '</div>';

                    if (d[i].again_images) {
                        html += '<div class="ns-evaluate-img ns-again-eva-img">';

                        var again_images = d[i].again_images.split(",");
                        for (var k = 0; k < again_images.length; k++) {
                            html += '<div class="ns-img-box" id="again_img_' + i + '_' + k + '">';
                            html += '<img layer-src src="' + ns.img(again_images[k]) + '"' +
                                ' onerror=src="' + ns.img('public/static/img/null.png') + '"' +
                                ' id="' + d[i].evaluate_id + '2' + j + '">';
                            // html += 	'<img layer-src src="' + ns.img(again_images[k]) + '" onerror=src="'+ns.img('public/static/img/null.png')+'">';
                            html += '</div>';
                        }

                        html += '</div>';
                    }
                }

                if (d[i].again_explain) {
                    html += '<div class="ns-evaluate-explain">' +
                        '<span class="again-evaluate required">[商家回复]</span>' +
                        '<p>' + d[i].again_explain + '</p>' +
                        '</div>';
                }

                html += '</td>';
                html += '<td>' +
                    '<div class="ns-table-tuwen-box">' +
                    '<div class="ns-img-box" id="goods_img_' + i + '">' +
                    '<img layer-src src="' + ns.img(d[i].sku_image) + '">' +
                    '</div>' +
                    '<div class="ns-font-box">' +
                    '<p>商品名称：' + d[i].sku_name + '</p>' +
                    '<p>商品价格：' + d[i].sku_price + '</p>' +
                    '</div>' +
                    '</div>' +
                    '</td>';
                html += '<td>' +
                    '<div class="ns-table-btn">' +
                    '<a class="default layui-btn" lay-event="del" onclick="delEvaluate(this)">删除</a>' +
                    '</div>' +
                    '</td>';
                html += '</tr>';
                $(".ns-evaluate-table").find("tbody").append(html);
            }

            layui.use(['form', 'layer', 'laypage'], function () {
                var form = layui.form,
                    layer = layui.layer,
                    laypage = layui.laypage;
                form.render();

                layer.photos({
                    photos: '.ns-img-box',
                    anim: 5
                });

                /* laypage.render({
                    elem: 'laypage',
                    count: d.length
                }) */
            });
        }
    });
};

Evaluate.prototype.pageInit = function (d) {
    var _this = d._this;
    layui.use(['laypage', 'form'], function () {
        var laypage = layui.laypage,
            form = layui.form;
        if ($(".ns-evaluate-table tbody tr td").eq(0).text() != "无数据") {
            laypage.render({
                elem: 'laypage',
                count: _this.listCount,
                limit: _this.limit,
                limits: _this.limits,
                prev: '<i class="layui-icon layui-icon-left"></i>',
                next: '<i class="layui-icon layui-icon-right"></i>',
                layout: ['count', 'limit', 'prev', 'page', 'next'],
                jump: function (obj, first) {
                    _this.limit = obj.limit;

                    if (!first) {
                        _this.page = obj.curr;
                        _this.getList({
                            _this: _this,
                            "explain_type": d.explain_type,
                            "search_text": d.search_text,
                            "search_type": d.search_type
                        });

                        $("input[lay-filter='selectAllTop']").prop("checked", false);
                        $("input[lay-filter='selectAllBot']").prop("checked", false);
                        form.render();
                        selectAll();
                    }
                }
            });
        }
    });
};

var form, laypage;
var evaluate = new Evaluate(2, [2, 4, 6]);
evaluate.getList({
    "_this": evaluate,
});
evaluate.pageInit({
    "_this": evaluate
});
layui.use(['form', 'laypage', 'layer'], function () {
    form = layui.form;
    laypage = layui.laypage;


    /**
     * 删除
     */
    $(".ns-del-eval .layui-btn").click(function () {
        var id;
        $(".ns-evaluate-check .layui-form-checkbox").each(function (i) {
            if ($(this).hasClass("layui-form-checked")) {
                id = $(this).siblings(".evaluate_id").val();
                console.log(id);
            }
        });

        $.ajax({
            url: ns.url("supply://admin/goods/deleteEvaluate"),
            data: {
                "id": id
            },
            type: "POST",
            dataType: "JSON",
            success: function (res) {
                evaluate.getList({
                    "_this": evaluate
                });
                location.reload();
            },
        });
    });

    selectAll();

    form.on('checkbox(selectAllTop)', function (data) {
        if (data.elem.checked) {
            $("tr .ns-evaluate-check input:checkbox").each(function () {
                $(this).prop("checked", true);
                $(".ns-del-eval input:checkbox").prop("checked", true);
            });
        } else {
            $("tr .ns-evaluate-check input:checkbox").each(function () {
                $(this).prop("checked", false);
                $(".ns-del-eval input:checkbox").prop("checked", false);
            });
        }
        form.render();
    });

    form.on('checkbox(selectAllBot)', function (data) {
        if (data.elem.checked) {
            $("tr .ns-evaluate-check input:checkbox").each(function () {
                $(this).prop("checked", true);
                $("th.ns-check-box input:checkbox").prop("checked", true);
            });
        } else {
            $("tr .ns-evaluate-check input:checkbox").each(function () {
                $(this).prop("checked", false);
                $("th.ns-check-box input:checkbox").prop("checked", false);
            });
        }
        form.render();
    });

    /**
     * 搜索
     */
    form.on('submit(search)', function (data) {
        $(".ns-evaluate-table tbody").empty();
        var evaluate = new Evaluate(2, [2, 4, 6]);

        evaluate.getList({
            "_this": evaluate,
            "search_type": data.field.search_type,
            "search_text": data.field.search_text,
            "explain_type": data.field.explain_type,
        });
        evaluate.pageInit({
            "_this": evaluate,
            "search_type": data.field.search_type,
            "search_text": data.field.search_text,
            "explain_type": data.field.explain_type,
        });
        return false;
    });

});

// 点击全选
function selectAll() {
    /**
     * 监听每一行的复选框
     */
    var len = $("tbody .ns-evaluate-check").length;

    for (var i = 0; i < len; i++) {
        var num = $(".evaluate_id").eq(i).val();

        form.on('checkbox(select' + num + ')', function (data) {
            if ($("tbody .ns-evaluate-check input:checked").length == len) {
                $("input[lay-filter='selectAllTop']").prop("checked", true);
                $("input[lay-filter='selectAllBot']").prop("checked", true);
            } else {
                $("input[lay-filter='selectAllTop']").prop("checked", false);
                $("input[lay-filter='selectAllBot']").prop("checked", false);
            }

            form.render();
        });
    }
}

function delEvaluate(e) {
    /**
     * 监听事件
     */
    var id = $(e).parents("tr").prev().find(".evaluate_id").val();
    layer.confirm('确定要删除吗?', function () {
        $.ajax({
            url: ns.url("supply://admin/goods/deleteEvaluate"),
            data: {
                "id": id
            },
            type: "POST",
            dataType: "JSON",
            success: function (res) {
                layer.msg(res.message);

                if (res.code == 0) {
                    evaluate.getList({
                        "_this": evaluate
                    });
                    location.reload();
                }
            }
        });
    }, function () {
        layer.close();
    });
}