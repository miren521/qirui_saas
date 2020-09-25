<?php /*a:2:{s:73:"/www/wwwroot/city.lpstx.cn/app/admin/view/shop_account/withdraw_list.html";i:1600312146;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
            <span class="ns-card-title">店铺提现</span>
        </div>
    </div>
    <div class="layui-card-body">
        <div class="content">
            <p class="title">已提现金额（元）</p>
            <p class="money"><?php echo htmlentities($shop_sum['data']['account_withdraw']); ?></p>
        </div>
        <div class="content">
            <p class="title">提现中金额（元）</p>
            <p class="money"><?php echo htmlentities($shop_sum['data']['account_withdraw_apply']); ?></p>
        </div>
    </div>
</div>

<div class="ns-single-filter-box">
	<div class="layui-form">
		<div class="layui-inline">
			<div class="layui-input-inline">
				<div class="layui-input-inline">
					<input type="text" class="layui-input" placeholder="开始时间" name="start_date" id="start_time" autocomplete="off" readonly>
				</div>
				<div class="layui-input-inline end-item">
					<input type="text" class="layui-input" placeholder="结束时间" name="end_date" id="end_time" autocomplete="off" readonly>
				</div>
			</div>
		</div>
		<div class="layui-input-inline">
			<input type="text" name="search_text" placeholder="请输入店铺名称" autocomplete="off" class="layui-input">
			<button type="button" class="layui-btn layui-btn-primary" lay-filter="search" lay-submit>
				<i class="layui-icon">&#xe615;</i>
			</button>
		</div>
	</div>
</div>

<input id="period_id" type="hidden" value="<?php echo htmlentities($period_id); ?>" />

<div class="layui-tab ns-table-tab" lay-filter="status">
	<ul class="layui-tab-title">
		<li class="layui-this" lay-id="">全部</li>
		<li lay-id="0">待审核</li>
		<li lay-id="1">待转账</li>
		<li lay-id="2">已转账</li>
	</ul>
	
	<div class="layui-tab-content">
		<!-- 列表 -->
		<table id="shop_withdraw_list" lay-filter="shop_withdraw_list"></table>
	</div>
</div>

<!--商家信息-->
<script type="text/html" id="shop_info">
    <div class="layui-elip" title="{{d.site_name}}">{{d.site_name}}</div>
<!--    <div class="layui-elip">联系人：{{d.name}}</div>-->
<!--    <div class="layui-elip">联系电话：{{d.mobile}}</div>-->
</script>

<!--账户信息-->
<script type="text/html" id="account">
    {{# if(d.bank_type == 1){ }}
    <div class="layui-elip">银行卡</div>
    {{# }else if(d.bank_type == 2){ }}
    <div class="layui-elip">支付宝</div>
    {{# }else if(d.bank_type == 3){ }}
    <div class="layui-elip">微信</div>
    {{# }else{ }}
    <div class="layui-elip">提现异常</div>
    {{# } }}
    
</script>

<!--时间-->
<script type="text/html" id="times">
    <div class="layui-elip" title="申请时间：{{ns.time_to_date(d.apply_time)}}">
        申请时间：{{ns.time_to_date(d.apply_time)}}</div>
    <div class="layui-elip" title="转账时间：{{ns.time_to_date(d.payment_time)}}">
        转账时间：{{ns.time_to_date(d.payment_time)}}</div>
</script>

<!--状态-->
<script type="text/html" id="status">
    {{# if(d.status == 0){ }}
    <div class="layui-elip" style="color: red">待审核</div>
    {{# }else if(d.status == 1){ }}
    <div class="layui-elip" style="color: blue">待转账</div>
    {{# }else if(d.status == 2){ }}
    <div class="layui-elip" style="color: green">已转账</div>
    {{# } }}
</script>

<!--操作-->
<script type="text/html" id="action">
    <div class="ns-table-btn">
        {{# if(d.status == 0){ }}
        <a class="layui-btn" lay-event="apply_pass">通过</a>
        {{# }else if(d.status == 1){ }}
        <a class="layui-btn" lay-event="apply_pay">转账</a>
            <?php if($is_transfer_action): ?>
                {{# if(support_type.indexOf(d.bank_type) >= 0){ }}
                    <a class="layui-btn" lay-event="transfer">在线转账</a>
                {{# } }}
            <?php endif; ?>
        {{# }else if(d.status == 2){ }}
        {{# }else if(d.status == -1){ }}
        {{# } }}
        <a class="layui-btn" lay-event="memo">备注</a>
        <a class="layui-btn" lay-event="detail">查看</a>
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
    var support_type = "<?php echo htmlentities($support_type); ?>";
    layui.use(['form', 'laytpl', 'laydate', 'element','upload'], function() {
        var table,
            form = layui.form,
            laydate = layui.laydate,
			element = layui.element,
            upload = layui.upload,
            currentDate = new Date(),
            repeat_flag = false,
            laytpl = layui.laytpl;
        minDate = "";
        form.render();

        currentDate.setDate(currentDate.getDate() - 7);

        //申请开始时间
        laydate.render({
            elem: '#start_time',
            type: 'datetime'
        });

        //申请结束时间
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
		
		//监听Tab切换
		element.on('tab(status)', function(data) {
			var status = $(this).attr("lay-id");
			table.reload( {
				page: {
					curr: 1
				},
				where: {
					'status': status
				}
			});
		});
        
        /**
         * 表格加载
         */
        table = new Table({
            elem: '#shop_withdraw_list',
            url: ns.url("admin/shopaccount/withdrawlist"),
            where: {
                "period_id": $("#period_id").val()
            },
            cols: [
                [{
                    width: "3%",
                    type: 'checkbox',
                    unresize: 'false'
                },{
                    field: 'withdraw_no',
                    title: '提现流水编号',
                    width: '13%',
                    unresize: 'false'
                },{
                    title: '店铺名称',
                    width: '11%',
                    unresize: 'false',
                    templet: '#shop_info',
                }, {
                    field:'name',
                    title: '姓名',
                    unresize: 'false',
                    width:'10%',
					templet: function(data) {
						return '<span class="ns-line-hiding" title="'+ data.name +'">'+ data.name +'</span>'
					}
                }, {
                    field:'mobile',
                    title: '电话',
                    unresize: 'false',
                    width:'9%',

                }, {
                    title: '账户类型',
                    unresize: 'false',
                    width:'8%',
                    templet: function (res){
                        if (res.bank_type == 1) {
                            return "银行";
                        } else if(res.bank_type == 2) {
                            return "支付宝";
                        } else if(res.bank_type == 3) {
                            return "微信";
                        }else{
                            return '未知';
                        }
                    }
                }, {
                    field: 'money',
                    title: '提现金额',
                    width: '8%',
                    unresize: 'false',
                    align: 'right',
					templet: function(data) {
						return '￥'+ data.money;
					}
                },{
                    field: 'status',
                    title: '状态',
                    width: '6%',
                    unresize: 'false',
                    templet: '#status'
                },{
                    title: '时间',
                    width: '18%',
                    unresize: 'false',
                    templet: '#times'
                }, {
                    title: '操作',
                    width: '14%',
                    toolbar: '#action',
                    unresize: 'false'
                }]
            ],
            bottomToolbar: "#batchOperation"
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
            // return false;
        });
        
        /**
         * 监听工具栏操作
         */
        table.tool(function(obj) {
            var data = obj.data,
                event = obj.event;

            switch (event){
                case 'detail': //查看
                    withdrawDetail(data);
                    break;
                case 'apply_pass': //通过
                    applyPass(data.id);
                    break;
                case 'apply_reject': //拒绝
                    applyReject(data.id);
                    break;
                case 'apply_pay': //转账
                    applyPay(data);
                    break;
                case 'memo': //备注
                    memo(data);
                    break;
                case 'transfer': //备注
                    transfer(data.id);
                    break;
            }
        });

        /**
         * 批量操作
         */
        table.bottomToolbar(function(obj) {

            if (obj.data.length < 1) {
                layer.msg('请选择要操作的数据');
                return;
            }

            switch (obj.event) {
                case "apply_pass":
                    var id_array = new Array();
                    for (i in obj.data) id_array.push(obj.data[i].id);
                    applyPass(id_array.toString());
                    break;
                case "apply_pay":
                    var id_array = new Array();
                    for (i in obj.data) id_array.push(obj.data[i].id);
                    applyPay(id_array.toString());
                    break;
            }
        });

        /**
         * 通过
         */
        function applyPass(ids) {
            if (repeat_flag) return false;
            repeat_flag = true;

            layer.confirm('确定要通过申请吗?', function() {
                $.ajax({
                    url: ns.url("admin/shopaccount/applypass"),
                    data: {'apply_ids':ids},
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
         * 拒绝
         */
        function applyReject(id) {

            if (repeat_flag) return false;
            repeat_flag = true;

            layer.confirm('确定要拒绝申请吗?', function() {
                $.ajax({
                    url: ns.url("admin/shopaccount/applyreject"),
                    data: {'apply_id':id},
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
         * 转账
         */
        function applyPay(data) {
            laytpl($("#applyPay").html()).render(data, function(html) {
                layer_pass = layer.open({
                    title: '商家转账',
                    skin: 'layer-tips-class',
                    type: 1,
                    area: ['800px'],
                    content: html,
                });
            });
			
			//转账凭证
			var uploadInst = upload.render({
			    elem: '#paying_money_certificate',
			    url: ns.url("admin/upload/upload"),
			    done: function(res) {
			        if (res.code >= 0) {
			            $("input[name='paying_money_certificate']").val(res.data.pic_path);
			            $("#paying_money_certificate").html("<img src=" + ns.img(res.data.pic_path) + " >");
			        }
			        return layer.msg(res.message);
			    }
			});
        }

        //提交
        form.on('submit(repass)', function(data) {
			if (repeat_flag) return false;
			repeat_flag = true;
			
			layer.confirm('确定要转账吗?', function() {
			    $.ajax({
			        url: ns.url("admin/shopaccount/applypay"),
			        data: data.field,
			        dataType: 'JSON',
			        type: 'POST',
			        success: function(res) {
			            layer.msg(res.message);
			            repeat_flag = false;
			
			            if (res.code == 0) {
							layer.closeAll();
			                table.reload();
			            }
			        }
			    });
			}, function () {
			    layer.closeAll();
			    repeat_flag = false;
			});
        });

        /**
         * 备注
         */
        function memo(data) {
			layer.prompt({
				formType: 2,
				value: '',
				title: '备注',
				area: ['300px', '100px'] ,//自定义文本域宽高
				yes: function(index, layero){
					// 获取文本框输入的值
					var value = layero.find(".layui-layer-input").val();
					if (value) {
						$.ajax({
						    type: "POST",
						    url: ns.url("admin/shopaccount/editshopwithdrawmemo"),
						    data: {
								"memo": value,
								"apply_id": data.id
							},
						    dataType: 'JSON',
						    success: function(res) {
						        layer.msg(res.message);
						        repeat_flag = false;

						        if (res.code == 0) {
						            table.reload();
						        }
						    }
						});
						layer.close(index);
					} else {
						layer.msg('请输入备注内容!', {icon: 5, anim: 6});
						return;
					}
				}
			});
			
			
            /* laytpl($("#memo_content").html()).render(data, function(html) {
                layer_memo = layer.open({
                    title: '备注',
                    skin: 'layer-tips-class',
                    type: 1,
                    area: ['450px'],
                    content: html,
                });
            }); */
        }

        form.on('submit(submit_memo)', function(data) {
			console.log(data);return;
            if (repeat_flag) return false;
            repeat_flag = true;

            $.ajax({
                type: "POST",
                url: ns.url("admin/shopaccount/editshopwithdrawmemo"),
                data: data.field,
                dataType: 'JSON',
                success: function(res) {
                    layer.msg(res.message);
                    repeat_flag = false;

                    if (res.code == 0) {
                        layer.closeAll('page');
                        table.reload();
                    }
                }
            });
        });

        //详情
        function withdrawDetail(data) {
            var detailHtml = $("#withdrawDetail").html();
            laytpl(detailHtml).render(data, function(html) {
                layer.open({
                    type: 1,
                    title: '转账详情',
                    area: ['500px'],
                    content: html

                });
            })
        }
    });
    
	//在线转账
    var transfer_repeat_flag = false;
    function transfer(id){
        if (transfer_repeat_flag) return false;
        transfer_repeat_flag = true;

		layer.confirm('确定要在线转账吗?', function() {
		    $.ajax({
		        type: "POST",
		        url: ns.url("shopwithdraw://admin/withdraw/transfer"),
		        data: {id:id},
		        dataType: 'JSON',
		        success: function(res) {
		            layer.msg(res.message);
		            transfer_repeat_flag = false;
		
		            if (res.code == 0) {
		                table.reload();
		            }
		        }
		    });
		}, function () {
		    layer.close();
		    transfer_repeat_flag = false;
		});
    }
	
    function closeMemo() {
        layer.close(layer_memo);
    }

    function closePass(){
        layer.closeAll('page');
    }
</script>

<!-- 重置密码弹框html -->
<script type="text/html" id="applyPay">
    <div class="layui-form" lay-filter="form">

        <div class="layui-form-item">
            <label class="layui-form-label">店铺名称：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{ d.site_name }}</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">联系人：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{ d.name }}</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">联系电话：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{ d.mobile }}</p>
            </div>
        </div>

        {{# if(d.bank_type == 1){ }}
        <div class="layui-form-item">
            <label class="layui-form-label">账户类型：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">银行卡</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">账户名称：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{d.settlement_bank_name}}</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">提现账号：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{d.settlement_bank_account_number}}</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开户名：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{d.settlement_bank_account_name}}</p>
            </div>
        </div>
        {{# }else if(d.bank_type == 3){ }}
        <div class="layui-form-item">
            <label class="layui-form-label">账户类型：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">微信</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">微信号：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{d.settlement_bank_name}}</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">真实姓名：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{d.settlement_bank_account_name}}</p>
            </div>
        </div>
        {{# }else{ }}
        <div class="layui-form-item">
            <label class="layui-form-label">账户类型：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">支付宝</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">支付宝用户名：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{d.settlement_bank_account_name}}</p>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">支付宝账号：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{d.settlement_bank_account_number}}</p>
            </div>
        </div>
        {{# } }}

        <div class="layui-form-item">
            <label class="layui-form-label">提现金额：</label>
            <div class="layui-input-block">
                <p class="ns-input-text ">{{d.money}}</p>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label img-upload-lable">支付凭证：</label>
            <div class="layui-input-block img-upload">
                <div class="upload-img-block">
					<input type="hidden" name="paying_money_certificate" >
                    <div class="upload-img-box" id="paying_money_certificate">
                        <div class="ns-upload-default">
                            <img src="http://city.lpstx.cn/public/static/img/upload_img.png" />
                            <p>点击上传</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">付款凭证说明：</label>
            <div class="layui-input-block ns-len-long">
                <textarea name="paying_money_certificate_explain" class="layui-textarea"></textarea>
            </div>
        </div>

        <input type="hidden" name="id" value="{{ d.id }}">
        <div class="ns-form-row">
            <button class="layui-btn ns-bg-color" lay-submit lay-filter="repass">确定</button>
            <button class="layui-btn layui-btn-primary" onclick="closePass()">返回</button>
        </div>
    </div>
</script>

<!-- 备注弹框 -->
<script type="text/html" id="memo_content">
    <div class="layui-form" lay-filter="form">

                <textarea name="memo" id="memo" placeholder="请输入内容" class="layui-textarea"></textarea>
           

        <div class="ns-form-row sm">
            <button class="layui-btn ns-bg-color" lay-submit lay-filter="submit_memo">确定</button>
            <button class="layui-btn layui-btn-primary" onclick="closeMemo()">返回</button>
        </div>

        <input class="reset-pass-id" type="hidden" name="apply_id" value="{{d.id}}"/>
    </div>
</script>
<script type="text/html" id="batchOperation">
    <button class="layui-btn layui-btn-primary" lay-event="apply_pass">批量通过</button>
    <!--<button class="layui-btn layui-btn-primary" lay-event="apply_pay">批量转账</button>-->
</script>

<script type="text/html" id="withdrawDetail">
    <table class="layui-table">
        <colgroup>
            <col width="150">
            <col width="200">
        </colgroup>
        <tbody>
        <tr>
            <td>店铺名称</td>
            <td>{{d.site_name}}</td>
        </tr>
        <tr>
            <td>联系人</td>
            <td>{{d.name}}</td>
        </tr>
        <tr>
            <td>联系电话</td>
            <td>{{d.mobile}}</td>
        </tr>
        <tr>
            <td>账户类型</td>
            {{# if(d.bank_type == 1){ }}
            <td>银行卡</td>
            {{# }else if(d.bank_type == 3){ }}
            <td>微信</td>
            {{# }else{ }}
            <td>支付宝</td>
            {{# } }}
        </tr>
        {{# if(d.bank_type == 1){ }}
        <tr>
            <td>账户名称</td>
            <td>{{d.settlement_bank_name}}</td>
        </tr>
        <tr>
            <td>提现账号</td>
            <td>{{d.settlement_bank_account_number}}</td>
        </tr>
        <tr>
            <td>开户名</td>
            <td>{{d.settlement_bank_account_name}}</td>
        </tr>
        {{# }else if(d.bank_type == 3){ }}
        <tr>
            <td>微信号</td>
            <td>{{d.settlement_bank_name}}</td>
        </tr>
        <tr>
            <td>真实姓名</td>
            <td>{{d.settlement_bank_account_name}}</td>
        </tr>
        {{# }else{ }}
        <tr>
            <td>支付宝用户名</td>
            <td>{{d.settlement_bank_account_name}}</td>
        </tr>
        <tr>
            <td>支付宝账号</td>
            <td>{{d.settlement_bank_account_number}}</td>
        </tr>
        {{# } }}
        <tr>
            <td>提现金额</td>
            <td>{{d.money}}元</td>
        </tr>
        <tr>
            <td>状态</td>
            {{# if(d.status == 0){ }}
            <td>待审核</td>
            {{# }else if(d.status == 1){ }}
            <td>待转账</td>
            {{# }else if(d.status == 2){ }}
            <td>已转账</td>
            {{# }else if(d.status == -1){ }}
            <td>已拒绝</td>
            {{# } }}
        </tr>
        <tr>
            <td>申请时间</td>
            <td>{{ ns.time_to_date(d.apply_time) }}</td>
        </tr>
        {{# if(d.status == 2){ }}
		<tr>
			<td>转账时间</td>
			<td>{{ ns.time_to_date(d.payment_time) }}</td>
		</tr>
		<tr>
			<td>转账凭证</td>
			<td><img src="{{ ns.img(d.paying_money_certificate) }}" alt=""></td>
		</tr>
		<tr>
			<td>转账凭证说明</td>
			<td>{{ d.paying_money_certificate_explain }}</td>
		</tr>
		{{# } }}
        <tr>
            <td>备注</td>
            <td id="detail_memo">{{d.memo}}</td>
        </tr>
        </tbody>
    </table>
</script>

</body>
</html>