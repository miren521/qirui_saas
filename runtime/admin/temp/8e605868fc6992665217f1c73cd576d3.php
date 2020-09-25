<?php /*a:2:{s:57:"/www/wwwroot/city.lpstx.cn/app/admin/view/stat/index.html";i:1600312146;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
	
<style>
	.ns-table-tuwen-box .ns-font-box {
		overflow: inherit;
	}
	
    .ns-card-head-right {
        display: flex;
        align-items: center;
    }

    .ns-card-head-right .layui-form-item {
        margin-bottom: 0;
    }
	
	.current-time, .ns-flow-time-today {
		margin-left: 10px;
		line-height: 34px;
	}

	/* 基本信息 */
	.ns-basic {
		display: flex;
	}
	
	.ns-basic .ns-table-tuwen-box {
		width: 25%;
		border-right: 1px solid #EEEEEE;
		padding: 10px 20px;
	}
	
	.ns-basic-count {
		font-size: 20px;
		font-weight: 600;
	}
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
				
				
<div class="layui-card ns-card-common ns-card-brief">
	<div class="layui-card-header">
		<span class="ns-card-title">实时概况</span>
		
		<div class="ns-card-head-right layui-form">
			<div class="layui-form-item">
				<div class="layui-input-block ns-len-mid">
					<select name="date" lay-filter="date">
						<option value="0">今日实时</option>
						<option value="1">近7天</option>
						<option value="2">近30天</option>
					</select>
				</div>
			</div>
			<div class="ns-flow-time">
				<div class="ns-flow-time-today"></div>
			</div>
		</div>
    </div>
	
	<div class="layui-card-body ns-basic">
		<div class="ns-table-tuwen-box">
			<div class="ns-img-box">
				<img src="http://city.lpstx.cn/app/admin/view/public/img/stat/goods_browse.png">
			</div>
			<div class="ns-font-box">
				<p class="ns-text-color-dark-gray">商品浏览数</p>
				<p class="ns-basic-count" id="visitCount">0</p>
			</div>
		</div>
		<div class="ns-table-tuwen-box">
			<div class="ns-img-box">
				<img src="http://city.lpstx.cn/app/admin/view/public/img/stat/goods_collect.png">
			</div>
			<div class="ns-font-box">
				<div class="ns-text-color-dark-gray ns-prompt-block">
					商品收藏数&nbsp;&nbsp;
					<div class="ns-prompt">
						<i class="iconfont iconwenhao1"></i>
						<div class="ns-prompt-box">
							<div class="ns-prompt-con">
								用户收藏的商品总数
							</div>
						</div>
					</div>
				</div>
				<p class="ns-basic-count" id="collectGoods">0</p>
			</div>
		</div>
		<div class="ns-table-tuwen-box">
			<div class="ns-img-box">
				<img src="http://city.lpstx.cn/app/admin/view/public/img/stat/goods_order.png">
			</div>
			<div class="ns-font-box">
				<p class="ns-text-color-dark-gray">订单商品数</p>
				<p class="ns-basic-count" id="goodsPayCount">0</p>
			</div>
		</div>
        <div class="ns-table-tuwen-box">
            <div class="ns-img-box">
                <img src="http://city.lpstx.cn/app/admin/view/public/img/stat/shop_collect.png">
            </div>
            <div class="ns-font-box">
                <p class="ns-text-color-dark-gray">店铺收藏数</p>
                <p class="ns-basic-count" id="collectShop">0</p>
            </div>
        </div>
	</div>
    <div class="layui-card-body ns-basic">
        <div class="ns-table-tuwen-box">
            <div class="ns-img-box">
                <img src="http://city.lpstx.cn/app/admin/view/public/img/stat/order_money.png">
            </div>
            <div class="ns-font-box">
				<div class="ns-text-color-dark-gray ns-prompt-block">
					订单金额&nbsp;&nbsp;
					<div class="ns-prompt">
						<i class="iconfont iconwenhao1"></i>
						<div class="ns-prompt-box">
							<div class="ns-prompt-con">
								商城已付款的订单总金额
							</div>
						</div>
					</div>
				</div>
                <p class="ns-basic-count" id="orderTotal">0</p>
            </div>
        </div>
        <div class="ns-table-tuwen-box">
            <div class="ns-img-box">
                <img src="http://city.lpstx.cn/app/admin/view/public/img/stat/order_num.png">
            </div>
            <div class="ns-font-box">
                <p class="ns-text-color-dark-gray">订单数</p>
                <p class="ns-basic-count" id="orderPayCount">0</p>
            </div>
        </div>
        <div class="ns-table-tuwen-box">
            <div class="ns-img-box">
                <img src="http://city.lpstx.cn/app/admin/view/public/img/stat/member_num.png">
            </div>
            <div class="ns-font-box">
				<div class="ns-text-color-dark-gray ns-prompt-block">
					会员人数&nbsp;&nbsp;
					<div class="ns-prompt">
						<i class="iconfont iconwenhao1"></i>
						<div class="ns-prompt-box">
							<div class="ns-prompt-con">
								商城中存在的会员人数
							</div>
						</div>
					</div>
				</div>
                <p class="ns-basic-count" id="memberCount">0</p>
            </div>
        </div>
        <div class="ns-table-tuwen-box">
        </div>
    </div>
