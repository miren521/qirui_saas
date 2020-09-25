<?php /*a:1:{s:42:"./app/component/view/image_ads/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" class="image-ads">
	
	<!-- 预览 -->
	<template slot="preview">
		
		<template v-if="nc.selectedTemplate!='carousel-posters'">
			<div v-for="(item,index) in nc.list" class="image-ads-item"
			     v-bind:style="{ marginBottom : ( nc.selectedTemplate=='vertically' ? (nc.imageClearance+'px') : '' ),marginRight : ( nc.selectedTemplate=='horizontal-sliding' ? (nc.imageClearance+'px') : '' ),padding : ( nc.selectedTemplate=='vertically' ? ('0 '+ nc.padding+'px') : '' ) }"
			     v-bind:class="[nc.selectedTemplate]">
				
				<p v-show="item.imageUrl==''">
					<span>点击编辑图片广告</span>
				</p>
				<div v-show="item.imageUrl">
					<img v-bind:src="changeImgUrl(item.imageUrl)" draggable="false"/>
				</div>
				
				<span v-show="item.title">{{item.title}}</span>
			
			</div>
		</template>
		
		<template v-if="nc.lazyLoad && nc.selectedTemplate=='carousel-posters'">
			<image-ads-carouse v-bind:index="index"></image-ads-carouse>
		</template>
	
	</template>
	
	<!-- 编辑 -->
	<template slot="edit">
		<div @click.stop="">
		<div class="layui-form-item">
			<label class="layui-form-label sm">选择模板</label>
			<div class="layui-input-block">
				<div class="selected-template-list">
					<ul>
						<li v-bind:class="[nc.selectedTemplate == 'carousel-posters' ? 'ns-border-color ns-text-color' : '']"
						    v-on:click="nc.selectedTemplate = 'carousel-posters'">
							<img src="<?php echo htmlentities($resource_path); ?>/image_ads/img/carousel_posters.png">
							<div>轮播海报</div>
						</li>
						<li v-bind:class="[nc.selectedTemplate == 'vertically' ? 'ns-border-color ns-text-color' : '']"
						    v-on:click="nc.selectedTemplate = 'vertically'">
							<img src="<?php echo htmlentities($resource_path); ?>/image_ads/img/ads_vertically.png">
							<div>垂直排列</div>
						</li>
						<li v-bind:class="[nc.selectedTemplate == 'horizontal-sliding' ? 'ns-border-color ns-text-color' : '']"
						    v-on:click="nc.selectedTemplate = 'horizontal-sliding'">
							<img src="<?php echo htmlentities($resource_path); ?>/image_ads/img/horizontal_sliding.png">
							<div>横向滑动</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="layui-form-item" v-show="nc.selectedTemplate == 'carousel-posters'">
			<label class="layui-form-label sm">图片高度</label>
			<div class="layui-input-block">
				<input type="text" v-model="nc.height" placeholder="请输入图片高度" class="layui-input">
			</div>
		</div>
		
		<slide v-show="nc.selectedTemplate != 'carousel-posters'" v-bind:data="{ field : 'padding', label : '左右边距' }"></slide>
		
		<slide v-show="nc.selectedTemplate != 'carousel-posters'" v-bind:data="{ field : 'imageClearance', label : '图片间隙' }"></slide>
		
		<template v-if="nc.lazyLoad">
			<image-ads-list></image-ads-list>
		</template>
		
		</div>
	
	</template>
	
	<!-- 资源 -->
	<template slot="resource">
		
		<css src="<?php echo htmlentities($resource_path); ?>/image_ads/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/image_ads/js/design.js"></js>
	
	</template>

</nc-component>