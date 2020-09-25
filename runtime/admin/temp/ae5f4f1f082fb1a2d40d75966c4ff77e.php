<?php /*a:1:{s:42:"./app/component/view/rich_text/design.html";i:1600314240;}*/ ?>
<nc-component v-bind:data="data[index]" class="rich-text" v-bind:style="{backgroundColor: nc.backgroundColor }">
	<!-- 预览 -->
	<template slot="preview">
		<div class="rich-text-box" v-bind:style="{ padding: (nc.padding + 'px') }">
			<div v-html="nc.html"></div>
		</div>
	</template>

	<!-- 编辑 -->
	<template slot="edit">
		<template v-if="nc.lazyLoad">
			<div @click.stop="">
			<rich-text></rich-text>
			</div>
		</template>

	</template>
	
	<!-- 资源 -->
	<template slot="resource">

		<css src="<?php echo htmlentities($resource_path); ?>/rich_text/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/rich_text/js/design.js"></js>

	</template>
	
</nc-component>