<?php /*a:2:{s:71:"/www/wwwroot/saas.goodsceo.com/addon/printer/shop/view/printer/add.html";i:1600312146;s:23:"app/shop/view/base.html";i:1600314240;}*/ ?>
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

    .yilianyun {
        display:none
    }
    .feie{
        display:none
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
			
				
<div class="layui-collapse ns-tips">
	<div class="layui-colla-item">
		<h2 class="layui-colla-title">操作提示</h2>
		<ul class="layui-colla-content layui-show">
			<li>管理员可以在此页添加小票打印机</li>
		</ul>
	</div>
</div>

<div class="layui-form ns-form">

	<div class="layui-card ns-card-common ns-card-brief">

		<div class="layui-card-body">

			<div class="layui-form-item">
				<label class="layui-form-label"><span class="required">*</span>打印机名称：</label>
				<div class="layui-input-block">
					<input type="text"  name="printer_name" lay-verify="required" autocomplete="off" class="layui-input ns-len-long">
				</div>
			</div>

			<div class="layui-form-item express_company">
				<label class="layui-form-label"><span class="required">*</span>打印机品牌：</label>
				<div class="layui-input-block ns-len-short">
					<select name="brand" lay-verify="required" lay-filter="brand">
                        <?php foreach($brand as $k=>$v): ?>
                        <option value="<?php echo htmlentities($v['brand']); ?>"><?php echo htmlentities($v['name']); ?></option>
                        <?php endforeach; ?>
					</select>
				</div>
			</div>

            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span>打印机编号：</label>
                <div class="layui-input-block">
                    <input type="text"  name="printer_code" lay-verify="required" autocomplete="off" class="layui-input ns-len-long">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span>打印机秘钥：</label>
                <div class="layui-input-block">
                    <input type="text"  name="printer_key" lay-verify="required" autocomplete="off" class="layui-input ns-len-long">
                </div>
            </div>

            <!-- 飞鹅打印机 -->
            <div class="layui-form-item feie">
                <label class="layui-form-label"><span class="required">*</span>USER：</label>
                <div class="layui-input-block">
                    <input type="text"  name="user" autocomplete="off" class="layui-input ns-len-long">
                </div>
                <div class="ns-word-aux">
                    <p>飞鹅云后台注册用户名</p>
                </div>
            </div>
            <div class="layui-form-item feie">
                <label class="layui-form-label"><span class="required">*</span>UKEY：</label>
                <div class="layui-input-block">
                    <input type="text"  name="ukey" autocomplete="off" class="layui-input ns-len-long">
                </div>
                <div class="ns-word-aux">
                    <p>飞鹅云后台登录生成的UKEY</p>
                </div>
            </div>

            <!-- 易联云打印机 -->
            <div class="layui-form-item yilianyun">
                <label class="layui-form-label"><span class="required">*</span>应用id：</label>
                <div class="layui-input-block">
                    <input type="text"  name="open_id" autocomplete="off" class="layui-input ns-len-long">
                </div>
                <div class="ns-word-aux">
                    <p>应用id（易联云-开发者中心后台应用中心里获取）</p>
                </div>
            </div>
            <div class="layui-form-item yilianyun">
                <label class="layui-form-label"><span class="required">*</span>apiKey：</label>
                <div class="layui-input-block">
                    <input type="text"  name="apikey" autocomplete="off" class="layui-input ns-len-long">
                </div>
                <div class="ns-word-aux">
                    <p>apiKey（易联云-开发者中心后台应用中心里获取）</p>
                </div>
            </div>

            <div class="layui-form-item express_company">
                <label class="layui-form-label"><span class="required">*</span>打印模板：</label>
                <div class="layui-input-block ns-len-short">
                    <select name="template_id" lay-verify="" lay-filter="brand">
                        <option value="">请选择</option>
                        <?php foreach($template_list as $k=>$v): ?>
                        <option value="<?php echo htmlentities($v['template_id']); ?>"><?php echo htmlentities($v['template_name']); ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span>打印联数：</label>
                <div class="layui-input-block">
                    <input type="radio"  name="print_num" value="1" lay-verify="required" checked autocomplete="off" title="1" class="layui-input ns-len-long">
                    <input type="radio"  name="print_num" value="2" lay-verify="required" autocomplete="off" title="2" class="layui-input ns-len-long">
                    <input type="radio"  name="print_num" value="3" lay-verify="required" autocomplete="off" title="3" class="layui-input ns-len-long">
                    <input type="radio"  name="print_num" value="4" lay-verify="required" autocomplete="off" title="4" class="layui-input ns-len-long">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span>订单类型：</label>
                <div class="layui-input-block">
                    <?php foreach($order_type_list as $v): ?>
                    <input class="order-type" type="checkbox" value="<?php echo htmlentities($v['type']); ?>" lay-verify="required" lay-skin="primary" title="<?php echo htmlentities($v['name']); ?>" checked="">
                    <?php endforeach; ?>

                </div>
            </div>

		</div>
	</div>

	<div class="ns-form-row">
		<button class="layui-btn ns-bg-color" lay-submit lay-filter="save">保存</button>
        <button class="layui-btn layui-btn-primary" onclick="testPrint()">返回</button>
		<button class="layui-btn layui-btn-primary" onclick="back()">返回</button>
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

    var brand = $('select[name="brand"] option:selected').val();
    if(brand == '365'){
        $('.feie').hide();
        $('.yilianyun').hide();

        $("input[name='user']").attr("lay-verify", "");
        $("input[name='ukey']").attr("lay-verify", "");

        $("input[name='open_id']").attr("lay-verify", "");
        $("input[name='apikey']").attr("lay-verify", "");
    }

    if(brand == 'feie'){
        $('.feie').show();
        $('.yilianyun').hide();

        $("input[name='user']").attr("lay-verify", "required");
        $("input[name='ukey']").attr("lay-verify", "required");

        $("input[name='open_id']").attr("lay-verify", "");
        $("input[name='apikey']").attr("lay-verify", "");
    }

    if(brand == 'yilianyun'){
        $('.yilianyun').show();
        $('.feie').hide();

        $("input[name='open_id']").attr("lay-verify", "required");
        $("input[name='apikey']").attr("lay-verify", "required");

        $("input[name='user']").attr("lay-verify", "");
        $("input[name='ukey']").attr("lay-verify", "");
    }
    layui.use(['form', 'laydate'], function() {
        var form = layui.form,
            laydate = layui.laydate,
            repeat_flag = false;

		form.render();

        /**
         * 表单验证
         */
        form.verify({
            time: function(value) {
                var now_time = (new Date()).getTime();
                var start_time = (new Date($("#start_time").val())).getTime();
                var end_time = (new Date(value)).getTime();
                if (now_time > end_time) {
                    return '结束时间不能小于当前时间!'
                }
                if (start_time > end_time) {
                    return '结束时间不能小于开始时间!';
                }
            },
            flnum: function(value) {
                var arrMen = value.split(".");
                var val = 0;
                if (arrMen.length == 2) {
                    val = arrMen[1];
                }
                if (val.length > 2) {
                    return '保留小数点后两位！'
                }
            },
            int: function(value) {
                if (value <= 1 || value % 1 != 0) {
                    return '请输入大于1的正整数！'
                }
            }
        });

        form.on('select(brand)', function(data){

            var value = data.value;

            if(value == '365'){
                $('.feie').hide();
                $('.yilianyun').hide();

                $("input[name='user']").attr("lay-verify", "");
                $("input[name='ukey']").attr("lay-verify", "");

                $("input[name='open_id']").attr("lay-verify", "");
                $("input[name='apikey']").attr("lay-verify", "");
            }

            if(value == 'feie'){
                $('.feie').show();
                $('.yilianyun').hide();

                $("input[name='user']").attr("lay-verify", "required");
                $("input[name='ukey']").attr("lay-verify", "required");

                $("input[name='open_id']").attr("lay-verify", "");
                $("input[name='apikey']").attr("lay-verify", "");
            }

            if(value == 'yilianyun'){
                $('.yilianyun').show();
                $('.feie').hide();

                $("input[name='open_id']").attr("lay-verify", "required");
                $("input[name='apikey']").attr("lay-verify", "required");

                $("input[name='user']").attr("lay-verify", "");
                $("input[name='ukey']").attr("lay-verify", "");
            }
        });

        /**
         * 表单提交
         */
        form.on('submit(save)', function(data){

//            if(repeat_flag) return;
//            repeat_flag = true;

            var field = data.field;
            if(field.brand == 'feie'){
                field.open_id = field.user;
                field.apikey = field.ukey;
            }

            var order_type_arr = [];
            $(".order-type").each(function () {
                if ($(this).is(":checked")) {
                    order_type_arr.push($(this).val());
                }
            });
            if(order_type_arr == ""){
                layer.msg('请选择要打印的订单类型');
                return false;
            }

            field.order_type = order_type_arr.toString();

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: ns.url("printer://shop/printer/add"),
                data: field,
                async: false,
                success: function(res){
//                    repeat_flag = false;

                    if (res.code == 0) {
                        layer.confirm('添加成功', {
                            title:'操作提示',
                            btn: ['返回列表', '继续添加'],
                            yes: function(){
                                location.href = ns.url("printer://shop/printer/lists");
                            },
                            btn2: function() {
                                location.href = ns.url("printer://shop/printer/add");
                            }
                        });
                    }else{
                        layer.msg(res.message);
                    }
                }
            })
        });
    });



    function back() {
        location.href = ns.url("printer://shop/printer/lists");
    }
</script>

</body>

</html>