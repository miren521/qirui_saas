<?php /*a:2:{s:61:"/www/wwwroot/saas.goodsceo.com/app/shop/view/index/index.html";i:1600314240;s:54:"/www/wwwroot/saas.goodsceo.com/app/shop/view/base.html";i:1600314240;}*/ ?>
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
	
<link rel="stylesheet" href="http://saas.goodsceo.com/app/shop/view/public/css/index.css">

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
				<?php endif; if(isset($shop['is_reopen']) && $shop['is_reopen']  == 3): ?>
<div class="expire-hint">
	<div class="expire-logo">
		<img src="http://saas.goodsceo.com/app/shop/view/public/img/warning.png" >
	</div>
	<div class="expire-center">
		<h3>店铺已暂停服务，无法正常营业
			<?php if($shop['cert_id'] == 0): ?>
			<a class="ns-text-color shop_auth_apply layui-btn-radius">立即认证</a>
			<?php else: if($is_reopen == 1): ?>
				<a class="ns-text-color reopen_apply layui-btn-radius">立即续费</a>
				<?php else: ?>
				<a class="ns-text-color reopen_apply_detail layui-btn-radius">立即续费</a>
				<?php endif; ?>
			<?php endif; ?>
		</h3>
		<p><span class="ns-red-color">剩余0天</span>（已到期）<span> 咨询电话：<?php echo htmlentities($website_info['web_phone']); ?></span></p>
	</div>
</div>
<?php elseif(isset($shop['is_reopen']) && $shop['is_reopen'] == 2): ?>
<div class="expire-hint">
	<div class="expire-logo">
		<img src="http://saas.goodsceo.com/app/shop/view/public/img/warning.png" >
	</div>
	<div class="expire-center">
		<h3>店铺即将到期，请尽快续费
			<?php if($shop['cert_id'] == 0): ?>
			<a class="ns-text-color shop_auth_apply layui-btn-radius">立即认证</a>
			<?php else: if($is_reopen == 1): ?>
				<a class="ns-text-color reopen_apply layui-btn-radius">立即续费</a>
				<?php else: ?>
				<a class="ns-text-color reopen_apply_detail layui-btn-radius">立即续费</a>
				<?php endif; ?>
			<?php endif; ?>
		</h3>
		<p><span class="ns-red-color">剩余<?php echo htmlentities($shop['expires_date']); ?>天</span> <span> 咨询电话：<?php echo htmlentities($website_info['web_phone']); ?></span></p>
	</div>
