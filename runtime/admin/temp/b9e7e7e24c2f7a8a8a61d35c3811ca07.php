<?php /*a:1:{s:52:"./addon/groupbuy/component/view/groupbuy/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" class="component-groupbuy" v-bind:style="{backgroundColor: nc.backgroundColor}">

	<!-- 预览 -->
	<template slot="preview">
		
		<div class="groupbuy-head">
			<div class="title-wrap">
				<span class="name">团购专区</span>
			</div>
			<div class="more ns-text-color">查看更多</div>
		</div>
		<div class="list-wrap">
			<div class="item">
				<div class="img-wrap">
					<img src="http://saas.com/public/static/ext/diyview/img/crack_figure.png" />
				</div>
				<div class="info-wrap">
					<h4>商品名称</h4>
					<div class="price-wrap">
						<span class="old-price">原价:￥1200.00</span>
						<span class="new-price ns-text-color">团购价:￥998.00</span>
					</div>
				</div>
				<button class="layui-btn layui-btn-sm ns-bg-color">查看详情</button>
			</div>
		</div>
		
	</template>

	<!-- 编辑 -->
	<template slot="edit">
		<template v-if="nc.lazyLoad">
			<div @click.stop="">
				<color v-bind:data="{ field : 'backgroundColor', 'label' : '背景颜色' }"></color>
			</div>
		</template>
	</template>
	
	<!-- 资源 -->
	<template slot="resource">

		<css src="<?php echo htmlentities($resource_path); ?>/groupbuy/css/design.css"></css>
		
	</template>
	
</nc-component>