<?php /*a:1:{s:50:"./addon/pintuan/component/view/pintuan/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" class="component-pintuan" v-bind:style="{backgroundColor: nc.backgroundColor}">

	<!-- 预览 -->
	<template slot="preview">
		
		<div class="pintuan-head">
			<div class="title-wrap">
				<span class="name">拼团专区</span>
			</div>
			<div class="more ns-text-color">更多</div>
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

		<css src="<?php echo htmlentities($resource_path); ?>/pintuan/css/design.css"></css>
		
	</template>
	
</nc-component>