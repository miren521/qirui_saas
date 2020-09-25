/**
 * 渲染订单列表	156
 * 创建时间：2018年8月15日18:28:43
 */
Order = function(){};

/**
 * 设置数据集
 */
Order.prototype.setData = function(data){
    Order.prototype.data = data;
};

/**
 * 列名数据
 */
Order.prototype.cols = [
    {
        title : '<input type="checkbox" name="sub" lay-skin="primary"><span style="margin-left:10px;">商品信息</span>',
        width : "25%",
        className : "product-info",
        template : function(item){
            var h = '<div class="img-block" >';
            h += '<img layer-src src="'+ ns.img(item.sku_image) +'">';
            h += '</div>';
            h += '<div class="info">';
            h += '<a href="javascript:;" class="ns-multi-line-hiding" title="'+ item.sku_name +'">' + item.sku_name + '</a>';
            return h;
        }
    },
    {
        title : "投诉人",
        width : "10%",
        align : "center",
        className : "member-name",
        template : function(item){
            var h = '<div>';
            h += '<span>' + item.member_name + '</span>';
            h += '</div>';
            return h;
        }
    },
    {
        title : "订单金额",
        width : "10%",
        align : "right",
        className : "order-money",
        template : function(item){
            var h = '<div style="padding-right: 15px;">';
            h += '<span>￥' + item.real_goods_money + '</span>';
            h += '</div>';
            return h;
        }
    },
    {
        title : "申请退款金额",
        width : "10%",
        align : "right",
        className : "complain-apply-money",
        template : function(item){
            var h = '<div style="padding-right: 15px;">';
            h += '<span>￥' + item.complain_apply_money + '</span>';
            h += '</div>';
            return h;
        }
    },
    {
        title : "申请时间",
        width : "15%",
        align : "center",
        className : "apply-time",
        merge : true,
        template : function(item){
            return '<div class="ns-line-hiding" title="'+ ns.time_to_date(item.complain_apply_time) +'">' + ns.time_to_date(item.complain_apply_time) + '</div>';
        }
    },
    {
        title : "维权状态",
        width : "15%",
        align : "center",
        className : "complain-status-name",
        merge : true,
        template : function(item){
            return '<div>' + item.complain_status_name + '</div>';
        }
    },
    {
        title : "操作",
        width : "15%",
        align : "left",
        className : "operation",
        merge : true,
        template : function(item){
            var html = '<div class="ns-table-btn"><a href="'+ ns.url("admin/complain/detail",{order_goods_id:item.order_goods_id})+'" class="layui-btn"  target="_blank">查看详情</a></div>';
            return html;

        }
    }
];

/**
 * 渲染表头
 */
Order.prototype.header = function(){
    var colgroup = '<colgroup>';
    var thead = '<thead><tr>';

    for(var i=0;i<this.cols.length;i++){
        var align = this.cols[i].align ? "text-align:" + this.cols[i].align : "";

        colgroup += '<col width="' + this.cols[i].width + '">';
        thead += '<th style="' + align + '" class="' + (this.cols[i].className || "") + '">';
        thead += '<div class="layui-table-cell">' + this.cols[i].title + '</div>';
        thead += '</th>';
    }
    colgroup += '</colgroup>';
    thead += '</tr></thead>';
    return colgroup + thead;
};

/**
 * 渲染内容
 */
Order.prototype.tbody = function(){

    var tbody = '<tbody>';
    for(var i=0;i<this.data.list.length;i++){

        var item = this.data.list[i];

        //分割行
        //订单项头部

        tbody += '<tr class="separation-row"><td colspan="7"></td></tr>';
        tbody += '<tr class="header-row">';
        tbody += '<td colspan="7">';
        tbody += '<span class="order-item-header" style="margin-right:50px;">维权编号：' + item.complain_no + '</span>';
        tbody += '<span class="order-item-header" style="margin-right:50px;">订单编号：' + item.order_no + '</span>';
        tbody += '<span class="order-item-header"></span>';
        tbody += '</td>';
        tbody += '</tr>';

        var orderitemHtml = '';
            orderitemHtml += '<tr class="content-row">';
            for(var k=0;k<this.cols.length;k++){

                    orderitemHtml += '<td class="' + (this.cols[k].className || "") + '" align="' + (this.cols[k].align || "") + '" style="' + (this.cols[k].style || "") + '">';
                    orderitemHtml += this.cols[k].template(item);
                    orderitemHtml += '</td>';
            }
            orderitemHtml += '</tr>';
        tbody += orderitemHtml;
    }

    tbody += '</tbody>';
    return tbody;
};

/**
 * 渲染表格
 */
Order.prototype.fetch = function(){
    if(this.data.list.length > 0){
        return '<table class="layui-table order-list-table layui-form">' + this.header() + this.tbody() + '</table>';
    }else{
        return '<table class="layui-table order-list-table layui-form">' + this.header() + '</table>'+'<div class="order-no-data-block"><ul><li><i class="layui-icon layui-icon-tabs"></i> </li><li>暂无数据</li></ul></div>';
    }
};