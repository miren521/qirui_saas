<?php /*a:2:{s:61:"/www/wwwroot/city.lpstx.cn/app/install/view/index/step-2.html";i:1600312146;s:53:"/www/wwwroot/city.lpstx.cn/app/install/view/base.html";i:1600314240;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1" />
	<title>安装程序 - niushop多商户</title>
	<!--<script src="http://city.lpstx.cn/app/install/view/public/js/jquery-2.2.js"></script>-->
	<link rel="icon" type="image/x-icon" href="http://city.lpstx.cn/public/static/img/shop_bitbug_favicon.ico" />
	<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/app/install/view/public/css/style.css" />
	<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/public/static/ext/layui/css/layui.css" />
	<link rel="stylesheet" type="text/css" href="http://city.lpstx.cn/app/shop/view/public/css/common.css" />
	<script src="http://city.lpstx.cn/public/static/js/jquery-3.1.1.js"></script>
	<script src="http://city.lpstx.cn/public/static/ext/layui/layui.js"></script>

	<script>
		layui.use(['layer', 'upload', 'element'], function() {});
		window.ns_url = {
			baseUrl: "http://city.lpstx.cn/install.php/",
			route: ['install', '<?php echo request()->controller(); ?>', '<?php echo request()->action(); ?>'],
		};
	</script>
	<script src="http://city.lpstx.cn/app/install/view/public/js/common.js"></script>
	


</head>

<body>
<div class="head-block">
	<div class="top">
		<div class="top-logo"></div>
		<div class="top-sub" style="flex:1;">开源商城多商户V4</div>
		<ul class="top-link">
			<li><a href="https://www.niushop.com" target="_blank">官方网站</a></li>
			<li><a href="https://bbs.niushop.com" target="_blank">技术论坛</a></li>
		</ul>
	</div>
</div>
<div class="step-content">
	<div style="width:1000px;margin:0 auto;">

		<!--  标题进度条 start-->
		<div class="content" style="margin:30px 0 0 0;width: 100%;">
			<div class="processBar">
				<div class="text" style="margin: 10px -25px;"><span class='poetry'>1.许可协议</span></div>
				<div id="line0" class="bar">
					<div id="point0" class="c-step c-select"></div>
				</div>

			</div>
			<div class="processBar">
				<div class="text" style="margin: 10px -30px;"><span class='poetry'>2.环境检测</span></div>
				<div id="line1" class="bar">
					<div id="point1" class="c-step"></div>
				</div>

			</div>
			<div class="processBar">
				<div class="text" style="margin: 10px -30px;"><span class='poetry'>3.参数配置</span></div>
				<div id="line2" class="bar">
					<div id="point2" class="c-step"></div>
				</div>
			</div>
			<div class="processBar" style="width:10px;">
				<div class="text" style="margin: 10px -39px;"><span class='poetry'>4.安装完成</span></div>
				<div id="line3" class="bar" style="width: 0;">
					<div id="point3" class="c-step"></div>
				</div>
			</div>
		</div>
		<!--  标题进度条 end-->
		<div style="clear: both;"></div>
	</div>
</div>
<div class="install-content">

	

<div class="testing">
	<div class="testing-item">
		<h3>服务器信息</h3>
		<table border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
			<tr>
				<th width="30%" align="center"><strong>参数</strong></th>
				<th width="70%"><strong>值</strong></th>
			</tr>
			<tr>
				<td><strong>服务器域名</strong></td>
				<td><?php echo htmlentities($name); ?></td>
			</tr>
			<tr>
				<td><strong>服务器操作系统</strong></td>
				<td><?php echo htmlentities($os); ?></td>
			</tr>
			<tr>
				<td><strong>服务器解译引擎</strong></td>
				<td><?php echo htmlentities($server); ?></td>
			</tr>
			<tr>
				<td><strong>PHP版本</strong></td>
				<td><?php echo htmlentities($phpv); ?></td>
			</tr>
			<tr>
				<td><strong>系统安装目录</strong></td>
				<td><?php echo htmlentities($root_path); ?></td>
			</tr>
		</table>
	</div>
	<div class="testing-item">
		<h3>系统环境检测<span class="desc">系统环境要求必须满足下列所有条件，否则系统或系统部份功能将无法使用。</span></h3>
		<table  border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
			<tr>
				<th width="30%" align="center"><strong>需开启的变量或函数</strong></th>
				<th width="35%"><strong>要求</strong></th>
				<th width="35%"><strong>实际状态及建议</strong></th>
			</tr>
			<tr>
				<td>php版本</td>
				<td style="color:#ff8143;">php7.1及以上 </td>
				<td><font color=<?php if($verison): ?>#ff8143<?php else: ?>red<?php endif; ?>><?php echo htmlentities($phpv); ?></font> </td>
			</tr>
			<?php foreach($system_variables as $variables_key => $variables_item): ?>
			<tr>
				<td><?php echo htmlentities($variables_item['name']); ?></td>
				<td><?php echo htmlentities($variables_item['need']); ?></td>
				<td><img clsss="check-icon" src="<?php if($variables_item['status']): ?>http://city.lpstx.cn/app/install/view/public/img/success.png<?php else: ?>http://city.lpstx.cn/app/install/view/public/img/error.png<?php endif; ?>"/></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>


	<div class="testing-item">
		<h3>目录权限检测<span class="desc">系统要求必须满足下列所有的目录权限全部可读写的需求才能使用，其它应用目录可安装后在管理后台检测。</span></h3>
		<table border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
			<tr>
				<th width="30%" align="center"><strong>目录名</strong></th>
				<th width="35%"><strong>读取权限</strong></th>
				<th width="35%"><strong>写入权限</strong></th>
			</tr>
			<?php foreach($dirs_list as $dirs_key => $dirs_item): ?>
			<tr>
				<td><?php echo htmlentities($dirs_item['path_name']); ?></td>
				<td>
					<img clsss="check-icon" src="<?php if($dirs_item['is_readable']): ?>http://city.lpstx.cn/app/install/view/public/img/success.png<?php else: ?>http://city.lpstx.cn/app/install/view/public/img/error.png<?php endif; ?>"/>
				</td>
				<td>
					<img clsss="check-icon" src="<?php if($dirs_item['is_write']): ?>http://city.lpstx.cn/app/install/view/public/img/success.png<?php else: ?>http://city.lpstx.cn/app/install/view/public/img/error.png<?php endif; ?>"/>
				</td>
			</tr>
			<?php endforeach; ?>

		</table>
	</div>


	<div class="btn-box">
		<input type="button" class="btn-back" value="后退" onclick="window.location.href = '<?php echo htmlentities($root_url); ?>/install.php?step=1';" />
		<input type="button" class="btn-next" value="继续" <?php if(!$continue): ?> style="background-color:#777;color:#FFF;"disabled="disabled"<?php endif; ?> onclick="window.location.href = '<?php echo htmlentities($root_url); ?>/install.php?step=3';" />
	</div>
</div>


</div>

<script>
	var index=0;
	$(document).ready(function(){
		$("#education").addClass('main-hide');
		$("#work").addClass('main-hide');
		$("#social").addClass('main-hide');
		$('#previous_step').hide();

		/*上一步*/
		$('#previous_step').bind('click', function () {
			index--;
			ControlContent(index);
		});
		/*下一步*/
		$('#next_step').bind('click', function () {
			index++;
			ControlContent(index);
		});


	});

	function ControlContent(index) {
		var stepContents = ["basicInfo","education","work","social"];
		var key;//数组中元素的索引值
		for (key in stepContents) {
			var stepContent = stepContents[key];//获得元素的值
			if (key == index) {
				if(stepContent=='basicInfo'){
					$('#previous_step').hide();
				}else{
					$('#previous_step').show();
				}
				if(stepContent=='social'){
					$('#next_step').hide();
				}else{
					$('#next_step').show();
				}
				$('#'+stepContent).removeClass('main-hide');
				$('#point'+key).addClass('c-select');
				$('#line'+key).removeClass('b-select');
			}else {
				$('#'+stepContent).addClass('main-hide');
				if(key>index){
					$('#point'+key).removeClass('c-select');
					$('#line'+key).removeClass('b-select');
				}else if(key<index){
					$('#point'+key).addClass('c-select');
					$('#line'+key).addClass('b-select');
				}
			}
		}

	}

	function success(message){
		layer.alert(message, {
			icon: 1,
			skin: 'layer-ext-moon',
			title:'提示'
		})
	}
	function error(message){
		layer.alert(message, {
			icon: 2,
			skin: 'layer-ext-moon',
			title:'提示'
		})
	}
</script>


<script>
	ControlContent(1);
</script>


</body>
</html>
