<?php /*a:1:{s:50:"./addon/bargain/component/view/bargain/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" class="component-bargain" v-bind:style="{ backgroundColor : nc.backgroundColor }">

	<!-- 预览 -->
	<template slot="preview">
		<div class="preview-box" v-bind:style="{ padding: (nc.padding + 'px 0') }">
			<template v-if="nc.lazyLoad">
				<bargain-top-content></bargain-top-content>
			</template>
			
			<div class="list-wrap">
				<div class="item">
					<div class="img-wrap">
						<img src="http://saas.com/public/static/ext/diyview/img/crack_figure.png" />
					</div>
					<div class="content">
						<div class="content-desc">商品名称</div>
						<div class="content-operation">
							<div class="price">
								<span>¥3000.00</span>
								<span>底价：<span class="ns-red-color">¥2500.00</span></span>
							</div>
							<button>去砍价</button>
						</div>
					</div>
				</div>
				<div class="item">
					<div class="img-wrap">
						<img src="http://saas.com/public/static/ext/diyview/img/crack_figure.png" />
					</div>
					<div class="content">
						<div class="content-desc">商品名称</div>
						<div class="content-operation">
							<div class="price">
								<span>¥3000.00</span>
								<span>底价：<span class="ns-red-color">¥2500.00</span></span>
							</div>
							<button>去砍价</button>
						</div>
					</div>
				</div>
				<div class="item">
					<div class="img-wrap">
						<img src="http://saas.com/public/static/ext/diyview/img/crack_figure.png" />
					</div>
					<div class="content">
						<div class="content-desc">商品名称</div>
						<div class="content-operation">
							<div class="price">
								<span>¥3000.00</span>
								<span>底价：<span class="ns-red-color">¥2500.00</span></span>
							</div>
							<button>去砍价</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</template>

	<!-- 编辑 -->
	<template slot="edit">
		<div @click.stop="">
		<template v-if="nc.lazyLoad">
			<bargain-list></bargain-list>
			<bargain-style></bargain-style>
		</template>
		
		<color v-bind:data="{ field : 'backgroundColor', 'label' : '背景颜色' }"></color>
		<slide v-bind:data="{ field : 'padding', label : '上下边距' }"></slide>
		
		<h3>顶部标题设置</h3>
		<template v-if="nc.lazyLoad">
			<bargain-top-list></bargain-top-list>
		</template>

		<!-- 弹框 -->
		<div class="bargain-list-style">
			<div class="style-list-bargain layui-form">
				<div class="style-list-con-bargain">
					<div class="style-li-bargain" v-bind:class="{'selected ns-border-color': nc.style == 1}">
						<img src="<?php echo htmlentities($resource_path); ?>/bargain/img/bargain_style_1.png" />
					</div>
				</div>
				
				<input type="hidden" name="style">
			</div>
		</div>
		</div>

	</template>

	<!-- 资源 -->
	<template slot="resource">

		<css src="<?php echo htmlentities($resource_path); ?>/bargain/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/bargain/js/design.js"></js>

	</template>

</nc-component>