</div>
<?php endif; ?>
<div class="ns-survey">
	<div class="ns-survey-left">
		<div class="ns-survey-item">
			<!-- 商家信息 -->
			<div class="ns-survey-shop">
				<div class="ns-item-pic">
					<img layer-src src="<?php echo img($shop['logo']); ?>" onerror=src="http://saas.goodsceo.com/app/shop/view/public/img/default_shop.png" />
				</div>

				<div class="ns-surver-shop-detail">
					<p class="ns-survey-shop-name"><?php echo htmlentities($shop['site_name']); ?></p>
					<p>最后登录：<span class="ns-text-color-dark-gray"><?php  echo date("Y-m-d H:i:s", $shop_user_info['login_time']); ?></span></p>
					<!--<p>用户名：<span class="ns-text-color-dark-gray"><?php echo htmlentities($shop_user_info['username']); ?></span></p>-->
					<p class="ns-shop-detail-label"><span><?php echo htmlentities($shop['group_name']); ?></span><span><?php echo htmlentities($shop_user_info['group_name']); ?></span></p>
					<p>
						<?php if($shop['cert_id'] == 0): ?>
						<a href="<?php echo url('shop/cert/index'); ?>" class="ns-text-color-dark-gray ns-red-color">未认证</a>
						<?php else: ?>
						<span class="ns-text-color-dark-gray">已认证</span>
						<?php endif; ?>
					</p>
					<!-- <p>开店套餐：<span class="ns-text-color-dark-gray"><?php echo htmlentities($shop['group_name']); ?></span></p> -->
					<p>主营行业：<span class="ns-text-color-dark-gray"><?php echo htmlentities($shop['category_name']); ?></span></p>
					<!-- <p>管理权限：<span class="ns-text-color-dark-gray"><?php echo htmlentities($shop_user_info['group_name']); ?></span></p> -->
					<p>店铺状态：
						<span class="ns-text-color-dark-gray">
						<?php if($shop['shop_status'] == 1): ?>
							<span class="ns-text-color-dark-gray">正常</span>
						<?php else: ?>
							<span class="ns-text-color-dark-gray ns-red-color">关闭</span>
						<?php endif; ?>
						</span>
					</p>
					<p>到期时间：<span class="ns-text-color-dark-gray">
						<?php if($shop['expire_time'] == 0): ?>
						永久
						<?php else:  echo date("Y-m-d", $shop['expire_time']); ?>
						<?php endif; ?>
						</span>
					</p>
				</div>
			</div>

			<!-- 概况 -->
			<div class="layui-card ns-survey-info ns-card-common">
				<div class="layui-card-header">
					<div>
						<span class="ns-card-title">实时概况</span>
						<span class="ns-card-sub">更新时间：<?php echo htmlentities($today); ?></span>
					</div>
				</div>
				<div class="layui-card-body">
					<div class="ns-survey-detail-con">
						<div class="ns-survey-detail-aco">
							今日订单数
							<div class="ns-prompt-block">
								<i class="iconfont iconwenhao1"></i>
								<div class="ns-prompt-box">
									<div class="ns-prompt-con">
										只有经过支付的订单才会参与统计,支付后关闭的订单也参与统计
									</div>
								</div>
							</div>
						</div>
						<p class="ns-survey-detail-num"><?php echo htmlentities($stat_day['order_pay_count']); ?></p>
						<p class="ns-survey-detail-yesterday">昨日：<?php echo htmlentities($stat_yesterday['order_pay_count']); ?></p>
					</div>
					<div class="ns-survey-detail-con">
						<p class="ns-survey-detail-aco">今日销售金额(元)</p>
						<p class="ns-survey-detail-num"><?php if(isset($stat_day['order_total'])): ?><?php echo htmlentities($stat_day['order_total']); else: ?> 0.00 <?php endif; ?></p>
						<p class="ns-survey-detail-yesterday">昨日：<?php if(isset($stat_yesterday['order_total'])): ?><?php echo htmlentities($stat_yesterday['order_total']); else: ?> 0.00 <?php endif; ?></p>
					</div>
					<div class="ns-survey-detail-con">
						<div class="ns-survey-detail-aco">
							订单总数
							<div class="ns-prompt-block">
								<i class="iconfont iconwenhao1"></i>
								<div class="ns-prompt-box">
									<div class="ns-prompt-con">
										只有经过支付的订单才会参与统计,支付后关闭的订单也参与统计
									</div>
								</div>
							</div>
						</div>
						<p class="ns-survey-detail-num"><?php echo htmlentities($shop_stat_sum['order_pay_count']); ?></p>
					</div>
					<div class="ns-survey-detail-con">
						<p class="ns-survey-detail-aco">订单销售额(元)</p>
						<p class="ns-survey-detail-num"><?php echo htmlentities($shop_stat_sum['order_total']); ?></p>
					</div>
					<div class="ns-survey-detail-con">
						<p class="ns-survey-detail-aco">今日店铺收藏</p>
						<p class="ns-survey-detail-num"><?php echo htmlentities($stat_day['collect_shop']); ?></p>
						<p class="ns-survey-detail-yesterday">昨日：<?php echo htmlentities($stat_yesterday['collect_shop']); ?></p>
					</div>
					<div class="ns-survey-detail-con">
						<p class="ns-survey-detail-aco">店铺收藏总数</p>
						<p class="ns-survey-detail-num"><?php echo htmlentities($shop_stat_sum['collect_shop']); ?></p>
					</div>
					<div class="ns-survey-detail-con">
						<p class="ns-survey-detail-aco">商品收藏总数</p>
						<p class="ns-survey-detail-num"><?php echo htmlentities($shop_stat_sum['collect_goods']); ?></p>
					</div>
					<div class="ns-survey-detail-con">
						<p class="ns-survey-detail-aco">商品总数</p>
						<p class="ns-survey-detail-num"><?php echo htmlentities($shop_stat_sum['goods_count']); ?></p>
					</div>
				</div>
			</div>

		</div>

        <!-- 常用功能 -->
        <div class="layui-card ns-card-common">
            <div class="layui-card-header">
                <div>
                    <span class="ns-card-title"><strong>常用功能</strong></span>
                    <span class="ns-card-sub">更新时间：<?php echo htmlentities($today); ?></span>
                </div>
            </div>

			<div class="layui-card-body">
				<div class="ns-item-block-parent">
					<a class="ns-item-block ns-item-block-hover-a" href="<?php echo url('shop/goods/addgoods'); ?>">
						<div class="ns-item-block-wrap">
							<div class="ns-item-pic">
								<img src="http://saas.goodsceo.com/app/shop/view/public/img/menu_icon/issue_good.png">
							</div>
							<div class="ns-item-con">
								<div class="ns-item-content-title">发布商品</div>
								<p class="ns-item-content-desc">发布实物商品</p>
							</div>
						</div>
					</a>
					<a class="ns-item-block ns-item-block-hover-a" href="<?php echo url('shop/order/lists'); ?>">
						<div class="ns-item-block-wrap">
							<div class="ns-item-pic">
								<img src="http://saas.goodsceo.com/app/shop/view/public/img/menu_icon/order_select.png">
							</div>
							<div class="ns-item-con">
								<div class="ns-item-content-title">订单查询</div>
								<p class="ns-item-content-desc">查询系统普通订单</p>
							</div>
						</div>
					</a>
					<a class="ns-item-block ns-item-block-hover-a" href="<?php echo url('shop/diy/index'); ?>">
						<div class="ns-item-block-wrap">
							<div class="ns-item-pic">
								<img src="http://saas.goodsceo.com/app/shop/view/public/img/menu_icon/page_decoration.png">
							</div>
							<div class="ns-item-con">
								<div class="ns-item-content-title">页面装修</div>
								<p class="ns-item-content-desc">主页面进行装修</p>
							</div>
						</div>
					</a>
					<a class="ns-item-block ns-item-block-hover-a" href="<?php echo url('shop/account/reopenlist'); ?>">
						<div class="ns-item-block-wrap">
							<div class="ns-item-pic">
								<img src="http://saas.goodsceo.com/app/shop/view/public/img/menu_icon/shop_apply.png">
							</div>
							<div class="ns-item-con">
								<div class="ns-item-content-title">店铺续签</div>
								<p class="ns-item-content-desc">店铺续签管理</p>
							</div>
						</div>
					</a>
				</div>
			</div>
        </div>

		<!-- 插件 -->
		<div class="layui-card ns-card-common">
			<div class="layui-card-header">
				<div>
					<span class="ns-card-title"><strong>营销插件</strong></span>
					<span class="ns-card-sub">更新时间：<?php echo htmlentities($today); ?></span>
				</div>
			</div>
			
			<div class="layui-card-body">
				<div class="ns-item-block-parent">
					<?php foreach($promotion as $list_k => $list_v): ?>
					<a class="ns-item-block ns-item-block-hover-a" <?php if(empty($list_v['is_developing']) || (($list_v['is_developing'] instanceof \think\Collection || $list_v['is_developing'] instanceof \think\Paginator ) && $list_v['is_developing']->isEmpty())): ?> href="<?php echo addon_url($list_v['url']); ?>" <?php endif; ?>>
						<div class="ns-item-block-wrap">
							<div class="ns-item-pic">
								<img src="<?php echo img($list_v['icon']); ?>">
							</div>
							<div class="ns-item-con">
								<div class="ns-item-content-title"><?php echo htmlentities($list_v['title']); ?></div>
								<p class="ns-item-content-desc ns-one-line-hiding" title="<?php echo htmlentities($list_v['description']); ?>"><?php echo htmlentities($list_v['description']); ?></p>
							</div>
							<?php if(!(empty($list_v['is_developing']) || (($list_v['is_developing'] instanceof \think\Collection || $list_v['is_developing'] instanceof \think\Paginator ) && $list_v['is_developing']->isEmpty()))): ?>
								<div class="ns-item-poa-pic">
									敬请期待
								</div>
							<?php endif; ?>
						</div>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="ns-survey-right">
		<!-- 客服 -->
		<div class="layui-card ns-survey-customer-service">
			<div class="ns-item-block-parent">
				<a href="">
					<div class="ns-item-block-wrap">
						<div class="ns-item-pic">
							<?php if($website_info['web_qrcode']): ?>
							<img src="<?php echo img($website_info['web_qrcode']); ?>">
							<?php else: ?>
							<img src="http://saas.goodsceo.com/public/static/img/wxewm.png">
							<?php endif; ?>
						</div>
						<div class="ns-item-con">
							<div class="ns-item-content-title">公众号管理</div>
							<p class="ns-item-content-desc">电话：<?php echo htmlentities($website_info['web_phone']); ?></p>
						</div>
					</div>
				</a>
			</div>
		</div>

		<!-- 店铺评分 -->
		<div class="layui-card ns-survey-guide">
			<div class="layui-card-header"><span><i></i>店铺评分</span></div>
			<div class="layui-card-body">
				<a class="layui-elip" href="JavaScript:;">描述相符：<?php echo htmlentities($shop['shop_desccredit']); ?> 分</a>
				<a class="layui-elip" href="JavaScript:;">服务态度：<?php echo htmlentities($shop['shop_servicecredit']); ?> 分</a>
				<a class="layui-elip" href="JavaScript:;">配送服务：<?php echo htmlentities($shop['shop_deliverycredit']); ?> 分</a>
			</div>
		</div>
		
		<!-- 入驻指南 -->
		<div class="layui-card ns-survey-guide">
			<div class="layui-card-header"><span><i></i>入驻指南</span><a class="ns-text-color" href="<?php echo url('shop/shopjoin/guide'); ?>">更多</a></div>
			<div class="layui-card-body">
                <?php foreach($shop_join_guide_list as $list_k => $list_v): ?>
			    <a class="layui-elip" href="<?php echo url('shop/shopjoin/guidedetail'); ?>?guide_index=<?php echo htmlentities($list_v['guide_index']); ?>">
			    	<span class="date"><?php echo htmlentities(date('m/d',!is_numeric($list_v['create_time'])? strtotime($list_v['create_time']) : $list_v['create_time'])); ?></span><span><?php echo htmlentities($list_v['title']); ?></span>
			    </a>
                <?php endforeach; ?>
			</div>
		</div>
		
		<!-- 入驻帮助 -->
		<div class="layui-card ns-survey-help">
			<div class="layui-card-header"><span><i></i>商家帮助</span><a class="ns-text-color" href="<?php echo url('shop/shophelp/helplist'); ?>">更多</a></div>
			<div class="layui-card-body">
                <?php foreach($help_list as $list_k => $list_v): ?>
			    <a class="layui-elip" href="<?php echo url('shop/shophelp/helpDetail'); ?>?help_id=<?php echo htmlentities($list_v['id']); ?>">
			    	<span class="type">[<?php echo htmlentities($list_v['class_name']); ?>]</span><span><?php echo htmlentities($list_v['title']); ?></span>
			    </a>
                <?php endforeach; ?>
			</div>
		</div>

		<!-- 网站公告 -->
		<div class="layui-card ns-survey-help">
			<div class="layui-card-header"><span><i></i>网站公告</span><a class="ns-text-color" href="<?php echo url('shop/notice/lists'); ?>">更多</a></div>
			<div class="layui-card-body">
				<?php foreach($notice_list as $list_k => $list_v): ?>
				<a class="layui-elip" href="<?php echo url('shop/notice/detail'); ?>?id=<?php echo htmlentities($list_v['id']); ?>">
					<span class="adorn ns-bg-color"><?php echo htmlentities($list_k+1); ?></span><span><?php echo htmlentities($list_v['title']); ?></span>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
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