</div>

<div class="layui-card ns-card-common ns-card-brief">
    <div class="layui-card-header">
		<span class="ns-card-title">趋势分析</span>
		
		<div class="ns-card-head-right layui-form">
			<div class="layui-form-item">
				<div class="layui-input-block ns-len-mid">
					<select name="date" lay-filter="date_chart">
						<option value="1">近7天</option>
						<option value="2">近30天</option>
					</select>
				</div>
			</div>
			<span class="current-time"></span>
		</div>
    </div>
	<div class="layui-card-body ns-basic">
		<div id="main" style="width: 100%; height: 400px;"></div>
	</div>
</div>

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


<script src="http://city.lpstx.cn/app/shop/view/public/js/echarts.min.js"></script>
<script>
    $(function() {
        getStat(0);
        getStatChart(1);
    });

    layui.use(['form'], function() {
        var form = layui.form;

        /**
         * 监听下拉框
         */
        form.on('select(date)', function(data) {
            getStat(data.value);
        });

        /**
         * 图表监听下拉框
         */
        form.on('select(date_chart)', function(data) {
            getStatChart(data.value);
        });
    });

    //获取店铺统计
    function getStat(date_type){
        $.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: ns.url("admin/stat/index"),
            data: {
                date_type: date_type
            },
            success: function(res){
                console.log(res);
                if (res.code == 0) {
                    $("#collectShop").html(res.data.collect_shop);
                    $("#collectGoods").html(res.data.collect_goods);
                    $("#visitCount").html(res.data.visit_count);
                    $("#orderTotal").html(res.data.order_total);
                    $("#orderPayCount").html(res.data.order_pay_count);
                    $("#goodsPayCount").html(res.data.goods_pay_count);
                    $("#memberCount").html(res.data.member_count);
                    $(".ns-flow-time-today").html(res.data.time_range);
                }else{
                    layer.msg(res.message);
                }
            }
        });
    }

    //获取店铺统计图表
    function getStatChart(date_type){
        $.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: ns.url("admin/stat/getstatlist"),
            data: {
                date_type: date_type
            },
            success: function(res){
                console.log(res);
                option.xAxis.data = res.time;
                option.series[0].data = res.visit_count;
                option.series[1].data = res.collect_goods;
                option.series[2].data = res.goods_pay_count;
                option.series[3].data = res.collect_shop;
                option.series[4].data = res.order_total;
                option.series[5].data = res.order_pay_count;
                option.series[6].data = res.member_count;
                $(".current-time").html(res.time_range);
                myChart.setOption(option);
            }
        });
    }

    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('main'));

    // 指定图表的配置项和数据
    option = {
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: ['商品浏览数', '商品收藏数', '订单商品数', '店铺收藏数', '订单金额', '订单数', '会员人数']
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: []
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: '{value} 个'
            }
        },
        series: [{
                name: '商品浏览数',
                type: 'line',
                data: [],
            },
            {
                name: '商品收藏数',
                type: 'line',
                data: [],
            },
            {
                name: '订单商品数',
                type: 'line',
                data: [],
            },
            {
                name: '店铺收藏数',
                type: 'line',
                data: [],
            },
            {
                name: '订单金额',
                type: 'line',
                data: [],
            },
            {
                name: '订单数',
                type: 'line',
                data: [],
            },
            {
                name: '会员人数',
                type: 'line',
                data: [],
            }
        ]
    };

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
</script>

</body>
</html>