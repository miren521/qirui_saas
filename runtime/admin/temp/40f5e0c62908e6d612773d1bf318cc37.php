<?php /*a:2:{s:62:"/www/wwwroot/city.lpstx.cn/app/admin/view/system/database.html";i:1600312146;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
	.ns-data-table {
		border-width: 0 !important;
	}

	.ns-data-table[lay-size=lg] th,
	.ns-data-table[lay-size=lg] td {
		padding: 15px 15px;
	}

	.ns-data-table[lay-size=lg] .ns-check-box {
		padding-left: 0;
		padding-right: 0;
	}

	.ns-check-box .layui-form {
		text-align: center;
	}

	.layui-form-checkbox[lay-skin=primary] {
		padding-left: 0;
	}

	.layui-table tr .toolbar a {
	    display: inline-block;
	    height: 23px;
	    line-height: 23px;
	    border-radius: 50px;
	    background-color: #F3F3F3;
	    font-size: 13px;
	    color: #5A5A5A;
	    text-align: center;
	    padding: 2px 8px;
	    margin: 5px;
	}
	
	.layui-table tr .toolbar a:hover {
	    color: #fff !important;
	    background-color: #4685FD;
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
			<li>展示数据库中所有的表</li>
			<li>可以对表进行恢复、备份操作</li>
		</ul>
	</div>
</div>

<!-- 搜索框 -->
<div class="ns-single-filter-box">
	<button type="button" class="layui-btn ns-bg-color" lay-filter="backups" lay-submit>备份</button>
<!--	<button type="button" class="layui-btn ns-bg-color" lay-filter="datarepair" lay-submit>恢复</button>-->
</div>

<table class="layui-table ns-data-table" lay-skin="line" lay-size="lg">
	<colgroup>
		<col width="3%">
		<col width="30%">
		<col width="21%">
		<col width="21%">
		<col width="25%">
		<!-- <col width="10%"> -->
	</colgroup>
	<thead>
		<tr>
			<th class="ns-check-box">
				<div class="layui-form">
					<input type="checkbox" class="selectAll" name="" lay-filter="selectAll" lay-skin="primary">
				</div>
			</th>
			<th>表名</th>
			<th>数据量</th>
			<th>数据大小</th>
			<th>创建时间</th>
			<!-- <th class="toolbar">操作</th> -->
		</tr>
	</thead>
	<tbody>
		<?php foreach($name=$list as $list_k => $list_v): ?>
		<tr>
			<td class="ns-check-box">
				<div class="layui-form">
					<input type="checkbox" name="" lay-filter="select<?php echo htmlentities($list_k); ?>" lay-skin="primary">
				</div>
			</td>
			<td class="ns-data-table-name"><?php echo htmlentities($list_v['Name']); ?></td>
			<td class="ns-data-table-num"><?php echo htmlentities($list_v['Rows']); ?></td>
			<td class="data-length">
				<input type="hidden" value="<?php echo htmlentities($list_v['Data_length'] / 1024); ?>" />
			</td>
			<td><?php echo htmlentities($list_v['Create_time']); ?></td>
			<!-- <td class="toolbar">
				<a class="default" lay-filter="tablerepair" lay-submit>恢复</a>
			</td> -->
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

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


<script type="text/javascript" src="http://city.lpstx.cn/public/static/loading/msgbox.js"></script>
<script>
	layui.use('form', function() {
		var form = layui.form,
			repeat_flag = false;
		form.render();

		$(".data-length").each(function(i) {
			var data = $(this).find("input").val();
			var dataNew = Math.round(data * 100) / 100;
			$(this).text(dataNew + "KB");
		});

		/**
		 * 监听全选按钮
		 */
		form.on('checkbox(selectAll)', function(data) {
			if (data.elem.checked) {
				$("tr .ns-check-box input:checkbox").each(function() {
					$(this).prop("checked", true);
				});
			} else {
				$("tr .ns-check-box input:checkbox").each(function() {
					$(this).prop("checked", false);
				});
			}
			form.render();
		});

		/**
		 * 监听每一行的多选按钮
		 */
		var len = $("tbody tr").length;
		for (var i = 0; i < len; i++) {
			form.on('checkbox(select' + i + ')', function(data) {
				if ($("tbody tr input:checked").length == len) {
					$("input[lay-filter='selectAll']").prop("checked", true);
				} else {
					$("input[lay-filter='selectAll']").prop("checked", false);
				}

				form.render();
			});
		}

		/**
		 * 监听备份
		 */
		form.on('submit(backups)', function() {
			var _selected = [];

			$("tbody tr input:checked").each(function() {
				_selected.push($(this).parents("td").siblings(".ns-data-table-name").text());
			});

			if (_selected.length == 0) {
				layer.msg('请选择需要备份的数据表！');
				return;
			}

			layer.confirm('是否备份?', function(index){
				$.ajax({
					url: ns.url("admin/system/backup"),
					data: {
						"tables": _selected
					},
					dataType: 'JSON',
					type: 'POST',
					success: function(res) {
						layer.close(index);
						if (res.status == 1 && res.message == "初始化成功") {
							backup(res.tab);
							ZENG.msgbox.show(res.message, 4, 3000);
							return;
						} else {
							ZENG.msgbox.show(res.message, 5);
						}
					}
				});
			});
		});

		//备份数据库
		function backup(tab, status) {
			$.ajax({
				url: ns.url("admin/system/backup"),
				data: {
					"id": tab.id,
					"start": tab.start
				},
				dataType: 'JSON',
				type: 'POST',
				success: function(res) {
					ZENG.msgbox.show("正在备份数据库，请不要关闭窗口", 6);

					if (res.status == 1) {
						if (!$.isPlainObject(res.tab)) {
							ZENG.msgbox.show(res.message, 5);
							return;
						}else{
							ZENG.msgbox.show("正在处理"+ res.tab.table +' ...', 6);
						}
						backup(res.tab, res.id != res.tab.id);
					} else {
						if(res.status == -1){
							ZENG.msgbox.show(res.message, 5);
						}else{
							ZENG.msgbox.show("备份完成", 4, 3000);
						}
					}
				}
			});
		}

		/**
		 * 监听数据表恢复
		 */
		form.on('submit(tablerepair)', function() {
			var table_name = $(this).parents("tr").find(".ns-data-table-name").text();
			console.log(table_name);
			repair(table_name);
		});

		/**
		 * 批量操作
		 */
		form.on('submit(datarepair)', function() {
			var tables = [];

			$("tbody tr input:checked").each(function() {
				tables.push($(this).parents("td").siblings(".ns-data-table-name").text());
			});

			if (tables.length == 0) {
				layer.msg('请选择需要恢复的数据表！');
				return;
			}

			repair(tables.toString());
		});

		function repair(tables) {
			if (repeat_flag) return false;
			repeat_flag = true;

			layer.confirm('确定要恢复改数据表吗?', function() {
				$.ajax({
					type: 'POST',
					dataType: 'JSON',
					url: ns.url("admin/system/tablerepair"),
					data: {
						"tables": tables
					},
					success: function(res) {
						layer.msg(res.message);
						repeat_flag = false;
					}
				});
			}, function () {
				layer.close();
				repeat_flag = false;
			});
		}
	})
</script>

</body>
</html>