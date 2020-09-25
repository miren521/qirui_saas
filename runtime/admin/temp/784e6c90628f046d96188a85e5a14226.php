<?php /*a:1:{s:50:"./addon/seckill/component/view/seckill/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" class="component-seckill" v-bind:style="{backgroundColor: nc.backgroundColor}">

	<!-- 预览 -->
	<template slot="preview">
		
		<div class="seckill-head">
			<div class="title-wrap">
				<span class="name">秒杀专区</span>
				<span class="time">21点秒杀 21:00:00~22:00:00</span>
			</div>
			<div class="more ns-text-color">更多秒杀</div>
		</div>
		<div class="list-wrap">
			<div class="item">
				<div class="img-wrap">
					<img src="http://saas.com/public/static/ext/diyview/img/crack_figure.png" />
				</div>
				<span class="new-price ns-text-color">￥998.00</span>
				<span class="old-price">￥1200.00</span>
			</div>
			<div class="item">
				<div class="img-wrap">
					<img src="http://saas.com/public/static/ext/diyview/img/crack_figure.png" />
				</div>
				<span class="new-price ns-text-color">￥998.00</span>
				<span class="old-price">￥1200.00</span>
			</div>
			<div class="item">
				<div class="img-wrap">
					<img src="http://saas.com/public/static/ext/diyview/img/crack_figure.png" />
				</div>
				<span class="new-price ns-text-color">￥998.00</span>
				<span class="old-price">￥1200.00</span>
			</div>
			<div class="item">
				<div class="img-wrap">
					<img src="http://saas.com/public/static/ext/diyview/img/crack_figure.png" />
				</div>
				<span class="new-price ns-text-color">￥998.00</span>
				<span class="old-price">￥1200.00</span>
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

		<css src="<?php echo htmlentities($resource_path); ?>/seckill/css/design.css"></css>
		
	</template>
	
</nc-component>