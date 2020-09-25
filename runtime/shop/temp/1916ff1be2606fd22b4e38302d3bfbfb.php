<?php /*a:2:{s:61:"/www/wwwroot/saas.goodsceo.com/app/shop/view/store/lists.html";i:1600312148;s:54:"/www/wwwroot/saas.goodsceo.com/app/shop/view/base.html";i:1600314240;}*/ ?>
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
	.ns-table-tab{margin-top: 0;}
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
			<li>当前页面对门店的信息进行管理，可以添加门店，管理门店等。</li>
		</ul>
	</div>
</div>
<!-- 搜索框 -->
<div class="ns-single-filter-box">
	<button class="layui-btn ns-bg-color" onclick="add()">添加门店</button>

	<div class="layui-form">
		<div class="layui-input-inline">
			<input type="text" name="keyword" placeholder="请输入门店名称" autocomplete="off" class="layui-input">
			<button type="button" class="layui-btn layui-btn-primary" lay-filter="search" lay-submit>
				<i class="layui-icon">&#xe615;</i>
			</button>
		</div>
	</div>
</div>

<div class="layui-tab ns-table-tab"  lay-filter="store_tab">
	<ul class="layui-tab-title">
		<li class="layui-this" lay-id="">全部门店</li>
		<li lay-id="1">已停业</li>
		<li lay-id="0">营业中</li>
	</ul>
	<div class="layui-tab-content">
		<!-- 列表 -->
		<table id="store_list" lay-filter="store_list"></table>
	</div>
</div>


