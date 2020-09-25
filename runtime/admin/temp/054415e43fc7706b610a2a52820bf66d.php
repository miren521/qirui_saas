<?php /*a:2:{s:55:"/www/wwwroot/city.lpstx.cn/app/admin/view/diy/edit.html";i:1600314240;s:24:"app/admin/view/base.html";i:1600312146;}*/ ?>
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
	
<link rel="stylesheet" href="http://city.lpstx.cn/public/static/ext/color_picker/css/colorpicker.css" />
<link rel="stylesheet" href="http://city.lpstx.cn/public/static/ext/diyview/css/diyview.css" />

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
				
				
<style type="text/css">
	.empty-box{
		width: 100%;
		height: 100%;
	}
</style>
<div class="diy-view-wrap">
	<div id="diyView" class='layui-form'>
		<div class="component-list-box">
			<!-- 组件列表 -->
			<nav class="component-list">
				<?php if(is_array($diy_view_utils) || $diy_view_utils instanceof \think\Collection || $diy_view_utils instanceof \think\Paginator): if( count($diy_view_utils)==0 ) : echo "" ;else: foreach($diy_view_utils as $k=>$vo): ?>
				<h3><?php echo htmlentities($vo['type_name']); ?></h3>
				<ul>
					<?php if(is_array($vo['list']) || $vo['list'] instanceof \think\Collection || $vo['list'] instanceof \think\Paginator): if( count($vo['list'])==0 ) : echo "" ;else: foreach($vo['list'] as $li_k=>$li): ?>
					<li title="<?php echo htmlentities($li['title']); ?>"
					    <?php if($li['value']): ?>v-on:click='addComponent(<?php echo htmlentities($li['value']); ?>,{ name : "<?php echo htmlentities($li['name']); ?>", title : "<?php echo htmlentities($li['title']); ?>", max_count : <?php echo htmlentities($li['max_count']); ?>, addon_name : "<?php echo htmlentities($li['addon_name']); ?>", controller : "<?php echo htmlentities($li['controller']); ?>" })'
					    v-bind:class="{ 'disabled' : !checkComponentIsAdd('<?php echo htmlentities($li['name']); ?>',<?php echo htmlentities($li['max_count']); ?>) }"
					    <?php if($li['support_diy_view']): ?>
					    class="hot"
					    <?php endif; else: ?>class="disabled"<?php endif; ?>
					><?php echo htmlentities($li['title']); ?></li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</nav>
			
			<div class="custom-save">
				<button class="layui-btn ns-bg-color save" lay-submit="" lay-filter="save">保存</button>
			</div>
		</div>
		
		<div class="components-box">
			<div class="preview-head"  :class="{'active' : global.textColor=='#ffffff'}" :style="{'background-color':global.bgTopColor}" v-on:click="changeCurrentIndex(-99)">
				<span :style="{color: global.textColor}">{{ global.title }}</span>
				<div v-bind:class="{selected : currentIndex==-99}" v-bind:data-sort="-99" @click.stop="" style="display:none;" v-show="data.length==0 || currentIndex==-99">
					<div class="edit-attribute">
						<div class="layui-form-item">
							<label class="layui-form-label sm">模板名称</label>
							<div class="layui-input-block">
								<input type="text" v-model="global.title" placeholder="请输入模板名称" class="layui-input">
							</div>
						</div>
						
						<div class="layui-form-item">
							<label class="layui-form-label sm">底部导航</label>
							<div class="layui-input-block">
								<div v-on:click="global.openBottomNav=true" class="layui-unselect layui-form-radio" v-bind:class="{ 'layui-form-radioed' : global.openBottomNav==true }"><i class="layui-anim layui-icon" >&#xe643;</i><div>显示</div></div>
								<div v-on:click="global.openBottomNav=false" class="layui-unselect layui-form-radio" v-bind:class="{ 'layui-form-radioed' : global.openBottomNav == false }"><i class="layui-anim layui-icon" >&#xe643;</i><div>不显示</div></div>
								<!-- <div class="layui-unselect layui-form-switch" v-on:click="global.openBottomNav=!global.openBottomNav" v-bind:class="{ 'layui-form-onswitch' : global.openBottomNav }" lay-skin="_switch">
									<em v-if="global.openBottomNav"></em>
									<em v-else></em>
									<i></i>
								</div> -->
							</div>
						</div>
						<div>
							<label class="layui-form-label sm">字体颜色</label>
							<div v-on:click="global.textColor='#ffffff'" class="layui-unselect layui-form-radio" v-bind:class="{'layui-form-radioed' : global.textColor=='#ffffff' }"><i class="layui-anim layui-icon">&#xe643;</i><div>白色</div></div>
							<div v-on:click="global.textColor='#333333'" class="layui-unselect layui-form-radio" v-bind:class="{'layui-form-radioed' : global.textColor=='#333333' }"><i class="layui-anim layui-icon">&#xe643;</i><div>黑色</div></div>
						</div>
						<color v-bind:data="{ field : 'bgTopColor', label : '顶部背景', value : '#ffffff' }"></color>
						<color v-bind:data="{ field : 'bgColor', label : '页面背景', value : '#ffffff' }"></color>
						<div class="layui-form-item">
							<label class="layui-form-label sm">背景图片</label>
							<div class="layui-input-block">
								<img-upload v-bind:data="{ data : global, field : 'bgUrl' }"></img-upload>
							</div>
						</div>
					
					</div>
				
				</div>
			
			</div>
			
			<div class="preview-block" v-bind:style="{ backgroundColor : global.bgColor,backgroundImage : 'url('+changeImgUrl(global.bgUrl)+')',}">
				<template v-for="(nc,index) in data" v-bind:k="index">
					<div v-bind:data-index="index" v-on:click="changeCurrentIndex(nc.index)" v-bind:class="{ 'draggable-element nc-border-color-selected' : true,selected : currentIndex == nc.index }" v-bind:data-sort="index">
						<?php if(is_array($diy_view_utils) || $diy_view_utils instanceof \think\Collection || $diy_view_utils instanceof \think\Paginator): if( count($diy_view_utils)==0 ) : echo "" ;else: foreach($diy_view_utils as $key=>$vo): if(is_array($vo['list']) || $vo['list'] instanceof \think\Collection || $vo['list'] instanceof \think\Paginator): if( count($vo['list'])==0 ) : echo "" ;else: foreach($vo['list'] as $key=>$li): ?>
						<template v-if="nc.type == '<?php echo htmlentities($li['name']); ?>'">
							<?php echo event('DiyViewUtils',['controller'=>$li['controller'],'addon_name'=>$li['addon_name']],true); ?>
						</template>
						<?php endforeach; endif; else: echo "" ;endif; ?>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
				</template>
				
			</div>
			<div class="empty-box">
				
			</div>
		</div>
		
		<?php if(!(empty($qrcode_info) || (($qrcode_info instanceof \think\Collection || $qrcode_info instanceof \think\Paginator ) && $qrcode_info->isEmpty()))): ?>
		<div class="popup-qrcode-wrap" v-if="currentIndex===null">
			
			<img src="<?php echo img($qrcode_info['path']['h5']['img']); ?>" alt="推广二维码">
			<p class="qrcode-item-description">扫码后直接访问页面</p>
			<a class="ns-text-color" href="javascript:ns.copy('h5_url');">复制链接</a>
			<a class="ns-text-color" href="<?php echo img($qrcode_info['path']['h5']['img']); ?>" download>下载二维码</a>
			<input class="layui-input nc-len-mid" type="text" value="<?php echo htmlentities($qrcode_info['path']['h5']['url']); ?>" id="h5_url" readonly>
		</div>
		<?php endif; ?>
	</div>
