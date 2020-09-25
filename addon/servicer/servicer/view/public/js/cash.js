Cash = function(limit = 0, limits = []) {
	var _this = this;
	_this.listCount = 0;
	_this.page = 1;
	_this.page_size = !limit ? 10 : limit;
	_this.limits = limits;
}

Cash.prototype.getGoodsList = function(data) {
	var _this = data._this,
		search_text = data.search_text == null ? '' : data.search_text,
		category_id = data.category_id == null ? '' : data.category_id;

	// 获取商品列表
	$.ajax({
		url: ns.url("store://store/cash/getGoodsSkuList"),
		async: false,
		data: {
			"page": _this.page,
			"page_size": _this.page_size,
			"search_text": search_text,
			"category_id": category_id
		},
		type: "POST",
		dataType: "JSON",
		success: function(res) {
			_this.listCount = res.data.count;
			var list = res.data.list;

			$(".ns-goods-list").empty();
			for (var i = 0; i < list.length; i++) {
				var html = '';

				if (list[i].store_stock == 0) {
					html += `<div class="ns-good-box disabled" data-sku-id="`+list[i].sku_id+`" data-sku-name="`+list[i].sku_name+`" data-store-stock="`+list[i].store_stock+`" data-sku-price="`+list[i].discount_price+`">`;
				} else {
					html += `<div class="ns-good-box" data-sku-id="`+list[i].sku_id+`" data-sku-name="`+list[i].sku_name+`" data-store-stock="`+list[i].store_stock+`" data-sku-price="`+list[i].discount_price+`" onclick="addGoods(this)">`;
				}
				html += `<div class="ns-table-title">`;
				html += `<div class="ns-title-pic">`;
				html += `<img src="` + ns.img(list[i].sku_image) + `" />`;
				html += `</div>`;
				html += `<div class="ns-title-content">`;
				html += `<p class="ns-multi-line-hiding good-name" title="`+list[i].sku_name+`">`+list[i].sku_name+`</p>`;
				html += `<p class="ns-line-hideing good-no" title="`+list[i].sku_no+`">商品编码：`+list[i].sku_no+`</p>`;
				html += `<p>￥<span class="good-price">` + list[i].discount_price + `</span>&nbsp;&nbsp;&nbsp;库存：<span class="store-stock">` + list[i].store_stock + `</span></p>`;
				html += `</div>`;
				html += `</div>`;

				if (list[i].store_stock == 0) {
					html += `<div class="ns-empty-stock">`;
					html += `<img src="${STOREIMG}/empty_stock.png" />`;
					html += `</div>`;
				}
				html += `</div>`;

				$(".ns-goods-list").append(html);
			}
		}
	})
}

/* Cash.prototype.getPageInit = function(data) {
	var _this = data._this;
	layui.use(['laypage'], function() {
		var laypage = layui.laypage;
		laypage.render({
			elem: 'goods_page',
			count: _this.listCount,
			limit: _this.page_size,
			prev: '<i class="layui-icon layui-icon-left"></i>',
			next: '<i class="layui-icon layui-icon-right"></i>',
			layout: ['count', 'prev', 'page', 'next'],
			jump: function(obj, first) {
				_this.limit = obj.limit;

				if (!first) {
					_this.page = obj.curr;
					_this.getGoodsList({
						_this: _this,
						"search_text": data.search_text,
						"category_id": data.category_id
					});
					
					console.log(1);
				}
			}
		});
	});
} */
