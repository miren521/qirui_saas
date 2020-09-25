<?php /*a:2:{s:64:"/www/wwwroot/saas.goodsceo.com/app/shop/view/user/user_list.html";i:1600312148;s:54:"/www/wwwroot/saas.goodsceo.com/app/shop/view/base.html";i:1600314240;}*/ ?>
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
	.ns-margin {margin-left: 5px;}
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
			
				
<div class="layui-collapse ns-tips">
	<div class="layui-colla-item">
		<h2 class="layui-colla-title">操作提示</h2>
		<ul class="layui-colla-content layui-show">
			<li>用户列表展示，admin为系统用户，不可进行编辑</li>
			<li>可以添加用户，用于登录、管理shop端后台</li>
			<li>可对用户进行编辑，修改用户状态，当用户状态为锁定时，不可进行登录</li>
			<li>添加时选择用户组，对用户的操作权限进行限制</li>
		</ul>
	</div>
</div>

<!-- 搜索框 -->
<div class="ns-single-filter-box">
	<button class="layui-btn ns-bg-color" onclick="add()">添加用户</button>

	<div class="layui-form">
		<div class="layui-input-inline">
			<input type="text" name="search_keys" placeholder="请输入用户名" autocomplete="off" class="layui-input">
			<button type="button" class="layui-btn layui-btn-primary" lay-filter="search" lay-submit>
				<i class="layui-icon">&#xe615;</i>
			</button>
		</div>
	</div>
</div>

<div class="layui-tab ns-table-tab" lay-filter="use_tab">
	<ul class="layui-tab-title">
		<li class="layui-this" lay-id="">全部</li>
		<li lay-id="1">正常</li>
		<li lay-id="0">锁定</li>
	</ul>
	<div class="layui-tab-content">
		<!-- 列表 -->
		<table id="user_list" lay-filter="user_list"></table>
	</div>
</div>

