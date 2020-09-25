<?php /*a:2:{s:59:"/www/wwwroot/saas.goodsceo.com/app/shop/view/stat/shop.html";i:1600312148;s:54:"/www/wwwroot/saas.goodsceo.com/app/shop/view/base.html";i:1600314240;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1" />
	<title><?php echo htmlentities((isset($menu_info['title']) && ($menu_info['title'] !== '')?$menu_info['title']:"")); ?> - <?php echo htmlentities((isset($shop_info['site_name']) && ($shop_info['site_name'] !== '')?$shop_info['site_name']:"")); ?></title>
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
	<style>
		.ns-calendar{background: url("http://saas.goodsceo.com/public/static/img/ns_calendar.png") no-repeat center / 16px 16px;}
		.layui-logo{height: 100%;display: flex;align-items: center;}
		.layui-logo a{display: flex;justify-content: center;align-items: center;width: 200px;height: 50px;}
		.layui-logo a img{max-height: 100%;max-width: 100%;}
	</style>
	
<style>
	/* 商品整体概况 */
	/* 头部 */
	.ns-card-common:first-child {
		margin-top: 0;
	}

	.ns-card-head-right {
		display: flex;
		align-items: center;
	}

	.ns-card-head-right .layui-form-item {
		margin-bottom: 0;
	}

	.current-time, .ns-flow-time-today {
		margin-left: 10px;
		line-height: 34px;
	}
	/* 头部 end */
	
	.ns-shop-table tr {
		border-bottom: 1px solid #E6E6E6;
	}
	
	.layui-table[lay-skin=line] td {
	    border-width: 0!important;
	}

	.ns-goods-survey-title {
		font-size: 17px;
		font-weight: 600;
		color: #000000;
	}

	.layui-table[lay-skin=line] {
		border-width: 0;
	}

	.ns-goods-intro-num {
		font-size: 18px;
		font-weight: 600;
		margin-top: 10px;
	}
	
	/* 基本信息 */
	.ns-basic {
		display: flex;
	}
	
	.ns-basic .ns-table-title {
		width: 25%;
		border-right: 1px solid #EEEEEE;
		padding: 10px 20px;
	}
	
	.ns-basic-count {
		font-size: 20px;
		font-weight: 600;
	}
	
	.ns-text-color-dark-gray {
		color: #898989;
	}
	
	.ns-title-pic {
		width: 40px;
		height: 40px;
		line-height: 40px;
	}
	
	.ns-title-content p {
		line-height: 24px;
	}
</style>

</head>

