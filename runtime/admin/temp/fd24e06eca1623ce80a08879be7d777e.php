<?php /*a:2:{s:67:"/www/wwwroot/city.lpstx.cn/app/admin/view/shopsettlement/lists.html";i:1600312146;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
    .ns-card-brief:nth-child(1){
        margin-top: 0;
    }
    .layui-card-body{
        display: flex;
        flex-wrap: wrap;
        padding-bottom: 0 !important;
        padding-left: 50px !important;
        padding-right: 50px !important;
    }
    .layui-card-body .content{
        width: 33.3%;
        margin-bottom: 30px;
        display: flex;
        flex-wrap: wrap;
        flex-direction: column;
        justify-content: center;
    }
    .layui-card-body .money{
        font-size: 20px;
        color: #000;
        font-weight: bold;
        margin-top: 10px;
        max-width: 250px;
    }
    .layui-card-body .subhead{
        font-size: 12px;
        margin-left: 3px;
        cursor: pointer;
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
        <div>
            <span class="ns-card-title">店铺结算</span>
        </div>
    </div>
    <div class="layui-card-body">
        <div class="content">
            <div class="title ns-prompt-block">
                店铺结算金额（元）
                <div class="ns-prompt">
                    <i class="iconfont iconwenhao1"></i>
                    <div class="ns-prompt-box">
                        <div class="ns-prompt-con">
                            店铺结算金额是平台按周期结算店铺订单金额后，扣除抽成后的店铺收入部分
                        </div>
                    </div>
                </div>
            </div>
            <p class="money"><?php echo htmlentities($shop_settlement); ?></p>
        </div>
        <div class="content">
            <p class="title">平台总抽成（元）</p>
            <p class="money"><?php echo htmlentities($platform_money); ?></p>
        </div>

        <?php if($is_addon_city == 1): ?>
        <div class="content">
            <p class="title">分站总抽成（元）</p>
            <p class="money"><?php echo htmlentities($website_commission); ?></p>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- 搜索框 -->
<div class="ns-single-filter-box">
    <div class="layui-form">
        <div class="layui-inline">
            <div class="layui-input-inline">
                <label class="layui-form-label">账期截止时间：</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input" placeholder="开始时间" name="start_date" id="start_time" autocomplete="off" readonly>
                </div>
                <div class="layui-input-inline end-item">
                    <input type="text" class="layui-input" placeholder="结束时间" name="end_date" id="end_time" autocomplete="off" readonly>
                </div>
            </div>
        </div>
        <div class="layui-input-inline">
            <button type="button" class="layui-btn layui-btn-primary" lay-filter="search" lay-submit>
                <i class="layui-icon">&#xe615;</i>
            </button>
        </div>
    </div>
</div>

<!-- 列表 -->
<table id="period_account_list" lay-filter="period_account_list"></table>

<!--账期开始时间-->
<script type="text/html" id="start_times">
    <div class="layui-elip">
        {{#  if(d.period_start_time == 0){ }}
            --
        {{#  } else { }}
            {{ns.time_to_date(d.period_start_time)}}
        {{#  } }}
    </div>
</script>

<!--账期结束时间-->
<script type="text/html" id="end_times">
    <div class="layui-elip">{{ns.time_to_date(d.period_end_time)}}</div>
</script>

<!--创建时间-->
<script type="text/html" id="times">
    <div class="layui-elip">{{ns.time_to_date(d.create_time)}}</div>
</script>

<!--店铺金额-->
<script type="text/html" id="shop_money">
    <div class="layui-elip" style="color: green">￥{{d.shop_money_actual}}</div>
</script>

<!--平台抽成-->
<script type="text/html" id="platform_money">
    <div class="layui-elip" style="color: red">￥{{d.money_actual}}</div>
</script>
<!--分站抽成-->
<script type="text/html" id="website_money">
    <div class="layui-elip" style="color: red">￥{{d.website_commission}}</div>
</script>
<!--操作-->
<script type="text/html" id="action">
    <div class="ns-table-btn">
        <a class="layui-btn" lay-event="basic">查看</a>
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
        var table,
            form = layui.form,
            laydate = layui.laydate,
            currentDate = new Date(),
            minDate = "";
        form.render();

        currentDate.setDate(currentDate.getDate() - 7);

        //开始时间
        laydate.render({
            elem: '#start_time',
            type: 'datetime'
        });

        //结束时间
        laydate.render({
            elem: '#end_time',
            type: 'datetime'
        });

        /**
         * 重新渲染结束时间
         */
        function reRender(){
            $("#end_time").remove();
            $(".end-time").html('<input type="text" class="layui-input" placeholder="申请结束时间" name="end_date" id="end_time" >');
            laydate.render({
                elem: '#end_time',
                type: 'datetime',
                min: minDate
            });
        }

        /**
         * 表格加载
         */
        table = new Table({
            elem: '#period_account_list',
            url: ns.url("admin/shopsettlement/lists"),
            cols: [
                [{
                    field: 'period_no',
                    title: '账单编号',
                    unresize: 'false',
                    width: '10%',
					templet: function(data) {
						return '<span title="'+ data.period_no +'">'+ data.period_no +'</span>';
					}
                },{
                    field: 'shop_num',
                    title: '店铺数',
                    unresize: 'false',
                    width: '6%',
                    align: 'right',
                },{
                    field: 'order_money',
                    title: '订单总额',
                    unresize: 'false',
                    width: '10%',
                    align: 'right',
					templet: function(data) {
						return '￥'+ data.order_money;
					}
                },{
                    title: '店铺结算金额',
                    unresize: 'false',
                    templet: '#shop_money',
                    width: '8%',
                    align: 'right'
                },{
                    title: '平台总抽成',
                    unresize: 'false',
                    templet: '#platform_money',
                    width: '8%',
                    align: 'right',
                },{
                    title: '分站抽成',
                    unresize: 'false',
                    templet: '#website_money',
                    width: '8%',
                    align: 'right',
                    hide: <?php if($is_addon_city == 1): ?> false <?php else: ?> true <?php endif; ?>
                },{
                    title: '账期开始时间',
                    unresize: 'false',
                    templet: '#start_times',
                    width: '14%',
                },{
                    title: '账期截止时间',
                    unresize: 'false',
                    templet: '#end_times',
                    width: '14%',
                },{
                    title: '结算时间',
                    unresize: 'false',
                    templet: '#times',
                    width: '15%',
                }, {
                    title: '操作',
                    toolbar: '#action',
                    unresize: 'false',
                    width: '7%',
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
        
        /**
         * 监听工具栏操作
         */
        table.tool(function(obj) {
            var data = obj.data,
                event = obj.event;

            switch (obj.event) {
                case 'basic': //查看
                    location.href = ns.url("admin/shopsettlement/detail?period_id=" + data.period_id)
                    break;
            }
        })
    });
</script>

</body>
</html>