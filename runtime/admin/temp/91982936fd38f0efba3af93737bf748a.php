<?php /*a:1:{s:43:"./app/component/view/pop_window/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" class="pop-window">

	<!-- 预览 -->
	<template slot="preview">

		<div class="pop-window-box">

			<img v-bind:src="nc.image_url? changeImgUrl(nc.image_url) : 'http://saas.com/public/static/ext/diyview/img/crack_figure.png'" class="pop-window-image"/>
		
		</div>
		
	</template>
	
	<!-- 编辑 -->
	<template slot="edit">
	
		<template v-if="nc.lazyLoad">
			<div @click.stop="">
				<pop-window></pop-window>
			</div>
		</template>
		
	</template>
	
	<!-- 资源 -->
	<template slot="resource">

		<css src="<?php echo htmlentities($resource_path); ?>/pop_window/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/pop_window/js/design.js"></js>
		
	</template>
	
</nc-component>