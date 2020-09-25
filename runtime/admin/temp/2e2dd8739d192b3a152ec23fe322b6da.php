<?php /*a:1:{s:43:"./app/component/view/rubik_cube/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" v-bind:class="['rubik-cube']" v-bind:style="{backgroundColor: nc.backgroundColor}">

	<template slot="preview">
	
		<template v-if="nc.list.length>0 && !nc.list[0].imageUrl">
			<div class="tip">点击编辑魔方</div>
		</template>

		<template v-if="(nc.selectedTemplate != 'custom-rubik-cube')">
		<ul>
			<li v-for="item in nc.list" v-bind:class="nc.selectedTemplate">
				<template v-if="item.imageUrl!=''">
					<img v-bind:src="changeImgUrl(item.imageUrl)">
				</template>
			</li>
		</ul>
		</template>

		<template v-else>
			<template v-if="nc.lazyLoad">
				<rubik-cube-diy-html v-bind:diy-html="nc.diyHtml"></rubik-cube-diy-html>
			</template>
		</template>
		
	</template>
	
	<template slot="edit">
	
		<template v-if="nc.lazyLoad">
			<div @click.stop="">
			<color v-bind:data="{ field : 'backgroundColor', 'label' : '背景颜色' }"></color>
			<rubik-cube></rubik-cube>
			</div>
		</template>
		
	</template>
	
	<!-- 资源 -->
	<template slot="resource">
		
		<js>
		var RESOURCEPATH = "<?php echo htmlentities($resource_path); ?>";
		</js>
		<css src="<?php echo htmlentities($resource_path); ?>/rubik_cube/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/rubik_cube/js/design.js"></js>
		
	</template>

</nc-component>