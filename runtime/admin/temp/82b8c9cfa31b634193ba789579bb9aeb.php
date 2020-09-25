<?php /*a:2:{s:57:"/www/wwwroot/city.lpstx.cn/app/admin/view/shop/lists.html";i:1600312146;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
			<li>当前页面对店铺的信息进行管理，可以添加店铺，查看店铺账户信息，认证信息等。</li>
		</ul>
	</div>
</div>

<div class="ns-single-filter-box">
   <button class="layui-btn ns-bg-color" onclick="clickAdd()">添加店铺</button>
</div>

<div class="ns-screen layui-collapse" lay-filter="selection_panel">
	<div class="layui-colla-item">
		<h2 class="layui-colla-title">筛选</h2>

		<?php if($is_addon_city == 1): ?>
		<form class="layui-colla-content layui-form layui-show">
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">店铺名称：</label>
					<div class="layui-input-inline">
						<input type="text" id="search_text" name="search_text" placeholder="请输入店铺名称" class="layui-input">
					</div>
				</div>

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
			</div>
			<div class="layui-form-item">

				<div class="layui-inline">
					<label class="layui-form-label">店铺状态：</label>
					<div class="layui-input-inline">
						<select name="shop_status" lay-filter="status">
							<option value="">全部</option>
							<option value="1">正常</option>
							<option value="0">已关闭</option>
						</select>
					</div>
				</div>

				<div class="layui-inline">
					<label class="layui-form-label">是否认证：</label>
					<div class="layui-input-inline">
						<select name="cert_id" lay-filter="">
							<option value="">全部</option>
							<option value="2">是</option>
							<option value="1">否</option>
						</select>
					</div>
				</div>

				<div class="layui-inline">
					<label class="layui-form-label">是否自营：</label>
					<div class="layui-input-inline">
						<select name="is_own" lay-filter="status">
							<option value="">全部</option>
							<option value="1">是</option>
							<option value="0">否</option>
						</select>
					</div>
				</div>
			</div>

			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">到期时间：</label>
					<!-- <div class="layui-input-inline">
						<input type="text" class="layui-input" name="expire_time" id="expire_time" autocomplete="off" >
					</div> -->
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

				<div class="layui-inline">
					<label class="layui-form-label">城市分站：</label>
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

			<div class="ns-form-row">
				<button class="layui-btn ns-bg-color" lay-submit lay-filter="search_website">筛选</button>
				<button class="layui-btn ns-bg-color" lay-submit lay-filter="export">导出</button>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
			</div>
		</form>
		<?php else: ?>
		<form class="layui-colla-content layui-form layui-show">
			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">店铺名称：</label>
					<div class="layui-input-inline">
						<input type="text" id="search_text" name="search_text" placeholder="请输入店铺名称" class="layui-input">
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
					<label class="layui-form-label">店铺状态：</label>
					<div class="layui-input-inline">
						<select name="shop_status" lay-filter="status">
							<option value="">全部</option>
							<option value="1">正常</option>
							<option value="0">已关闭</option>
						</select>
					</div>
				</div>
			</div>
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
					<label class="layui-form-label">是否认证：</label>
					<div class="layui-input-inline">
						<select name="cert_id" lay-filter="">
							<option value="">全部</option>
							<option value="2">是</option>
							<option value="1">否</option>
						</select>
					</div>
				</div>
				<div class="layui-inline">
					<label class="layui-form-label">是否自营：</label>
					<div class="layui-input-inline">
						<select name="is_own" lay-filter="status">
							<option value="">全部</option>
							<option value="1">是</option>
							<option value="0">否</option>
						</select>
					</div>
				</div>
			</div>

			<div class="layui-form-item">
				<div class="layui-inline">
					<label class="layui-form-label">到期时间：</label>
					<!-- <div class="layui-input-inline">
						<input type="text" class="layui-input" name="expire_time" id="expire_time" autocomplete="off" >
					</div> -->
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

			<div class="ns-form-row">
				<button class="layui-btn ns-bg-color" lay-submit lay-filter="search">筛选</button>
				<button class="layui-btn ns-bg-color" lay-submit lay-filter="export">导出</button>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
			</div>
		</form>
		<?php endif; ?>
	</div>
</div>

<!-- 列表 -->
<?php if($is_addon_city == 1): ?>
<table id="shop_website_list" lay-filter="shop_website_list"></table>
<?php else: ?>
<table id="shop_list" lay-filter="shop_list"></table>
<?php endif; ?>

<!-- 是否自营 -->
<script type="text/html" id="is_own">
	{{ d.is_own == 1 ? '是' : '否' }}
</script>

<!-- 状态 -->
<script type="text/html" id="status">
	{{ d.shop_status == 1 ? '正常' : '关闭' }}
</script>

<!-- 工具栏操作 -->
<script type="text/html" id="action">
	<div class="ns-table-btn">
		<a class="layui-btn" lay-event="basic">店铺管理</a>
		<!-- <a class="layui-btn" lay-event="basic">基本信息</a> -->
		<a class="layui-btn" lay-event="identify">认证信息</a>
		<!-- <a class="layui-btn" lay-event="settlement">银行账户</a> -->
		<!-- <a class="layui-btn" lay-event="account">账户信息</a> -->
	</div>
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
	layui.use(['form', 'laydate'], function() {
		var table, table_website,
			form = layui.form,
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
		
		/**
		 * 渲染表格
		 */
		table = new Table({
			elem: '#shop_list',
			url: ns.url("admin/shop/lists"),
			cols: [
				[{
					field: 'site_name',
					title: '店铺名称',
					width: '12%',
					unresize: 'false',
					// templet: '<div><div class="layui-elip">店铺名称：{{d.site_name}}<div class="layui-elip">卖家账号：{{d.username}}</div>'
				}, {
					field: 'username',
					title: '商家账号',
					width: '8%',
					unresize: 'false'
				}, {
					field: 'group_name',
					title: '开店套餐',
					width: '10%',
					unresize: 'false'
				}, {
					field: 'category_name',
					title: '主营行业',
					width: '8%',
					unresize: 'false'
				}, {
					field: 'is_own',
					title: '是否自营',
					width: '8%',
					unresize: 'false',
					templet: '#is_own'
				}, {
					field: 'cert_id',
					title: '店铺认证',
					width: '8%',
					unresize: 'false',
					templet: function(data) {
						return data.cert_id == 0 ? '<span style="color: red">未认证</span>' : '<span style="color: green">已认证</span>';
					}
				}, {
					field: 'shop_status',
					title: '店铺状态',
					width: '8%',
					templet: '#status',
					unresize: 'false'
				}, {
					field: 'create_time',
					title: '入驻时间',
					width: '12%',
					unresize: 'false',
					templet: function(data) {
						return ns.time_to_date(data.create_time);
					}
				}, {
					field: 'expire_time',
					title: '到期时间',
					width: '12%',
					unresize: 'false',
					templet: function(data) {
						return ns.time_to_date(data.expire_time);
					}
				}, {
					title: '操作',
					width: '12%',
					toolbar: '#action',
					unresize: 'false'
				}]
			]
		});
		
		// 有城市分站
		table_website = new Table({
			elem: '#shop_website_list',
			url: ns.url("city://admin/shop/lists"),
			cols: [
				[{
					field: 'site_name',
					title: '店铺名称',
					width: '12%',
					unresize: 'false',
				}, {
					field: 'username',
					title: '商家账号',
					width: '8%',
					unresize: 'false'
				}, {
					field: 'group_name',
					title: '开店套餐',
					width: '10%',
					unresize: 'false'
				}, {
					field: 'category_name',
					title: '主营行业',
					width: '8%',
					unresize: 'false'
				}, {
					field: 'is_own',
					title: '是否自营',
					width: '8%',
					unresize: 'false',
					templet: '#is_own'
				}, {
					field: 'site_area_name',
					title: '城市分站',
					width: '8%',
					unresize: 'false',
					templet: function(data) {
						return data.site_area_name == '全国' ? '--' : data.site_area_name;
					}
				}, {
					field: 'cert_id',
					title: '店铺认证',
					width: '7%',
					unresize: 'false',
					templet: function(data) {
						return data.cert_id == 0 ? '<span style="color: red">未认证</span>' : '<span style="color: green">已认证</span>';
					}
				}, {
					field: 'shop_status',
					title: '店铺状态',
					width: '7%',
					templet: '#status',
					unresize: 'false'
				}, {
					field: 'create_time',
					title: '入驻时间',
					width: '10%',
					unresize: 'false',
					templet: function(data) {
						return ns.time_to_date(data.create_time);
					}
				}, {
					field: 'expire_time',
					title: '到期时间',
					width: '10%',
					unresize: 'false',
					templet: function(data) {
						return ns.time_to_date(data.expire_time);
					}
				}, {
					title: '操作',
					width: '10%',
					toolbar: '#action',
					unresize: 'false'
				}]
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
		
		// 城市分站
		form.on('submit(search_website)', function(data) {
			table_website.reload({
				page: {
					curr: 1
				},
				where: data.field
			});
			return false;
		});


        //批量导出
        form.on('submit(export)', function(data){
            data.field.order_type = 1;
            location.href = ns.url("admin/shop/exportShop",data.field);
            return false;
        });

		/**
		 * 监听工具栏操作
		 */
		table.tool(function(obj) {
			var data = obj.data,
					event = obj.event;
			switch (event) {
				case 'basic': //基本信息
					location.href = ns.url("admin/shop/basicInfo" + "?site_id=" + data.site_id);
					break;
				case 'identify': //认证信息
					location.href = ns.url("admin/shop/certInfo" + "?site_id=" + data.site_id)
					break;
				// case 'settlement': //结算信息
				// 	location.href = ns.url("admin/shop/settlementInfo" + "?site_id=" + data.site_id)
				// 	break;
				// case 'account': //账户信息
				// 	location.href = ns.url("admin/shop/accountInfo" + "?site_id=" + data.site_id)
				// 	break;
			}
		});
		
		table_website.tool(function(obj) {
			var data = obj.data,
					event = obj.event;
			switch (event) {
				case 'basic': //基本信息
					location.href = ns.url("admin/shop/basicInfo" + "?site_id=" + data.site_id);
					break;
				case 'identify': //认证信息
					location.href = ns.url("admin/shop/certInfo" + "?site_id=" + data.site_id)
					break;
				// case 'settlement': //结算信息
				// 	location.href = ns.url("admin/shop/settlementInfo" + "?site_id=" + data.site_id)
				// 	break;
				// case 'account': //账户信息
				// 	location.href = ns.url("admin/shop/accountInfo" + "?site_id=" + data.site_id)
				// 	break;
			}
		});
	});

	function clickAdd() {
		location.href = ns.url("admin/shop/addShop");
	}
</script>

</body>
</html>