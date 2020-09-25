<?php /*a:2:{s:68:"/www/wwwroot/city.lpstx.cn/app/admin/view/system/upgrade_action.html";i:1600314240;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
	
<script type="text/javascript" src="http://city.lpstx.cn/app/admin/view/public/js/progressbar.js"></script>
<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/app/admin/view/public/css/upgrade/style.css" media="screen">
<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/app/admin/view/public/css/upgrade/jquery.easy-pie-chart.css" media="screen">
<style>
	.chart{
		display:inline-block;
		float:unset;
	}
	.up-footer{padding:20px;text-align:center;}
	.up-footer button{
		background-color: #067cf3;
		padding:8px 15px;
		color:#FFF;
		border:none;
		border-radius:3px;
		font-size:15px;
		margin: 0 10px;
	}
	/*.up-footer button:nth-child(2){
		display: none;
	}*/
	.install-content{
		margin-top:40px;
		padding: 10px 0 10px 20px;
		border: 1px solid #AAD2E5;
		background-color: #E1F2FA;
	}
	.panel-title{
		font-size:15px;
		margin:7px auto;
		line-height:30px;
		color: #31708f;
		border-bottom:1px solid #aad2e5 !important;
	}
	.step-title{
		color: #31708f;
		font-weight:bold;
	}
	.step-text{
		color: #31708f;
	}
	#container {
		width: 300px;
		height: 300px;
		margin: 50px auto;
		position: relative;
	}

	.alert-warning-word {
		background-color: #FCF8E3;
		border-top: 1px solid #FBEED5;
		border-bottom: 1px solid #FBEED5;
		padding: 5px 20px;
		line-height: 30px;
		color: #C09853;
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
				
				
<div class="ns-tips">
	<div id="container"></div>

	<div style="clear:both;"></div>

	<div class="alert alert-warning alert-warning-word">
		<strong>警告！</strong>版本升级中,请不要关闭当前页面!
	</div>

	<div class="install-content alert alert-info">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					详细过程
				</h3>
			</div>
			<div class="panel-body">
				<span class="step-title">升级操作&nbsp;：&nbsp;</span><span class="step-text action-name"></span><br/>
				<span class="step-title">整体进度&nbsp;：&nbsp;</span><span class="step-text count-percent">0%</span><br/>
				<span class="step-title">操作显示&nbsp;：&nbsp;</span><span class="step-text step-content"></span></span>
			</div>
		</div>
	</div>

	<div class="space-10"></div>

	<div class="up-view" >
		<div class="up-footer">
			<button class="up-btn" onclick="upgradeStart();">开始升级</button>
			<button class="up-btn" onclick="downloadFile();">下载升级文件</button>
		</div>
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


<script type="text/javascript">
	var action_type = 'upgrade';
	var upgrade_obj = {
		'data' : [],
		'total_action' : {
			'list' : [
				'backupFile',
				'backupSql',
				'downloadFile',
				'upgradeStart',//升级开始
				'executeFile',
				'executeSql',
				'upgradeEnd',//升级结束
			],
			'method' : '',
			'status' : {},
			'error_message' : '',
		},
		'download_file' : {
			'index' : -1,
			'error_index' : -1,
			'error_num' : 0,
			'error_limit' : 10,
			'download_root': ''
		},
		'backup_sql' : {
			'last_table' : '',
			'index' : 0,
			'series' : 1,
		},
	};

	//总操作控制
	upgrade_obj.totalActionControl = function(){
		var that = this;
		var method = that.total_action.method;
		var index = that.total_action.list.indexOf(method);
		if(index < 0){
			method = that.total_action.method = that.total_action.list[0];
			that.total_action.status[method] = 'start';
			that[method]();
		}else{
			var status = that.total_action.status[method];
			if(status === 'end'){
				index ++;
				if(index < that.total_action.list.length){
					method = that.total_action.method = that.total_action.list[index];
					that.total_action.status[method] = 'start';
					that[method]();
				}else{
					that.totalActionEnd();
				}
			}else{

			}
		}
	}

	//总操作完成
	upgrade_obj.totalActionEnd = function(){
		setActionName('升级完成');
		layer.msg('升级完成', function(index, layero){
			window.location.href = ns.url("admin/system/upgrade");
		})
	}

	//备份文件
	upgrade_obj.backupFile = function(){
		var that = this;
		var method = 'backupFile';

		setActionName('备份系统文件...');

		$.ajax({
			type:'post',
			url : ns.url("admin/system/backupFile"),
			dataType : 'json',
			success:function(res){
				if(res.code >= 0){
					that.total_action.status[method] = 'end';
				}else{
					that.total_action.status[method] = 'error';
					setActionContent(res.message);
				}
				that.totalActionControl();
			}
		})
	}

	//备份sql
	upgrade_obj.backupSql = function(){
		var that = this;
		var method = 'backupSql';

		var last_table = that.backup_sql.last_table;
		var index = that.backup_sql.index;
		var series = that.backup_sql.series;

		if(!last_table){
			setActionName('备份数据库文件...');
		}

		$.ajax({
			type:'post',
			url : ns.url("admin/system/backupSql"),
			dataType : 'json',
			data : {
				last_table : last_table,
				index : index,
				series : series,
			},
			success:function(res){
				if(res.code >= 0){
					var data = res.data;
					//判断是否备份完成
					if(data.is_backup_end){
						that.total_action.status[method] = 'end';
						that.totalActionControl();
					}else{
						that.backup_sql.last_table = data.last_table;
						that.backup_sql.index = data.index;
						that.backup_sql.series = data.series;
						setActionContent(res.message);
						that.backupSql();
					}
				}else{
					that.total_action.status[method] = 'error';
					setActionContent(res.message);
					that.totalActionControl();
				}

			}
		})
	}

	//下载文件操作
	upgrade_obj.downloadFile = function(){
		var that = this;
		setActionName('文件下载');

		if(that.data.files.length > 0){
			that.downloadFileControl();
		}else{
			that.total_action.status['downloadFile'] = 'end';
			if (action_type == 'upgrade') {
				that.totalActionControl();
			}
		}
	}

	//下载文件控制
	upgrade_obj.downloadFileControl = function(){
		var that = this;
		that.download_file.index ++;
		var files = that.data['files'];
		if(that.download_file.index < files.length){
			that.downloadFileExec();
		}else{
			that.download_file.index = -1;
			that.total_action.status['downloadFile'] = 'end';
			if (action_type == 'upgrade') {
				setActionContent("文件下载完成");
				that.totalActionControl();
			} else {
				setActionContent("文件下载完成，升级文件已下载到：网站根目录/" + that.download_file.download_root + "目录下");
			}
		}
	}

	//执行下载文件
	upgrade_obj.downloadFileExec = function(){
		var that = this;
		var download_file_index = that.download_file.index;
		$.ajax({
			type:'post',
			url : ns.url("admin/system/download"),
			dataType : 'json',
			data:{
				'action_type': action_type,
				'tag' : 'system_upgrade',
				'download_file_index':download_file_index
			},
			success:function(data){
				if(data.code >= 0){
					//显示进度条和下载文件
					that.download_file.download_root = data.download_root;
					setCountPercent((that.download_file.index + 1) / that.data.files.length);
					setActionContent(that.data.files[that.download_file.index]['file_path']);
					that.downloadFileControl();
				}else{
					//如果下载文件有错误可以在一定时间后重新发起请求
					if(that.download_file.index === that.download_file.error_index){
						that.download_file.error_num ++;
					}else{
						that.download_file.error_num = 1;
					}
					if(that.download_file.error_num <= that.download_file.error_limit){
						that.download_file.index --;
						setActionContent("文件下载出错，即将重新发起请求");
						setTimeout(function(){
							that.downloadFileControl();
						}, 300);
					}else{
						setActionContent("文件下载出错,已达到最大错误次数，请稍后重新进行系统升级");
						layer.msg('error',data.message);
					}
				}
			}
		})
	}

	//覆盖文件
	upgrade_obj.executeFile = function(){
		var that = this;
		var method = 'executeFile';
		setActionName('覆盖文件');
		if(that.data.files.length > 0){
			$.ajax({
				type:'post',
				url : ns.url("admin/system/executeFile"),
				dataType : 'json',
				data:{},
				success:function(data){
					if(data.code >= 0){
						//显示进度条和下载文件
						that.total_action.status[method] = 'end';
						that.totalActionControl();
					}else{
						layer.msg(data.message);
					}
				}
			})
		}else{
			that.total_action.status[method] = 'end';
			that.totalActionControl();
		}
	}

	//执行sql
	upgrade_obj.executeSql = function(){
		var that = this;
		var method = 'executeSql';
		setActionName('执行数据库脚本');
		$.ajax({
			type:'post',
			url : ns.url("admin/system/executeSql"),
			dataType : 'json',
			success:function(data){
				if(data.code >= 0){
					that.total_action.status[method] = 'end';
					that.totalActionControl();
				}else{
					layer.msg(data.message);
					setActionContent(data.message);
					that.executeRecovery();
				}
			}
		})
	}

	//开始系统升级
	upgrade_obj.upgradeStart = function(){
		var that = this;
		var method = 'upgradeStart';

		setActionName('开始系统升级...');

		$.ajax({
			type:'post',
			url : ns.url("admin/system/upgradeStart"),
			dataType : 'json',
			success:function(res){
				if(res.code >= 0){
					that.total_action.status[method] = 'end';
				}else{
					that.total_action.status[method] = 'error';
					setActionContent(res.message);
				}
				that.totalActionControl();
			}
		})
	}

	//结束系统升级
	upgrade_obj.upgradeEnd = function(){
		var that = this;
		var method = 'upgradeEnd';

		setActionName('结束系统升级...');

		$.ajax({
			type:'post',
			url : ns.url("admin/system/upgradeEnd"),
			dataType : 'json',
			success:function(res){
				if(res.code >= 0){
					that.total_action.status[method] = 'end';
					that.totalActionControl();
				}else{
					that.total_action.status[method] = 'error';
					layer.msg(res.message);
					setActionContent(res.message);
					that.executeRecovery();
				}
			}
		})
	}

	//执行sql
	upgrade_obj.executeRecovery = function(){
		var that = this;
		setActionName('升级出错，系统正在执行回滚操作，请勿关闭浏览器');
		$.ajax({
			type:'post',
			url : ns.url("admin/system/executeRecovery"),
			dataType : 'json',
			data : {
				upgrade_no : that.data.upgrade_no,
			},
			success:function(data){
				if(data.code >= 0){
					setActionName('回滚完成');
					layer.msg('回滚完成', function(index, layero){
						window.location.href = ns.url("admin/system/upgrade");
					})
				}else{
					layer.msg(data.message);
				}
			}
		})
	}

	var container = document.querySelector('#container');
	var bar = new ProgressBar.Circle(container, {
		color: '#067cf3',
		strokeWidth: 5,  // 正好是从圆心开始画起，>50会越过圆心，<50画出的是圆环
		trailWidth: 0,  // 也设置为50，就是一个未填充的圆形，而不是圆环。要么设置为0
		easing: 'easeInOut',
		duration: 10,
		text: {
			style: {
				color: '#31708f',
				display: 'inline-block',
				position: 'absolute',
				top: '50%',
				left: '50%',
				transform: 'translate(-50%,-50%)'
			},
			autoStyleContainer: false
		},
		fill: '#e1f2fa',   // 圆形内容区域填充色，当需要画圆环时，效果应该最好。
		from: { color: '#aaa', width: 1},
		to: { color: '#333', width: 5},
		step: function(state, circle) {
			circle.path.setAttribute('stroke-width', state.width);

			var value = Math.round(circle.value() * 100);
			circle.setText(value+'%');
		}
	});
	bar.text.style.fontFamily = '"Raleway", Helvetica, sans-serif';
	bar.text.style.fontSize = '32px';

	//升级操作
	function upgradeStart(){
		action_type = 'upgrade';
		$(".up-btn").attr('disabled',true);
		$(".up-footer .up-btn").css({"background-color":"#c0c1c1", "cursor": "not-allowed"});
		upgrade_obj.totalActionControl();
	}

	//控制进度条
	function setProgress(width, text){
		var barValue = width / 100;
		width = width.toFixed(2);
		bar.animate(barValue);
		$(".count-percent").text(width+"%");
		$(".step-content").text(text);
	}

	//设置操作名称
	function setActionName(action_name)
	{
		$(".action-name").text(action_name);
	}

	//设置统计进度
	function setCountPercent(percent)
	{
		bar.animate(percent);
		percent = percent * 100;
		percent = percent.toFixed(2);
		$(".count-percent").text(percent + "%");
	}

	//设置操作名称
	function setActionContent(content){
		$(".step-content").text(content);
	}

	/**
	 * 获取升级信息
	 */
	function getUpgradeInfo() {
		$.ajax({
			type:'post',
			url : ns.url("admin/system/upgradeAction"),
			dataType : 'json',
			success:function(res){
				if(res.code >= 0){
					upgrade_obj.data = res.data;
				}
			}
		})
	}

	/**
	 * 下载升级文件
	 */
	function downloadFile(){
		action_type = 'download';
		$(".up-btn").attr('disabled',true);
		$(".up-footer .up-btn").css({"background-color":"#c0c1c1", "cursor": "not-allowed"});
		upgrade_obj.downloadFile();
	}

	$(function(){
		getUpgradeInfo();
	})

</script>

</body>
</html>