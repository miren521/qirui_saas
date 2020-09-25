<?php /*a:1:{s:47:"./app/component/view/goods_category/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" class="goods-category">
	
	<!-- 预览 -->
	<template slot="preview">

		<div class="real-image-box">
			<img v-bind:src="'<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_' +  nc.level + '_' + nc.template + '.png'">
		</div>
	</template>
	
	<!-- 编辑 -->
	<template slot="edit">
		<div @click.stop="">
		<template v-if="nc.lazyLoad">
			<goods-category></goods-category>
		</template>

		<div class="goods-category-popup-wrap">
			<div class="goods-classification-style layui-form">

				<ul class="style-title">
					<li v-bind:class="{'selected ns-bg-color': nc.level == 1}">一级分类样式</li>
					<li v-bind:class="{'selected ns-bg-color': nc.level == 2}">二级分类样式</li>
					<li v-bind:class="{'selected ns-bg-color': nc.level == 3}" v-if="nc.module!='shop'">三级分类样式</li>
				</ul>
				<ul class="style-content">
					<li v-bind:class="{'layui-hide': nc.level != 1}">
						<div  v-bind:class="{'style-img-box':true,'selected ns-border-color ns-bg-color-after': nc.template == 1}">
							<img src="<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_1_1.png" alt="">
						</div>
						<div  v-bind:class="{'style-img-box':true,'selected ns-border-color ns-bg-color-after': nc.template == 2}">
							<img src="<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_1_2.png" alt="">
						</div>
						<div v-bind:class="{'style-img-box':true,'selected ns-border-color ns-bg-color-after': nc.template == 3}">
							<img src="<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_1_3.png" alt="">
						</div>
					</li>
					<li v-bind:class="{'layui-hide': nc.level != 2}">
						<div v-bind:class="{'style-img-box':true,'selected ns-bg-color-after ns-border-color': nc.template == 1}">
							<img src="<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_2_1.png" alt="">
						</div>
						<div v-bind:class="{'style-img-box':true,'selected ns-bg-color-after ns-border-color': nc.template == 2}">
							<img src="<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_2_2.png" alt="">
						</div>
						<div v-bind:class="{'style-img-box':true,'selected ns-bg-color-after ns-border-color': nc.template == 3}">
							<img src="<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_2_3.png" alt="">
						</div>
					</li>
					<li v-bind:class="{'layui-hide': nc.level != 3}">
						<div v-bind:class="{'style-img-box':true,'selected ns-border-color ns-bg-color-after': nc.template == 1}">
							<img src="<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_3_1.png" alt="">
						</div>
						<div v-bind:class="{'style-img-box':true,'selected ns-border-color ns-bg-color-after': nc.template == 2}">
							<img src="<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_3_2.png" alt="">
						</div>
						<div v-bind:class="{'style-img-box':true,'selected ns-border-color ns-bg-color-after': nc.template == 3}">
							<img src="<?php echo htmlentities($resource_path); ?>/goods_category/img/category_real_3_3.png" alt="">
						</div>
					</li>
				</ul>
				<input type="hidden" class="layui-input" name="level">
				<input type="hidden" class="layui-input" name="template">

				<div class="btn-box">
					<button class="layui-btn ns-bg-color" lay-submit lay-filter="confirm">确定</button>
					<button class="layui-btn layui-btn-primary back">返回</button>
				</div>

			</div>
		</div>
		</div>

	</template>
	
	<!-- 资源 -->
	<template slot="resource">

		<js>
			var RESOURCEPATH = "<?php echo htmlentities($resource_path); ?>";
		</js>
		<css src="<?php echo htmlentities($resource_path); ?>/goods_category/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/goods_category/js/design.js"></js>

	</template>

</nc-component>