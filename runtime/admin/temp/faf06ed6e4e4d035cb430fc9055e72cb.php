<?php /*a:2:{s:64:"/www/wwwroot/saas.goodsceo.com/app/admin/view/upload/config.html";i:1600312146;s:55:"/www/wwwroot/saas.goodsceo.com/app/admin/view/base.html";i:1600312146;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1" />
	<title><?php echo htmlentities((isset($menu_info['title']) && ($menu_info['title'] !== '')?$menu_info['title']:"")); ?> - <?php echo htmlentities((isset($website['title']) && ($website['title'] !== '')?$website['title']:"Niushop开源商城")); ?></title>
	<meta name="keywords" content="<?php echo htmlentities((isset($website['keywords']) && ($website['keywords'] !== '')?$website['keywords']:'Niushop开源商城')); ?>">
	<meta name="description" content="<?php echo htmlentities((isset($website['desc']) && ($website['desc'] !== '')?$website['desc']:'描述')); ?>}">
	<link rel="icon" type="image/x-icon" href="http://saas.goodsceo.com/public/static/img/bitbug_favicon.ico" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/public/static/css/iconfont.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/public/static/ext/layui/css/layui.css" />
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/public/static/loading/msgbox.css"/>
	<link rel="stylesheet" type="text/css" href="http://saas.goodsceo.com/app/admin/view/public/css/common.css" />
	<script src="http://saas.goodsceo.com/public/static/js/jquery-3.1.1.js"></script>
	<script src="http://saas.goodsceo.com/public/static/ext/layui/layui.js"></script>
	<script>
		layui.use(['layer', 'upload', 'element'], function() {});
		window.ns_url = {
			baseUrl: "http://saas.goodsceo.com/index.php/",
			route: ['<?php echo request()->module(); ?>', '<?php echo request()->controller(); ?>', '<?php echo request()->action(); ?>'],
		};
	</script>
	<script src="http://saas.goodsceo.com/public/static/js/common.js"></script>
	<style>
		.ns-calendar{background: url("http://saas.goodsceo.com/public/static/img/ns_calendar.png") no-repeat center / 16px 16px;}
	</style>
	
<style>
	.ns-watermark-img, .ns-watermark-font { display: none; }
</style>

	<script type="text/javascript">
	</script>
</head>
<body>

<!-- logo -->
<div class="ns-logo">
	<div class="logo-box">
		<img src="http://saas.goodsceo.com/app/admin/view/public/img/logo.png">
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
						<img src="http://saas.goodsceo.com/app/admin/view/public/img/default_headimg.png" alt="">
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
				
				

<div class="layui-form">
	
	<!-- 基础上传 -->
	<div class="layui-card ns-card-common ns-card-brief">
		<div class="layui-card-header">
			<span class="ns-card-title">基础上传</span>
		</div>
		<div class="layui-card-body">
			<div class="layui-form-item">
				<label class="layui-form-label">上传大小：</label>
				<div class="layui-input-block">
					<div class="layui-input-inline">
						<input name="max_filesize" type="number" lay-verify="num" value="<?php echo htmlentities($config['value']['upload']['max_filesize']); ?>" class="layui-input ns-len-short">
					</div>
					<div class="layui-form-mid">b</div>
				</div>
				<div class="ns-word-aux">允许上传的文件大小，0为不限制</div>
			</div>

			<div class="layui-form-item">
				<label class="layui-form-label">图片扩展名：</label>
				<div class="layui-input-block">
					<input name="image_allow_ext" type="text" value="<?php echo htmlentities($config['value']['upload']['image_allow_ext']); ?>" class="layui-input ns-len-long">
				</div>
				<div class="ns-word-aux">设置允许上传的文件扩展名，多个扩展名之间用“,”隔开，如不填则为不限制</div>
			</div>

			<div class="layui-form-item">
				<label class="layui-form-label">图片Mime类型：</label>
				<div class="layui-input-block">
					<input name="image_allow_mime" type="text" value="<?php echo htmlentities($config['value']['upload']['image_allow_mime']); ?>" class="layui-input ns-len-long">
				</div>
				<div class="ns-word-aux">设置允许上传的文件mime类型，多个类型之间用“,”隔开，如不填则为不限制</div>
			</div>
		</div>
	</div>

	<!-- 缩略图上传 -->
	<div class="layui-card ns-card-common ns-card-brief">
		<div class="layui-card-header">
			<span class="ns-card-title">缩略图</span>
		</div>
		<div class="layui-card-body">
			<div class="layui-form-item">
				<label class="layui-form-label">自定义裁剪位置：</label>
				<div class="layui-input-block">
					<?php foreach($position as $thumb_position_k => $thumb_position_v): ?>
						<input type="radio" name="thumb_position" value="<?php echo htmlentities($thumb_position_k); ?>" lay-filter="thumbPosition" title="<?php echo htmlentities($thumb_position_v); ?>" <?php echo $config['value']['thumb']['thumb_position']==$thumb_position_k ? 'checked'  :  ''; ?> />
					<?php endforeach; ?>
				</div>
			</div>

			<div class="layui-form-item">
				<label class="layui-form-label">缩略大图（单位：px）：</label>
				<div class="layui-input-block">
					<div class="layui-form-mid">宽</div>
					<div class="layui-input-inline">
						<input name="thumb_big_width" type="number" value="<?php echo htmlentities($config['value']['thumb']['thumb_big_width']); ?>" lay-verify="int" class="layui-input ns-len-short">
					</div>
					<div class="layui-form-mid">高</div>
					<div class="layui-input-inline">
						<input name="thumb_big_height" type="number" value="<?php echo htmlentities($config['value']['thumb']['thumb_big_height']); ?>" lay-verify="int" class="layui-input ns-len-short">
					</div>
				</div>
			</div>

			<div class="layui-form-item">
				<label class="layui-form-label">缩略中图（单位：px）：</label>
				<div class="layui-input-block">
					<div class="layui-form-mid">宽</div>
					<div class="layui-input-inline">
						<input name="thumb_mid_width" type="number" value="<?php echo htmlentities($config['value']['thumb']['thumb_mid_width']); ?>" lay-verify="int" class="layui-input ns-len-short">
					</div>
					<div class="layui-form-mid">高</div>
					<div class="layui-input-inline">
						<input name="thumb_mid_height" type="number" value="<?php echo htmlentities($config['value']['thumb']['thumb_mid_height']); ?>" lay-verify="int" class="layui-input ns-len-short">
					</div>
				</div>
			</div>

			<div class="layui-form-item">
				<label class="layui-form-label">缩略小图（单位：px）：</label>
				<div class="layui-input-block">
					<div class="layui-form-mid">宽</div>
					<div class="layui-input-inline">
						<input name="thumb_small_width" type="number" value="<?php echo htmlentities($config['value']['thumb']['thumb_small_width']); ?>" lay-verify="int" class="layui-input ns-len-short">
					</div>
					<div class="layui-form-mid">高</div>
					<div class="layui-input-inline">
						<input name="thumb_small_height" type="number" value="<?php echo htmlentities($config['value']['thumb']['thumb_small_height']); ?>" lay-verify="int" class="layui-input ns-len-short">
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 水印设置 -->
	<div class="layui-card ns-card-common ns-card-brief">
		<div class="layui-card-header">
			<span class="ns-card-title">水印设置</span>
		</div>
		<div class="layui-card-body">
			<div class="layui-form-item">
				<label class="layui-form-label">是否开启水印：</label>
				<div class="layui-input-block">
					<input type="checkbox" name="is_watermark" value="1" lay-skin="switch" lay-filter="isOpen" <?php if($config['value']['water']['is_watermark'] == 1): ?> checked <?php endif; ?> />
				</div>
				<div class="ns-word-aux">开启水印设置之后所有上传图片都会有水印标志</div>
			</div>
			
			<!-- 水印开启 -->
			<div class="layui-form-item">
				<label class="layui-form-label">水印类型：</label>
				<div class="layui-input-block" id="watermark_type">
					<input type="radio" name="watermark_type" lay-filter="watermark" value="1" title="图片" <?php echo $config['value']['water']['watermark_type']==1 ? 'checked'  :  ''; ?> >
					<input type="radio" name="watermark_type" lay-filter="watermark" value="2" title="文字" <?php echo $config['value']['water']['watermark_type']==2 ? 'checked'  :  ''; ?> >
				</div>
				<div class="ns-word-aux">水印可为图片或文字形式</div>
			</div>

			<!-- 图片水印 -->
			<div class="ns-watermark-img">
				<div class="layui-form-item">
					<label class="layui-form-label img-upload-lable">水印图片来源：</label>
					<div class="layui-input-block">
						<div class="upload-img-block">
							<!-- 用于存储图片路径 -->
							<input type="hidden" name="watermark_source" value="<?php echo htmlentities($config['value']['water']['watermark_source']); ?>" class="layui-input"/>
							<div class="upload-img-box" id="watermark_source">
								<?php if(empty($config['value']['water']['watermark_source'])): ?>
								<div class="ns-upload-default">
									<img src="http://saas.goodsceo.com/public/static/img/upload_img.png" />
									<p>点击上传</p>
								</div>
								<?php else: ?>
								<img src="<?php echo img($config['value']['water']['watermark_source']); ?>" alt="">
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="ns-word-aux">水印为图片时，上传水印图片</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">水印图片位置：</label>
					<div class="layui-input-block">
						<?php foreach($position as $watermark_position_k => $watermark_position_v): ?>
							<input type="radio" name="watermark_position" value="<?php echo htmlentities($watermark_position_k); ?>" title="<?php echo htmlentities($watermark_position_v); ?>" <?php echo $config['value']['water']['watermark_position']==$watermark_position_k ? 'checked'  :  ''; ?> />
						<?php endforeach; ?>
					</div>
					<div class="ns-word-aux">水印图片在图片上的位置</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">水印图片透明度：</label>
						<div class="layui-input-block">
							<div class="layui-input-inline">
								<input name="watermark_opacity" type="number" value="<?php echo htmlentities($config['value']['water']['watermark_opacity']); ?>" lay-verify="intrange" class="layui-input ns-len-short">
							</div>
							<div class="layui-form-mid">%</div>
						</div>
						<div class="ns-word-aux">水印图片透明度，用百分数表示，可为0-100%，0为不透明</div>
					</div>
				<div class="layui-form-item">
					<label class="layui-form-label">水印图片倾斜度：</label>
					<div class="layui-input-block">
						<div class="layui-input-inline">
							<input name="watermark_rotate" type="number" value="<?php echo htmlentities($config['value']['water']['watermark_rotate']); ?>" lay-verify="angle" class="layui-input ns-len-short">
						</div>
						<div class="layui-form-mid">度</div>
					</div>
					<div class="ns-word-aux">水印图片倾斜的角度</div>
				</div>
			</div>
			
			<!-- 文字水印 -->
			<div class="ns-watermark-font">
				<div class="layui-form-item">
					<label class="layui-form-label">水印文字：</label>
					<div class="layui-input-inline">
						<input name="watermark_text" type="text" value="<?php echo htmlentities($config['value']['water']['watermark_text']); ?>" class="layui-input ns-len-long">
					</div>
				</div>
				
				<div class="layui-form-item">
					<label class="layui-form-label">字体大小：</label>
					<div class="layui-input-block">
						<div class="layui-input-inline">
							<input name="watermark_text_size" type="number" value="<?php echo htmlentities($config['value']['water']['watermark_text_size']); ?>" lay-verify="int" class="layui-input ns-len-short">
						</div>
						<div class="layui-form-mid">px</div>
					</div>
					<div class="ns-word-aux">水印文字的字体大小</div>
				</div>
				
				<div class="layui-form-item">
					<label class="layui-form-label">字体颜色：</label>
					<div class="layui-input-inline">
						<input name="watermark_text_color" type="text" value="<?php echo htmlentities($config['value']['water']['watermark_text_color']); ?>" class="layui-input ns-len-short" id="watermark_color_input">
					</div>
					<div class="layui-input-block">
						<div id="watermark_color">
							
						</div>
					</div>
					<div class="ns-word-aux">水印文字颜色</div>
				</div>
				
				<div class="layui-form-item">
					<label class="layui-form-label">水印文字水平对齐方式：</label>
					<div class="layui-input-block">
						<input type="radio" name="watermark_text_align" value="left" title="居左对齐" <?php echo $config['value']['water']['watermark_text_align']=='left' ? 'checked'  :  ''; ?> >
						<input type="radio" name="watermark_text_align" value="center" title="居中对齐" <?php echo $config['value']['water']['watermark_text_align']=='center' ? 'checked'  :  ''; ?> >
						<input type="radio" name="watermark_text_align" value="right" title="居右对齐" <?php echo $config['value']['water']['watermark_text_align']=='right' ? 'checked'  :  ''; ?> >
					</div>
				</div>
				
				<div class="layui-form-item">
					<label class="layui-form-label">水印文字垂直对齐方式：</label>
					<div class="layui-input-block">
						<input type="radio" name="watermark_text_valign" value="top" title="居上对齐" <?php echo $config['value']['water']['watermark_text_valign']=='top' ? 'checked'  :  ''; ?> >
						<input type="radio" name="watermark_text_valign" value="center" title="居中对齐" <?php echo $config['value']['water']['watermark_text_valign']=='center' ? 'checked'  :  ''; ?> >
						<input type="radio" name="watermark_text_valign" value="bottom" title="居下对齐" <?php echo $config['value']['water']['watermark_text_valign']=='bottom' ? 'checked'  :  ''; ?> >
					</div>
				</div>
			
				<div class="layui-form-item">
					<label class="layui-form-label">文本旋转角度：</label>
					<div class="layui-input-block">
						<div class="layui-input-inline">
							<input name="watermark_text_angle" type="number" value="<?php echo htmlentities($config['value']['water']['watermark_text_angle']); ?>" lay-verify="angle" class="layui-input ns-len-short">
						</div>
						<div class="layui-form-mid">度</div>
					</div>
					<div class="ns-word-aux">水印文字相对于图片旋转的角度</div>
				</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">水印横坐标偏移量：</label>
				<div class="layui-input-block">
					<div class="layui-input-inline">
						<input name="watermark_x" type="number" value="<?php echo htmlentities($config['value']['water']['watermark_x']); ?>" lay-verify="int" class="layui-input ns-len-short">
					</div>
					<div class="layui-form-mid">px</div>
				</div>
				<div class="ns-word-aux">水印相对于横坐标偏移的距离，用像素单位表示</div>
			</div>
			
			<div class="layui-form-item">
				<label class="layui-form-label">水印纵坐标偏移量：</label>
				<div class="layui-input-block">
					<div class="layui-input-inline">
						<input name="watermark_y" type="number" value="<?php echo htmlentities($config['value']['water']['watermark_y']); ?>" lay-verify="int" class="layui-input ns-len-short">
					</div>
					<div class="layui-form-mid">px</div>
				</div>
				<div class="ns-word-aux">水印相对于纵坐标偏移的距离，用像素单位表示</div>
			</div>
		</div>
	</div>

	<!-- 提交 -->
	<div class="ns-single-filter-box">
		<div class="ns-form-row">
			<button class="layui-btn ns-bg-color" lay-submit lay-filter="save">保存</button>
		</div>
	</div>
	
	<!-- 隐藏域 -->
	<input class="watermark-type" type="hidden" value="<?php echo htmlentities($config['value']['water']['watermark_type']); ?>"  /><!-- 水印类型 -->
</div>

			</div>
			
			<!-- 版权信息 -->
			<div class="ns-footer">
				<div class="ns-footer-img">
					<a href="#"><img style="-webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);-ms-filter: grayscale(100%);-o-filter: grayscale(100%);filter: grayscale(100%);filter: gray;" src="<?php if(!empty($copyright['logo'])): ?> <?php echo img($copyright['logo']); else: ?>http://saas.goodsceo.com/public/static/img/copyright_logo.png<?php endif; ?>" /></a>
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
	layui.use(['form', 'upload', 'colorpicker'], function() {
		var form = layui.form,
			upload = layui.upload,
			colorpicker = layui.colorpicker,
			repeat_flag = false; //防重复标识
		form.render();

		/**
		 * 监听保存
		 */
		form.on('submit(save)', function(data) {
			if(repeat_flag) return;
			repeat_flag = true;
			
			$.ajax({
				type: 'POST',
				url: ns.url("admin/upload/config"),
				dataType: 'JSON',
				data: data.field,
				success: function(res) {
					layer.msg(res.message);
					repeat_flag = false;
					if (res.code == 0) {
						location.reload();
					}
				}
			});
		});
		
		/**
		 * 水印类型
		 */
		var type = $(".watermark-type").val();
		if (type == 1) {
			$(".ns-watermark-img").show();
		} else {
			$(".ns-watermark-font").show();
		}
		form.on('radio(watermark)', function(data) {
			if (data.value == 1) {
				$(".ns-watermark-img").show();
				$(".ns-watermark-font").hide();
			} else{
				$(".ns-watermark-img").hide();
				$(".ns-watermark-font").show();
			}
		});

		/**
		 * 图片上传
		 */
		var uploadLogo = upload.render({
			elem: '#watermark_source',
			url: ns.url("admin/upload/upload"),
			done: function(res) {
				if (res.code >= 0) {
					$("input[name='watermark_source']").val(res.data.pic_path);
					$('.upload-img-box').html("<img src=" + ns.img(res.data.pic_path) + " >"); //图片链接（base64）
				}
				return layer.msg(res.message);
			}
		});
		
		/**
		 * 水印文字颜色
		 */
		colorpicker.render({
		    elem: '#watermark_color',  //绑定元素
            color: "<?php echo htmlentities($config['value']['water']['watermark_text_color']); ?>",
			done: function(color) {
				$("#watermark_color_input").attr("value", color);
			}
		});
		
		/**
		 * 表单验证
		 */
		form.verify({
			num: function(value) {

				var arrMen = value.split("."),
					val = 0;

				if (arrMen.length == 2) {
					val = arrMen[1];
				}

				if (value == "") {
					return false;
				}
				
				if (value < 0 || val.length > 2) {
					return '请输入大于0的数，保留小数点后两位'
				}
			},
			int: function(value) {
				if (value == "") {
					return false;
				}
				if (value < 0 || !(value % 1 === 0)) {
					return '请输入大于0的整数'
				}
			},
			intrange: function(value) {
				if (value == "") {
					return false;
				}
				if (value < 0 || value > 100 || !(value % 1 === 0)) {
					return '请输入0-100之间的整数'
				}
			},
			angle: function(value) {
				if (value == "") {
					return false;
				}
				if (!(value % 1 === 0)) {
					return '请输入整数'
				}
			}
		});
	});
</script>

</body>
</html>