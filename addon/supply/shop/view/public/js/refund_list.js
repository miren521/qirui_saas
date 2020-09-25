/**
 * 渲染订单列表
 */
Order = function () {
};

/**
 * 设置数据集
 */
Order.prototype.setData = function (data) {
	Order.prototype.data = data;
};

/**
 * 列名数据
 */
Order.prototype.cols = [
	{
		title: '<span>商品信息</span>',
		width: "40%",
		className: "product-info",
		template: function (order) {
            
            var h = '<div class="ns-table-title">';
                h +=    '<div class="ns-title-pic">';
                h +=    	'<img layer-src src="' + ns.img(order.sku_image) + '">';
                h +=    '</div>';
                h +=    '<div class="ns-title-content">';
				h += 		'<a href="'+ ns.url("supply://shop/goods/detail", {sku_id: order.sku_id}) +'" target="_blank" title="'+ order.sku_name +'" class="ns-multi-line-hiding ns-text-color">' + order.sku_name + '</a>';
				h += 		'<p class="goods-class-name">' + order.goods_class_name + '</p>';
                h +=    '</div>';
				h += '</div>';
				
			return h;
		}
	},
	{
		title: "退款金额",
		width: "15%",
		align: "right",
		className: "order-price",
		template: function (order) {
			var h = '<span>￥' + order.refund_apply_money + '</span>';
			return h;
		}
	},
	{
		title: "退款类型",
		width: "15%",
		align: "center",
		className: "order-price",
		template: function (order) {
			return order.refund_type == 1 ? '退款' : '退货';
		}
	},
	{
		title: "退款状态",
		width: "15%",
		align: "center",
		className: "transaction-status",
		merge: true,
		template: function (order) {
			var h = '<div class="ns-text-color">' + order.refund_status_name + '</div>';
				h += '<a href="'+ ns.url("supply://shop/orderrefund/detail", {order_goods_id: order.order_goods_id}) +'">退款详情</a>';
			return h;
		}
	},
	{
		title: "操作",
		width: "15%",
		align: "left",
		className: "operation",
		merge: true,
		template: function (order) {
			var url = "shop/order/detail";
			var h = '';
			h += '<div class="ns-table-btn" style="display: block;">';
			for (var k = 0; k < order.refund_action.length; k++) {
				h += '<p><a class="layui-btn" onclick="orderAction(\''+ order.refund_action[k].event +'\', '+ order.order_goods_id +')">' + order.refund_action[k].title + '</a></p>';
			}
			h += '</div>';
			return h;
			
		}
	}
];

/**
 * 渲染表头
 */
Order.prototype.header = function (hasThead) {
	var colgroup = '<colgroup>';
	var thead = '';
	if (hasThead) thead = '<thead><tr>';
	
	for (var i = 0; i < this.cols.length; i++) {
		var align = this.cols[i].align ? "text-align:" + this.cols[i].align : "";
		
		colgroup += '<col width="' + this.cols[i].width + '">';
		if (hasThead) {
			thead += '<th style="' + align + '" class="' + (this.cols[i].className || "") + '">';
			thead += '<div class="layui-table-cell">' + this.cols[i].title + '</div>';
			thead += '</th>';
		}
	}
	colgroup += '</colgroup>';
	if (hasThead) thead += '</tr></thead>';
	return colgroup + thead;
};

/**
 * 渲染内容
 */
Order.prototype.tbody = function () {
	
	var tbody = '<tbody>';
	for (var i = 0; i < this.data.list.length; i++) {
		
		var order = this.data.list[i];
		
		if (i > 0) {
			//分割行
			tbody += '<tr class="separation-row">';
			tbody += '<td colspan="' + this.cols.length + '"></td>';
			tbody += '</tr>';
		}
		
		//订单项头部
		tbody += '<tr class="header-row">';
		tbody += 	'<td colspan="6">';
		tbody += 		'<span class="order-item-header" style="margin-right:20px;">' + ns.time_to_date(order.refund_action_time) + '</span>';
		tbody +=		'<span class="order-item-header" style="margin-right:20px;">订单号：' + order.refund_no + '</span>';
		tbody += 		'<span class="order-item-header order-site" style="margin-right:20px;"><a href="">'+ order.buyer_shop_name +'</a></span>';

		if (order.refund_status == 3) {
			tbody += 	'<span class="order-item-header" style="margin-right:20px;">退款成功</span>';
		} else {
			tbody += 	'<span class="order-item-header" style="margin-right:20px;">退款中</span>';
		}

		tbody += 	'</td>';
		tbody += '</tr>';
		
		var orderitemHtml = '';
			orderitemHtml += '<tr class="content-row">';
			for (var k = 0; k < this.cols.length; k++) {
				
				if (this.cols[k].merge && this.cols[k].template) {
					
					orderitemHtml += '<td class="' + (this.cols[k].className || "") + '" align="' + (this.cols[k].align || "") + '" style="' + (this.cols[k].style || "") + '">';
					orderitemHtml += this.cols[k].template(order);
					orderitemHtml += '</td>';
					
				} else if (this.cols[k].template && !this.cols[k].merge) {
					
					orderitemHtml += '<td class="' + (this.cols[k].className || "") + '" align="' + (this.cols[k].align || "") + '" style="' + (this.cols[k].style || "") + '">';
					orderitemHtml += this.cols[k].template(order);
					orderitemHtml += '</td>';
					
				}
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
Order.prototype.fetch = function () {
	if (this.data.list.length > 0) {
		return '<table class="layui-table layui-form">' + this.header(true) + '</table><table class="layui-table order-list-table layui-form">' + this.header(false) + this.tbody() + '</table>';
	} else {
		return '<table class="layui-table order-list-table layui-form">' + this.header(true) + '</table>' + '<div class="order-no-data-block"><ul><li><i class="layui-icon layui-icon-tabs"></i> </li><li>暂无订单</li></ul></div>';
	}
};

function orderAction(orderAction, id) {
	switch (orderAction) {
		case 'orderRefundCancel': //撤销维权
			orderRefundCancel(id);
			break;
		case 'orderRefundDelivery': //退款发货
			location.href = ns.url("supply://shop/orderrefund/delivery", {"order_goods_id": id})
			break;
		case 'orderRefundAsk': 
			location.href = ns.url("supply://shop/orderrefund/refund", {"order_goods_id": id})
			break;
	}
}

// 订单关闭
function orderRefundCancel(id) {
	$.ajax({
		url: ns.url("supply://shop/orderrefund/cancel"),
		data: {"order_goods_id": id},
		dataType: 'JSON',
		type: 'POST',
		success: function (res) {
			if (res.code == 0) {
				location.reload();
			}
		}
	})
}

$(".layui-nav-child dd").click(function() {
	if (!$(this).hasClass("layui-this")) {
		$(this).addClass("layui-this");
		$(this).siblings().removeClass("layui-this");
	}
})