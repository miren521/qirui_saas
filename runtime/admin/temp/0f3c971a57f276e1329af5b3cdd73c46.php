<?php /*a:2:{s:64:"E:\mi\company\SaaS\code\back-end\app\admin\view\index\index.html";i:1600314240;s:57:"E:\mi\company\SaaS\code\back-end\app\admin\view\base.html";i:1600312146;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1" />
	<title><?php echo htmlentities((isset($menu_info['title']) && ($menu_info['title'] !== '')?$menu_info['title']:"")); ?> - <?php echo htmlentities((isset($website['title']) && ($website['title'] !== '')?$website['title']:"Niushop开源商城")); ?></title>
	<meta name="keywords" content="<?php echo htmlentities((isset($website['keywords']) && ($website['keywords'] !== '')?$website['keywords']:'Niushop开源商城')); ?>">
	<meta name="description" content="<?php echo htmlentities((isset($website['desc']) && ($website['desc'] !== '')?$website['desc']:'描述')); ?>}">
	<link rel="icon" type="image/x-icon" href="http://saas.com/public/static/img/bitbug_favicon.ico" />
	<link rel="stylesheet" type="text/css" href="http://saas.com/public/static/css/iconfont.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.com/public/static/ext/layui/css/layui.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.com/public/static/loading/msgbox.css"/>
	<link rel="stylesheet" type="text/css" href="http://saas.com/app/admin/view/public/css/common.css" />
	<script src="http://saas.com/public/static/js/jquery-3.1.1.js"></script>
	<script src="http://saas.com/public/static/ext/layui/layui.js"></script>
	<script>
		layui.use(['layer', 'upload', 'element'], function() {});
		window.ns_url = {
			baseUrl: "http://saas.com/",
			route: ['<?php echo request()->module(); ?>', '<?php echo request()->controller(); ?>', '<?php echo request()->action(); ?>'],
		};
	</script>
	<script src="http://saas.com/public/static/js/common.js"></script>
	<style>
		.ns-calendar{background: url("http://saas.com/public/static/img/ns_calendar.png") no-repeat center / 16px 16px;}
	</style>
	
<link rel="stylesheet" href="http://saas.com/app/admin/view/public/css/index.css">

	<script type="text/javascript">
	</script>
</head>
<body>

<!-- logo -->
<div class="ns-logo">
	<div class="logo-box">
		<img src="http://saas.com/app/admin/view/public/img/logo.png">
	</div>
	<span>B2B2C多商户平台端</span>
	<span>
		服务电话：400-886-7993
	</span>
</div>

<div class="layui-layout layui-layout-admin">
	
	<div class="layui-header">
		<!-- 一级菜单 -->
		<ul class="layui-nav layui-layout-left">
			<?php $second_menu = []; foreach($menu as $menu_k => $menu_v): ?>
			<li class="layui-nav-item <?php if($menu_v['selected']): ?> layui-this<?php endif; ?>">
				<a href="<?php echo htmlentities($menu_v['url']); ?>"><?php echo htmlentities($menu_v['title']); ?></a>
			</li>
			<?php if($menu_v['selected']): 
				$second_menu = $menu_v['child_list'];
				 ?>
			<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<ul class="layui-nav layui-layout-right">
			<li class="layui-nav-item">
				<a href="javascript:;">
					<div class="ns-img-box">
						<img src="http://saas.com/app/admin/view/public/img/default_headimg.png" alt="">
					</div>
					<?php echo htmlentities($user_info['username']); ?>
				</a>
				<dl class="layui-nav-child">
					<dd class="ns-reset-pass" onclick="resetPassword();">
						<a href="javascript:;">修改密码</a>
					</dd>
					<dd>
						<a onclick="clearCache()" href="javascript:;">清除缓存</a>
					</dd>
					<dd>
						<a href="<?php echo addon_url('admin/login/logout'); ?>" class="login-out">退出登录</a>
					</dd>
				</dl>
			</li>
		</ul>
	</div>
	

	<?php if(!(empty($second_menu) || (($second_menu instanceof \think\Collection || $second_menu instanceof \think\Paginator ) && $second_menu->isEmpty()))): ?>
	<div class="layui-side">
		<div class="layui-side-scroll">
			<span class="ns-side-title"><?php echo htmlentities($crumbs[0]['title']); ?></span>
			<!-- 二三级菜单-->
			<ul class="layui-nav layui-nav-tree"  lay-filter="test">
				<?php foreach($second_menu as $menu_second_k => $menu_second_v): ?>
				<li class="layui-nav-item <?php if($menu_second_v['selected']): ?> layui-nav-itemed <?php endif; if(!$menu_second_v['child_list'] && $menu_second_v['selected']): ?> layui-this<?php endif; ?>">
					<a class="layui-menu-tips" href="<?php if(!$menu_second_v['child_list']): ?> <?php echo htmlentities($menu_second_v['url']); else: ?>javascript:;<?php endif; ?>"><?php echo htmlentities($menu_second_v['title']); ?></a>
					<?php if(!(empty($menu_second_v['child_list']) || (($menu_second_v['child_list'] instanceof \think\Collection || $menu_second_v['child_list'] instanceof \think\Paginator ) && $menu_second_v['child_list']->isEmpty()))): ?>
					<dl class="layui-nav-child">
						<?php foreach($menu_second_v["child_list"] as $menu_third_k => $menu_third_v): ?>
						<dd class="<?php if($menu_third_v['selected']): ?> layui-this<?php endif; ?>">
							<a href="<?php echo htmlentities($menu_third_v['url']); ?>"><?php echo htmlentities($menu_third_v['title']); ?></a>
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

	<div class="layui-body<?php if(empty($second_menu) || (($second_menu instanceof \think\Collection || $second_menu instanceof \think\Paginator ) && $second_menu->isEmpty())): ?> child_no_exit<?php endif; ?>">
		<!-- 面包屑 -->
		
		<?php if(count($second_menu) > 0): ?>
		<div class="ns-crumbs<?php if(empty($second_menu) || (($second_menu instanceof \think\Collection || $second_menu instanceof \think\Paginator ) && $second_menu->isEmpty())): ?> child_no_exit<?php endif; ?>">
		<span class="layui-breadcrumb" lay-separator="-">
			<?php foreach($crumbs as $crumbs_k => $crumbs_v): if(count($crumbs) == ($crumbs_k + 1)): ?>
			<a href="<?php echo htmlentities($crumbs_v['url']); ?>"><cite><?php echo htmlentities($crumbs_v['title']); ?></cite></a>
			<?php else: ?>
			<a href="<?php echo htmlentities($crumbs_v['url']); ?>"><?php echo htmlentities($crumbs_v['title']); ?></a>
			<?php endif; ?>
			<?php endforeach; ?>
		</span>
		</div>
		<?php endif; ?>
		
		<div class="ns-body-content <?php if(count($second_menu) < 1): ?> crumbs_no_exit<?php endif; ?>">
			<div class="ns-body">
				<!-- 四级导航 -->
				<?php if(isset($forth_menu) && !empty($forth_menu)): ?>
				<div class="fourstage-nav layui-tab layui-tab-brief" lay-filter="edit_user_tab">
					<ul class="layui-tab-title">
						<?php if(is_array($forth_menu) || $forth_menu instanceof \think\Collection || $forth_menu instanceof \think\Paginator): $i = 0; $__LIST__ = $forth_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?>
						<li class="<?php echo $menu['selected']==1 ? 'layui-this'  :  ''; ?>" lay-id="basic_info"><a href="<?php echo htmlentities($menu['parse_url']); ?>"><?php echo htmlentities($menu['title']); ?></a></li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<?php endif; if($need_upgrade == 1): ?>
<p class="version-upgrade ns-border-color">
    温馨提示：多商户系统发布了最新版本，请尽快进行升级
    <span>x</span>
</p>
<?php endif; ?>

<div class="index-box">
    <div class="index-content">
        <div class="basic-info">
            <blockquote class="layui-elem-quote">基础信息 <span class="ns-card-sub"></span></blockquote>
            <div class="basic-info-box">
                <div class="basic-list">
                    <div class="basic-pic">
                        <img src="http://saas.com/app/admin/view/public/img/index/order_num.png" alt="">
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">今日销售总额（元）</div>
                        <div class="content layui-elip"><?php if(isset($stat_info['order_total'])): ?><?php echo htmlentities($stat_info['order_total']); else: ?> 0 <?php endif; ?></div>
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">今日销售订单数（笔）</div>
                        <div class="content layui-elip"><?php if(isset($stat_info['order_pay_count'])): ?><?php echo htmlentities($stat_info['order_pay_count']); else: ?> 0 <?php endif; ?></div>
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">订单销售总额（元）</div>
                        <div class="content layui-elip"><?php if(isset($order_money)): ?><?php echo htmlentities($order_money); else: ?> 0.00 <?php endif; ?></div>
                    </div>
                </div>
                <div class="basic-list">
                    <div class="basic-pic">
                        <img src="http://saas.com/app/admin/view/public/img/index/good_num.png" alt="">
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">今日新增商品数</div>
                        <div class="content layui-elip"><?php echo htmlentities($stat_info['add_goods_count']); ?></div>
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">今日商品购买数</div>
                        <div class="content layui-elip"><?php echo htmlentities($stat_info['goods_pay_count']); ?></div>
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">商品总数</div>
                        <div class="content layui-elip"><?php echo htmlentities($goods_count); ?></div>
                    </div>
                </div>
                <div class="basic-list">
                    <div class="basic-pic">
                        <img src="http://saas.com/app/admin/view/public/img/index/member_num.png" alt="">
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">今日新增会员数</div>
                        <div class="content layui-elip"><?php echo htmlentities($stat_info['member_count']); ?></div>
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">会员总数</div>
                        <div class="content layui-elip"><?php echo htmlentities($member_count); ?></div>
                    </div>
                    <div class="basic-item"></div>
                </div>
                <div class="basic-list">
                    <div class="basic-pic">
                        <img src="http://saas.com/app/admin/view/public/img/index/shop_num.png" alt="">
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">今日申请店铺数</div>
                        <div class="content layui-elip"><?php echo htmlentities($shop_apply_count); ?></div>
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">今日新增店铺数</div>
                        <div class="content layui-elip"><?php echo htmlentities($shop_count); ?></div>
                    </div>
                    <div class="basic-item">
                        <div class="title layui-elip">店铺总数</div>
                        <div class="content layui-elip"><?php echo htmlentities($shop_total_count); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="common-function">
            <blockquote class="layui-elem-quote">常用功能</blockquote>
            <div class="common-function-box layui-row">
                <a class="layui-col-md2" href="<?php echo url('admin/member/memberlist'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/member_check.png">
                    </div>
                    <div class="ns-text-box">会员查询</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/member/addMember'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/member_add.png">
                    </div>
                    <div class="ns-text-box">会员新增</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/shop/lists'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/shop_check.png">
                    </div>
                    <div class="ns-text-box">店铺查询</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/shop/addshop'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/shop_add.png">
                    </div>
                    <div class="ns-text-box">店铺新增</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/shopreopen/reopen'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/shop_renewal.png">
                    </div>
                    <div class="ns-text-box">店铺续签</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/goods/lists'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/goods_check.png">
                    </div>
                    <div class="ns-text-box">商品查询</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/goodscategory/lists'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/goods_category.png">
                    </div>
                    <div class="ns-text-box">商品分类</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/goodsbrand/lists'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/goods_brand.png">
                    </div>
                    <div class="ns-text-box">商品品牌</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/order/lists'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/order_check.png">
                    </div>
                    <div class="ns-text-box">订单查询</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/refund/lists'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/refund.png">
                    </div>
                    <div class="ns-text-box">退款维权</div>
                </a>
                <a class="layui-col-md2" href="<?php echo url('admin/express/expresscompany'); ?>">
                    <div class="ns-index-img-box">
                        <img src="http://saas.com/app/admin/view/public/img/index/express.png">
                    </div>
                    <div class="ns-text-box">物流公司</div>
                </a>
            </div>
        </div>
        <div class="layui-row layui-col-space10">
            <div class="layui-col-md6">
                <blockquote class="layui-elem-quote">版本信息</blockquote>
                <div class="versions-info-box">
                    <p><span>产品名称：</span><?php echo htmlentities($sys_product_name); ?></p>
                    <p><span>版本号：</span><?php echo htmlentities($sys_version_no); ?></p>
                    <p><span>版本名称：</span><?php echo htmlentities($sys_version_name); ?></p>
                    <p><span>官方网站：</span><a class="ns-text-color" href="https://www.niushop.com" target="_blank">官方网站</a></p>
                    <p><span>官方论坛：</span><a class="ns-text-color" href="https://bbs.niushop.com/forum.php" target="_blank">交流论坛</a></p>
                </div>
            </div>
            <div class="layui-col-md6">
                <blockquote class="layui-elem-quote">未处理事项</blockquote>
                <div class="layui-tab untreated-box layui-tab-card">
                    <ul class="layui-tab-title">
                        <li class="layui-this">商品</li>
                        <li>订单</li>
                        <li>店铺</li>
                    </ul>
                    <div class="layui-tab-content" style="height: 100px;">
                        <div class="layui-tab-item layui-show">
                            <?php if($verify_goods_count == 0 && $inform_count == 0): ?>
                                <div class="layui-row todo-item">
                                    <div class="layui-col-md12">
                                        当前没有需处理的事件
                                    </div>
                                </div>

                            <?php else: if($verify_goods_count > 0): ?>
                                    <div class="layui-row todo-item">
                                        <div class="layui-col-md10">
											<div class="todo-item-box">
												<img src="http://saas.com/app/admin/view/public/img/index/message.png" />
												<p>有<?php echo htmlentities($verify_goods_count); ?>件待审核的商品等待处理</p>
											</div>
                                        </div>
                                        <div class="layui-col-md2">
                                            <a href="<?php echo addon_url('admin/goods/lists'); ?>" class="ns-line-hiding ns-text-color" target="_blank">立即处理</a>
                                        </div>
                                    </div>
                                <?php endif; if($inform_count > 0): ?>
                                    <div class="layui-row todo-item">
                                        <div class="layui-col-md10">
											<div class="todo-item-box">
												<img src="http://saas.com/app/admin/view/public/img/index/message.png" />
												<p>有<?php echo htmlentities($inform_count); ?>件商品被举报,等待处理</p>
											</div>
                                        </div>
                                        <div class="layui-col-md2">
                                            <a href="<?php echo addon_url('admin/inform/lists'); ?>" class="ns-line-hiding ns-text-color" target="_blank">立即处理</a>
                                        </div>
                                    </div>

                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="layui-tab-item">
                            <?php if($complain_count_result == 0): ?>
                                <div class="layui-row todo-item">
                                    <div class="layui-col-md12">
                                        当前没有需处理的事件
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="layui-row todo-item">
                                    <div class="layui-col-md10">
										<div class="todo-item-box">
											<img src="http://saas.com/app/admin/view/public/img/index/message.png" />
											<p>有<?php echo htmlentities($complain_count_result); ?>件平台维权等待处理</p>
										</div>
                                    </div>
                                    <div class="layui-col-md2">
                                        <a href="<?php echo addon_url('admin/complain/lists'); ?>" class="ns-line-hiding ns-text-color" target="_blank">立即处理</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="layui-tab-item">
                            <?php if($shop_apply_count == 0 && $shop_reopen_count == 0): ?>
                            <div class="layui-row todo-item">
                                <div class="layui-col-md12">
                                    当前没有需处理的事件
                                </div>
                            </div>

                            <?php else: if($shop_apply_count > 0): ?>
                                <div class="layui-row todo-item">
                                    <div class="layui-col-md10">
										<div class="todo-item-box">
											<img src="http://saas.com/app/admin/view/public/img/index/message.png" />
											<p>有<?php echo htmlentities($shop_apply_count); ?>件待处理的开店申请</p>
										</div>
                                    </div>
                                    <div class="layui-col-md2">
                                        <a href="<?php echo addon_url('admin/shopapply/apply'); ?>" class="ns-line-hiding ns-text-color" target="_blank">立即处理</a>
                                    </div>
                                </div>
                                <?php endif; if($shop_reopen_count > 0): ?>
                                <div class="layui-row todo-item">
                                    <div class="layui-col-md10">
										<div class="todo-item-box">
											<img src="http://saas.com/app/admin/view/public/img/index/message.png" />
											<p>有<?php echo htmlentities($shop_reopen_count); ?>件待处理的店铺续签申请</p>
										</div>
                                    </div>
                                    <div class="layui-col-md2">
                                        <a href="<?php echo addon_url('admin/shopreopen/reopen'); ?>" class="ns-line-hiding ns-text-color" target="_blank">立即处理</a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="system-function">
            <blockquote class="layui-elem-quote">系统信息</blockquote>
            <table class="layui-table">
                <colgroup>
                    <col width="20%">
                    <col width="30%">
                    <col width="20%">
                    <col width="30%">
                </colgroup>
                <tbody>
                <tr>
                    <td class="ns-bg-color-light-gray">服务器操作系统</td>
                    <td><?php echo htmlentities($system_config['os']); ?></td>
                    <td class="ns-bg-color-light-gray">服务器域名</td>
                    <td><?php echo htmlentities($system_config['dns']); ?>:<?php echo htmlentities($system_config['port']); ?></td>
                </tr>
                <tr>
                    <td class="ns-bg-color-light-gray">服务器环境</td>
                    <td><?php echo htmlentities($system_config['server_software']); ?></td>
                    <td class="ns-bg-color-light-gray">PHP版本</td>
                    <td><?php echo htmlentities($system_config['php_version']); ?></td>
                </tr>
                <tr>
                    <td class="ns-bg-color-light-gray">文件上传限制</td>
                    <td><?php echo htmlentities($system_config['upload_max_filesize']); ?></td>
                    <td class="ns-bg-color-light-gray">GD版本</td>
                    <td><?php echo htmlentities($system_config['gd_version']); ?></td>
                </tr>
                <tr>
                    <td class="ns-bg-color-light-gray">sockets开启</td>
                    <td><?php if($system_config['sockets'] == true): ?>已开启<?php else: ?><span style="color:red;">未开启，邮箱功能将无法正常使用</span><?php endif; ?></td>
                    <td class="ns-bg-color-light-gray">curl支持</td>
                    <td><?php if($system_config['curl'] == true): ?>支持<?php else: ?><span style="color:red;">不支持，微信和支付宝等功能将无法正常使用</span><?php endif; ?></td>
                </tr>
                <tr>
                    <td class="ns-bg-color-light-gray">openssl开启</td>
                    <td><?php if($system_config['openssl'] == true): ?>已开启<?php else: ?><span style="color:red;">未开启，不支持https</span><?php endif; ?></td>
                    <td class="ns-bg-color-light-gray">fileinfo开启</td>
                    <td><?php if($system_config['fileinfo'] == true): ?>已开启<?php else: ?><span style="color:red;">未开启，将无法获取上传文件类型，导致上传图片失败</span><?php endif; ?></td>
                </tr>
                <tr>
                    <td class="ns-bg-color-light-gray">upload目录权限</td>
                    <td><?php if($system_config['upload_dir_jurisdiction'] == 1): ?>可读可写<?php else: ?><span style="color:red;">不可读不可写，会造成图片无法上传和访问</span><?php endif; ?></td>
                    <td class="ns-bg-color-light-gray">runtime目录权限</td>
                    <td><?php if($system_config['runtime_dir_jurisdiction'] == 1): ?>可读可写<?php else: ?><span style="color:red;">不可读不可写，会造成编译文件缓存文件无法生成读取，是网站访问出错</span><?php endif; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="index-info">
        <div class="layui-card ns-card-common">
            <div class="layui-card-header">
                <span class="">客户服务</span>
            </div>
            <div class="layui-card-body">
                <p class="ns-text-color-dark-gray">客服电话：400-886-7993</p>
                <p class="ns-text-color-dark-gray">官网：https://www.niushop.com</p>
                <p class="ns-text-color-dark-gray">交流群：29507902</p>
            </div>
        </div>
        <div class="layui-card ns-card-common">
            <div class="layui-card-header">
                <span class="">最新动态</span>
            </div>
            <div class="layui-card-body news-list">
                <!--<p class="ns-text-color-dark-gray"><span class="ns-bg-color">1</span>Niushop多商户V4上线啦！</p>-->
                <!--<p class="ns-text-color-dark-gray"><span class="ns-bg-color">2</span>Niushop多商户V4上线啦！</p>-->
                <!--<p class="ns-text-color-dark-gray"><span class="ns-bg-color">3</span>Niushop多商户V4上线啦！</p>-->
                <!--<p class="ns-text-color-dark-gray"><span class="ns-bg-color-gray">4</span>Niushop多商户V4上线啦！</p>-->
                <!--<p class="ns-text-color-dark-gray"><span class="ns-bg-color-gray">5</span>Niushop多商户V4上线啦！</p>-->
                <!--<p class="ns-text-color-dark-gray"><span class="ns-bg-color-gray">6</span>Niushop多商户V4上线啦！</p>-->
                <!--<p class="ns-text-color-dark-gray"><span class="ns-bg-color-gray">7</span>Niushop多商户V4上线啦！</p>-->
            </div>
        </div>
    </div>

</div>


			</div>
			
			<!-- 版权信息 -->
			<div class="ns-footer">
				<div class="ns-footer-img">
					<a href="#"><img style="-webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);-ms-filter: grayscale(100%);-o-filter: grayscale(100%);filter: grayscale(100%);filter: gray;" src="<?php if(!empty($copyright['logo'])): ?> <?php echo img($copyright['logo']); else: ?>http://saas.com/public/static/img/copyright_logo.png<?php endif; ?>" /></a>
				</div>
			</div>
		</div>
	</div>
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

	// $(".ns-reset-pass").on('click', function() {
	// 	$(this).removeClass('layui-this');
	// })

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
            url: ns.url("admin/login/modifypassword"),
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
	
	/**
	 * 打开相册
	 */
	function openAlbum(callback, imgNum) {
		layui.use(['layer'], function () {
			//iframe层-父子操作
			layer.open({
				type: 2,
				title: '图片管理',
				area: ['825px', '675px'],
				fixed: false, //不固定
				btn: ['保存', '返回'],
				content: ns.url("admin/album/album?imgNum=" + imgNum),
				yes: function (index, layero) {
					var iframeWin = window[layero.find('iframe')[0]['name']];//得到iframe页的窗口对象，执行iframe页的方法：
					
					iframeWin.getCheckItem(function (obj) {
						if (typeof callback == "string") {
							try {
								eval(callback + '(obj)');
								layer.close(index);
							} catch (e) {
								console.error('回调函数' + callback + '未定义');
							}
						} else if (typeof callback == "function") {
							callback(obj);
							layer.close(index);
						}
						
					});
				}
			});
		});
	}
	
	layui.use('element', function() {
		var element = layui.element;
		element.init();
	});
</script>


<script>
	var time = (new Date()).getTime();
	var date = ns.time_to_date(time / 1000);
	$(".ns-card-sub").text("更新时间：" + date);

	/* 版本更新 */
    $("body").on("click",".version-upgrade",function () {
        $(this).addClass("layui-hide");
    });
    news();
    //官网新闻

    function news(){
        $.ajax({
            url: "<?php echo addon_url('admin/index/news'); ?>",
            type: "POST",
            dataType: "JSON",
            success: function(res) {

                if(res.code < 0 || res.data.list.length == 0){
                    $(".news-list").append('<p class="ns-text-color-dark-gray"><span class="ns-bg-color">1</span>暂时没有官网资讯！</p>');
                }else{
                    $.each(res.data.list, function(key, value) {
                        var sort = key + 1;
                        $(".news-list").append('<p class="ns-text-color-dark-gray"><span class="ns-bg-color">'+sort+'</span><a href="'+ value.url +'" target="_blank" >'+value.title+'！</a></p>');
                    });
                }
            }
        });
    }
</script>

</body>
</html>