</div>

<?php if(!empty($diy_view_info) && !empty($diy_view_info['value'])): ?>
<input type="hidden" id="info" value='<?php echo htmlentities($diy_view_info['value']); ?>' />
<?php endif; if(!empty($diy_view_info) && !empty($diy_view_info['name'])): ?>
<input type="hidden" id="name" value="<?php echo htmlentities($diy_view_info['name']); ?>" />
<?php elseif(!empty($name)): ?>
<input type="hidden" id="name" value="<?php echo htmlentities($name); ?>" />
<?php else: ?>
<input type="hidden" id="name" value="DIY_VIEW_RANDOM_<?php echo htmlentities($time); ?>" />
<?php endif; ?>

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
	var STATICIMG = 'http://city.lpstx.cn/public/static/img';
	var link_url = '<?php echo htmlentities($app_module); ?>/diy/link';
	var module = '<?php echo htmlentities($app_module); ?>';
</script>
<script src="http://city.lpstx.cn/public/static/js/vue.js"></script>
<script src="http://city.lpstx.cn/public/static/ext/color_picker/js/colorpicker.js"></script>
<script src="http://city.lpstx.cn/public/static/ext/diyview/js/async_load_css.js"></script>
<script src="http://city.lpstx.cn/public/static/ext/diyview/js/ddsort.js"></script>
<script src="http://city.lpstx.cn/public/static/ext/ueditor/ueditor.config.js"></script>
<script src="http://city.lpstx.cn/public/static/ext/ueditor/ueditor.all.js"> </script>
<script src="http://city.lpstx.cn/public/static/ext/ueditor/lang/zh-cn/zh-cn.js"></script>
<script src="http://city.lpstx.cn/public/static/ext/diyview/js/components.js"></script>
<script src="http://city.lpstx.cn/public/static/ext/diyview/js/custom_template.js"></script>
<script>
	
	<?php if(!empty($diy_view_info) && (!empty($diy_view_info['value']) || !empty($diy_view_info['id']) )): ?>
		var id = "<?php echo htmlentities($diy_view_info['id']); ?>";
		var info = JSON.parse($("#info").val().toString());
		
		if(!$.isEmptyObject(info) && info.value){
			for(var i=0;i<info.value.length;i++) vue.addComponent(info.value[i]);
			vue.setGlobal(info.global);
		}else{
			vue.setGlobal({ title : "<?php echo htmlentities($diy_view_info['title']); ?>" });
		}
		vue.title = "<?php echo htmlentities($diy_view_info['title']); ?>";
	<?php else: ?>
		var id = 0;
	<?php endif; ?>

	var repeat_flag = false;//防重复标识
	$("button.save").click(function(){

		if(vue.verify()){
			
			//全局属性
			var global = JSON.stringify(vue.global);
			global = eval("("+global+")");
			
			//组件属性
			var value = JSON.stringify(vue.data);
			value = eval(value);
			
			//重新排序
			value.sort(function(a,b){
				return a.sort-b.sort;
			});
			
			for(var item in value){
				delete value[item].verify;
				delete value[item].lazyLoad;
				delete value[item].lazyLoadCss;
				delete value[item].index;
				delete value[item].sort;
				delete value[item].outerCountJs;
			}
			
			if(repeat_flag) return;
			repeat_flag = true;
			
			var v = {
				global : global,
				value : value
			};

			$.ajax({
				type : "post",
				url : "<?php echo addon_url($request_url); ?>",
				data : { id : id, name : $("#name").val(), title : vue.global.title, value : JSON.stringify(v) },
				dataType : "JSON",
				success : function(res) {
					layer.msg(res.message);
					if (res.code == 0) {
						
						if (id > 0 || $("#name").val() != "DIY_VIEW_RANDOM_<?php echo htmlentities($time); ?>") {
							location.reload();
						} else {
							location.href = ns.url("<?php echo htmlentities($app_module); ?>/diy/lists");
						}
						
					} else {
						repeat_flag = false;
					}
				}
			});
		}
	});
</script>

<script type="text/javascript">
	var height = $('.layui-side').height() - 45 - 20;
	$('.components-box').css('height', height);
	$('.components-box .empty-box').css('height', height/2);
</script>

</body>
</html>