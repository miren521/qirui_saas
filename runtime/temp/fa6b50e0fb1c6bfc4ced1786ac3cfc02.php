<?php /*a:3:{s:71:"/www/wwwroot/saas.goodsceo.com/addon/supply/shop/view/market/index.html";i:1600312146;s:63:"/www/wwwroot/saas.goodsceo.com/addon/supply/shop/view/base.html";i:1600312146;s:39:"addon/supply/shop/view/market_head.html";i:1600314240;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1" />
	<title><?php echo htmlentities((isset($menu_info['title']) && ($menu_info['title'] !== '')?$menu_info['title']:"")); ?></title>
	<meta name="keywords" content="$shop_info['seo_keywords']}">
	<meta name="description" content="$shop_info['seo_description']}">
	<link rel="icon" type="image/x-icon" href="http://saas.goodsceo.com/public/static/img/shop_bitbug_favicon.ico" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/public/static/css/iconfont.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/public/static/ext/layui/css/layui.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/app/shop/view/public/css/common.css" />
	<script src="http://saas.goodsceo.com/public/static/js/jquery-3.1.1.js"></script>
	<script src="http://saas.goodsceo.com/public/static/ext/layui/layui.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
	<script>
		layui.use(['layer', 'upload', 'element'], function() {});
		
		window.ns_url = {
			baseUrl: "http://saas.goodsceo.com/index.php/",
			route: ['<?php echo request()->module(); ?>', '<?php echo request()->controller(); ?>', '<?php echo request()->action(); ?>'],
		};


	</script>
	<script src="http://saas.goodsceo.com/public/static/js/common.js"></script>
	<script src="http://saas.goodsceo.com/app/shop/view/public/js/common.js"></script>

	<script>
		/**
		 * 通过ajax请求 验证是否登录
		 */
		function checkLogin(data) {
			if (data != undefined) {
				if (data.error_code == 'NOT_LOGIN') {
					layer.msg(data.message);
					setTimeout(function(){
						location.href = ns.url('shop/login/login');
					},1000);
				}else{
					return true;
				}
			}else{
				return true;
			}


		}
	</script>
	<style>
		.ns-calendar{background: url("http://saas.goodsceo.com/public/static/img/ns_calendar.png") no-repeat center / 16px 16px;}
		.layui-logo{height: 100%;display: flex;align-items: center;}
		.layui-logo a{display: flex;justify-content: center;align-items: center;width: 200px;height: 50px;}
		.layui-logo a img{max-height: 100%;max-width: 100%;}
	</style>
	
<link rel="stylesheet" href="http://saas.goodsceo.com/addon/supply/shop/view/public/css/index.css">

</head>

<body>
<div class="layui-layout layui-layout-admin">

<!-- 顶部html文件-->
<div class="ns-supply-wrap">
	<div class="ns-supply-wrap-con">
		<link rel="stylesheet" href="http://saas.goodsceo.com/addon/supply/shop/view/public/css/header.css">

<div class="ns-supply-header-wrap">
	<div class="ns-supply-header">
		<div class="ns-supply-header-index"><a href="<?php echo addon_url('supply://shop/market/index'); ?>">市场首页</a></div>
		<div class="ns-supply-header-opretion">
			<a href="<?php echo addon_url('supply://shop/order/lists'); ?>"><i class="iconfont iconjinhuodan"></i><span>我的订单</span></a>
			<span></span>
			<a href="#" onclick="toCartList()"><i class="iconfont iconjinhuodan"></i><span>我的供货单</span></a>
			<span></span>
			<a href="<?php echo addon_url('supply://supply/index/index'); ?>" target="_blank"><i class="iconfont iconwo"></i><span>我是供货商</span></a>
			<span></span>
			<a href="<?php echo addon_url('shop/index/index'); ?>" target="_blank"><span>返回店铺</span></a>
		</div>
	</div>

	<!-- 顶部搜索栏 -->
	<div class="ns-supply-header-search">
		<a href="<?php echo addon_url('supply://shop/market/index'); ?>">
			<div class="ns-supply-logo">
				<img src="<?php echo img($website_info['logo']); ?>" onerror="this.src='http://saas.goodsceo.com/addon/supply/shop/view/public/img/logo.png'"/>
			</div>
		</a>
	
		<div class="ns-supply-search">
			<div class="ns-supply-search-box layui-form">
				<!-- <select name="" lay-verify="">
					<option value="">商品</option>
					<option value="">供货商</option>
				</select> -->
				<input type="text" name="keyword" placeholder="请输入关键字" autocomplete="off" class="layui-input ns-border-color">
				<button type="button" class="layui-btn ns-bg-color" lay-submit lay-filter="search">搜索</button>
			</div>
			<button class="layui-btn ns-bg-color" onclick="toPurchase()">发布求购单</button>
		</div>

		<div class="ns-supply-header-cart" onclick="toCartList()">
			<span>我的供货单</span>
			<i class="iconfont icongouwuche"></i>
			<span class="cart-num layui-hide"></span>
			<input type="hidden" value="" id="cartCount" />
		</div>
	</div>