<body>

	<div class="layui-layout layui-layout-admin">
		
		<div class="layui-header">
			<div class="layui-logo">
				<a href="">
					<?php if(!(empty($website_info['logo']) || (($website_info['logo'] instanceof \think\Collection || $website_info['logo'] instanceof \think\Paginator ) && $website_info['logo']->isEmpty()))): ?>
					<img src="<?php echo img($website_info['logo']); ?>">
					<!-- <h1>开源商城</h1> -->
					<?php else: ?>
					<img src="http://saas.goodsceo.com/app/shop/view/public/img/shop_logo.png">
					<?php endif; ?>
				</a>
			</div>
			<ul class="layui-nav layui-layout-left">
				<?php foreach($menu as $menu_k => $menu_v): ?>
				<li class="layui-nav-item">
					<a href="<?php echo htmlentities($menu_v['url']); ?>" <?php if($menu_v['selected']): ?>class="active"<?php endif; ?>>
						<span><?php echo htmlentities($menu_v['title']); ?></span>
					</a>
				</li>
				<?php if($menu_v['selected']): 
					$second_menu = $menu_v["child_list"];
					 ?>
				<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			
			<!-- 账号 -->
			<div class="ns-login-box layui-layout-right">
				<div class="ns-shop-ewm"> 
					<a href="#" onclick="getShopUrl()">访问店铺</a>
				</div>
				
				<ul class="layui-nav ns-head-account">
					<li class="layui-nav-item layuimini-setting">
						<a href="javascript:;">
							<?php echo htmlentities($user_info['username']); ?></a>
						<dl class="layui-nav-child">
							<dd class="ns-reset-pass" onclick="resetPassword();">
								<a href="javascript:;">修改密码</a>
							</dd>
							<dd>
								<a href="<?php echo addon_url('shop/login/logout'); ?>" class="login-out">退出登录</a>
							</dd>
						</dl>
					</li>
				</ul>
			</div>
		</div>
		
		
		
		<?php if(!(empty($second_menu) || (($second_menu instanceof \think\Collection || $second_menu instanceof \think\Paginator ) && $second_menu->isEmpty()))): ?>
		<div class="layui-side ns-second-nav">
			<div class="layui-side-scroll">
				
				<!--二级菜单 -->
				<ul class="layui-nav layui-nav-tree">
					<?php foreach($second_menu as $menu_second_k => $menu_second_v): ?>
					<li class="layui-nav-item layui-nav-itemed <?php if($menu_second_v['selected']): ?>layui-this<?php endif; ?>">
						<a href="<?php if(empty($menu_second_v['child_list']) || (($menu_second_v['child_list'] instanceof \think\Collection || $menu_second_v['child_list'] instanceof \think\Paginator ) && $menu_second_v['child_list']->isEmpty())): ?><?php echo htmlentities($menu_second_v['url']); else: ?>javascript:;<?php endif; ?>" class="layui-menu-tips">
							<div class="stair-menu<?php if($menu_v['selected']): ?> ative<?php endif; ?>">
								<img src="http://saas.goodsceo.com/<?php echo htmlentities($menu_second_v['icon']); ?>" alt="">
							</div>
							<span><?php echo htmlentities($menu_second_v['title']); ?></span>
						</a>
						
						<?php if(!(empty($menu_second_v['child_list']) || (($menu_second_v['child_list'] instanceof \think\Collection || $menu_second_v['child_list'] instanceof \think\Paginator ) && $menu_second_v['child_list']->isEmpty()))): ?>
						<dl class="layui-nav-child">
							<?php foreach($menu_second_v["child_list"] as $menu_third_k => $menu_third_v): ?>
							<dd class="<?php if($menu_third_v['selected']): ?> layui-this<?php endif; ?>">
								<a href="<?php echo htmlentities($menu_third_v['url']); ?>" class="layui-menu-tips">
									<i class="fa fa-tachometer"></i><span class="layui-left-nav"><?php echo htmlentities($menu_third_v['title']); ?></span>
								</a>
							</dd>
							<?php endforeach; ?>
						</dl>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php endif; ?>
		
		
		<!-- 面包屑 -->
		
		<?php if(!(empty($second_menu) || (($second_menu instanceof \think\Collection || $second_menu instanceof \think\Paginator ) && $second_menu->isEmpty()))): ?>
		<div class="ns-crumbs<?php if(!(empty($second_menu) || (($second_menu instanceof \think\Collection || $second_menu instanceof \think\Paginator ) && $second_menu->isEmpty()))): ?> submenu-existence<?php endif; ?>">
			<span class="layui-breadcrumb" lay-separator="-">
				<?php foreach($crumbs as $crumbs_k => $crumbs_v): if(count($crumbs) >= 3): if($crumbs_k == 1): ?>
					<a href="<?php echo htmlentities($crumbs_v['url']); ?>"><?php echo htmlentities($crumbs_v['title']); ?></a>
					<?php endif; if($crumbs_k == 2): ?>
					<a><cite><?php echo htmlentities($crumbs_v['title']); ?></cite></a>
					<?php endif; else: if($crumbs_k == 1): ?>
					<a><cite><?php echo htmlentities($crumbs_v['title']); ?></cite></a>
					<?php endif; ?>
				<?php endif; ?>
				<?php endforeach; ?>
			</span>
		</div>
		<?php endif; if(empty($second_menu) || (($second_menu instanceof \think\Collection || $second_menu instanceof \think\Paginator ) && $second_menu->isEmpty())): ?>
		<div class="ns-body layui-body" style="left: 0; top: 60px;">
		<?php else: ?>
		<div class="ns-body layui-body">
		<?php endif; ?>
			<!-- 内容 -->
			<div class="ns-body-content">
				<!-- 四级导航 -->
				<?php if(isset($forth_menu) && !empty($forth_menu)): ?>
				<div class="fourstage-nav layui-tab layui-tab-brief" lay-filter="edit_user_tab">
					<ul class="layui-tab-title">
						<?php if(is_array($forth_menu) || $forth_menu instanceof \think\Collection || $forth_menu instanceof \think\Paginator): $i = 0; $__LIST__ = $forth_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?>
						<li class="<?php echo $menu['selected']==1 ? 'layui-this'  :  ''; ?>" lay-id="basic_info"><a href="<?php echo htmlentities($menu['parse_url']); ?>"><?php echo htmlentities($menu['title']); ?></a></li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<?php endif; ?>
			
				
