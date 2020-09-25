<?php /*a:1:{s:43:"./app/component/view/horz_blank/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" v-bind:class="['auxiliary-blank']" v-bind:style="{ backgroundColor : nc.backgroundColor }">

	<!-- 预览 -->
	<template slot="preview">
	
		<div v-bind:style="{ height : nc.height+'px'}"></div>
	
	</template>
	
	<!-- 编辑 -->
	<template slot="edit">
		<div @click.stop="">
		<color v-bind:data="{ field : 'backgroundColor', label : '空白颜色' }"></color>
		<slide v-bind:data="{ field : 'height', label : '空白高度' }"></slide>
		</div>
	</template>

	<!-- 资源 -->
	<template slot="resource">
		
		<css src="<?php echo htmlentities($resource_path); ?>/horz_blank/css/design.css"></css>
		
	</template>
	
</nc-component>