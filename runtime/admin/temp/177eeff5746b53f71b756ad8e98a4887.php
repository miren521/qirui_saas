<?php /*a:2:{s:62:"/www/wwwroot/city.lpstx.cn/app/admin/view/shopapply/apply.html";i:1600312146;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1" />
	<title><?php echo htmlentities((isset($menu_info['title']) && ($menu_info['title'] !== '')?$menu_info['title']:"")); ?> - <?php echo htmlentities((isset($website['title']) && ($website['title'] !== '')?$website['title']:"Niushop开源商城")); ?></title>
	<meta name="keywords" content="<?php echo htmlentities((isset($website['keywords']) && ($website['keywords'] !== '')?$website['keywords']:'Niushop开源商城')); ?>">
	<meta name="description" content="<?php echo htmlentities((isset($website['desc']) && ($website['desc'] !== '')?$website['desc']:'描述')); ?>}">
	<link rel="icon" type="image/x-icon" href="http://city.lpstx.cn/public/static/img/bitbug_favicon.ico" />
	<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/public/static/css/iconfont.css" />
	<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/public/static/ext/layui/css/layui.css" />
	<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/public/static/loading/msgbox.css"/>
	<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/app/admin/view/public/css/common.css" />
	<script src="http://city.lpstx.cn/public/static/js/jquery-3.1.1.js"></script>
	<script src="http://city.lpstx.cn/public/static/ext/layui/layui.js"></script>
	<script>
		layui.use(['layer', 'upload', 'element'], function() {});
		window.ns_url = {
			baseUrl: "http://city.lpstx.cn/",
			route: ['<?php echo request()->module(); ?>', '<?php echo request()->controller(); ?>', '<?php echo request()->action(); ?>'],
		};
	</script>
	<script src="http://city.lpstx.cn/public/static/js/common.js"></script>
	<style>
		.ns-calendar{background: url("http://city.lpstx.cn/public/static/img/ns_calendar.png") no-repeat center / 16px 16px;}
	</style>
	

	<script type="text/javascript">
	</script>
</head>
<body>

<!-- logo -->
<div class="ns-logo">
	<div class="logo-box">
		<img src="http://city.lpstx.cn/app/admin/view/public/img/logo.png">
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
						<img src="http://city.lpstx.cn/app/admin/view/public/img/default_headimg.png" alt="">
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
				
				
<div class="layui-collapse ns-tips">
	<div class="layui-colla-item">
		<h2 class="layui-colla-title">操作提示</h2>
		<ul class="layui-colla-content layui-show">
			<li>当前页面针对入驻申请进行管理，可以针入驻申请进行审核认证，审核支付操作。</li>
			<li>入驻流程：会员申请->审核认证信息->会员上传支付凭据->审核支付信息->审核通过后入驻。</li>
		</ul>
	</div>
</div>

<div class="ns-screen layui-collapse" lay-filter="selection_panel">
	<div class="layui-colla-item">
		<h2 class="layui-colla-title">筛选</h2>
		
		<?php if($is_addon_city == 1): ?>
		<form class="layui-colla-content layui-form layui-show">
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">主营行业：</label>
					<div class="layui-input-inline">
						<select name="category_id" lay-filter="category_id">
							<option value="">全部</option>
							<?php if(is_array($shop_category_list) || $shop_category_list instanceof \think\Collection || $shop_category_list instanceof \think\Paginator): $i = 0; $__LIST__ = $shop_category_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$category): $mod = ($i % 2 );++$i;?>
							<option value="<?php echo htmlentities($category['category_id']); ?>"><?php echo htmlentities($category['category_name']); ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
				</div>
				
				<div class="layui-inline">
					<label class="layui-form-label">开店套餐：</label>
					<div class="layui-input-inline">
						<select name="group_id" lay-filter="group_id">
							<option value="">全部</option>
							<?php if(is_array($shop_group_list) || $shop_group_list instanceof \think\Collection || $shop_group_list instanceof \think\Paginator): $i = 0; $__LIST__ = $shop_group_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?>
							<option value="<?php echo htmlentities($group['group_id']); ?>"><?php echo htmlentities($group['group_name']); ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
				</div>
				
				<div class="layui-inline">
					<label class="layui-form-label">分站城市：</label>
					<div class="layui-input-inline">
						<select name="website_id" lay-filter="" lay-search="">
							<option value="">请选择城市分站</option>
							<?php foreach($website_list as $website_k => $website_v): if($website_v['site_id'] == 0): ?>
							<option value="-1">无</option>
							<?php else: ?>
							<option value="<?php echo htmlentities($website_v['site_id']); ?>"><?php echo htmlentities($website_v['site_area_name']); ?></option>
							<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
			
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">店铺名称：</label>
					<div class="layui-input-inline">
						<input type="text" id="search_text" name="search_text" placeholder="店铺名称" autocomplete="off" class="layui-input">
					</div>
				</div>
				
				<div class="layui-inline">
					<label class="layui-form-label">申请账号：</label>
					<div class="layui-input-inline">
						<input type="text" id="search_text_user" name="search_text_user" placeholder="申请账号" autocomplete="off" class="layui-input">
					</div>
				</div>
			</div>
			
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">申请时间：</label>
					<div class="layui-input-inline">
					    <input type="text" class="layui-input" name="start_time"  id="start_time" autocomplete="off" placeholder="开始时间" readonly>
						<i class="ns-calendar"></i>
					</div>
					<div class="layui-form-mid">-</div>
					<div class="layui-input-inline">
					    <input type="text" class="layui-input" name="end_time" id="end_time" autocomplete="off" placeholder="结束时间" readonly>
						<i class="ns-calendar"></i>
					</div>
				</div>
			</div>
			
			<input type="hidden" name="apply_state" />
			
			<div class="ns-form-row">
				<button class="layui-btn ns-bg-color" lay-submit lay-filter="search_website">筛选</button>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
			</div>
		</form>
		<?php else: ?>
		<form class="layui-colla-content layui-form layui-show">
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">主营行业：</label>
					<div class="layui-input-inline">
						<select name="category_id" lay-filter="category_id">
							<option value="">请选择主营行业</option>
							<?php if(is_array($shop_category_list) || $shop_category_list instanceof \think\Collection || $shop_category_list instanceof \think\Paginator): $i = 0; $__LIST__ = $shop_category_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$category): $mod = ($i % 2 );++$i;?>
							<option value="<?php echo htmlentities($category['category_id']); ?>"><?php echo htmlentities($category['category_name']); ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
				</div>
				<div class="layui-inline">
					<label class="layui-form-label">开店套餐：</label>
					<div class="layui-input-inline">
						<select name="group_id" lay-filter="group_id">
							<option value="">请选择开店套餐</option>
							<?php if(is_array($shop_group_list) || $shop_group_list instanceof \think\Collection || $shop_group_list instanceof \think\Paginator): $i = 0; $__LIST__ = $shop_group_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?>
							<option value="<?php echo htmlentities($group['group_id']); ?>"><?php echo htmlentities($group['group_name']); ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
				</div>
			</div>
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">店铺名称：</label>
					<div class="layui-input-inline">
						<input type="text" id="search_text" name="search_text" placeholder="店铺名称" autocomplete="off" class="layui-input">
					</div>
				</div>
				
				<div class="layui-inline">
					<label class="layui-form-label">申请账号：</label>
					<div class="layui-input-inline">
						<input type="text" id="search_text_user" name="search_text_user" placeholder="申请账号" autocomplete="off" class="layui-input">
					</div>
				</div>
			</div>
			
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">申请时间：</label>
					<div class="layui-input-inline">
					    <input type="text" class="layui-input" name="start_time"  id="start_time" autocomplete="off" placeholder="开始时间" readonly>
						<i class="ns-calendar"></i>
					</div>
					<div class="layui-form-mid">-</div>
					<div class="layui-input-inline">
					    <input type="text" class="layui-input" name="end_time" id="end_time" autocomplete="off" placeholder="结束时间" readonly>
						<i class="ns-calendar"></i>
					</div>
				</div>
			</div>
			<input type="hidden" name="apply_state" />
			<div class="ns-form-row">
				<button class="layui-btn ns-bg-color" lay-submit lay-filter="search">筛选</button>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
			</div>
		</form>
		<?php endif; ?>
	</div>
</div>

<div class="layui-tab ns-table-tab" lay-filter="apply_list_tab">
	<ul class="layui-tab-title">
		<li class="layui-this" lay-id="">全部</li>
		<li lay-id="1" data-type="apply_state">待认证审核</li>
		<li lay-id="-1" data-type="apply_state">认证审核失败</li>
		<li lay-id="4" data-type="apply_state">待提交财务凭证</li>
		<li lay-id="2" data-type="apply_state">待财务审核</li>
		<li lay-id="3" data-type="apply_state">财务审核成功</li>
		<li lay-id="-2" data-type="apply_state">财务审核失败</li>
	</ul>
	<div class="layui-tab-content">
		<!-- 列表 -->
		<?php if($is_addon_city == 1): ?>
		<table id="apply_website_list" lay-filter="apply_website_list"></table>
		<?php else: ?>
		<table id="apply_list" lay-filter="apply_list"></table>
		<?php endif; ?>
	</div>
</div>

<input type="hidden" id="is_addon_city" value="<?php echo htmlentities($is_addon_city); ?>" />

<!-- 工具栏操作 -->
<script type="text/html" id="action">
	<div class="ns-table-btn">
		{{#  if(d.apply_state == 1){ }}
		<a class="layui-btn" lay-event="apply_pass">同意申请</a>
		<a class="layui-btn" id="apply_message" lay-event="apply_refuse">拒绝申请</a>
		<a class="layui-btn" lay-event="apply_detail">认证信息</a>
		{{#  } }}
		{{#  if(d.apply_state == -1){ }}
		<a class="layui-btn" lay-event="apply_detail">认证信息</a>
		{{#  } }}
		{{#  if(d.apply_state == 2){ }}
			{{#  if(d.paying_money_certificate){ }}
			<a class="layui-btn" lay-event="edit_apply">支付信息</a>
			{{# } }}
		<a class="layui-btn" lay-event="apply_detail">认证信息</a>
		{{#  } }}
		{{#  if(d.apply_state == 3){ }}
		<a class="layui-btn" lay-event="apply_detail">认证信息</a>
		<a class="layui-btn" lay-event="edit_apply">支付信息</a>
		{{#  } }}
		{{#  if(d.apply_state == -2){ }}
		<a class="layui-btn" lay-event="edit_apply">财务拒绝</a>
		<a class="layui-btn" lay-event="apply_detail">认证信息</a>
		{{#  } }}
	</div>
</script>

<!-- 申请状态 -->
<script type="text/html" id="apply_state_name">
	{{#  if(d.apply_state == -1){ }}
	<span style="color: gray;">认证审核失败</span>
	{{#  } }}
	{{#  if(d.apply_state == 1){ }}
	<span style="color: red;">待认证审核</span>
	{{#  } }}
	{{#  if(d.apply_state == 2){ }}
		{{#  if(d.paying_money_certificate){ }}
		<span style="color: blue;">待财务审核</span>
		{{#  }else{ }}
		<span style="color: blue;">待提交财务凭证</span>
		{{#  } }}
	{{#  } }}
	{{#  if(d.apply_state == 3){ }}
	<span style="color: green;">财务审核成功</span>
	{{#  } }}
	{{#  if(d.apply_state == -2){ }}
	<span style="color: gray;">财务审核失败</span>
	{{#  } }}
</script>

			</div>
			
			<!-- 版权信息 -->
			<div class="ns-footer">
				<div class="ns-footer-img">
					<a href="#"><img style="-webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);-ms-filter: grayscale(100%);-o-filter: grayscale(100%);filter: grayscale(100%);filter: gray;" src="<?php if(!empty($copyright['logo'])): ?> <?php echo img($copyright['logo']); else: ?>http://city.lpstx.cn/public/static/img/copyright_logo.png<?php endif; ?>" /></a>
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
	var element;
	layui.use(['form', 'layer', 'laydate', 'element'], function() {
		var table, table_website,
			form = layui.form,
			layer = layui.layer,
			element = layui.element,
			laydate = layui.laydate;
		form.render();

		//渲染时间
		laydate.render({
			elem: '#start_time',
			type: 'datetime'
		});
		
		laydate.render({
			elem: '#end_time',
			type: 'datetime'
		});
		
		
		//监听Tab切换，以改变地址hash值
		element.on('tab(apply_list_tab)', function () {
			var is_addon_city = $("#is_addon_city").val();
			if (is_addon_city == 1) {
				var type = this.getAttribute('data-type');
				if (type) {
					var id = this.getAttribute('lay-id');
					$("input[name='" + type + "']").val(id);
					$("button[lay-filter='search_website']").click()
				} else {
					$("input[name='apply_state']").val("");
					$("button[lay-filter='search_website']").click()
				}
			} else {
				var type = this.getAttribute('data-type');
				if (type) {
					var id = this.getAttribute('lay-id');
					$("input[name='" + type + "']").val(id);
					$("button[lay-filter='search']").click()
				} else {
					$("input[name='apply_state']").val("");
					$("button[lay-filter='search']").click()
				}
			}
		});
		
		/**
		 * 表格加载
		 */
		table = new Table({
			elem: '#apply_list',
			url: ns.url("admin/shopapply/apply"),
			parseData: function(res) { //res 即为原始返回的数据
				return {
					"code": res.code, //解析接口状态
					"msg": res.message, //解析提示文本
					"count": res.data.count, //解析数据长度
					"data": res.data.list //解析数据列表
				};
			},
			cols: [
				[ //表头
					{
						width:'12%',
						field: 'shop_name',
						title: '店铺名称',
						unresize: 'false',
					}, {
						width:'10%',
						field: 'group_name',
						title: '开店套餐',
						unresize: 'false',
					}, {
						width:'10%',
						field: 'category_name',
						title: '主营行业',
						unresize: 'false',
					}, {
						width:'10%',
						field: 'username',
						title: '申请账号',
						unresize: 'false',
					}, {
						width:'8%',
						field: 'apply_year',
						title: '入驻时长',
						unresize: 'false',
						templet: function(data) {
							return data.apply_year+'年';
						},
					}, {
						width:'13%',
						field: 'paying_amount',
						title: '<span title="入驻费用" style="padding-right: 15px;">入驻费用</span>',
						unresize: 'false',
						align: 'right',
						templet: function(data) {
							return '<span title="'+ data.paying_amount +'" style="padding-right: 15px;">￥'+ data.paying_amount +'</span>';
						},
					}, {
						width:'9%',
						field: 'apply_state_name',
						title: '申请状态',
						unresize: 'false',
						templet: '#apply_state_name',
					},  {
						width:'13%',
						field: 'create_time',
						title: '申请时间',
						unresize: 'false',
						templet: function(data) {
							return ns.time_to_date(data.create_time);
						},
					}, {
						width:'15%',
						title: '操作',
						toolbar: '#action',
						unresize: 'false'
					}
				]
			]
		});
		
		// 有城市分站
		table_website = new Table({
			elem: '#apply_website_list',
			url: ns.url("city://admin/shop/apply"),
			parseData: function(res) { //res 即为原始返回的数据
				return {
					"code": res.code, //解析接口状态
					"msg": res.message, //解析提示文本
					"count": res.data.count, //解析数据长度
					"data": res.data.list //解析数据列表
				};
			},
			cols: [
				[ //表头
					{
						width:'12%',
						field: 'shop_name',
						title: '店铺名称',
						unresize: 'false',
					}, {
						width:'9%',
						field: 'group_name',
						title: '开店套餐',
						unresize: 'false',
					}, {
						width:'9%',
						field: 'category_name',
						title: '主营行业',
						unresize: 'false',
					}, {
						width:'10%',
						field: 'username',
						title: '申请账号',
						unresize: 'false',
					}, {
						width:'7%',
						field: 'apply_year',
						title: '入驻时长',
						unresize: 'false',
						templet: function(data) {
							return data.apply_year+'年';
						},
					}, {
						width:'7%',
						field: 'site_area_name',
						title: '城市分站',
						unresize: 'false',
						templet: function(data) {
							return data.site_area_name == '全国' ? '--' : data.site_area_name;
						},
					}, {
						width:'10%',
						field: 'paying_amount',
						title: '<span title="入驻费用" style="padding-right: 15px;">入驻费用</span>',
						unresize: 'false',
						align: 'right',
						templet: function(data) {
							return '<span title="'+ data.paying_amount +'" style="padding-right: 15px;">￥'+ data.paying_amount +'</span>';
						},
					}, {
						width:'8%',
						field: 'apply_state_name',
						title: '申请状态',
						unresize: 'false',
						templet: '#apply_state_name',
					},  {
						width:'13%',
						field: 'create_time',
						title: '申请时间',
						unresize: 'false',
						templet: function(data) {
							return ns.time_to_date(data.create_time);
						},
					}, {
						width:'15%',
						title: '操作',
						toolbar: '#action',
						unresize: 'false'
					}
				]
			]
		});
		
		
		/**
		 * 搜索功能
		 */
		form.on('submit(search)', function(data) {
			table.reload({
				page: {
					curr: 1
				},
				where: data.field
			});
			return false;
		});
		
		// 有城市分站
		form.on('submit(search_website)', function(data) {
			table_website.reload({
				page: {
					curr: 1
				},
				where: data.field
			});
			return false;
		});
		
		
		/**
		 * 监听工具栏操作
		 */
		table.tool(function(obj) {
			var data = obj.data,
				event = obj.event;
			switch (event){  
				case 'apply_pass':  //审核通过
					applyPass(data);
					break;
				case 'apply_refuse':  //拒绝
					applyRefuse(data);
					break;
				case 'apply_detail':  //认证信息
					location.href = ns.url("admin/shopapply/applyDetail?apply_id=" +data.apply_id)
					break;
				case 'open_shop':  //入驻通过
					openShop(data);
					break;
				case 'edit_apply' : //修改资料
					location.href = ns.url("admin/shopapply/editApply?apply_id=" +data.apply_id)
					break;
			}
		});
		
		// 有城市分站
		table_website.tool(function(obj) {
			var data = obj.data,
				event = obj.event;
			switch (event){  
				case 'apply_pass':  //审核通过
					applyPass(data);
					break;
				case 'apply_refuse':  //拒绝
					applyRefuse(data);
					break;
				case 'apply_detail':  //认证信息
					location.href = ns.url("admin/shopapply/applyDetail?apply_id=" +data.apply_id)
					break;
				case 'open_shop':  //入驻通过
					openShop(data);
					break;
				case 'edit_apply' : //修改资料
					location.href = ns.url("admin/shopapply/editApply?apply_id=" +data.apply_id)
					break;
			}
		});
		
		
		// 通过审核
		function applyPass(data) {
			layer.confirm('确定要通过审核吗?', function() {
				$.ajax({
					url: ns.url("admin/shopapply/applyPass"),
					data: {
						"apply_id":data.apply_id,
					},
					dataType: 'JSON', //服务器返回json格式数据
					type: 'POST', //HTTP请求类型
					success: function(res) {
						layer.msg(res.message);
						if (res.code == 0) {
							var is_addon_city = $("#is_addon_city").val();
							if (is_addon_city == 1) {
								table_website.reload();
							} else {
								table.reload();
							}
						}
					}
				});
			});
		}
		
		// 拒绝通过
		function applyRefuse(data) {
			/**
			 * 拒绝理由弹窗
			 */
			layer.prompt({
				formType: 2,
				value: '',
				title: '请输入拒绝理由',
				area: ['300px', '100px'] ,//自定义文本域宽高
				yes: function(index, layero){
					// 获取文本框输入的值
					var value = layero.find(".layui-layer-input").val();
					if (value) {
						$.ajax({
							url: ns.url("admin/shopapply/applyReject"),
							data: {
								"apply_id":data.apply_id,
								"reason":value
							},
							dataType: 'JSON', //服务器返回json格式数据
							type: 'POST', //HTTP请求类型
							success: function(res) {
								layer.msg(res.message);
								if (res.code == 0) {
									var is_addon_city = $("#is_addon_city").val();
									if (is_addon_city == 1) {
										table_website.reload();
									} else {
										table.reload();
									}
								}
							}
						});
						layer.close(index);
					} else {
						layer.msg('请输入拒绝原因!', {icon: 5, anim: 6});
						return;
					}
				}
			}); 
		}
		
		// 入住通过
		function openShop(data) {
			layer.confirm('确定要通过他的入驻申请吗?', function() {
				$.ajax({
					url: ns.url("admin/shopapply/openShop"),
					data: {
						"apply_id":data.apply_id,
					},
					dataType: 'JSON', //服务器返回json格式数据
					type: 'POST', //HTTP请求类型
					success: function(res) {
						layer.msg(res.message);
						if (res.code == 0) {
							var is_addon_city = $("#is_addon_city").val();
							if (is_addon_city == 1) {
								table_website.reload();
							} else {
								table.reload();
							}
						}
					}
				});
			});
		}
	});
	
	function clickAdd() {
		location.href = ns.url("admin/shopapply/addShop");
	}
</script>

</body>
</html>