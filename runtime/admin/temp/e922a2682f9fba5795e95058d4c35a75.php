<?php /*a:2:{s:61:"/www/wwwroot/city.lpstx.cn/app/admin/view/system/upgrade.html";i:1600314240;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
    .layui-layer-content {
        word-break: break-all;
        line-height: 24px;
        overflow-y: scroll!important;
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
				
				
<div class="layui-collapse ns-tips">
    <div class="layui-colla-item">
        <h2 class="layui-colla-title">操作提示</h2>
        <ul class="layui-colla-content layui-show">
            <li>一、建议在系统升级前将网站的文件权限全部修改为777，保证系统备份文件、下载文件和覆盖文件的正常进行</li>
            <li>二、在升级完成后，为保证网站的安全，需要重新设置文件权限。建议进行如下修改</li>
            <li>1、runtime文件夹，upload文件夹依旧为777权限</li>
            <li>2、其他文件设置为644权限</li>
        </ul>
    </div>
</div>
<!-- 操作栏 -->
<div class="ns-single-filter-box">
    <button class="layui-btn ns-bg-color" id="upgrade_btn">一键升级</button>
</div>
<table id="version_list" lay-filter="version_list"></table>

<script type="text/html" id="operation">
    <div class="ns-table-btn">
        <a class="layui-btn" lay-event="upgrade_desc">查看更新说明</a>
        <a class="layui-btn" lay-event="upgrade_file">查看更新文件</a>
    </div>
</script>

<script type="text/html" id="upgrade_desc">
    <div><span>需更新版本数：{{d.scripts.length}}</span></div>
    <table class="layui-table" lay-skin="lg">
        <colgroup>
            <col width="20%">
            <col width="80%">
        </colgroup>
        <thead>
        <tr>
            <th>版本号</th>
            <th>更新说明</th>
        </tr>
        </thead>
        <tbody>
        {{#  layui.each(d.scripts, function(index, item){  }}
        <tr>
            <td>{{item.version_no}}</td>
            <td>{{item.description}}</td>
        </tr>
        {{#  })  }}
        </tbody>
    </table>
</script>

<script type="text/html" id="upgrade_file">
    <div style="height:100%; overflow-y: auto;">
        <div><span>需更新文件数：{{d.files.length}}</span></div>
        <table class="layui-table" lay-skin="lg">
            <tbody>
            {{#  layui.each(d.files, function(index, item){  }}
            <tr>
                <td>{{item.file_path}}</td>
            </tr>
            {{#  })  }}
            </tbody>
        </table>
    </div>
</script>

<script type="text/html" id="right_check">
    <div style="height:100%; overflow-y: auto;">
        <div><span>共有{{d.length}}个文件或文件夹不可写，请先修正这些问题，再继续升级</span></div>
        <table class="layui-table" lay-skin="lg">
            <thead>
            <tr>
                <th>类型</th>
                <th>路径</th>
            </tr>
            </thead>
            <tbody>
            {{#  layui.each(d, function(index, item){  }}
            <tr>
                <td>{{item.type_name}}</td>
                <td>{{item.path}}</td>
            </tr>
            {{#  })  }}
            </tbody>
        </table>
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
    //升级的权限检测
    var right_check = null;
    var system_upgrade_info_ready = null;

    layui.use(['form', 'laytpl'], function() {
        var table,
            laytpl = layui.laytpl,
            form = layui.form;
        form.render();

        table = new Table({
            elem: '#version_list',
            url: ns.url("admin/system/upgrade"),
            parseData: function(res){ //res 即为原始返回的数据
                if (res.code == 0) {
                    right_check = res.data.right_check;
                    system_upgrade_info_ready = res.data.system_upgrade_info_ready;
                    return {
                        "code": res.code, //解析接口状态
                        "msg": res.message, //解析提示文本
                        "data": res.data.system_upgrade_info_ready //解析数据列表
                    };
                } else {
                    return {
                        "code": res.code, //解析接口状态
                        "msg": res.message, //解析提示文本
                        "data": [] //解析数据列表
                    };
                }
            },
            page: false,
            cols: [
                [{
                    field: 'type_name',
                    title: '主体',
                    width: '10%',
                    unresize: 'false',
                    templet: function (data) {
                        return data.goods_name;
                    }
                },{
                    field: 'type_name',
                    title: '操作类型',
                    width: '10%',
                    unresize: 'false',
                    templet: function (data) {
                        return data.action_name;
                    }
                }, {
                    field: 'current_version_name',
                    title: '当前版本',
                    width: '10%',
                    unresize: 'false'
                }, {
                    field: 'latest_version_name',
                    title: '最新版本',
                    width: '10%',
                    unresize: 'false'
                }, {
                    field: 'scripts',
                    title: '更新版本数',
                    width: '10%',
                    unresize: 'false',
                    templet: function (data) {
                        return data.scripts.length;
                    }
                }, {
                    field: 'files',
                    title: '更新文件数',
                    width: '10%',
                    unresize: 'false',
                    templet: function (data) {
                        return data.files.length;
                    }
                }, {
                    title: '操作',
                    width: '30%',
                    toolbar: '#operation',
                    unresize: 'false'
                }]
            ]
        });

        /**
         * 监听工具栏操作
         */
        table.tool(function(obj) {
            var data = obj.data,
                event = obj.event;
            switch (event) {
                case 'upgrade_desc':
                    upgradeDesc(data);
                    break;
                case 'upgrade_file':
                    upgradeFile(data);
                    break;
            }
        });

        //详情说明
        function upgradeDesc(data) {
            var uploadHtml = $("#upgrade_desc").html();
            laytpl(uploadHtml).render(data, function(html) {
                layer.open({
                    type: 1,
                    title: '更新说明',
                    area: ['700px', '500px'],
                    content: html
                });
            })
        }

        //更新文件
        function upgradeFile(data) {
            var uploadHtml = $("#upgrade_file").html();
            laytpl(uploadHtml).render(data, function(html) {
                layer.open({
                    type: 1,
                    title: '更新文件',
                    area: ['700px', '500px'],
                    content: html
                });
            })
        }

        $("#upgrade_btn").click(function(){
            if(system_upgrade_info_ready === null){
                layer.msg('数据正在获取中，请稍候');
                return false;
            }
            if(system_upgrade_info_ready.length <= 0){
                layer.msg('暂无可以升级的内容');
                return false;
            }
            if(right_check.length > 0){
                var uploadHtml = $("#right_check").html();
                laytpl(uploadHtml).render(right_check, function(html) {
                    layer.open({
                        type: 1,
                        title: '权限检测',
                        area: ['700px', '500px'],
                        content: html
                    });
                })
                return false;
            }
            window.location.href = ns.url("admin/system/upgradeAction");
        })
    });
</script>

</body>
</html>