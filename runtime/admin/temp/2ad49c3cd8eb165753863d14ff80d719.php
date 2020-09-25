<?php /*a:1:{s:41:"./app/component/view/text_nav/design.html";i:1600312146;}*/ ?>
<nc-component v-bind:data="data[index]" v-bind:style="{backgroundColor : nc.backgroundColor}" class="text-navigation">

	<!-- 预览 -->
	<template slot="preview">
		<template v-if="nc.lazyLoad">
			<text-nav></text-nav>
		</template>
	</template>
	
	<!-- 编辑 -->
	<template slot="edit">
		<div @click.stop="">
		<font-size v-bind:data="{ value : nc.fontSize }"></font-size>
		
		<color></color>
		
		<color v-bind:data="{ field : 'backgroundColor', label : '背景颜色' }"></color>
		
		<text-align v-show="nc.arrangement=='vertical'"></text-align>

		<template v-if="nc.lazyLoad">
			<arrangement></arrangement>
		</template>

		<div class="text-navigation-block">
			<ul>
				<li v-for="(item,index) in nc.list" v-bind:key="index">
					<div class="layui-form-item">
						<label class="layui-form-label sm">导航名称</label>
						<div class="layui-input-block">
							<input type="text" v-model="item.text" placeholder="请输入导航名称" class="layui-input" />
						</div>
					</div>
					<nc-link v-bind:data="{ field : nc.list[index].link }"></nc-link>
					<i class="del" v-on:click="nc.list.splice(index,1)" v-if="index>0" data-disabled="1">x</i>
				</li>
			</ul>
		</div>
		
		<div style="text-align: center;margin:20px 0;" v-show="nc.arrangement=='horizontal'">
			<button class="layui-btn layui-btn-sm ns-bg-color" v-on:click='nc.list.push({ text : "『文本导航』",link : {} })'>添加</button>
		</div>
		</div>
	</template>
	
	<!-- 资源 -->
	<template slot="resource">

		<css src="<?php echo htmlentities($resource_path); ?>/text_nav/css/design.css"></css>
		<js src="<?php echo htmlentities($resource_path); ?>/text_nav/js/design.js"></js>
		
	</template>

</nc-component>