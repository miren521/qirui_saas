<?php /*a:2:{s:72:"/www/wwwroot/saas.goodsceo.com/addon/printer/shop/view/template/add.html";i:1600312146;s:23:"app/shop/view/base.html";i:1600314240;}*/ ?>
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
.printer-box{display: flex;}
.printer-box > .layui-form{flex: 1;}
.printer-box .preview{width: 310px;margin: 0 20px;}
.printer-box .preview .layui-card-body{margin: 20px;padding: 0 10px;border: 1px solid #ededed;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;}
.printer-box .preview .layui-card-body div{font-size: 12px;color: #333;}
.printer-box .preview .layui-card-body div ~ div{border-top: 1px dashed #ededed ;}
.printer-box .preview .receipt-name{text-align: center;line-height: 40px;}
.printer-box .preview .shopping-name{line-height: 40px;font-size: 16px !important;text-align: center;}
.printer-box .preview .order-info, .printer-box .preview .goods-info, .printer-box .preview .price-info, .printer-box .preview .buyer-info, .printer-box .preview .shopping-info{padding: 8px 0;}
.printer-box .preview .order-info span{display: block;line-height: 2.5;}
.printer-box .preview .goods-info table{width: 100%;}
.printer-box .preview .goods-info table tr{line-height: 2.5;}
.printer-box .preview .goods-info table th{font-weight: normal;}
.printer-box .preview .price-info p{display: flex;line-height: 2.5;justify-content: space-between;}
.printer-box .preview .buyer-info span, .printer-box .preview .shopping-info span{display: block;line-height: 2;}
.printer-box .preview .buyer-message,.printer-box .preview .merchant-message{padding: 10px 0;line-height: 1.5;}
.preview .button-info{height: 40px;line-height: 40px;text-align: center}
.preview .shopping-code{text-align: center;}
.preview .shopping-code img{width: 100px;height: 100px;margin: 10px 0;}
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
			<li>管理员可以在此页添加打印模板</li>
		</ul>
	</div>
</div>

<div class="printer-box">
    <div class="layui-form ns-form">

        <div class="layui-card ns-card-common ns-card-brief">

            <div class="layui-card-header">
                <span class="ns-card-title">模板信息</span>
            </div>

            <div class="layui-card-body">

                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="required">*</span>模板名称：</label>
                    <div class="layui-input-block">
                        <input type="text"  name="template_name" lay-verify="required" autocomplete="off" class="layui-input ns-len-long">
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-card ns-card-common ns-card-brief">

            <div class="layui-card-header">
                <span class="ns-card-title">打印信息</span>
            </div>

            <div class="layui-card-body">

                <div class="layui-form-item">
                    <label class="layui-form-label">小票名称：</label>
                    <div class="layui-input-block">
                        <input type="text"  name="title" value="小票名称" lay-verify="required" autocomplete="off" class="layui-input ns-len-long">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">商城名称：</label>
                    <div class="layui-input-block">
                        <input type="radio" lay-filter="shop_name"  name="head" value="1" title="显示" checked autocomplete="off" class="layui-input ns-len-long">
                        <input type="radio" lay-filter="shop_name" name="head" value="0" title="不显示" autocomplete="off" class="layui-input ns-len-long">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">备注信息：</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="buy_notes" value="1"   lay-skin="primary" title="买家留言" checked="" lay-filter="buyer_message">
                        <!--<input type="checkbox" name="seller_notes" value="1"   lay-skin="primary" title="卖家留言" lay-filter="merchant_message">-->
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">买家信息：</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="buy_name" value="1" lay-filter="buyer_name"  lay-skin="primary" title="姓名" checked="">
                        <input type="checkbox" name="buy_mobile" value="1" lay-filter="buyer_phone"  lay-skin="primary" title="联系方式" checked="">
                        <input type="checkbox" name="buy_address" value="1" lay-filter="buyer_address"  lay-skin="primary" title="地址" checked="">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">商城信息：</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="shop_mobile" value="1" lay-filter="shopping_phone" lay-skin="primary" title="联系方式">
                        <input type="checkbox" name="shop_address" value="1" lay-filter="shopping_address" lay-skin="primary" title="地址">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">商城二维码：</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="shop_qrcode" value="1" lay-filter="shop_qrcode" lay-skin="primary" title="商城二维码" checked="">
                    </div>
                </div>

                <div class="layui-form-item qrcode_url">
                    <label class="layui-form-label"><span class="required">*</span>商城二维码链接：</label>
                    <div class="layui-input-block">
                        <input type="text"  name="qrcode_url" lay-verify="required" autocomplete="off" class="layui-input ns-len-long">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">底部信息：</label>
                    <div class="layui-input-block">
                        <input type="text"  name="bottom" value="谢谢惠顾，欢迎下次光临" lay-verify="required" placeholder="" autocomplete="off" class="layui-input ns-len-long">
                    </div>
                </div>


            </div>
        </div>

        <div class="ns-form-row">
            <button class="layui-btn ns-bg-color" lay-submit lay-filter="save">保存</button>
            <button class="layui-btn layui-btn-primary" onclick="back()">返回</button>
        </div>
    </div>

    <div class="preview">
        <div class="layui-card ns-card-common ns-card-brief">

            <div class="layui-card-header">
                <span class="ns-card-title">预览图</span>
            </div>

            <div class="layui-card-body">
                <div class="receipt-name">小票名称</div>
                <div class="shopping-name">商城名称</div>
                <div class="order-info">
                    <span>订单编号：ME20180702231831547866</span>
                    <span>支付方式：微信支付</span>
                </div>
                <div class="goods-info">
                    <table>
                        <tr>
                            <th align="left">商品名称</th>
                            <th>数量</th>
                            <th align="right">金额</th>
                        </tr>
                        <tr>
                            <td>男子运动外套</td>
                            <td>2</td>
                            <td align="right">￥388.00</td>
                        </tr>
                        <tr>
                            <td>白色跑步鞋</td>
                            <td>2</td>
                            <td align="right">￥1689.00</td>
                        </tr>
                        <tr>
                            <td>运费	</td>
                            <td colspan="2" align="right">￥0.00</td>
                        </tr>
                    </table>
                </div>
                <div class="price-info">
                    <p>
                        <span>订单原价</span>
                        <span>￥2010</span>
                    </p>
                    <p>
                        <span>优惠金额</span>
                        <span>￥2010</span>
                    </p>
                    <p>
                        <span>实付金额</span>
                        <span>￥2010</span>
                    </p>
                </div>
                <div class="buyer-message">
                    买家留言：物流很快
                </div>
                <div class="merchant-message layui-hide">
                    卖家留言：欢迎下次购买
                </div>
                <div class="buyer-info">
                    <span class="name">niushop</span>
                    <span class="phone">15135669878</span>
                    <span class="address">山西省 太原市 小店区 创业街</span>
                </div>
                <div class="shopping-info layui-hide">
                    <span class="phone layui-hide">4008867993 </span>
                    <span class="address layui-hide">山西省 太原市 小店区 创业街 世纪中心4单元1025</span>
                </div>
                <div class="shopping-code">
                    <img src="http://saas.goodsceo.com/app/shop/view/public/img/shopping_code.png" alt="">
                </div>
                <div class="button-info">谢谢惠顾，欢迎下次光临</div>
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

        form.on('checkbox(shop_qrcode)', function(data){

            var value = data.elem.checked;
            if(value == true){
                $(".preview .shopping-code").removeClass("layui-hide");
                $(".qrcode_url").show();
                $("input[name='qrcode_url']").attr("lay-verify", "required");
            }else{
                $(".preview .shopping-code").addClass("layui-hide");
                $(".qrcode_url").hide();
                $("input[name='qrcode_url']").attr("lay-verify", "");
            }
        });

        /**
         * 表单提交
         */
        form.on('submit(save)', function(data){

            if(repeat_flag) return;
            repeat_flag = true;

            var field = data.field;

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: ns.url("printer://shop/template/add"),
                data: field,
                async: false,
                success: function(res){
                    repeat_flag = false;

                    if (res.code == 0) {
                        layer.confirm('添加成功', {
                            title:'操作提示',
                            btn: ['返回列表', '继续添加'],
                            yes: function(){
                                location.href = ns.url("printer://shop/template/lists");
                            },
                            btn2: function() {
                                location.href = ns.url("printer://shop/template/add");
                            }
                        });
                    }else{
                        layer.msg(res.message);
                    }
                }
            })
        });


        /*
        * 效果图
        * */

        // 小票打印
        $("input[name='title']").bind("input propertychange",function(event){
            $(".preview .receipt-name").text($("input[name='title']").val());
        });

        //商城名称
        form.on('radio(shop_name)', function(data){
            if (parseInt(data.value)) $(".shopping-name").removeClass("layui-hide");
            else $(".shopping-name").addClass("layui-hide");
        });

        //买家留言
        form.on('checkbox(buyer_message)', function(data){
            if(data.elem.checked)
                $(".buyer-message").removeClass("layui-hide");
            else
                $(".buyer-message").addClass("layui-hide");
        });
        //卖家留言
        form.on('checkbox(merchant_message)', function(data){
            if(data.elem.checked)
                $(".merchant-message").removeClass("layui-hide");
            else
                $(".merchant-message").addClass("layui-hide");
        });

        var buyerName = true,buyerPhone=true,buyerAddress=true;
        //买家姓名
        form.on('checkbox(buyer_name)', function(data){
            buyerName = data.elem.checked;

            if(data.elem.checked)
                $(".buyer-info .name").removeClass("layui-hide");
            else
                $(".buyer-info .name").addClass("layui-hide");

            buyerFn();
        });
        //买家手机号
        form.on('checkbox(buyer_phone)', function(data){
            buyerPhone = data.elem.checked;

            if(data.elem.checked)
                $(".buyer-info .phone").removeClass("layui-hide");
            else
                $(".buyer-info .phone").addClass("layui-hide");

            buyerFn();
        });
        //买家地址
        form.on('checkbox(buyer_address)', function(data){
            buyerAddress= data.elem.checked;
            if(data.elem.checked)
                $(".buyer-info .address").removeClass("layui-hide");
            else
                $(".buyer-info .address").addClass("layui-hide");
            buyerFn();
        });

        function buyerFn() {
            if (!buyerName && !buyerPhone && !buyerAddress)
                $(".buyer-info").addClass("layui-hide");
            else
                $(".buyer-info").removeClass("layui-hide");
        }


        var shoppingPhone=false,shoppingAddress=false;
        //商家手机号
        form.on('checkbox(shopping_phone)', function(data){
            shoppingPhone = data.elem.checked;

            if(data.elem.checked)
                $(".shopping-info .phone").removeClass("layui-hide");
            else
                $(".shopping-info .phone").addClass("layui-hide");

            shoppingFn();
        });
        //商家地址
        form.on('checkbox(shopping_address)', function(data){
            shoppingAddress= data.elem.checked;

            if(data.elem.checked)
                $(".shopping-info .address").removeClass("layui-hide");
            else
                $(".shopping-info .address").addClass("layui-hide");
            shoppingFn();
        });

        function shoppingFn() {
            if (!shoppingPhone && !shoppingAddress)
                $(".shopping-info").addClass("layui-hide");
            else
                $(".shopping-info").removeClass("layui-hide");
        }

        // 底部信息
        $("input[name='bottom']").bind("input propertychange",function(event){
            $(".preview .button-info").text($("input[name='bottom']").val());
        });
    });

    function back() {
        location.href = ns.url("printer://shop/template/lists");
    }




</script>

</body>

</html>