<script>
    var table, form, laytpl, laydate, upload, addRenewal, repeat_flag = false;
    layui.use(['form', 'laytpl', 'laydate', 'upload'], function() {
        form = layui.form;
        laytpl = layui.laytpl;
        laydate = layui.laydate;
        upload = layui.upload;
		form.render();

        /**
         * 监听开店套餐下拉选(添加)
         */
        form.on('select(shop_group)', function (data) {        //对应lay-filter
            obj.group_id = data.value;
            moneyChange(obj);
        });

        /**
         * 监听续签年限(添加)
         */
        form.on('select(apply_year)', function (data) {        //对应lay-filter
            obj.apply_year = data.value;
            moneyChange(obj);
        });

        function moneyChange(data) {
            $.ajax({
                type: "POST",
                url: ns.url("shop/Apply/getApplyMoney"),
                data: data,
                dataType: 'JSON',
                success: function(res) {
                    repeat_flag = false;

                    $(".paying-amount").text(res.code.paying_amount + '元');
                    $(".pay-amount").val(res.code.paying_amount);
                    if (res.code == 0) {
                        layer.closeAll('page');
                    }
                }
            });
        }

        //申请续签
        $(".reopen_apply").click(function () {
            location.href = ns.url("shop/cert/reopen");
        });
        //编辑续签
        $(".reopen_apply_detail").click(function () {
            location.href = ns.url("shop/cert/editreopeninfo");
        });
        //认证
        $(".shop_auth_apply").click(function () {
            location.href = ns.url("shop/cert/index");
        });

    })
</script>

</body>

</html>