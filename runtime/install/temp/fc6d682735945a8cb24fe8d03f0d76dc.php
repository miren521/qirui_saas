<?php /*a:2:{s:65:"/www/wwwroot/saas.goodsceo.com/app/install/view/index/step-3.html";i:1600312146;s:57:"/www/wwwroot/saas.goodsceo.com/app/install/view/base.html";i:1600314240;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1" />
	<title>安装程序 - niushop多商户</title>
	<!--<script src="http://saas.goodsceo.com/app/install/view/public/js/jquery-2.2.js"></script>-->
	<link rel="icon" type="image/x-icon" href="http://saas.goodsceo.com/public/static/img/shop_bitbug_favicon.ico" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/app/install/view/public/css/style.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/public/static/ext/layui/css/layui.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/app/shop/view/public/css/common.css" />
	<script src="http://saas.goodsceo.com/public/static/js/jquery-3.1.1.js"></script>
	<script src="http://saas.goodsceo.com/public/static/ext/layui/layui.js"></script>

	<script>
		layui.use(['layer', 'upload', 'element'], function() {});
		window.ns_url = {
			baseUrl: "http://saas.goodsceo.com/install.php/",
			route: ['install', '<?php echo request()->controller(); ?>', '<?php echo request()->action(); ?>'],
		};
	</script>
	<script src="http://saas.goodsceo.com/app/install/view/public/js/common.js"></script>
	
<style>
    .install-content-procedure .content-procedure-item:first-of-type{
        background: url("http://saas.goodsceo.com/app/install/view/public/img/complete_two.png") no-repeat center / contain;
        color: #fff;
    }
    .install-content-procedure .content-procedure-item:nth-child(2){
        background: url("http://saas.goodsceo.com/app/install/view/public/img/complete_four.png") no-repeat center / contain;
        color: #fff;
    }
    .install-content-procedure .content-procedure-item:nth-child(3){
        background: url("http://saas.goodsceo.com/app/install/view/public/img/conduct.png") no-repeat center / contain;
        color: #fff;
    }
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

	
<div id='postloader' class='waitpage'></div>
<form  class="layui-form" >
    <div class="testing parameter">
        <div class="testing-item">
            <h3>数据库设定</h3>
            <table border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
                <tr>
                    <td class="onetd"><span class="required">*</span>数据库主机：</td>
                    <td>
                        <input name="dbhost" id="dbhost" type="text" lay-verify="empty" placeholder="请输入数据库主机" value="" class="input-txt" />
                        <small>一般为localhost</small>
                    </td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span>Mysql端口：</td>
                    <td>
                        <input name="dbport" id="dbport" type="text" value="3306" class="input-txt"lay-verify="empty" placeholder="请输入Mysql端口"/>
                        <small>一般为3306</small>
                    </td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span>数据库用户：</td>
                    <td>
                        <input name="dbuser" id="dbuser" type="text" value="" class="input-txt" lay-verify="empty" placeholder="请输入数据库用户"/>
                        <small>默认root</small>
                    </td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span>数据库密码：</td>
                    <td>
                        <div style='float:left;margin-right:3px;'>
                            <input name="dbpwd" id="dbpwd" type="text" class="input-txt" lay-verify="empty" placeholder="请输入数据库密码" />
                        </div>
                        <div style='float:left' class="mysql-message" id='dbpwdsta'></div>
                    </td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span>数据库名称：</td>
                    <td>
                        <div style='float:left;margin-right:3px;'><input name="dbname" id="dbname" type="text" value="" class="input-txt" lay-verify="empty" placeholder="请输入数据库名称" /></div>
                        <div style='float:left' class="mysql-message" id='havedbsta'></div>
                    </td>
                </tr>
                <tr>
                    <td class="onetd">数据表前缀：</td>
                    <td>
                        <div style='float:left;margin-right:3px;'><input name="dbprefix" id="dbprefix" type="text" value="" class="input-txt" placeholder="请输入数据表前缀"/></div>
                    </td>
                </tr>

                <tr>
                    <td class="onetd">数据库编码：</td>
                    <td>
                        <label class="install-code">UTF8</label>

                    </td>
                </tr>
            </table>
        </div>
        <div class="testing-item">
            <h3>平台设定</h3>
            <table border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
                <tr>
                    <td class="onetd"><span class="required">*</span><strong>平台名称：</strong></td>
                    <td><input name="site_name" id="site_name" type="text" value="" class="input-txt"lay-verify="empty" placeholder="请输入平台名称"/>
                        <small id="mess_site_name">站点名称 必填</small></td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span><strong>平台用户名：</strong></td>
                    <td><input name="username" id="username" type="text" value="" class="input-txt" lay-verify="empty" placeholder="请输入平台用户名"/>
                        <small id="mess_username">管理员用户名 必填</small></td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span><strong>平台密码：</strong></td>
                    <td><input name="password" id="password" type="password" value="" class="input-txt"lay-verify="empty" placeholder="请输入平台密码"/>
                        <small id="mess_password">密码 必填</small></td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span><strong>确认密码：</strong></td>
                    <td><input name="password2" id="password2" type="password" value="" class="input-txt"lay-verify="empty" placeholder="请输入平台确认密码"/>
                        <small id="mess_password2">确认密码 必填</small></td>
                </tr>
            </table>
        </div>

        <div class="testing-item">
            <h3>官方直营店设定</h3>
            <table border="0" align="center" cellpadding="0" cellspacing="0" class="twbox">
                <tr>
                    <td class="onetd"><span class="required">*</span><strong>店铺名称：</strong></td>
                    <td><input name="shop_name" id="shop_name" type="text" value="" class="input-txt"lay-verify="empty" placeholder="请输入店铺名称"/>
                        <small id="mess_shop_name">店铺名称 必填</small></td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span><strong>店铺用户名：</strong></td>
                    <td><input name="shop_username" id="shop_username" type="text" value="" class="input-txt" lay-verify="empty" placeholder="请输入店铺用户名"/>
                        <small id="mess_shop_username">商家用户名 必填</small></td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span><strong>店铺密码：</strong></td>
                    <td><input name="shop_password" id="shop_password" type="password" value="" class="input-txt"lay-verify="empty" placeholder="请输入店铺密码"/>
                        <small id="mess_shop_password">密码 必填</small></td>
                </tr>
                <tr>
                    <td class="onetd"><span class="required">*</span><strong>确认密码：</strong></td>
                    <td><input name="shop_password2" id="shop_password2" type="password" value="" class="input-txt"lay-verify="empty" placeholder="请输入店铺确认密码"/>
                        <small id="mess_shop_password2">确认密码 必填</small></td>
                </tr>
            </table>
        </div>
        <div class="btn-box">

        </div>

        <div class="btn-box">
            <input type="button" class="btn-back" value="后退" onclick="window.location.href='<?php echo htmlentities($root_url); ?>/install.php?step=2'" />
            <input type="button" class="btn-next" lay-submit lay-filter="install"value="开始安装" id="form_submit">
        </div>
    </div>
</form>


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

<script language="javascript" type="text/javascript">
    ControlContent(2);

    var is_install = false;
    layui.use('form', function(){
        var form = layui.form;
        form.verify({
            empty: function(value, item){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    var msg = $(item).attr("placeholder");
                    return msg;
                }
            }

        });
        form.on('submit(install)', function(data){
            var index = layer.load(2);
            $.ajax({ //post也可
                url: '<?php echo htmlentities($root_url); ?>/install.php/index/testdb',
                data: data.field,
                type: "post",
                dataType: 'json',
                success: function(res){
                    if(res.code >= 0){
                        if(res.data.status == '-1'){
                            layer.closeAll(); //疯狂模式，关闭所有层
                            error(res.message);
                            return false;
                        }
                        if(res.data.status == 2){
                            layer.confirm('数据库存在，系统将覆盖数据库!', {
                                btn: ['继续','取消'] //按钮
                            }, function(){
                                layer.closeAll(); //疯狂模式，关闭所有层
                                install(data.field);

                            }, function(){
                                layer.closeAll(); //疯狂模式，关闭所有层
                                return false;
                            });
                        }else{
                            layer.closeAll(); //疯狂模式，关闭所有层
                            install(data.field);
                        }

                    }else{
                        layer.closeAll(); //疯狂模式，关闭所有层
                        error(res.message);
                    }
                }
            });
            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });


        function install(data){

            if(is_install){
                return false;
            }
            document.getElementById('form_submit').disabled= true;
            $("#form_submit").val("正在安装...");
            $("#form_submit").addClass("installimg-btn");
            var index = layer.load(2);
            is_install = true;

            $.ajax({
                url: "<?php echo htmlentities($root_url); ?>/install.php?step=4",
                data: data,
                dataType: 'json',
                type: 'post',
                success : function(data) {
                    layer.close(index);
                    if(data.code < 0){
                        error(data.message);
                        is_install = false;
                        document.getElementById('form_submit').disabled= false;
                        $("#form_submit").val("开始安装");
                        $("#form_submit").removeClass("installimg-btn");
                    }else{
                        window.location.href = '<?php echo htmlentities($root_url); ?>/install.php/index/installSuccess'
                    }
                }
            })
        }
    });

</script>


</body>
</html>
