<?php /*a:1:{s:38:"./app/component/view/title/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" class="top-title" v-bind:style="{ backgroundColor : nc.backgroundColor }">

	<!-- 预览 -->
	<template slot="preview">

		<header v-bind:style="{ color : nc.textColor, backgroundColor : nc.backgroundColor }">
			<a href="javascript:;" class="go-back">
				<svg t="1535096586623" class="icon" style="" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1036" xmlns:xlink="http://www.w3.org/1999/xlink">
					<path d="M352 512l384-384c12.8-12.8 12.8-32 0-44.8-12.8-12.8-32-12.8-44.8 0L288 486.4C281.6 492.8 275.2 505.6 275.2 512c0 6.4 0 19.2 6.4 25.6l409.6 409.6c12.8 12.8 32 12.8 44.8 0s12.8-32 0-44.8L352 512z" p-id="1037"></path>
				</svg>
			</a>
			<h4 v-bind:style="{ fontSize : nc.fontSize + 'px' }">{{nc.title}}</h4>
			<a v-bind:style="{ fontSize : (nc.fontSize-2 + 'px') }" v-show="nc.isOpenOperation" href="javascript:;" class="operation">{{nc.operation_name}}</a>
		</header>
		
	</template>
	
	<!-- 编辑 -->
	<template slot="edit">
		<div @click.stop="">
		<div class="layui-form-item">
			<label class="layui-form-label sm">顶部标题名</label>
			<div class="layui-input-block">
				<input type="text" v-model="nc.title" v-bind:id="'title_'+index" placeholder="请输入顶部标题" class="layui-input">
			</div>
		</div>
		
		<nc-link v-bind:data="{ field : nc.leftLink, label : '左侧链接地址' }"></nc-link>
		
		<font-size v-bind:data="{ value : nc.fontSize }"></font-size>
		
		<color v-bind:data="{ field : 'backgroundColor', label : '背景颜色' }"></color>
		
		<color></color>

		<div class="layui-form-item">
			<label class="layui-form-label sm">开启右侧功能</label>
			<div class="layui-input-block">
				<div v-bind:class="{ 'layui-unselect layui-form-switch' : true, 'layui-form-onswitch' : nc.isOpenOperation }" v-on:click="nc.isOpenOperation=!nc.isOpenOperation">
					<em></em>
					<i></i>
				</div>
			</div>
		</div>

		<nc-link v-show="nc.isOpenOperation" v-bind:data="{ field : nc.rightLink }"></nc-link>

		<div class="layui-form-item" v-show="nc.isOpenOperation">
			<label class="layui-form-label sm">功能名称</label>
			<div class="layui-input-block">
				<input type="text" v-model="nc.operation_name" v-bind:id="'top_operation_'+index" placeholder="请输入右侧功能名称" class="layui-input">
			</div>
		</div>
		
		<template v-if="nc.lazyLoad">
			<title-empty></title-empty>
		</template>
		</div>
	</template>
	
	<!-- 资源 -->
	<template slot="resource">

		<css src="<?php echo htmlentities($resource_path); ?>/title/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/title/js/design.js"></js>
		
	</template>
	
</nc-component>