</div>

<script src="http://saas.goodsceo.com/addon/supply/shop/view/public/js/common.js"></script>
<script>
	$(document).ready(function() {
		cartCount();
	})

	layui.use('form', function () {
		var form = layui.form;
		// 商品搜索
		form.on('submit(search)', function(data) {
			location.href = ns.url("supply://shop/goods/goodslist", {"keyword": data.field.keyword})
		})
	})
	
	function toCartList() {
		location.href = ns.url("supply://shop/cart/cart", {"count": $("#cartCount").val()});
	}

	function toPurchase() {
		location.href = ns.url("supply://shop/purchase/release");
	}
</script>

		<div class="ns-supply-index">
			<div class="ns-supply-index-center">
				<div class="ns-supply-index-category">
					<ul>
						<li onclick="toGoodsList()">
							<span class="ns-text-color">所有类目</span>
							<div class="ns-cate-bg"></div>
						</li>
						<?php foreach($category_list as $category_k => $category_v): ?>
						<li onclick="toGoodsList(<?php echo htmlentities($category_v['category_id']); ?>, <?php echo htmlentities($category_v['level']); ?>)">
							<div class="ns-cate-title">
								<div><span><?php echo htmlentities($category_v['category_name']); ?></span></div>
								<div><i class="iconfont iconyoujiantou"></i></div>
							</div>
							<div class="na-cate-list">
							<?php if(!empty($category_v['child_list'])): foreach($category_v['child_list'] as $second_category_k => $second_category_v): ?>
								<div class="ns-cate-li">
									<h4 onclick="toGoodsList(<?php echo htmlentities($second_category_v['category_id']); ?>, <?php echo htmlentities($second_category_v['level']); ?>)"><?php echo htmlentities($second_category_v['category_name']); ?></h4>
									<?php if(!empty($second_category_v['child_list'])): ?>
									<i class="iconfont iconyoujiantou"></i>			
									<div class="ns-cate-con">
										<?php foreach($second_category_v['child_list'] as $third_category_k => $third_category_v): ?>
											<span onclick="toGoodsList(<?php echo htmlentities($third_category_v['category_id']); ?>, <?php echo htmlentities($third_category_v['level']); ?>)"><?php echo htmlentities($third_category_v['category_name']); ?></span>
										<?php endforeach; ?>
									</div>
									<?php endif; ?>
								</div>
								<?php endforeach; ?>
							<?php endif; ?>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
		
				<div class="ns-supply-index-banner">
					<div class="layui-carousel" id="test1">
						<div carousel-item>
							<?php foreach($adv['adv_list'] as $adv_k => $adv_v): 
								$url = json_decode($adv_v['adv_url'], true);
							 if($url['url']): ?>
							<a href="<?php echo htmlentities($url['url']); ?>" target="_blank">
							<?php else: ?>
							<a href="#">
							<?php endif; ?>
								<div class="layui-carousel-img"><img src="<?php echo img($adv_v['adv_image']); ?>" /></div>
							</a>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			
			<div class="goods-list-title ns-red-color">优选新品</div>
		
			<div class="ns-goods-list">
		
			</div>
		</div>
	</div>
</div>

