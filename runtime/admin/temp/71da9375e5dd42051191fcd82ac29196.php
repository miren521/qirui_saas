<?php /*a:2:{s:58:"/www/wwwroot/city.lpstx.cn/app/admin/view/login/login.html";i:1600312146;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
	
<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/app/admin/view/public/css/login.css" />

	<script type="text/javascript">
	</script>
</head>
<body>

<div class="layui-container">
	<div class="layui-form login-form">
		<div class="ns-login-logo">
			<img src="http://city.lpstx.cn/app/admin/view/public/img/login/login_logo.png" />
		</div>
		<div class="layui-form-title">
			<h1>多商户后台登录系统</h1>
		</div>

		<div class="layui-form-item">
			<img class="ns-input-icon" src="http://city.lpstx.cn/app/admin/view/public/img/login/username.png" />
			<input type="text" name="username" lay-verify="userName" placeholder="请输入用户名" autocomplete="off" class="layui-input">
		</div>
		<div class="layui-form-item">
			<img class="ns-input-icon" src="http://city.lpstx.cn/app/admin/view/public/img/login/password.png" />
			<input type="password" name="password" lay-verify="password" placeholder="请输入密码" autocomplete="off" class="layui-input">
		</div>

		<?php if($admin_login == 1): ?>
		<div class="layui-form-item verify-code-box">
			<input type="text" name="captcha" lay-verify="verificationCode" placeholder="请输入验证码" class="layui-input" value="">
			<div class="verify-code-img">
				<img id='verify_img' src="<?php echo htmlentities($captcha['img']); ?>" alt='captcha' onclick="verificationCode()"/>
			</div>
		</div>
		<input type="hidden" name="captcha_id" value="<?php echo htmlentities($captcha['id']); ?>">
		<?php endif; ?>

		<div class="layui-form-item ns-login-btn">
			<button class="layui-btn layui-btn-fluid ns-bg-color" lay-submit lay-filter="login">登 录</button>
		</div>
	</div>
	
	<div class="ns-login-bottom">
		<?php if(!empty($copyright['copyright_desc'])): ?> <?php echo htmlentities($copyright['copyright_desc']); else: ?>版权所有 © 2019-2020 山西牛酷信息科技有限公司，并保留所有权利<?php endif; ?>
	</div>
</div>


<script>
	layui.use('form', function() {
		var form = layui.form,
			repeat_flag = false; //防重复标识

		/**
		 * 登录
		 */
		form.on('submit(login)', function(data) {

			if (repeat_flag) return false;
			repeat_flag = true;

			$.ajax({
				type: "POST",
				url: "<?php echo url('admin/login/login'); ?>",
				data: data.field,
				dataType: "JSON",
				success: function(res) {
					if (res.code == 0) {
						layer.msg('登录成功',{anim: 5,time: 500},function () {
							window.location = "<?php echo url('admin/index/index'); ?>";
						})
					} else {
						layer.msg(res.message);
						repeat_flag = false;
						verificationCode();
					}
				}
			});
		});

		$(document).keydown(function(event) {
			if (event.keyCode == 13) {
				$(".ns-login-btn button").trigger("click");
			}
		});

		/**
		 * 表单验证
		 */
		form.verify({
			userName: function(value) {
				if (!value) {
					return "用户名不能为空";
				}
			},
			password: function(value) {
				if (!value) {
					return "密码不能为空";
				}
			},
			verificationCode: function(value) {
				if (!value) {
					return "验证码不能为空";
				}
			}

		});
	});
	
	/**
	 * 验证码
	 */
	function verificationCode(){
		$.ajax({
			type: "get",
			url: "<?php echo url('admin/login/captcha'); ?>",
			dataType: "JSON",
			async: false,
			success: function (res) {
				var data = res.data;
				$("#verify_img").attr("src",data.img);
				$("input[name='captcha_id']").val(data.id);
			}
		});
	}
</script>

</body>
</html>