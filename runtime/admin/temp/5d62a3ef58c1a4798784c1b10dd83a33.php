<?php /*a:1:{s:49:"./addon/live/component/view/live_info/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" class="component-live-info">
	
	<!-- 预览 -->
	<template slot="preview">

		<div class="live-wrap" v-bind:style="{paddingTop: (nc.paddingUpDown + 'px'), paddingBottom: (nc.paddingUpDown + 'px')}">
			<div class="banner-wrap">
				<img src="<?php echo htmlentities($resource_path); ?>/live_info/img/live_default_banner.png">
				<div class="shade"></div>
				<div class="wrap">
					<div class="room-name">
						<span class="status-name">直播中</span>	
						直播间标题
					</div>
				</div>
			</div>
			<div class="room-info" v-if="nc.isShowAnchorInfo || nc.isShowLiveGood">
				<template v-if="nc.isShowAnchorInfo">
					<img src="<?php echo htmlentities($resource_path); ?>/live_info/img/default_headimg.png" class="anchor-img">
					<span class="anchor-name">主播：主播昵称</span>
				</template>
				<template v-if="nc.isShowAnchorInfo && nc.isShowLiveGood">
					<span class="separate">|</span>
				</template>
				<template v-if="nc.isShowLiveGood">
					<span class="goods-text">直播商品：1</span>
				</template>
			</div>
		</div>

	</template>
	
	<!-- 编辑 -->
	<template slot="edit">
		<div @click.stop="">
		<slide v-bind:data="{ field : 'paddingUpDown', label : '上下边距' }"></slide>
		<h3>显示内容</h3>
		<template v-if="nc.lazyLoad">
			<live-show-content></live-show-content>
		</template>
		</div>
	</template>
	
	<!-- 资源 -->
	<template slot="resource">
		
		<css src="<?php echo htmlentities($resource_path); ?>/live_info/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/live_info/js/design.js"></js>
		
	</template>

</nc-component>