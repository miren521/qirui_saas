<?php /*a:1:{s:43:"./app/component/view/goods_list/design.html";i:1600314240;}*/ ?>
<nc-component v-bind:data="data[index]" class="goods-list">

	<!-- 预览 -->
	<template slot="preview">
		
		<!-- 图一 -->
		<div class="goods-list-preview" v-bind:class="'text-title-'+ nc.style" v-if="nc.style == 1" v-bind:style="{ backgroundColor : nc.backgroundColor, paddingTop: (nc.paddingUpDown + 'px'), paddingBottom: (nc.paddingUpDown + 'px'), paddingLeft: (nc.paddingLeftRight + 'px'), paddingRight: (nc.paddingLeftRight + 'px') }">
			<div class="control-goods-list-small">
				<ul>
					<li>
						<div class="control-thumbnail blue-bg">第一个商品</div>
						<h5 class="control-goods-name" v-show="nc.isShowGoodName == 1">商品名称</h5>
						<h6 class="control-goods-subname" v-show="nc.isShowGoodSubTitle == 1">商品副标题</h6>
						<h6 class="control-goods-delprice" v-show="nc.isShowMarketPrice == 1">￥0.00</h6>
						<div class="control-goods-price">
							<em>￥638.24</em>
						</div>
						<h6 class="control-goods-sellnum" v-bind:class="{'control-goods-sellnum-abs': nc.isShowCart != 1}" v-show="nc.isShowGoodSaleNum == 1">0人付款</h6>
						<div class="cart-box" v-show="nc.isShowCart == 1">
							<div class="cart-icon">+</div>
						</div>
					</li>
					<li>
						<div class="control-thumbnail pink-bg">第二个商品</div>
						<h5 class="control-goods-name" v-show="nc.isShowGoodName == 1">商品名称</h5>
						<h6 class="control-goods-subname" v-show="nc.isShowGoodSubTitle == 1">商品副标题</h6>
						<h6 class="control-goods-delprice" v-show="nc.isShowMarketPrice == 1">￥0.00</h6>
						<div class="control-goods-price">
							<em>￥148.18</em>
						</div>
						<h6 class="control-goods-sellnum" v-bind:class="{'control-goods-sellnum-abs': nc.isShowCart != 1}" v-show="nc.isShowGoodSaleNum == 1">0人付款</h6>
						<div class="cart-box" v-show="nc.isShowCart == 1">
							<div class="cart-icon">+</div>
						</div>
					</li>
					<li>
						<div class="control-thumbnail green-bg">第三个商品</div>
						<h5 class="control-goods-name" v-show="nc.isShowGoodName == 1">商品名称</h5>
						<h6 class="control-goods-subname" v-show="nc.isShowGoodSubTitle == 1">商品副标题</h6>
						<h6 class="control-goods-delprice" v-show="nc.isShowMarketPrice == 1">￥0.00</h6>
						<div class="control-goods-price">
							<em>￥633.05</em>
						</div>
						<h6 class="control-goods-sellnum" v-bind:class="{'control-goods-sellnum-abs': nc.isShowCart != 1}" v-show="nc.isShowGoodSaleNum == 1">0人付款</h6>
						<div class="cart-box" v-show="nc.isShowCart == 1">
							<div class="cart-icon">+</div>
						</div>
					</li>
					<li>
						<div class="control-thumbnail orange-bg">第N个商品</div>
						<h5 class="control-goods-name" v-show="nc.isShowGoodName == 1">商品名称</h5>
						<h6 class="control-goods-subname" v-show="nc.isShowGoodSubTitle == 1">商品副标题</h6>
						<h6 class="control-goods-delprice" v-show="nc.isShowMarketPrice == 1">￥0.00</h6>
						<div class="control-goods-price">
							<em>￥264.67</em>
						</div>
						<h6 class="control-goods-sellnum" v-bind:class="{'control-goods-sellnum-abs': nc.isShowCart != 1}" v-show="nc.isShowGoodSaleNum == 1">0人付款</h6>
						<div class="cart-box" v-show="nc.isShowCart == 1">
							<div class="cart-icon">+</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</template>

	<!-- 编辑 -->
	<template slot="edit">
		<div @click.stop="">
		<template v-if="nc.lazyLoad">
			<goods-list></goods-list>
			<goods-list-style></goods-list-style>
		</template>
		
		<color v-bind:data="{ field : 'backgroundColor', 'label' : '背景颜色' }"></color>
		<slide v-bind:data="{ field : 'paddingUpDown', label : '上下边距' }"></slide>
		<!-- <slide v-bind:data="{ field : 'paddingLeftRight', label : '左右边距' }"></slide> -->
		
		<h3>购物车</h3>
		<template v-if="nc.lazyLoad">
			<goods-list-more-btn></goods-list-more-btn>
			<cart-style></cart-style>
		</template>
		
		<h3>显示内容</h3>
		<template v-if="nc.lazyLoad">
			<show-content></show-content>
		</template>
		
		<!-- 商品列表风格弹框 -->
		<div class="goods-list-style">
			<div class="style-list-goods layui-form">
				<div class="style-list-con-goods">
					<div class="style-li-goods" v-bind:class="{'selected ns-border-color': nc.style == 1}">
						<img src="<?php echo htmlentities($resource_path); ?>/goods_list/img/style1.png" />
					</div>
				</div>
				
				<input type="hidden" name="style">
				
			</div>
		</div>
		
		<!-- 购物车图标 -->
		<div class="cart-list-style">
			<div class="cart-list layui-form">
				<div class="cart-list-con">
					<div class="cart-li" v-bind:class="{'selected ns-border-color': nc.cartStyle == 1}">
						<img src="<?php echo htmlentities($resource_path); ?>/goods_list/img/cart_style1.png" />
					</div>
				</div>
				
				<input type="hidden" name="cart_style">
			</div>
		</div>
		</div>

	</template>
	
	<!-- 资源 -->
	<template slot="resource">

		<css src="<?php echo htmlentities($resource_path); ?>/goods_list/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/goods_list/js/design.js"></js>
		
	</template>
	
</nc-component>