<?php /*a:2:{s:58:"/www/wwwroot/city.lpstx.cn/app/admin/view/order/lists.html";i:1600314240;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
	
<link rel="stylesheet" href="http://city.lpstx.cn/app/admin/view/public/css/order_list.css"/>

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
				
				
<div class="ns-screen layui-collapse" lay-filter="selection_panel">
    <div class="layui-colla-item">
        <h2 class="layui-colla-title">筛选</h2>
        <form class="layui-colla-content layui-form layui-show"  lay-filter="order_list">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">搜索方式：</label>
                    <div class="layui-input-inline">
                        <select name="order_label" >
                            <?php foreach($order_label_list as $k => $label_val): ?>
                            <option value="<?php echo htmlentities($k); ?>"><?php echo htmlentities($label_val); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" name="search" autocomplete="off" class="layui-input" />
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">下单时间：</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" name="start_time" placeholder="开始时间" id="start_time" readonly>
                        <i class="ns-calendar"></i>
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" name="end_time" placeholder="结束时间" id="end_time" readonly>
                        <i class="ns-calendar"></i>
                    </div>
                    <a class="layui-btn layui-btn-primary date-picker-btn" onclick="datePick(7, this);return false;">近7天</a>
                    <a class="layui-btn layui-btn-primary date-picker-btn" onclick="datePick(30, this);return false;">近30天</a>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">订单类型：</label>
                    <div class="layui-input-inline">
                        <select name="order_type" lay-filter="order_type">
                            <?php foreach($order_type_list as $order_type_k => $order_type_val): ?>
                            <option value="<?php echo htmlentities($order_type_val['type']); ?>"><?php echo htmlentities($order_type_val['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">订单状态：</label>
                    <div class="layui-input-inline">
                        <select name="order_status" lay-filter="order_status">
                            <option value="">全部</option>
                            <?php foreach($order_status_list as $k => $status_val): ?>
                            <option value="<?php echo htmlentities($status_val['status']); ?>"><?php echo htmlentities($status_val['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">营销类型：</label>
                    <div class="layui-input-inline">
                        <select name="promotion_type" lay-filter="promotion_type">
                            <option value="">全部</option>
                            <?php foreach($promotion_type as $promotion_type_k => $promotion_type_val): ?>
                            <option value="<?php echo htmlentities($promotion_type_val['type']); ?>"><?php echo htmlentities($promotion_type_val['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">付款方式：</label>
                    <div class="layui-input-inline">
                        <select name="pay_type" >
                            <option value="">全部</option>
                            <?php foreach($pay_type_list as $pay_type_k => $pay_type_v): ?>
                            <option value="<?php echo htmlentities($pay_type_k); ?>"><?php echo htmlentities($pay_type_v); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">订单来源：</label>
                    <div class="layui-input-inline">
                        <select name="order_from">
                            <option value="">全部</option>
                            <?php foreach($order_from_list as $order_from_key => $order_from_v): ?>
                            <option value="<?php echo htmlentities($order_from_key); ?>"><?php echo htmlentities($order_from_v['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="ns-form-row">
                <button class="layui-btn ns-bg-color" lay-submit id="btn_search"lay-filter="btn_search">筛选</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                <button class="layui-btn layui-btn-primary" lay-submit lay-filter="batch_export_order_goods" >导出订单商品</button>
                <button class="layui-btn layui-btn-primary" lay-submit lay-filter="batch_export_order" >导出订单</button>

                <!--<button class="layui-btn layui-btn-primary">查看已生成报表</button>-->
            </div>
            <input type="hidden" name="status" val=""/>
            <input type="hidden" name="page" val=""/>
        </form>
    </div>
</div>

<div class="layui-tab ns-table-tab" lay-filter="order_tab">
    <ul class="layui-tab-title">
        <li class="layui-this" lay-id="">全部订单</li>
    </ul>
    <div class="layui-tab-content">
        <!-- 列表 -->
        <div id="order_list"></div>
    </div>
</div>


<div id="order_page"></div>
<div id="order_operation"></div>

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


<script src="http://city.lpstx.cn/app/admin/view/public/js/order_list.js"></script>
<script>
    var laypage,element, form, hash_url;
    var is_refresh = false;
    var order_type_status_json = <?php echo json_encode($order_type_list); ?>;
    /**
     *通过hash获取页数
     */
    function getHashPage(){
        var page = 1;
        var hash = location.hash;

        var hash_arr = hash.split("&");
        $.each(hash_arr,function(index, itemobj){
            var item_arr = itemobj.split("=");
            if(item_arr.length == 2){
                if(item_arr[0].indexOf("page") != "-1"){
                    page = item_arr[1];
                }
            }
        })
        return page;
    }

    //从hash中获取数据
    function getHashData(){
        var hash = ns.urlReplace(location.hash);
        var data= [];

        var hash_arr = hash.split("&");

        var form_json = {
            "end_time" : "",
            "order_from" : "",
            "order_label" : $("select[name=order_label]").val(),
            "order_name" : "",
            "order_status" : "",
            "promotion_type" : "",
            "pay_type" : "",
            "search" : "",
            "start_time" : "",
            "order_type" : 'all',
            "page" : ""
        };
        if(hash_arr.length > 0){
            // page = hash_arr[0].replace('#!page=', '');
            $.each(hash_arr,function(index, itemobj){
                var item_arr = itemobj.split("=");
                if(item_arr.length == 2){
                    $.each(form_json,function(key, form_val){
                        if(item_arr[0].indexOf(key) != "-1"){
                            form_json[key] = item_arr[1];
                        }
                    })
                }
            })
        }
        resetOrderStatus(form_json.order_type, 2);
        form.val("order_list", form_json);
        setOrderStatusTab(form_json.order_status);
        return form_json;
    }



    /**
     * 获取哈希值order_type
     */
    function getHashOrderType(){
        var hash = ns.urlReplace(location.hash);
        var hash_arr = hash.split("&");
        var order_type = "all";
        if(hash_arr.length > 0){
            // page = hash_arr[0].replace('#!page=', '');
            $.each(hash_arr,function(index, itemobj){
                var item_arr = itemobj.split("=");
                if(item_arr.length == 2){
                    if(item_arr[0].indexOf("order_type") != "-1") {
                        order_type = item_arr[1];
                    }
                }
            })
        }
        return order_type;
    }
    layui.use(['laypage','laydate','form', 'element'], function(){
        form = layui.form;
        laypage = layui.laypage;
        element = layui.element;
        var laydate = layui.laydate;
        form.render();

        //渲染时间
        laydate.render({
            elem: '#start_time'
            ,type: 'datetime'
            ,change: function(value, date, endDate){
                $(".date-picker-btn").removeClass("selected");
            }
        });
        laydate.render({
            elem: '#end_time'
            ,type: 'datetime'
            ,change: function(value, date, endDate){
                $(".date-picker-btn").removeClass("selected");
            }
        });

        //监听筛选事件
        form.on('submit(btn_search)', function(data){
            is_refresh = true;
            data.field.page = 1;
            resetOrderStatus(data.field.order_type, 2);
            setHashOrderList(data.field);
            setOrderStatusTab(data.field.order_status);
            return false;
        });

        //批量导出（订单项）
        form.on('submit(batch_export_order_goods)', function(data){
            location.href = ns.url("admin/order/exportOrderGoods",data.field);
            return false;
        });

        //批量导出（订单）
        form.on('submit(batch_export_order)', function(data){
            location.href = ns.url("admin/order/exportOrder",data.field);
            return false;
        });


        //订单类型
        form.on('select(order_type)', function(data){

            resetOrderStatus(data.value, 1);
            return false;
        });

        //监听Tab切换，以改变地址hash值
        element.on('tab(order_tab)', function(){
            var status = this.getAttribute('lay-id');
            form.val("order_list", {"order_status":status});

            var hash_data = getHashList();
            hash_data.order_status = status;
            hash_data.page = 1;
            setHashOrderList(hash_data);
        });
        getHashData();
        getOrderList();//筛选

    });


    function setOrderStatusTab(order_status){
        $(".layui-tab-title li").removeClass("layui-this");
        $(".layui-tab-title li").each(function(){
            var status = $(this).attr("lay-id");
            if(status == order_status){
                $(this).addClass("layui-this")
            }
        });
    }
    //重置状态tab 选项卡
    function resetOrderStatus(order_type, is_tab){
        var hash_order_type = getHashOrderType();
        if(hash_order_type != order_type || is_refresh == false){
            if(is_tab != 1 || is_refresh == false) {
                $(".layui-tab-title li").not(':first').remove();
                $(".layui-tab-title li").find(":first").addClass("layui-this");
            }
            if(is_tab != 2 || is_refresh == false){
                $("select[name=order_status] option").not(':first').remove();
            }

            var status_item = order_type_status_json[order_type]["status"];
            $.each(status_item,function(index, itemobj){
                if(is_tab != 1 || is_refresh == false) {
                    $(".layui-tab-title").append('<li lay-id="' + index + '">' + itemobj + '</li>');
                }
                if(is_tab != 2 || is_refresh == false) {
                    $("select[name=order_status]").append('<option value="' + index + '">' + itemobj + '</option>');
                }
            })
            form.render('select');
        }
    }
    //哈希值 订单数据
    function setHashOrderList(data){
        var hash = "";
        $.each(data,function(index, itemobj){
            if(itemobj != "" && itemobj != "all"){
                if(hash == ""){
                    hash += "#!"+index +"="+itemobj;
                }else{
                    hash += "&"+index +"="+itemobj;
                }
            }
        })
        // window.location.href = hash;
        hash_url = hash;
        location.hash = hash;
        getOrderList();
    }

    function getHashList(){
        var hash = ns.urlReplace(location.hash);
        var data= [];
        var hash_arr = hash.split("&");

        var form_json = {
            "end_time" : "",
            "order_from" : "",
            "order_label" : $("select[name=order_label]").val(),
            "order_name" : "",
            "order_status" : "",
            "promotion_type" : "",
            "pay_type" : "",
            "search" : "",
            "start_time" : "",
            "order_type" : 'all',
            "page" : ""
        };
        if(hash_arr.length > 0){
            // page = hash_arr[0].replace('#!page=', '');
            $.each(hash_arr,function(index, itemobj){
                var item_arr = itemobj.split("=");
                if(item_arr.length == 2){
                    $.each(form_json,function(key, form_val){
                        if(item_arr[0].indexOf(key) != "-1"){
                            form_json[key] = item_arr[1];
                        }
                    })
                }
            })
        }

        return form_json;
    }


    var order = new Order();
    function getOrderList(){
        var url = ns.url("admin/order/lists", ns.urlReplace(location.hash.replace('#!', '')));

        $.ajax({
            type : 'get',
            dataType: 'json',
            url :url,
            success : function(res){
                if(res.code == 0){
                    order.setData(res.data);
                    $("#order_list").html(order.fetch());
                    //执行一个laypage实例
                    laypage.render({
                        elem: 'order_page',
                        count: res.data.count,
                        curr: getHashPage(),
                        layout: ['count', 'prev', 'page', 'next'],
                        // hash: 'page',
                        jump: function(obj, first){
                            //首次不执行
                            if(!first){
                                var hash_data = getHashData();
                                hash_data.page = obj.curr;
                                setHashOrderList(hash_data);
                                // $("#btn_search").click();//筛选
                            }
                        }
                    });
                }else{
                    layer.msg(res.message);
                }
            }
        });
    }

    /**
     * 七天时间
     */
    function datePick(date_num,event_obj){
        $(".date-picker-btn").removeClass("selected");
        $(event_obj).addClass('selected');
        // alert(new Date().format("yyyy-MM-dd hh:mm"));
        var now_date = new Date();

        Date.prototype.Format = function (fmt,date_num) { //author: meizz
            this.setDate(this.getDate()-date_num);
            var o = {
                "M+": this.getMonth() + 1, //月份
                "d+": this.getDate(), //日
                "H+": this.getHours(), //小时
                "m+": this.getMinutes(), //分
                "s+": this.getSeconds(), //秒
                "q+": Math.floor((this.getMonth() + 3) / 3), //季度
                "S": this.getMilliseconds() //毫秒
            };
            if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
            for (var k in o)
                if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            return fmt;
        }
        // var now_time =  new Date().Format("yyyy-MM-dd HH:mm:ss",0);//当前日期
        var now_time =  new Date().Format("yyyy-MM-dd 23:59:59",0);//当前日期
        var before_time =  new Date().Format("yyyy-MM-dd 00:00:00",date_num-1);//前几天日期
        $("input[name=start_time]").val(before_time,0);
        $("input[name=end_time]").val(now_time,date_num-1);

    }
</script>

</body>
</html>