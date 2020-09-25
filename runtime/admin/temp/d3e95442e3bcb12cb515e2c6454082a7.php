<?php /*a:2:{s:64:"/www/wwwroot/saas.goodsceo.com/app/admin/view/account/index.html";i:1600312146;s:55:"/www/wwwroot/saas.goodsceo.com/app/admin/view/base.html";i:1600312146;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1" />
	<title><?php echo htmlentities((isset($menu_info['title']) && ($menu_info['title'] !== '')?$menu_info['title']:"")); ?> - <?php echo htmlentities((isset($website['title']) && ($website['title'] !== '')?$website['title']:"Niushop开源商城")); ?></title>
	<meta name="keywords" content="<?php echo htmlentities((isset($website['keywords']) && ($website['keywords'] !== '')?$website['keywords']:'Niushop开源商城')); ?>">
	<meta name="description" content="<?php echo htmlentities((isset($website['desc']) && ($website['desc'] !== '')?$website['desc']:'描述')); ?>}">
	<link rel="icon" type="image/x-icon" href="http://saas.goodsceo.com/public/static/img/bitbug_favicon.ico" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/public/static/css/iconfont.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/public/static/ext/layui/css/layui.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/public/static/loading/msgbox.css"/>
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/app/admin/view/public/css/common.css" />
	<script src="http://saas.goodsceo.com/public/static/js/jquery-3.1.1.js"></script>
	<script src="http://saas.goodsceo.com/public/static/ext/layui/layui.js"></script>
	<script>
		layui.use(['layer', 'upload', 'element'], function() {});
		window.ns_url = {
			baseUrl: "http://saas.goodsceo.com/",
			route: ['<?php echo request()->module(); ?>', '<?php echo request()->controller(); ?>', '<?php echo request()->action(); ?>'],
		};
	</script>
	<script src="http://saas.goodsceo.com/public/static/js/common.js"></script>
	<style>
		.ns-calendar{background: url("http://saas.goodsceo.com/public/static/img/ns_calendar.png") no-repeat center / 16px 16px;}
	</style>
	
<style>
    .ns-card-brief:nth-child(1){
        margin-top: 0;
    }
    .layui-card-body{
        display: flex;
        flex-wrap: wrap;
        padding-bottom: 0 !important;
        padding-left: 50px !important;
        padding-right: 50px !important;
    }
    .layui-card-body .content{
        width: 33.3%;
        margin-bottom: 30px;
        display: flex;
        flex-wrap: wrap;
        flex-direction: column;
        justify-content: center;
    }
    .layui-card-body .money{
        font-size: 20px;
        color: #000;
        font-weight: bold;
        margin-top: 10px;
        max-width: 250px;
    }
    .layui-card-body .subhead{
        font-size: 12px;
        margin-left: 3px;
        cursor: pointer;
    }
</style>

	<script type="text/javascript">
	</script>
</head>
<body>

<!-- logo -->
<div class="ns-logo">
	<div class="logo-box">
		<img src="http://saas.goodsceo.com/app/admin/view/public/img/logo.png">
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
						<img src="http://saas.goodsceo.com/app/admin/view/public/img/default_headimg.png" alt="">
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
				<?php endif; ?>
				
				
<div class="layui-card ns-card-common ns-card-brief">
    <div class="layui-card-header">
        <div>
            <span class="ns-card-title">平台概况</span>
        </div>
    </div>
    <div class="layui-card-body">
        <div class="content">
            <div class="title ns-prompt-block">
				订单销售总金额（元）
				<div class="ns-prompt">
					<i class="iconfont iconwenhao1"></i>
					<div class="ns-prompt-box">
						<div class="ns-prompt-con">
						本平台所有订单销售的总金额
						</div>
					</div>
				</div>
			</div>
            <p class="money"><?php echo htmlentities($account['order_sum']); ?></p>
        </div>

        <?php if($is_addon_supply > 0): ?>
        <div class="content">
            <div class="title ns-prompt-block">
                供应商订单销售总金额（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            本平台所有供应商订单销售的总金额
                        </div>
                    </div>
                </div>
            </div>
            <p class="money"><?php echo htmlentities($supply_account['order']); ?></p>
        </div>
        <?php endif; ?>
        <div class="content">

            <div class="title ns-prompt-block">
                平台订单抽成（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            订单周期结算后按约定抽成的总金额
                        </div>
                    </div>
                </div>
                <a href="<?php echo url('admin/shopsettlement/lists'); ?>" class="subhead ns-text-color">查看</a>
            </div>
            <p class="money"><?php echo htmlentities($account['account_info']); ?></p>
        </div>
    </div>
</div>

<div class="layui-card ns-card-common ns-card-brief">
    <div class="layui-card-header">
        <div>
            <span class="ns-card-title">店铺概况</span>
        </div>
    </div>
    <div class="layui-card-body">
        <div class="content">
            <p class="title">店铺结算总金额（元） <a href="<?php echo url('admin/shopsettlement/lists'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($account['shop_settlement_sum']); ?></p>
        </div>
        <div class="content">
            <p class="title">店铺入驻总费用（元）<a href="<?php echo url('admin/shopaccount/fee'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($account['shop_fee']); ?></p>
        </div>
        <div class="content">
            <p class="title">店铺总保证金（元）<a href="<?php echo url('admin/account/shopdeposit'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($account['shop_baozhrmb']); ?></p>
        </div>
        <div class="content">
            <p class="title">已提现金额 | 提现中金额（元）<a href="<?php echo url('admin/shopaccount/withdrawlist'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($account['account_withdraw']); ?> | <?php echo htmlentities($account['account_withdraw_apply']); ?></p>
        </div>
        <div class="content">
            <p class="title">店铺总可用余额（元）<a href="<?php echo url('admin/account/shopbalance'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($account['account']); ?></p>
        </div>
    </div>
</div>

<?php if($is_addon_city == 1): ?>
<div class="layui-card ns-card-common ns-card-brief">
    <div class="layui-card-header">
        <div>
            <span class="ns-card-title">分站概况</span>
        </div>
    </div>
    <div class="layui-card-body">

        <div class="content">
            <div class="title ns-prompt-block">
                分站总收入（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            由订单结算抽成、店铺开店续签费用抽成组成
                        </div>
                    </div>
                </div>
            </div>
            <p class="money"><?php echo htmlentities($total_account); ?></p>
        </div>
        <div class="content">
            <div class="title ns-prompt-block">
                分站总可用余额（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            平台可转账给城市分站的剩余金额
                        </div>
                    </div>
                </div>
            </div>
            <p class="money"><?php echo htmlentities($website_info['account']); ?></p>
        </div>
        <div class="content">
            <div class="title ns-prompt-block">
                分站总转账（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            平台已实际转账给城市分站的总金额
                        </div>
                    </div>
                </div>
            </div>
            <p class="money"><?php echo htmlentities($website_info['account_withdraw']); ?></p>
        </div>
        <div class="content">
            <p class="title">分站店铺入驻总抽成(元)</p>
            <p class="money"><?php echo htmlentities($website_info['account_shop']); ?></p>
        </div>
        <div class="content">
            <p class="title">分站订单结算总抽成(元) </p>
            <p class="money"><?php echo htmlentities($website_info['account_order']); ?></p>
        </div>
    </div>
</div>
<?php endif; if($is_addon_fenxiao == 1): ?>
<div class="layui-card ns-card-common ns-card-brief">
    <div class="layui-card-header">
        <div>
            <span class="ns-card-title">分销概况</span>
        </div>
    </div>
    <div class="layui-card-body">

        <div class="content">
            <div class="title ns-prompt-block">
                分销订单总金额（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            分销商的订单总金额统计
                        </div>
                    </div>
                </div>
            </div>
            <p class="money"><?php echo htmlentities($fenxiao_order_money['real_goods_money']); ?></p>
        </div>
        <div class="content">
            <div class="title ns-prompt-block">
                分销总佣金（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            分销商累计总佣金统计
                        </div>
                    </div>
                </div>
            </div>
            <p class="money"><?php echo htmlentities($fenxiao_account); ?></p>
        </div>
        <div class="content">
            <div class="title ns-prompt-block">
                提现中佣金（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            分销商提现待审核佣金统计
                        </div>
                    </div>
                </div>
            </div>
            <p class="money"><?php echo htmlentities($account_data['account_withdraw_apply']); ?></p>
        </div>
        <div class="content">
            <div class="title ns-prompt-block">
                已提现佣金（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            分销商已提现的佣金统计
                        </div>
                    </div>
                </div>
            </div>
            <p class="money"><?php echo htmlentities($account_data['account_withdraw']); ?></p>
        </div>


    </div>
</div>
<?php endif; if($is_addon_supply > 0): ?>
<div class="layui-card ns-card-common ns-card-brief">
    <div class="layui-card-header">
        <div>
            <span class="ns-card-title">供应商概况</span>
        </div>
    </div>
    <div class="layui-card-body">
        <div class="content">
            <p class="title">结算总金额（元） <a href="<?php echo addon_url('supply://admin/supplysettlement/lists'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($supply_account['settlement']); ?></p>
        </div>
        <div class="content">
            <p class="title">入驻总费用（元）<a href="<?php echo addon_url('supply://admin/supplyaccount/openaccount'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($supply_account['fee']); ?></p>
        </div>
        <div class="content">
            <p class="title">总保证金（元）<a href="<?php echo addon_url('supply://admin/account/deposit'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($supply_account['account']); ?></p>
        </div>
        <div class="content">
            <p class="title">已提现金额 | 提现中金额（元）<a href="<?php echo addon_url('supply://admin/supplyaccount/withdraw'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($supply_account['withdraw']); ?> | <?php echo htmlentities($supply_account['withdraw_apply']); ?></p>
        </div>
        <div class="content">
            <p class="title">总可用余额（元）<a href="<?php echo addon_url('supply://admin/account/balance'); ?>" class="subhead ns-text-color">查看</a></p>
            <p class="money"><?php echo htmlentities($supply_account['account']); ?></p>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="layui-card ns-card-common ns-card-brief">
    <div class="layui-card-header">
        <div>
            <span class="ns-card-title">会员概况</span>
        </div>
    </div>
    <div class="layui-card-body">
        <?php if($is_memberwithdraw == 1): ?>
        <div class="content">
            <p class="title">会员可提现余额（元）</p>
            <p class="money"><?php echo htmlentities($member_balance_sum['balance_money']); ?></p>
        </div>
        <div class="content">
            <p class="title">会员已提现余额（元）</p>
            <p class="money"><?php echo htmlentities($member_balance_sum['balance_withdraw']); ?></p>
        </div>
        <div class="content">
            <p class="title">会员提现中余额（元）</p>
            <p class="money"><?php echo htmlentities($member_balance_sum['balance_withdraw_apply']); ?></p>
        </div>
        <div class="content">
            <p class="title">不可提现余额（元）</p>
            <p class="money"><?php echo htmlentities($member_balance_sum['balance']); ?></p>
        </div>

        <?php else: ?>
        <div class="content">
            <p class="title">会员总余额（元）</p>
            <p class="money"><?php echo htmlentities($member_balance); ?></p>
        </div>
        <?php endif; ?>

    </div>
</div>


			</div>
			
			<!-- 版权信息 -->
			<div class="ns-footer">
				<div class="ns-footer-img">
					<a href="#"><img style="-webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);-ms-filter: grayscale(100%);-o-filter: grayscale(100%);filter: grayscale(100%);filter: gray;" src="<?php if(!empty($copyright['logo'])): ?> <?php echo img($copyright['logo']); else: ?>http://saas.goodsceo.com/public/static/img/copyright_logo.png<?php endif; ?>" /></a>
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



</body>
</html>