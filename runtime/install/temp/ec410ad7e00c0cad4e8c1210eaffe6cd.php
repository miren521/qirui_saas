<?php /*a:2:{s:61:"/www/wwwroot/city.lpstx.cn/app/install/view/index/step-1.html";i:1600312146;s:53:"/www/wwwroot/city.lpstx.cn/app/install/view/base.html";i:1600314240;}*/ ?>
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

	

<div class="pright layui-form">
	<h3 class="pr-title">阅读许可协议</h3>
	<div class="pr-agreement">

		<strong>版权所有 (c)2016，Niushop开源商城团队保留所有权利。</strong>
		<p>
			感谢您选择Niushop开源商城（以下简称NiuShop）NiuShop基于 PHP + MySQL的技术开发，全部源码开放。 <br />
			为了使你正确并合法的使用本软件，请你在使用前务必阅读清楚下面的协议条款：
		</p>
		<p>
			<strong>一、本授权协议适用且仅适用于Niushop开源商城系统(以下简称Niushop)任何版本，Niushop开源商城官方对本授权协议的最终解释权。</strong>
		</p>
		<p>
			<strong>二、协议许可的权利 </strong>
		<ol>
			<li>非授权用户允许商用，严禁去除Niushop相关的版权信息。</li>
			<li>请尊重Niushop开发人员劳动成果，严禁使用本系统转卖、销售或二次开发后转卖、销售等商业行为。</li>
			<li>任何企业和个人不允许对程序代码以任何形式任何目的再发布。</li>
			<li>您可以在协议规定的约束和限制范围内修改Niushop开源商城源代码或界面风格以适应您的网站要求。</li>
			<li>您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。</li>
			<li>获得商业授权之后，您可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。商业授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。</li>
		</ol>
		</p>
		<p>
			<strong>三、协议规定的约束和限制 </strong>
		<ol>
			<li>未获商业授权之前，允许您对Niushop应用于商业用途，但严禁去除Niushop任何相关的版权信息。</li>
			<li>未经官方许可，不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。</li>
			<li>未经官方许可，禁止在Niushop开源商城的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。</li>
			<li>如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。</li>
		</ol>
		</p>
		<p>
			<strong>四、有限担保和免责声明 </strong>
		<ol>
			<li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</li>
			<li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</li>
			<li>电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装  Niushop，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</li>
			<li>如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。</li>
		</ol>
		</p>
	</div>
	<div class="btn-box">
		<div class="btn-box-text">
			<div class="layui-form-item">
				<input type="checkbox" name="readpact" id="readpact"title="我已经阅读并同意此协议" value="1" lay-skin="primary">
			</div>
		</div>
		<button class="layui-btn ns-bg-color" lay-submit lay-filter="go">继续</button>
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
	layui.use('form', function(){
		var form = layui.form;

		form.on('submit(go)', function(data) {
			var readpact = data.field.readpact;
			if(readpact == 1){
				window.location.href = '<?php echo htmlentities($root_url); ?>/install.php?step=2';
			}else{
				error('您必须同意软件许可协议才能安装！');
			}

		});
	});
</script>


</body>
</html>
