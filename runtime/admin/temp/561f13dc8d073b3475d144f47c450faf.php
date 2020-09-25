<?php /*a:2:{s:66:"/www/wwwroot/city.lpstx.cn/app/admin/view/goodscategory/lists.html";i:1600312146;s:51:"/www/wwwroot/city.lpstx.cn/app/admin/view/base.html";i:1600312146;}*/ ?>
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
	.goods-category-list .layui-table td {
		border-left: 0;
		border-right: 0;
	}
	.goods-category-list .layui-table .switch {
		font-size: 16px;
		cursor: pointer;
		width: 12px;
		line-height: 1;
		display: inline-block;
		text-align: center;
		vertical-align: middle;
	}
	.goods-category-list .layui-table img {width: 40px;}
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
			<li>商品分类由平台端进行维护，商家添加商品的时候需要选择对应的商品分类,用户可以根据商品分类搜索商品。</li>
			<li>点击商品分类名前“+”符号，显示当前商品分类的下级分类。</li>
			<li>商品分类关联类型是前台搜索分类查询商品之后可以通过商品类型的属性进行进一步搜索。</li>
			<li>商品分类的佣金比率是商家商品销售之后，平台获取佣金，具体佣金金额按照商品分类进行计算。</li>
		</ul>
	</div>
</div>
<div class="ns-single-filter-box">
	<button class="layui-btn ns-bg-color" onclick="addCategory()">添加商品分类</button>
</div>

