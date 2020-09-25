function cartCount() {
	$.ajax({
		url: ns.url("supply://shop/cart/count"),
		dataType: 'JSON',
		type: 'POST',
		success: function(res) {
			if (res.code >= 0) {
				var count = res.data;
				if (count > 0) {
					$(".ns-supply-header-cart .cart-num").removeClass("layui-hide");
					$(".ns-supply-header-cart .cart-num").text(count);
					$("#cartCount").val(count);
				} else {
					$(".ns-supply-header-cart .cart-num").addClass("layui-hide");
				}
			}
		}
	})
}