<!-- 实时概况 -->
<div class="layui-card ns-card-common ns-card-brief">
	<div class="layui-card-header">
		<span class="ns-card-title">实时概况</span>

		<div class="ns-card-head-right layui-form">
			<div class="layui-form-item">
				<div class="layui-input-block ns-len-mid">
					<select name="date" lay-filter="date">
						<option value="0">今日实时</option>
						<option value="1">近7天</option>
						<option value="2">近30天</option>
					</select>
				</div>
			</div>
			<div class="ns-flow-time">
				<div class="ns-flow-time-today"></div>
			</div>
		</div>
	</div>
	
	<div class="layui-card-body ns-basic">
		<div class="ns-table-title">
			<div class="ns-title-pic">
				<img src="http://saas.goodsceo.com/app/shop/view/public/img/stat/add_goods.png">
			</div>
			<div class="ns-title-content">
				<p class="ns-text-color-dark-gray">新增商品</p>
				<p class="ns-basic-count" id="addGoodsCount">0</p>
			</div>
		</div>
		<div class="ns-table-title">
			<div class="ns-title-pic">
				<img src="http://saas.goodsceo.com/app/shop/view/public/img/stat/goods_browse.png">
			</div>
			<div class="ns-title-content">
				<p class="ns-text-color-dark-gray">商品浏览</p>
				<p class="ns-basic-count" id="visitCount">0</p>
			</div>
		</div>
		<div class="ns-table-title">
			<div class="ns-title-pic">
				<img src="http://saas.goodsceo.com/app/shop/view/public/img/stat/goods_collect.png">
			</div>
			<div class="ns-title-content">
				<p class="ns-text-color-dark-gray">商品收藏</p>
				<p class="ns-basic-count" id="collectGoods">0</p>
			</div>
		</div>
	    <div class="ns-table-title">
	        <div class="ns-title-pic">
	            <img src="http://saas.goodsceo.com/app/shop/view/public/img/stat/goods_order.png">
	        </div>
	        <div class="ns-title-content">
	            <p class="ns-text-color-dark-gray">订单商品</p>
	            <p class="ns-basic-count" id="goodsPayCount">0</p>
	        </div>
	    </div>
	</div>
	<div class="layui-card-body ns-basic">
	    <div class="ns-table-title">
	        <div class="ns-title-pic">
	            <img src="http://saas.goodsceo.com/app/shop/view/public/img/stat/shop_collect.png">
	        </div>
	        <div class="ns-title-content">
	            <p class="ns-text-color-dark-gray">店铺收藏</p>
	            <p class="ns-basic-count" id="collectShop">0</p>
	        </div>
	    </div>
	    <div class="ns-table-title">
	        <div class="ns-title-pic">
	            <img src="http://saas.goodsceo.com/app/shop/view/public/img/stat/order_money.png">
	        </div>
	        <div class="ns-title-content">
	            <p class="ns-text-color-dark-gray">订单金额</p>
	            <p class="ns-basic-count" id="orderTotal">0</p>
	        </div>
	    </div>
	    <div class="ns-table-title">
	        <div class="ns-title-pic">
	            <img src="http://saas.goodsceo.com/app/shop/view/public/img/stat/order_num.png">
	        </div>
	        <div class="ns-title-content">
	            <p class="ns-text-color-dark-gray">订单数</p>
	            <p class="ns-basic-count" id="orderPayCount">0</p>
	        </div>
	    </div>
	    <div class="ns-table-title">
	    </div>
	</div>
