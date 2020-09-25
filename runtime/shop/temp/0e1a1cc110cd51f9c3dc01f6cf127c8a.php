<?php /*a:2:{s:61:"/www/wwwroot/saas.goodsceo.com/app/shop/view/login/login.html";i:1600312148;s:54:"/www/wwwroot/saas.goodsceo.com/app/shop/view/base.html";i:1600314240;}*/ ?>
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
			baseUrl: "http://saas.goodsceo.com/",
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
	
<link rel="stylesheet" href="http://saas.goodsceo.com/app/shop/view/public/css/login.css">
<style>
	/* .login-body{background-image: url("<?php echo img('http://saas.goodsceo.com/app/shop/view/public/img/login/login_bg.png'); ?>"); background-size: cover;} */
</style>

</head>

<body>

<div class="layui-layout layui-layout-admin">
	<div class="apply-header">
		<div class="apply-header-box">
			<div class="apply-header-title">
				<a href="<?php echo url('shop/index/index'); ?>">
					<?php if(!(empty($website_info['logo']) || (($website_info['logo'] instanceof \think\Collection || $website_info['logo'] instanceof \think\Paginator ) && $website_info['logo']->isEmpty()))): ?>
					<img src="<?php echo img($website_info['logo']); ?>">
					<?php else: ?>
					<img src="http://saas.goodsceo.com/app/shop/view/public/img/shop_logo.png">
					<?php endif; ?>
					<span class="ns-text-color">B2B2C多商户店铺端</span>
				</a>
			</div>
			<span class="phone">联系电话：<?php echo htmlentities($website_info['web_phone']); ?> </span>
		</div>
	</div>
</div>
<div class="login-body">
	<div class="login-content">
		<h2>商家登录</h2>
<!--		<h3>登录之后可进入店铺或申请入驻</h3>-->
		<div class="layui-form">
			<div class="login-input login-info">
				<div class="login-icon">
					<img src="http://saas.goodsceo.com/app/shop/view/public/img/login/login_username.png" alt="">
				</div>
				<input type="text" name="username" lay-verify="userName" placeholder="请输入用户名" autocomplete="off" class="layui-input">
			</div>
			<div class="login-input login-info">
				<div class="login-icon">
					<img src="http://saas.goodsceo.com/app/shop/view/public/img/login/login_password.png" alt="">
				</div>
				<input type="password" name="password" lay-verify="password" placeholder="请输入密码" autocomplete="off" class="layui-input">
			</div>
			<?php if($shop_login == 1): ?>
			<div class="login-input login-verification">
				<input type="text" name="captcha" lay-verify="verificationCode" placeholder="请输入验证码" autocomplete="off" class="layui-input">
				<div class="login-verify-code-img">
					<img id='verify_img' src="<?php echo htmlentities($captcha['img']); ?>" alt='captcha' onclick="verificationCode()"/>
				</div>
			</div>
			<input type="hidden" name="captcha_id" value="<?php echo htmlentities($captcha['id']); ?>">
			<?php endif; ?>
			<button id="login_btn" type="button" class="layui-btn ns-bg-color ns-login-btn" lay-submit lay-filter="login">登录</button>
			<p class="operation-register">还没有成为我们的伙伴？<a href="javascript:;" class="ns-text-color" onclick="register()">&nbsp;申请入驻</a></p>
		</div>
	</div>

	<div class="ns-login-bottom">
		<a class="ns-footer-img" href="#"><img src="<?php if(!empty($copyright['logo'])): ?> <?php echo img($copyright['logo']); else: ?>http://saas.goodsceo.com/public/static/img/copyright_logo.png<?php endif; ?>" /></a>
		<p><?php if(!(empty($copyright['company_name']) || (($copyright['company_name'] instanceof \think\Collection || $copyright['company_name'] instanceof \think\Paginator ) && $copyright['company_name']->isEmpty()))): ?><?php echo htmlentities($copyright['company_name']); else: ?>上海牛之云网络科技有限公司<?php endif; if(!(empty($copyright['icp']) || (($copyright['icp'] instanceof \think\Collection || $copyright['icp'] instanceof \think\Paginator ) && $copyright['icp']->isEmpty()))): ?><a href=<?php echo htmlentities($copyright['copyright_link']); ?>>&nbsp;&nbsp;备案号<?php echo htmlentities($copyright['icp']); ?></a><?php endif; ?></p>
		<?php if(!(empty($copyright['gov_record']) || (($copyright['gov_record'] instanceof \think\Collection || $copyright['gov_record'] instanceof \think\Paginator ) && $copyright['gov_record']->isEmpty()))): ?><a class="gov-box" href=<?php echo htmlentities($copyright['gov_url']); ?>><img src="http://saas.goodsceo.com/app/shop/view/public/img/gov_record.png" alt="">公安备案<?php echo htmlentities($copyright['gov_record']); ?></a><?php endif; ?>
	</div>
</div>


<script type="text/javascript">
	var form, login_repeat_flag = false;
	/**
	 * 验证码
	 */
	function verificationCode(){
		$.ajax({
			type: "get",
			url: "<?php echo url('shop/login/captcha'); ?>",
			dataType: "JSON",
			async: false,
			success: function (res) {
				var data = res.data;
				$("#verify_img").attr("src",data.img);
				$("input[name='captcha_id']").val(data.id);
			}
		});
	}

	layui.use('form', function(){
		form = layui.form;
		form.render();

		/* 登录 */
		form.on('submit(login)', function(data) {

			if (login_repeat_flag) return;
			login_repeat_flag = true;

			$.ajax({
				type: "POST",
				dataType: "JSON",
				url: '<?php echo url("shop/login/login"); ?>',
				data: data.field,
				success: function(res) {

					if (res.code == 0) {
						layer.msg('登录成功',{anim: 5,time: 500},function () {
							window.location = ns.url('shop/index/index');
						});
					} else {
						layer.msg(res.message);
						login_repeat_flag = false;
						verificationCode();
					}

				}
			})
		});

		/**
		 * 表单验证
		 */
		form.verify({
			userName: function(value) {
				if (!value.trim()) {
					return "账号不能为空";
				}
			},
			password: function(value) {
				if (!value.trim()) {
					return "密码不能为空";
				}
			},
			verificationCode: function(value) {
				if (!value.trim()) {
					return "验证码不能为空";
				}
			}

		});
	});

	function register(){
		location.href = ns.url("shop/login/register");
	}
	
	$("body").on("blur",".login-content .login-input",function(){
		$(this).removeClass("login-input-select");
	});
	$("body").on("focus",".login-content .login-input",function(){
		$(this).addClass("login-input-select");
	});

	$(document).keydown(function (event) {
		if (event.keyCode == 13) {
			$(".ns-login-btn").trigger("click");
		}
	});
</script>

</body>

</html>