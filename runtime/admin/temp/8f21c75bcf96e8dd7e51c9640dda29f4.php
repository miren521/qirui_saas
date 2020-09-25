<?php /*a:1:{s:39:"./app/component/view/notice/design.html";i:1600314240;}*/ ?>
<nc-component v-bind:data="data[index]" v-bind:style="{ backgroundColor : nc.backgroundColor}" class="notice">

	<!-- 预览 -->
	<template slot="preview" >
		
		<template v-if="nc.lazyLoad">
			<notice></notice>
		</template>
		
	</template>

	<!-- 编辑 -->
	<template slot="edit">
		
		<template v-if="nc.lazyLoad">
			<div @click.stop="">
			<notice-edit></notice-edit>
			</div>
		</template>
	
	</template>
	
	<!-- 资源 -->
	<template slot="resource">
		
		<js>
			var RESOURCEPATH = "<?php echo htmlentities($resource_path); ?>";
		</js>
		<css src="<?php echo htmlentities($resource_path); ?>/notice/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/notice/js/design.js"></js>
		
	</template>

</nc-component>