<!-- 操作 -->
<script type="text/html" id="action">
	<div class="ns-table-btn">
		{{#  if(d.is_admin == 1){ }}
		<span class="ns-cursor ns-text-color-dark-gray ns-margin">系统用户不可编辑</span>
		{{#  }else if(d.group_id == 2 || d.uid == <?php echo htmlentities($shop_member_id); ?>){ }}
		<a class="layui-btn" lay-event="reset_pass">重置密码</a>
		{{#  }else{ }}
		<a class="layui-btn" lay-event="edit">编辑</a>
		<a class="layui-btn" lay-event="delete">删除</a>
		<a class="layui-btn" lay-event="reset_pass">重置密码</a>
		{{#  }  }}
	</div>
</script>

<!-- 状态 -->
<script type="text/html" id="status">
	{{ d.status == 1 ? '正常' : '锁定'}}
</script>

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
	var table, form, laytpl, element, layer_pass, repeat_flag = false; //防重复标识
	layui.use(['form', 'laytpl', 'element'], function() {
		form = layui.form;
		laytpl = layui.laytpl;
		element = layui.element;
		form.render();
			

		table = new Table({
			elem: '#user_list',
			url: ns.url("shop/user/user"),
			cols: [
				[{
					field: 'username',
					title: '用户名',
					width: '20%',
					unresize: 'false'
				}, {
					field: 'group_name',
					title: '用户组',
					width: '15%',
					unresize: 'false'
				}, {
					field: 'login_ip',
					title: '最后登录IP',
					width: '18%',
					unresize: 'false'
				}, {
					field: 'login_time',
					title: '最后登录时间',
					width: '18%',
					unresize: 'false',
					templet: function(data) {
						return ns.time_to_date(data.login_time); //创建时间转换方法
					}
				}, {
					field: 'status',
					title: '用户状态',
					width: '12%',
					unresize: 'false',
					templet: '#status'
				}, {
					title: '操作',
					width: '17%',
					toolbar: '#action',
					unresize: 'false'
				}]
			]
		});
		
		
		//监听Tab切换，以改变地址hash值
		element.on('tab(use_tab)', function(){
		    table.reload({
		        page: {curr: 1},
		        where:{'status':this.getAttribute('lay-id')}
		    });
		});
		
		
		/**
		 * 监听工具栏操作
		 */
		table.tool(function(obj) {
			var data = obj.data;
			switch (obj.event) {
				case 'edit': //编辑
					location.href = ns.url("shop/user/editUser", {"uid": data.uid});
					break;
				case 'delete': //删除
					deleteUser(data.uid);
					break;
				case 'reset_pass': //重置密码
					resetPassword(data);
					break;
			}
		});
		
		
		/**
		 * 删除
		 */
		function deleteUser(uid) {
			if (repeat_flag) return false;
			repeat_flag = true;
			
			layer.confirm('确定要删除该用户吗?', function() {
				$.ajax({
					url: ns.url("shop/user/deleteUser"),
					data: {uid: uid},
					dataType: 'JSON',
					type: 'POST',
					success: function(res) {
						layer.msg(res.message);
						repeat_flag = false;
						
						if (res.code == 0) {
							table.reload();
						}
					}
				});
			}, function () {
				layer.close();
				repeat_flag = false;
			});
		}
		
		
		/**
		 * 重置密码
		 */
		function resetPassword(data) {
			laytpl($("#pass_change").html()).render(data, function(html) {
				layer_pass = layer.open({
					title: '重置密码',
					skin: 'layer-tips-class',
					type: 1,
					area: ['500px'],
					content: html,
				});
			});
		}
		
		form.on('submit(repass)', function(data) {
			if (repeat_flag) return false;
			repeat_flag = true;
			
			$.ajax({
				type: "POST",
				url: ns.url("shop/user/modifyPassword"),
				data: data.field,
				dataType: 'JSON',
				success: function(res) {
					layer.msg(res.message);
					repeat_flag = false;
		
					if (res.code == 0) {
						layer.closeAll('page');
					}
				}
			});
		});
		
		
		/**
		 * 搜索功能
		 */
		form.on('submit(search)', function(data){
			table.reload({
				page: {
					curr: 1
				},
				where: data.field
			});
		});
		
		
		/**
		 * 表单验证
		 */
		form.verify({
			repass: function(value) {
				if (value != $("input[name='password']").val()) {
					return "输入错误,两次密码不一致!";
				}
			}
		});
	});

	function add() {
		location.href = ns.url("shop/user/addUser");
	}
	
	function closePass() {
		layer.close(layer_pass);
	}
</script>

<!-- 重置密码弹框html -->
<script type="text/html" id="pass_change">
	<div class="layui-form" id="reset_pass">
		<div class="layui-form-item">
			<label class="layui-form-label mid"><span class="required">*</span>新密码</label>
			<div class="layui-input-block">
				<input type="password" id="new_pass" name="password" placeholder="请输入密码" class="layui-input ns-len-mid" lay-verify="required"  maxlength="18" autocomplete="off">
			</div>
		</div>
		
		<div class="layui-form-item">
			<label class="layui-form-label mid"><span class="required">*</span>确认新密码</label>
			<div class="layui-input-block">
				<input type="password" name="password" placeholder="请输入密码" lay-verify="repass" class="layui-input ns-len-mid" maxlength="18" autocomplete="off">
			</div>
			<div class="ns-word-aux mid">
				<p>请再一次输入密码，两次输入密码须一致</p>
			</div>
		</div>
		
		<div class="ns-form-row mid">
			<button class="layui-btn ns-bg-color" lay-submit lay-filter="repass">确定</button>
			<button class="layui-btn layui-btn-primary" onclick="closePass()">返回</button>
		</div>

		<input class="reset-pass-id" type="hidden" name="uid" value="{{d.uid}}"/>
	</div>
</script>

</body>

</html>