</div>

<!-- 趋势分析 -->
<div class="layui-card ns-card-common ns-card-brief">
	<div class="layui-card-header">
		<span class="ns-card-title">趋势分析</span>
		
		<div class="ns-card-head-right layui-form">
			<div class="layui-form-item">
				<div class="layui-input-block ns-len-mid">
					<select name="date" lay-filter="date_chart">
						<option value="1">近7天</option>
						<option value="2">近30天</option>
					</select>
				</div>
			</div>
			<span class="current-time"></span>
		</div>
	</div>
	<div class="layui-card-body">
		<div id="main" style="width: 100%; height: 400px;"></div>
	</div>
</div>


			</div>
			
			<!-- 版权信息 -->
<!--			<div class="ns-footer">-->
<!--				<div class="ns-footer-img">-->
<!--					<a href="#"><img style="-webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);-ms-filter: grayscale(100%);-o-filter: grayscale(100%);filter: grayscale(100%);filter: gray;" src="<?php if(!empty($copyright['logo'])): ?> <?php echo img($copyright['logo']); else: ?>http://saas.goodsceo.com/public/static/img/copyright_logo.png<?php endif; ?>" /></a>-->
<!--				</div>-->
<!--			</div>-->

			<div class="ns-footer">
				
				<a class="ns-footer-img" href="#"><img src="<?php if(!empty($copyright['logo'])): ?> <?php echo img($copyright['logo']); else: ?>http://saas.goodsceo.com/public/static/img/copyright_logo.png<?php endif; ?>" /></a>
				<p><?php if(!(empty($copyright['company_name']) || (($copyright['company_name'] instanceof \think\Collection || $copyright['company_name'] instanceof \think\Paginator ) && $copyright['company_name']->isEmpty()))): ?><?php echo htmlentities($copyright['company_name']); else: ?>上海牛之云网络科技有限公司<?php endif; if(!(empty($copyright['icp']) || (($copyright['icp'] instanceof \think\Collection || $copyright['icp'] instanceof \think\Paginator ) && $copyright['icp']->isEmpty()))): ?><a href=<?php echo htmlentities($copyright['copyright_link']); ?>>&nbsp;&nbsp;备案号<?php echo htmlentities($copyright['icp']); ?></a><?php endif; ?></p>
				<?php if(!(empty($copyright['gov_record']) || (($copyright['gov_record'] instanceof \think\Collection || $copyright['gov_record'] instanceof \think\Paginator ) && $copyright['gov_record']->isEmpty()))): ?><a class="gov-box" href=<?php echo htmlentities($copyright['gov_url']); ?>><img src="http://saas.goodsceo.com/app/shop/view/public/img/gov_record.png" alt="">公安备案<?php echo htmlentities($copyright['gov_record']); ?></a><?php endif; ?>
				
			</div>

		</div>
		<!-- </div>	 -->
	</div>	
	
	<!-- 重置密码弹框html -->
	<div class="layui-form" id="reset_pass" style="display: none;">
		<div class="layui-form-item">
			<label class="layui-form-label"><span class="required">*</span>原密码</label>
			<div class="layui-input-block">
				<input type="password" id="old_pass" name="old_pass" required class="layui-input ns-len-mid" maxlength="18" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly',true);">
				<span class="required"></span>
			</div>
		</div>

		<div class="layui-form-item">
			<label class="layui-form-label"><span class="required">*</span>新密码</label>
			<div class="layui-input-block">
				<input type="password" id="new_pass" name="new_pass" required class="layui-input ns-len-mid" maxlength="18" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly',true);">
				<span class="required"></span>
			</div>
		</div>

		<div class="layui-form-item">
			<label class="layui-form-label"><span class="required">*</span>确认新密码</label>
			<div class="layui-input-block">
				<input type="password" id="repeat_pass" name="repeat_pass" required class="layui-input ns-len-mid" maxlength="18" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly',true);">
				<span class="required"></span>
			</div>
		</div>

		<div class="ns-form-row">
			<button class="layui-btn ns-bg-color" onclick="repass()">确定</button>
			<button class="layui-btn layui-btn-primary" onclick="closePass()">返回</button>
		</div>
	</div>
	
	<script type="text/javascript">
		layui.use('element',function () {
			var element = layui.element;
			element.render('breadcrumb');
		});
		function clearCache () {
			$.ajax({
				type: 'post',
				url: ns.url("admin/Login/clearCache"),
				dataType: 'JSON',
				success: function(res) {
					layer.msg(res.message);
					location.reload();
				}
			})
		}
		/**
		 * 重置密码
		 */
		var index;
		function resetPassword() {
			index = layer.open({
				type:1,
				content:$('#reset_pass'),
				offset: 'auto',
				area: ['650px']
			});
			
			setTimeout(function() {
				$(".ns-reset-pass").removeClass('layui-this');
			}, 1000);
		}

		var repeat_flag = false;
		function repass(){
			var old_pass = $("#old_pass").val();
			var new_pass = $("#new_pass").val();
			var repeat_pass = $("#repeat_pass").val();

			if (old_pass == '') {
				$("#old_pass").focus();
				layer.msg("原密码不能为空");
				return;
			}

			if (new_pass == '') {
				$("#new_pass").focus();
				layer.msg("密码不能为空");
				return;
			} else if ($("#new_pass").val().length < 6) {
				$("#new_pass").focus();
				layer.msg("密码不能少于6位数");
				return;
			}
			if (repeat_pass == '') {
				$("#repeat_pass").focus();
				layer.msg("密码不能为空");
				return;
			} else if ($("#repeat_pass").val().length < 6) {
				$("#repeat_pass").focus();
				layer.msg("密码不能少于6位数");
				return;
			}
			if (new_pass != repeat_pass) {
				$("#repeat_pass").focus();
				layer.msg("两次密码输入不一样，请重新输入");
				return;
			}

			if(repeat_flag)return;
			repeat_flag = true;

			$.ajax({
				type: "POST",
				dataType: 'JSON',
				url: ns.url("shop/login/modifypassword"),
				data: {"old_pass": old_pass,"new_pass": new_pass},
				success: function(res) {
					layer.msg(res.message);
					repeat_flag = false;

					if (res.code == 0) {
						layer.close(index);
						location.reload();
					}
				}
			});
		}

		function closePass() {
			layer.close(index);
		}
		
		layui.use('element', function() {
			var element = layui.element;
			element.init();
		});

		function getShopUrl(e) {
			$.ajax({
				type: "POST",
				dataType: 'JSON',
				url: ns.url("shop/shop/shopUrl"),
				success: function(res) {
					if(res.data.path.h5.status == 1) {
						layui.use('laytpl', function(){
							var laytpl = layui.laytpl;
							
							laytpl($("#h5_preview").html()).render(res.data, function (html) {
								var layerIndex = layer.open({
									title: '访问店铺',
									skin: 'layer-tips-class',
									type: 1,
									area: ['600px', '600px'],
									content: html,
								});
							});
						})
					} else {
						layer.msg(res.data.path.h5.message);
					}
				}
			});
		}
		
	</script>
	
	<!-- 店铺预览 -->
	<script type="text/html" id="h5_preview">
		<div class="goods-preview">
			<div class="qrcode-wrap">
				{{# if(d.path.h5.img){ }}
				<img src="{{ ns.img(d.path.h5.img) }}" alt="推广二维码">
				<p class="tips ns-text-color">扫码访问店铺</p>
				<br/>
				{{# } }}
				{{# if(d.path.weapp.img){ }}
				<img src="{{ ns.img(d.path.weapp.img) }}" alt="推广二维码">
				<p class="tips ns-text-color">扫码访问店铺</p>
				{{# } }}
			</div>
			<div class="phone-wrap">
				<div class="iframe-wrap">
					<iframe src="{{ d.path.h5.url }}&preview=1" frameborder="0"></iframe>
				</div>
			</div>
		</div>
	</script>


<script src="http://saas.goodsceo.com/app/shop/view/public/js/echarts.min.js"></script>
<script>
	$(function() {
		getShopStat(0);
		getShopStatChart(1);
	});

	layui.use(['form'], function() {
		var form = layui.form;
		form.render();
		
		/**
		 * 监听下拉框
		 */
		form.on('select(date)', function(data) {
			getShopStat(data.value);
		});

		/**
		 * 图表监听下拉框
		 */
		form.on('select(date_chart)', function(data) {
			getShopStatChart(data.value);
		});
	})

	//获取店铺统计
	function getShopStat(date_type) {
		$.ajax({
			dataType: 'JSON',
			type: 'POST',
			url: ns.url("shop/stat/shop"),
			data: {
				date_type: date_type
			},
			success: function(res) {
				if (res.code == 0) {
					$("#addGoodsCount").html(res.data.add_goods_count);
					$("#collectShop").html(res.data.collect_shop);
					$("#collectGoods").html(res.data.collect_goods);
					$("#visitCount").html(res.data.visit_count);
					$("#orderTotal").html(res.data.order_total);
					$("#orderPayCount").html(res.data.order_pay_count);
					$("#goodsPayCount").html(res.data.goods_pay_count);
					$(".ns-flow-time-today").html(res.data.time_range);
				} else {
					layer.msg(res.message);
				}
			}
		});
	}

	//获取店铺统计图表
	function getShopStatChart(date_type) {
		$.ajax({
			dataType: 'JSON',
			type: 'POST',
			url: ns.url("shop/stat/getshopstatlist"),
			data: {
				date_type: date_type
			},
			success: function(res) {
				option.xAxis.data = res.time;
				option.series[0].data = res.add_goods_count;
				option.series[1].data = res.visit_count;
				option.series[2].data = res.collect_goods;
				option.series[3].data = res.goods_pay_count;
				option.series[4].data = res.collect_shop;
				option.series[5].data = res.order_total;
				option.series[6].data = res.order_pay_count;
				
				$(".current-time").html(res.time_range);
				myChart.setOption(option);
			}
		});
	}

	// 基于准备好的dom，初始化echarts实例
	var myChart = echarts.init(document.getElementById('main'));

	// 指定图表的配置项和数据
	option = {
		tooltip: {
			trigger: 'axis'
		},
		legend: {
			data: ['新增商品数', '商品浏览数', '商品收藏数', '订单商品数', '店铺收藏数', '订单金额', '订单数']
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: []
		},
		yAxis: {
			type: 'value',
			axisLabel: {
				formatter: '{value} '
			}
		},
		series: [{
				name: '新增商品数',
				type: 'line',
				data: [],
			},
			{
				name: '商品浏览数',
				type: 'line',
				data: [],
			},
			{
				name: '商品收藏数',
				type: 'line',
				data: [],
			},
			{
				name: '订单商品数',
				type: 'line',
				data: [],
			},
			{
				name: '店铺收藏数',
				type: 'line',
				data: [],
			},
			{
				name: '订单金额',
				type: 'line',
				data: [],
			},
			{
				name: '订单数',
				type: 'line',
				data: [],
			}
			
		]
	};

	// 使用刚指定的配置项和数据显示图表。
	myChart.setOption(option);
</script>

</body>

</html>