<div class="goods-category-list">
	<table class="layui-table ns-pithy-table">
		<colgroup>
			<col width="3%">
			<col width="25%">
			<col width="10%">
			<col width="8%">
			<col width="10%">
			<col width="12%">
			<col width="10%">
			<col width="10%">
			<col width="12%">
		</colgroup>
		<thead>
			<tr>
				<th></th>
				<th>分类名称</th>
				<th>简称</th>
				<th>图片</th>
				<th>关联类型</th>
				<th>平台抽成比率</th>
				<th>是否显示</th>
				<th>排序</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if($list): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
			<tr>
				<td>
					<?php if(!(empty($vo['child_list']) || (($vo['child_list'] instanceof \think\Collection || $vo['child_list'] instanceof \think\Paginator ) && $vo['child_list']->isEmpty()))): ?>
					<span class="switch ns-text-color js-switch" data-category-id="<?php echo htmlentities($vo['category_id']); ?>" data-level="<?php echo htmlentities($vo['level']); ?>" data-open="0">+</span>
					<?php endif; ?>
				</td>
				<td><?php echo htmlentities($vo['category_name']); ?></td>
				<td><?php echo htmlentities($vo['short_name']); ?></td>
				<td>
					<?php if(!(empty($vo['image']) || (($vo['image'] instanceof \think\Collection || $vo['image'] instanceof \think\Paginator ) && $vo['image']->isEmpty()))): ?>
					<div class="ns-img-box">
						<img layer-src src="<?php echo img($vo['image']); ?>"/>
					</div>
					<?php endif; ?>
				</td>
				<td><?php echo htmlentities($vo['attr_class_name']); ?></td>
				<td><?php echo htmlentities($vo['commission_rate']); ?>%</td>
				<td><?php if($vo['is_show'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
				<td><input type="number" class="layui-input ns-len-short" value="<?php echo htmlentities($vo['sort']); ?>" onchange="editSort('<?php echo htmlentities($vo['category_id']); ?>')" id="category_sort<?php echo htmlentities($vo['category_id']); ?>"></td>
				<td>
					<div class="ns-table-btn">
						<a class="layui-btn" href="<?php echo addon_url('admin/goodscategory/editcategory',['category_id'=>$vo['category_id']]); ?>">编辑</a>
						<a class="layui-btn" href="javascript:deleteCategory(<?php echo htmlentities($vo['category_id']); ?>);">删除</a>
					</div>
				</td>
			</tr>
				<?php if(!(empty($vo['child_list']) || (($vo['child_list'] instanceof \think\Collection || $vo['child_list'] instanceof \think\Paginator ) && $vo['child_list']->isEmpty()))): if(is_array($vo['child_list']) || $vo['child_list'] instanceof \think\Collection || $vo['child_list'] instanceof \think\Paginator): if( count($vo['child_list'])==0 ) : echo "" ;else: foreach($vo['child_list'] as $key=>$second): ?>
					<tr data-category-id-1="<?php echo htmlentities($second['category_id_1']); ?>" style="display:none;">
						<td></td>
						<td style="padding-left: 20px;">
							<span class="switch ns-text-color js-switch" data-category-id="<?php echo htmlentities($second['category_id']); ?>" data-level="<?php echo htmlentities($second['level']); ?>" data-open="1" style="padding-right: 20px;">-</span>
							<span><?php echo htmlentities($second['category_name']); ?></span>
						</td>
						<td><?php echo htmlentities($second['short_name']); ?></td>
						<td>
							<?php if(!(empty($second['image']) || (($second['image'] instanceof \think\Collection || $second['image'] instanceof \think\Paginator ) && $second['image']->isEmpty()))): ?>
							<img layer-src src="<?php echo img($second['image']); ?>"/>
							<?php endif; ?>
						</td>
						<td><?php echo htmlentities($second['attr_class_name']); ?></td>
						<td><?php echo htmlentities($second['commission_rate']); ?>%</td>
						<td><?php if($second['is_show'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
						<td><input type="number" class="layui-input ns-len-short" value="<?php echo htmlentities($second['sort']); ?>" onchange="editSort('<?php echo htmlentities($second['category_id']); ?>')" id="category_sort<?php echo htmlentities($second['category_id']); ?>"></td>
						<td>
							<div class="ns-table-btn">
							<a class="layui-btn" href="<?php echo addon_url('admin/goodscategory/editcategory',['category_id'=>$second['category_id']]); ?>">编辑</a>
							<a class="layui-btn" href="javascript:deleteCategory(<?php echo htmlentities($second['category_id']); ?>);">删除</a>
							</div>
						</td>
					</tr>
						<?php if(!(empty($second['child_list']) || (($second['child_list'] instanceof \think\Collection || $second['child_list'] instanceof \think\Paginator ) && $second['child_list']->isEmpty()))): if(is_array($second['child_list']) || $second['child_list'] instanceof \think\Collection || $second['child_list'] instanceof \think\Paginator): if( count($second['child_list'])==0 ) : echo "" ;else: foreach($second['child_list'] as $key=>$third): ?>
							<tr data-category-id-1="<?php echo htmlentities($third['category_id_1']); ?>" data-category-id-2="<?php echo htmlentities($third['category_id_2']); ?>" style="display:none;">
								<td></td>
								<td style="padding-left: 80px;">
									<span><?php echo htmlentities($third['category_name']); ?></span>
								</td>
								<td><?php echo htmlentities($third['short_name']); ?></td>
								<td>
									<?php if(!(empty($third['image']) || (($third['image'] instanceof \think\Collection || $third['image'] instanceof \think\Paginator ) && $third['image']->isEmpty()))): ?>
									<img layer-src src="<?php echo img($third['image']); ?>"/>
									<?php endif; ?>
								</td>
								<td><?php echo htmlentities($third['attr_class_name']); ?></td>
								<td><?php echo htmlentities($third['commission_rate']); ?>%</td>
								<td><?php if($third['is_show'] == 1): ?>是<?php else: ?>否<?php endif; ?></td>
								<td><input type="number" class="layui-input ns-len-short" value="<?php echo htmlentities($third['sort']); ?>" onchange="editSort('<?php echo htmlentities($third['category_id']); ?>')" id="category_sort<?php echo htmlentities($third['category_id']); ?>"></td>
								<td>
									<div class="ns-table-btn">
										<a class="layui-btn" href="<?php echo addon_url('admin/goodscategory/editcategory',['category_id'=>$third['category_id']]); ?>">编辑</a>
										<a class="layui-btn" href="javascript:deleteCategory(<?php echo htmlentities($third['category_id']); ?>);">删除</a>
									</div>
								</td>
							</tr>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						<?php endif; ?>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				<?php endif; ?>
			<?php endforeach; endif; else: echo "" ;endif; else: ?>
			<tr>
				<td colspan="9" style="text-align: center">无数据</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
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


<script>
$(function () {
	loadImgMagnify();  //图片放大
	
	//展开收齐点击事件
	$(".js-switch").click(function () {
		var category_id = $(this).attr("data-category-id");
		var level = $(this).attr("data-level");
		var open = parseInt($(this).attr("data-open").toString());
		
		if(open){
			$(".goods-category-list .layui-table tr[data-category-id-"+ level+"='" + category_id + "']").hide();
			$(this).text("+");
		}else{
			$(".goods-category-list .layui-table tr[data-category-id-"+ level+"='" + category_id + "']").show();
			$(this).text("-");
		}
		$(this).attr("data-open", (open ? 0 : 1));
		
	});
});

function deleteCategory(category_id,level) {
	
	layer.confirm('子级分类也会删除，请谨慎操作', function() {
		$.ajax({
			url: ns.url("admin/goodscategory/deleteCategory"),
			data: {category_id : category_id},
			dataType: 'JSON',
			type: 'POST',
			async: false,
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					location.reload();
				}
			}
		});
	});
}
function addCategory() {
	location.href = ns.url("admin/goodscategory/addcategory");
}

// 监听单元格编辑
function editSort(category_id) {
    var sort = $("#category_sort"+category_id).val();

    if (!new RegExp("^-?[1-9]\\d*$").test(sort)) {
        layer.msg("排序号只能是整数");
        return;
    }
    if(sort<0){
        layer.msg("排序号必须大于0");
        return ;
    }
    $.ajax({
        type: 'POST',
        url: ns.url("admin/goodscategory/modifySort"),
        data: {
            sort: sort,
            category_id: category_id
        },
        dataType: 'JSON',
        success: function(res) {
            layer.msg(res.message);
            if (res.code == 0) {
                table.reload();
            }
        }
    });
}
</script>

</body>
</html>