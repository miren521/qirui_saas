<?php /*a:2:{s:73:"/www/wwwroot/city.lpstx.cn/app/admin/view/goodscategory/add_category.html";i:1600312146;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
	.layui-input-inline.js-pid a{
		margin-left: 20px;
	}
	.ns-form {
		margin-top: 0;
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
				
				
<form class="layui-form ns-form">
	<div class="layui-form-item">
		<label class="layui-form-label"><span class="required">*</span>分类名称：</label>
		<div class="layui-input-block ns-len-long">
			<input name="category_name" type="text" placeholder="请输入分类名称" maxlength="30" lay-verify="required" class="layui-input" autocomplete="off">
		</div>
		<div class="ns-word-aux">
			<p>分类名称最长不超过30个字符</p>
		</div>
	</div>
	
	<div class="layui-form-item">
		<label class="layui-form-label">简称：</label>
		<div class="layui-input-block ns-len-long">
			<input name="short_name" type="text" placeholder="请输入简称" maxlength="20" class="layui-input" autocomplete="off">
		</div>
		<div class="ns-word-aux">
			<p>分类名过长设置简称方便显示，字数设置为不超过20个字符</p>
		</div>
	</div>
	
	<div class="layui-form-item">
		<label class="layui-form-label">上级分类：</label>
		<div class="layui-input-block js-pid">
			<span class="ns-input-text">顶级分类</span>
			<input type="hidden" name="pid" value="0">
			<input type="hidden" name="level" value="1">
			<input type="hidden" name="category_id_1" value="0">
			<input type="hidden" name="category_id_2" value="0">
			<a class="ns-text-color" href="javascript:selectedCategoryPopup();">选择分类</a>
		</div>
		<div class="ns-word-aux">
			<p>如果选择上级分类，那么新增的分类则为被选择上级分类的子分类，不选择上级分类默认为顶级分类</p>
		</div>
	</div>
	
	<div class="layui-form-item">
		<label class="layui-form-label">商品类型：</label>
		<div class="layui-input-block ns-len-mid">
			<select name="attr_class_id" lay-filter="attr_class_id">
				<option value=""></option>
				<?php if(is_array($attr_class_list) || $attr_class_list instanceof \think\Collection || $attr_class_list instanceof \think\Paginator): if( count($attr_class_list)==0 ) : echo "" ;else: foreach($attr_class_list as $key=>$vo): ?>
				<option value="<?php echo htmlentities($vo['class_id']); ?>"><?php echo htmlentities($vo['class_name']); ?></option>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
		<div class="ns-word-aux">商品分类绑定后，前台搜索商品分类会按照商品类型进一步搜索</div>
	</div>
	
	<div class="layui-form-item">
		<label class="layui-form-label">平台抽成比率：</label>
		<div class="layui-input-block">
			<div class="layui-input-inline">
				<input name="commission_rate" type="number" value="5" placeholder="请输入平台抽成比率" lay-verify="commission_rate" class="layui-input ns-len-short" autocomplete="off">
			</div>
			<span class="layui-form-mid">%</span>
		</div>
		<div class="ns-word-aux">
			<p>必须为0-100的整数</p>
			<p>对应分类的商品销售后，平台按照抽成比率收取费用</p>
		</div>
	</div>
	
	<div class="layui-form-item">
		<label class="layui-form-label">分类图标：</label>
		<div class="layui-input-block">
			<div class="upload-img-block">
				<div class="upload-img-box" id="imgUpload">
					<div class="ns-upload-default">
						<img src="http://city.lpstx.cn/public/static/img/upload_img.png" />
						<p>点击上传</p>
					</div>
				</div>
			</div>
		</div>
		<div class="ns-word-aux">
			<p>建议图片尺寸：100px * 100px。图片格式：jpg、png、jpeg。</p>
		</div>
	</div>

    <div class="layui-form-item">
        <label class="layui-form-label">分类广告图：</label>
        <div class="layui-input-block">
            <div class="upload-img-block">
                <div class="upload-img-box" id="imgUploadAdv">
					<div class="ns-upload-default">
						<img src="http://city.lpstx.cn/public/static/img/upload_img.png" />
						<p>点击上传</p>
					</div>
                </div>
            </div>
        </div>
	    <!--<div class="ns-word-aux">-->
		    <!--<p>建议图片尺寸：650px * 450px。图片格式：jpg、png、jpeg。</p>-->
	    <!--</div>-->
    </div>
	<div class="layui-form-item">
		<label class="layui-form-label">是否显示：</label>
		<div class="layui-input-block">
			<input type="checkbox" name="is_show" lay-skin="switch" value="1" checked>
		</div>
		<div class="ns-word-aux">
			<p>用于控制前台是否展示</p>
		</div>
	</div>
	
	<div class="layui-form-item">
		<label class="layui-form-label">关键字：</label>
		<div class="layui-input-block">
			<input name="keywords" type="text" placeholder="请输入关键字" class="layui-input ns-len-long" autocomplete="off">
		</div>
		<div class="ns-word-aux">
			<p>用于网站搜索引擎的优化，关键字之间请用英文逗号分隔</p>
		</div>
	</div>
	
	<div class="layui-form-item">
		<label class="layui-form-label">分类描述：</label>
		<div class="layui-input-block ns-len-long">
			<textarea name="description" placeholder="请输入分类描述" class="layui-textarea"></textarea>
		</div>
	</div>
	
	<div class="layui-form-item">
		<label class="layui-form-label">排序：</label>
		<div class="layui-input-block">
			<input name="sort" type="number" value="0" placeholder="请输入排序" lay-verify="num" class="layui-input ns-len-short" autocomplete="off">
		</div>
		<div class="ns-word-aux">
			<p>排序值必须为整数</p>
		</div>
	</div>
	
	<input type="hidden" name="image_adv" value="">
	<div class="ns-form-row">
		<button class="layui-btn ns-bg-color" lay-submit lay-filter="save">保存</button>
		<button type="reset" class="layui-btn layui-btn-primary" onclick="back()">返回</button>
	</div>
</form>

<script type="text/html" id="selectedCategory">
	
	<form class="layui-form">
		
		<div class="layui-form-item">
			<label class="layui-form-label sm">一级分类</label>
			<div class="layui-input-block ns-len-mid">
				<select name="category_id_1" lay-filter="category_id_1">
					<option value="0" data-level="0">顶级分类</option>
					{{# for(var i=0;i<d.category_list_1.length;i++){ }}
						{{# if(d.category_id_1 ==d.category_list_1[i].category_id){ }}
						<option value="{{ d.category_list_1[i].category_id }}" data-level="{{d.category_list_1[i].level}}" selected>{{ d.category_list_1[i].category_name }}</option>
						{{# }else{ }}
						<option value="{{ d.category_list_1[i].category_id }}" data-level="{{d.category_list_1[i].level}}">{{ d.category_list_1[i].category_name }}</option>
						{{# } }}
					{{# } }}
				</select>
			</div>
		</div>
		
		<div class="layui-form-item">
			<label class="layui-form-label sm">二级分类</label>
			<div class="layui-input-block ns-len-mid">
				<select name="category_id_2" lay-filter="category_id_2"></select>
			</div>
		</div>
		
		<div class="ns-form-row sm">
			<button class="layui-btn ns-bg-color" lay-submit lay-filter="save_pid">保存</button>
		</div>
		
	</form>
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


<script src="http://city.lpstx.cn/app/admin/view/public/js/goods_edit_category.js"></script>

</body>
</html>