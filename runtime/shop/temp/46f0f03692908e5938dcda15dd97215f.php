<?php /*a:2:{s:67:"/www/wwwroot/saas.goodsceo.com/app/shop/view/account/dashboard.html";i:1600312148;s:54:"/www/wwwroot/saas.goodsceo.com/app/shop/view/base.html";i:1600314240;}*/ ?>
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
    .ns-card-common:first-of-type{margin-top: 0;}
    .layui-card-body{display: flex;justify-content: space-around;padding-bottom: 0 !important;padding-right: 50px !important;padding-left: 50px !important;flex-wrap: wrap;}
    .layui-card-body .content{width: 33.3%;display: flex;flex-direction: column;margin-bottom: 30px;justify-content: center;}
    .layui-card-body .money{font-size: 20px;color: #666;font-weight: bold;margin-top: 10px;max-width: 250px;}
    .layui-card-body .subhead{font-size: 12px;margin-left: 3px;cursor: pointer;}
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
			
				
<div class="layui-card ns-card-common ns-card-brief">
    <div class="layui-card-header">
        <div>
            <span class="ns-card-title">账户概况</span>
        </div>
    </div>
    <div class="layui-card-body">
        <div class="content">
            <p class="title">店铺总收入(元)</p>
            <p class="money"><?php echo htmlentities($total); ?></p>
        </div>
        <div class="content">
            <p class="title">可用余额(元) <span class="subhead withdrawal ns-text-color">提现</span></p>
            <p class="money"><?php echo htmlentities($account); ?></p>
        </div>
        <div class="content">
            <p class="title">待结算(元) <span class="subhead order-record ns-text-color">查看明细</span></p>
            <p class="money"><?php echo htmlentities($order_apply); ?></p>
        </div>
        <div class="content">
            <p class="title">入驻费用(元)</p>
            <p class="money"><?php echo htmlentities($shop['shop_open_fee']); ?></p>
        </div>
        <div class="content">
            <p class="title">店铺保证金(元)</p>
            <p class="money"><?php echo htmlentities($shop['shop_baozhrmb']); ?></p>
        </div>
        <div class="content">
            <p class="title">已提现/提现中(元) <span class="subhead withdrawal-record ns-text-color">提现记录</span></p>
            <p class="money"><?php echo htmlentities($account_withdraw); ?> / <?php echo htmlentities($account_withdraw_apply); ?></p>
        </div>
    </div>
</div>

<div class="layui-card ns-card-common ns-card-brief">
    <div class="layui-card-header">
        <div>
            <span class="ns-card-title">收支记录</span>
        </div>
    </div>
</div>

<!-- 筛选面板 -->
<div class="ns-single-filter-box">
    <div class="layui-form">
        <div class="layui-inline">
            <div class="layui-input-inline">
                <input type="text" name="start_time" id="start_time" placeholder="开始时间" class="layui-input" autocomplete="off" readonly>
                <i class="ns-calendar"></i>
            </div>
            <div class="layui-input-inline end-time">
                <input type="text" name="end_time" id="end_time" placeholder="结束时间" class="layui-input" autocomplete="off" readonly>
                <i class="ns-calendar"></i>
            </div>
            <button class="layui-btn layui-btn-primary" lay-submit lay-filter="search">搜索</button>
        </div>
    </div>
</div>

<div class="layui-tab ns-table-tab" lay-filter="goods_list_tab">
    <ul class="layui-tab-title">
        <li class="layui-this" data-status="">全部</li>
        <li data-status="1">收入</li>
        <li data-status="2">支出</li>
    </ul>
    <div class="layui-tab-content">
        <!-- 列表 -->
        <table id="account_list" lay-filter="account_list"></table>
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


<script type="text/html" id="account_data">
    {{#  if(d.account_data>0){ }}
    <span style="color: red; padding-right: 50px;">+{{d.account_data}}</span>
    {{#  } else if (d.account_data == 0){ }}
    <span style="padding-right: 50px;">{{d.account_data}}</span>
    {{#  } else { }}
    <span style="color: green; padding-right: 50px;">{{d.account_data}}</span>
    {{#  } }}
</script>

<script>
    var start_time,end_time,repeat_flag = false;

    layui.use(['element','laydate','laytpl','form'], function () {
        var element = layui.element,
            laydate = layui.laydate,
            laytpl = layui.laytpl,
            form = layui.form;
			
		form.render();
		
        //监听Tab切换，以改变地址hash值
        element.on('tab(goods_list_tab)', function () {
            table.reload({
                page: {
                    curr: 1
                },
                where: {
                    'type': this.getAttribute('data-status')
                }
            });
        });
        
        //开始时间
        laydate.render({
            elem: '#start_time' //指定元素
            ,done: function(value, date, endDate){
                start_time = ns.date_to_time(value);
            }
        });
        //结束时间
        laydate.render({
            elem: '#end_time' //指定元素
            ,done: function(value, date, endDate){
                end_time = ns.date_to_time(value);
            }
        });

        /**
         * 搜索功能
         */
        form.on('submit(search)', function (data) {
            data.field.start_time = start_time;
            data.field.end_time = end_time;
            table.reload({
                page: {
                    curr: 1
                },
                where: data.field
            });
            return false;
        });
        
        //提现
        $(".withdrawal").click(function (data) {

            laytpl($("#withdrawal").html()).render(data, function(html) {
                layer_pass = layer.open({
                    title: '提现',
                    skin: 'layer-tips-class',
                    type: 1,
                    area: ['600px'],
                    content: html,
                });
            });
        });

        //确认提现
        form.on('submit(withdrawal)', function(data) {
            if (repeat_flag) return false;
            repeat_flag = true;
            field = data.field;
            if(parseFloat(field.money) <= 0){
                layer.msg('您的可提现金额为0.00元，暂不能发起提现');
                repeat_flag = false;
                return;
            }
            if(parseFloat(field.apply_money) > parseFloat(field.money)){
                layer.msg('提现金额不能大于可提现金额');
                repeat_flag = false;
                return;
            }

            if(parseFloat(field.apply_money) < parseFloat(field.min_withdraw)){
                layer.msg('提现金额不能小于最低提现金额');
                repeat_flag = false;
                return;
            }
            if(parseFloat(field.apply_money) > parseFloat(field.max_withdraw)){
                layer.msg('提现金额不能大于最高提现金额');
                repeat_flag = false;
                return;
            }

            $.ajax({
                type: "POST",
                url: ns.url("shop/shopwithdraw/apply"),
                data: {apply_money:field.apply_money},
                dataType: 'JSON',
                success: function(res) {
                    if (res.code == 0) {
                        layer.closeAll();
                        layer.msg('提现申请已经发出，可以进入提现记录查看', {
                            time: 3000 //2秒关闭（如果不配置，默认是3秒）
                        }, function(){
                            location.href = ns.url("shop/account/dashboard");
                        });
                    }else{
                        layer.msg(res.message);
                    }
                }
            });
        });

    });

    var table = new Table({
        elem: '#account_list',
        url: ns.url("shop/account/dashboard"),
        cols: [
            [{
                field: 'account_no',
                title: '账单编号',
                unresize: 'false',
                width:'16%'
            },{
                field: 'type_name',
                title: '收支来源',
                unresize: 'false',
                width:'14%'

            },{
                field: 'account_data',
                title: '<span style="padding-right: 50px;">金额（元）</span>',
                unresize: 'false',
                templet: '#account_data',
                width:'15%',
				align: 'right'
            }, {
                title: '收支类型',
                unresize: 'false',
                width:'8%',
                templet: function (res){
                    return res.account_data >= 0 ? "收入" : "支出";
                }

            }, {
                field: 'create_time',
                title: '时间',
                unresize: 'false',
                width:'17%',
                templet: function (res){
                    if(res.create_time == 0){
                        return '--';
                    }else{
                        return ns.time_to_date(res.create_time)
                    }

                }
            }, {
                title: '说明',
                unresize: 'false',
                width:'30%',
                templet: function (res){
                    return '<span title="'+res.remark+'">'+res.remark+'</span>';
                }

            }]
        ]
    });

    //提现记录
    $(".withdrawal-record").click(function () {
        location.href = ns.url("shop/shopwithdraw/lists");
    });

    //交易记录
    $(".order-record").click(function () {
        location.href = ns.url("shop/account/orderlist");
    });
    
    function closePass() {
        layer.close(layer_pass);
    }
</script>

<script type="text/html" id="withdrawal">
    <div class="layui-form" id="reset_pass" lay-filter="form">

        <div class="layui-form-item">
            <label class="layui-form-label">提现金额：</label>
            <div class="layui-input-block">
                <input type="number" name="apply_money" lay-verify="required" class="layui-input ns-len-mid" min="0">
            </div>
            <div class="ns-word-aux">
                <p>最低提现：￥<?php echo htmlentities($shop_withdraw_config['min_withdraw']); ?>； 最高提现：￥<?php echo htmlentities($shop_withdraw_config['max_withdraw']); ?> </p>
                <input type="hidden" name="min_withdraw" value="<?php echo htmlentities($shop_withdraw_config['min_withdraw']); ?>">
                <input type="hidden" name="max_withdraw" value="<?php echo htmlentities($shop_withdraw_config['max_withdraw']); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">可提现金额：</label>
            <div class="layui-input-block">
                <p class="ns-input-text "><?php echo htmlentities($account); ?></p>
                <input type="hidden" name="money" value="<?php echo htmlentities($account); ?>">
            </div>
        </div>
        <?php if(!empty($shop_cert_info)): if($shop_cert_info['bank_type'] == 1): ?>
        <div class="layui-form-item">
            <label class="layui-form-label">提现账户类型：</label>
            <div class="layui-input-block ">
                    <p class="ns-input-text ">提现类型：银行卡</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">银行开户名：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text "><?php echo htmlentities($shop_cert_info['settlement_bank_account_name']); ?></p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">银行账号：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text "><?php echo htmlentities($shop_cert_info['settlement_bank_account_number']); ?></p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开户银行支行名称：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text "><?php echo htmlentities($shop_cert_info['settlement_bank_name']); ?></p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开户银行所在地：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text "><?php echo htmlentities($shop_cert_info['settlement_bank_address']); ?></p>
            </div>
        </div>
        <?php elseif($shop_cert_info['bank_type'] == 3): ?>
        <div class="layui-form-item">
            <label class="layui-form-label">提现账户类型：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text ">微信</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">微信昵称：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text "><?php echo htmlentities($shop_cert_info['settlement_bank_address']); ?></p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">微信号：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text "><?php echo htmlentities($shop_cert_info['settlement_bank_name']); ?></p>
            </div>
        </div>
        <?php else: ?>
        <div class="layui-form-item">
            <label class="layui-form-label">提现账户类型：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text ">支付宝</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">支付宝用户名：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text "><?php echo htmlentities($shop_cert_info['settlement_bank_account_name']); ?></p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">支付宝账户：</label>
            <div class="layui-input-block ">
                <p class="ns-input-text "><?php echo htmlentities($shop_cert_info['settlement_bank_account_number']); ?></p>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <div class="ns-form-row">
            <button class="layui-btn ns-bg-color" lay-submit lay-filter="withdrawal">确定</button>
            <button class="layui-btn layui-btn-primary" onclick="closePass()">返回</button>
        </div>

        <input class="reset-pass-id" type="hidden" name="member_ids" value="{{d.member_id}}"/>
    </div>
</script>

</body>

</html>