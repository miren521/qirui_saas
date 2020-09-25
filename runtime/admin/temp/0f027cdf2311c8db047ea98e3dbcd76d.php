<?php /*a:1:{s:44:"./app/component/view/graphic_nav/design.html";i:1600314240;}*/ ?>
<nc-component v-bind:data="data[index]" v-bind:class="['graphic-navigation']">

	<!-- 预览 -->
	<template slot="preview">
		
		<template v-if="nc.lazyLoad">
			<graphic-nav></graphic-nav>
		</template>

	</template>
	
	<!-- 编辑 -->
	<template slot="edit">
	
		<template v-if="nc.lazyLoad">
			<div @click.stop="">
				<graphic-nav-list>
					
				</graphic-nav-list>
			</div>

		</template>
		
	</template>
	
	<!-- 资源 -->
	<template slot="resource">
		
		<js>
			var RESOURCEPATH = "<?php echo htmlentities($resource_path); ?>";
			var STATICEXT_IMG ="http://saas.com/public/static/ext/diyview/img";
		</js>
		<css src="<?php echo htmlentities($resource_path); ?>/graphic_nav/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/graphic_nav/js/design.js"></js>
		
	</template>

</nc-component>