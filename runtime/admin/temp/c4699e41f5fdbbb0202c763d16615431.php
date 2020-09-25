<?php /*a:1:{s:42:"./app/component/view/float_btn/design.html";i:1600314240;}*/ ?>
<nc-component v-bind:data="data[index]" class="float-btn">
	
	<!-- 预览 -->
	<template slot="preview">
		<div class="float-btn-box">
<!--			v-bind:style="{ backgroundColor : nc.backgroundColor }"-->
			<a v-for="(item, index) in nc.list" href="javascript:;" class="float-btn-item">
				<div class="img-box">
					<img v-bind:src="changeImgUrl(item.imageUrl)" alt="">
				</div>
<!--				<span v-bind:style="{color: nc.textColor}">{{item.title}}</span>-->
			</a>
		</div>

	</template>
	
	<!-- 编辑 -->
	<template slot="edit">
<!--		<color v-bind:data="{ field : 'textColor', 'label' : '文字颜色' }"></color>-->
<!--		<color v-bind:data="{ field : 'backgroundColor', 'label' : '背景颜色' }"></color>-->
		<div @click.stop="">
			<template v-if="nc.lazyLoad">
				<float-btn-list></float-btn-list>
			</template>
		</div>
	</template>
	
	<!-- 资源 -->
	<template slot="resource">
		
		<js>
			var RESOURCEPATH = "<?php echo htmlentities($resource_path); ?>";
		</js>
		<css src="<?php echo htmlentities($resource_path); ?>/float_btn/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/float_btn/js/design.js"></js>
	
	</template>

</nc-component>