<script type="text/html" id="goodsList">
	{{#  layui.each(d.list, function(index, item){ }}
		<div class="ns-goods-li">
			<a href="{{ns.url('supply://shop/goods/detail?sku_id='+ item.sku_id)}}">
				<div class="ns-goods-img">
					<img src="{{ ns.img(item.sku_image.split(',')[0]) }}" onerror="this.src = 'http://saas.goodsceo.com/addon/supply/shop/view/public/img/default_goods.png' " />
				</div>
			</a>
			<a href="{{ns.url('supply://shop/goods/detail?sku_id='+ item.sku_id)}}">
				<p class="ns-goods-name ns-multi-line-hiding" title="{{ item.sku_name }}">{{ item.sku_name }}</p>
			</a>
			<div class="ns-goods-info">
				<p>
					<span>供货价：</span>
					{{#  if(item.max_price == item.min_price) {  }}
					<span class="ns-goods-profit ns-red-color">￥{{ item.max_price }}</span>
					{{#  } else if(item.max_price != item.min_price) {  }}
					<!-- <span class="ns-goods-profit ns-red-color">￥{{ item.min_price }}~￥{{ item.max_price }}</span> -->
					<span class="ns-goods-profit ns-red-color">￥{{ item.min_price }}</span>
					{{#  }  }}
				</p>
				<p><span>库存：</span><span>{{ item.stock }}</span></p>
			</div>
		</div>
	{{#  }); }}
</script>



	<!-- 版权信息 -->
	<div class="ns-footer">
		
		<a class="ns-footer-img" href="#"><img src="<?php if(!empty($copyright['logo'])): ?> <?php echo img($copyright['logo']); else: ?>http://saas.goodsceo.com/public/static/img/copyright_logo.png<?php endif; ?>" /></a>
		<p><?php if(!(empty($copyright['company_name']) || (($copyright['company_name'] instanceof \think\Collection || $copyright['company_name'] instanceof \think\Paginator ) && $copyright['company_name']->isEmpty()))): ?><?php echo htmlentities($copyright['company_name']); else: ?>上海牛之云网络科技有限公司<?php endif; if(!(empty($copyright['icp']) || (($copyright['icp'] instanceof \think\Collection || $copyright['icp'] instanceof \think\Paginator ) && $copyright['icp']->isEmpty()))): ?><a href=<?php echo htmlentities($copyright['copyright_link']); ?>>&nbsp;&nbsp;备案号<?php echo htmlentities($copyright['icp']); ?></a><?php endif; ?></p>
		<?php if(!(empty($copyright['gov_record']) || (($copyright['gov_record'] instanceof \think\Collection || $copyright['gov_record'] instanceof \think\Paginator ) && $copyright['gov_record']->isEmpty()))): ?><a class="gov-box" href=<?php echo htmlentities($copyright['gov_url']); ?>><img src="http://saas.goodsceo.com/app/shop/view/public/img/gov_record.png" alt="">公安备案<?php echo htmlentities($copyright['gov_record']); ?></a><?php endif; ?>
		
	</div>
</div>

<script type="text/javascript">
	layui.use(['carousel', 'form', 'laytpl'], function() {
		var carousel = layui.carousel;
		var form = layui.form;
		var laytpl = layui.laytpl;
		//建造实例
		carousel.render({
			elem: '#test1',
			width: '100%', //设置容器宽度
			height: '100%',
			arrow: 'always' //始终显示箭头
		});
		
		form.on('submit(search)', function(data) {
			location.href = ns.url("supply://shop/goods/goodslist", {"keyword": data.field.keyword})
		})

		$(".ns-goods-list").empty();
		$.ajax({
			url: ns.url("supply://shop/goods/goodslist"),
			data: {
				"page": 1,
				"page_size": 20
			},
			async: false,
			dataType: 'JSON',
			type: 'POST',
			success: function(res) {
				if (res.data.list.length != 0) {
					laytpl($("#goodsList").html()).render(res.data, function(html) {
						$(".ns-goods-list").append(html)
					});
				}
			}
		});
	});
	
	function toGoodsList(id, level) {
		layui.stope(event);
		location.href = ns.url("supply://shop/goods/goodslist", {"category_id": id, "category_level": level});
	}
</script>

</body>

</html>