<!-- 门店详情 -->
<script type="text/html" id="store">
	<div class="ns-table-title">
		<div class="ns-title-pic">
			{{#  if(d.store_image){  }}
			<img layer-src src={{ns.img(d.store_image)}} alt="">
			{{#  }else{  }}
			<img layer-src src="http://saas.goodsceo.com/public/static/img/default_store.png" alt="">
			{{#  }  }}
		</div>
		<div class="ns-title-content">
			<p class="layui-elip">店铺名称：{{d.store_name}}</p>
			<p class="layui-elip">联系方式：{{d.telphone}}</p>
		</div>
	</div>
</script>

<!-- 操作 -->
<script type="text/html" id="action">
	<div class="ns-table-btn">
		<a class="layui-btn" lay-event="edit">编辑</a>
		{{# if(d.is_frozen) { }}
		<a class="layui-btn" lay-event="frozen">开启</a>
		{{# } else{ }}
		<a class="layui-btn" lay-event="frozen">关闭</a>
		{{# } }}
		<a class="layui-btn" lay-event="delete">删除</a>

		<?php if(addon_is_exit('store')): ?>
		<a class="layui-btn" lay-event="reset_pass">重置密码</a>
		<?php endif; ?>
	</div>
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


<?php if(addon_is_exit('store')): ?>
<!-- 重置密码弹框html -->
<script type="text/html" id="pass_change">
	<div class="layui-form" id="reset_pass" lay-filter="form">

		<div class="layui-form-item">
			<label class="layui-form-label mid"><span class="required">*</span>新密码：</label>
			<div class="layui-input-block">
				<input type="password" name="password" lay-verify="required" class="layui-input ns-len-mid new_pass" maxlength="18">
			</div>
		</div>

		<div class="layui-form-item">
			<label class="layui-form-label mid"><span class="required">*</span>确认新密码：</label>
			<div class="layui-input-block">
				<input type="password" name="repassword" lay-verify="repass|required" class="layui-input ns-len-mid" maxlength="18">
			</div>
			<div class="ns-word-aux mid">请再一次输入密码，两次输入密码须一致</div>
		</div>

		<div class="ns-form-row mid">
			<button class="layui-btn ns-bg-color" lay-submit lay-filter="repass">确定</button>
			<button class="layui-btn layui-btn-primary" onclick="closePass()">返回</button>
		</div>

		<input class="reset-pass-id" type="hidden" name="store_id" value="{{d.store_id}}"/>
	</div>
</script>
<?php endif; ?>
<script>
	var layer_pass;
	layui.use(['form','laytpl','element'], function() {
		var table,
			form = layui.form,
            laytpl = layui.laytpl,
            element = layui.element,
			repeat_flag = false; //防重复标识
		form.render();

        //监听Tab切换，以改变地址hash值
        element.on('tab(store_tab)', function(){
            table.reload({
                page: {
                    curr: 1
                },
                where:{
                    'status':this.getAttribute('lay-id')
                }
            });
        });

		table = new Table({
			elem: '#store_list',
			url: ns.url("shop/store/lists"),
			cols: [
				[{
					title: '门店',
					unresize: 'false',
					width: '25%',
					templet: '#store'
				}, {
					field: 'full_address',
					title: '门店地址',
					unresize: 'false',
					width: '25%',
					templet: function(data) {
						return '<span title="'+data.full_address+data.address+'">'+data.full_address+data.address+'</span>'; //创建时间转换方法
					}
				},{
					title: '创建时间',
					unresize: 'false',
					width: '20%',
					templet: function(data) {
						return ns.time_to_date(data.create_time); //创建时间转换方法
					}
				}, {
					title: '门店状态',
					unresize: 'false',
					width: '20%',
					templet: function(data) {
						if (data.is_frozen == 1) {
							return '关闭';
						} else {
							return '正常';
						}
					}
				},{
					title: '操作',
					toolbar: '#action',
					unresize: 'false',
					width: '10%'
				}]
			]
		});

		/**
		 * 监听工具栏操作
		 */
		table.tool(function(obj) {
 			var data = obj.data;
			switch (obj.event) {
				case 'edit': //编辑
					location.href = ns.url("shop/store/editStore",{"store_id":data.store_id});
					break;
				case 'delete': //删除
					deleteCompany(data.store_id);
					break;
				case 'frozen'://冻结&解冻
					frozenStore(data.store_id, data.is_frozen);
					break;
				<?php if(addon_is_exit('store')): ?>
				case 'reset_pass': //重置密码
					resetPassword(data);
					break;
				<?php endif; ?>

			}
		});

		/**
		 * 删除
		 */
		function deleteCompany(store_id) {
			layer.confirm('门店已开始运营，确认要删除吗?', function() {
				$.ajax({
					url: ns.url("shop/store/deleteStore"),
					data: {store_id: store_id},
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
			});
		}

		/**
		 * 删除
		 */
		function frozenStore(store_id, is_frozen) {
			var msg = '门店已开始运营，确认要关闭吗?';
			if(is_frozen == 1) {
				msg = '确定要开启该门店吗？';
			}
			layer.confirm(msg, function() {
				$.ajax({
					url: ns.url("shop/store/frozenStore"),
					data: {store_id:store_id, is_frozen:is_frozen},
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
			});
		}
		
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
		});


		/**
		 * 重置密码
		 */
		function resetPassword(data) {
			laytpl($("#pass_change").html()).render(data, function(html) {
				layer_pass = layer.open({
					title: '重置密码',
					skin: 'layer-tips-class',
					type: 1,
					area: ['550px'],
					content: html,
				});
			});
		}

		/**
		 * 表单验证
		 */
		form.verify({
			repass: function(value) {
				if (value != $(".new_pass").val()) {
					return "输入错误,两次密码不一致!";
				}
			}
		});
		var repass_flag = false;
		form.on('submit(repass)', function(data) {

			if (repass_flag) return false;
			repass_flag = true;

			$.ajax({
				type: "POST",
				url: ns.url("shop/store/modifyPassword"),
				data: data.field,
				dataType: 'JSON',
				success: function(res) {
					layer.msg(res.message);
					repass_flag = false;
					if (res.code == 0) {
						layer.closeAll();
					}
				}
			});
		});

	});
	function closePass() {
		layer.close(layer_pass);
	}
	function add() {
		location.href = ns.url("shop/store/addStore");
	}
</script>

</body>

</html>