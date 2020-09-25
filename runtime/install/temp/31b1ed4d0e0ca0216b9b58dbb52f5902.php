<?php /*a:2:{s:61:"/www/wwwroot/city.lpstx.cn/app/install/view/index/step-4.html";i:1600314240;s:53:"/www/wwwroot/city.lpstx.cn/app/install/view/base.html";i:1600314240;}*/ ?>
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
	
<style>
	.install-content-procedure .content-procedure-item:first-of-type{
		background: url("http://city.lpstx.cn/app/install/view/public/img/complete_two.png") no-repeat center / contain;
		color: #fff;
	}
	.install-content-procedure .content-procedure-item:nth-child(2), .install-content-procedure .content-procedure-item:nth-child(3){
		background: url("http://city.lpstx.cn/app/install/view/public/img/complete_four.png") no-repeat center / contain;
		color: #fff;
	}
	.install-content-procedure .content-procedure-item:nth-child(4){
		background: url("http://city.lpstx.cn/app/install/view/public/img/complete_three.png") no-repeat center / contain;
		color: #fff;
	}
	.install-content-procedure{border: none;}
</style>

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

	
<div class="install-success">
	<div class="install-success-box">
		<img class="install-success-pic" src="http://city.lpstx.cn/app/install/view/public/img/install_complete.png" alt="">
		<div class="install-success-text">
			<p class="install-success-title">恭喜您！已成功安装Niushop多商户商城系统。</p>
			<p class="install-success-desc">建议删除安装目录install后使用</p>
		</div>
	</div>
</div>
<div class="other-links">
	<p class="other-links-title">您现在可以访问：</p>
	<ul class="other-links-list">
		<li class="other-links-item">
			<div  class="other-links-pic">
				<img src="http://city.lpstx.cn/app/install/view/public/img/site_backstage.png" alt="">
			</div>
			<a href="<?php echo htmlentities($root_url); ?>/index.php/admin" class="other-links-text" target="_blank">网站后台</a>
		</li>
		<li class="other-links-item">
			<div  class="other-links-pic">
				<img src="http://city.lpstx.cn/app/install/view/public/img/site_index.png" alt="">
			</div>
			<a href="<?php echo htmlentities($root_url); ?>/index.php/shop" class="other-links-text" target="_blank">店铺后台</a>
		</li>
		<li class="other-links-item">
			<div  class="other-links-pic">
				<img src="http://city.lpstx.cn/app/install/view/public/img/official_website.png" alt="">
			</div>
			<a href="https://www.niushop.com" class="other-links-text" target="_blank">NIUSHOP官方网站</a>
		</li>
		<li class="other-links-item">
			<div  class="other-links-pic">
				<img src="http://city.lpstx.cn/app/install/view/public/img/forum.png" alt="">
			</div>
			<a href="http://bbs.niushop.com.cn" class="other-links-text" target="_blank">NIUSHOP官方论坛</a>
		</li>
	</ul>
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
	ControlContent(3);
</script>


</body>
</html>
