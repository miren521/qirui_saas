<template>
	<div class="error-wrap">
		<!-- <i class="iconfont icon404"></i>
		<h2>抱歉！页面无法访问……</h2> -->
		<div>
			<img src="@/assets/images/index/404.png" />
		</div>
		<el-link :underline="false" @click="$router.go(-1)" class="go-back">返回上一页</el-link>
		<router-link to="/" class="go-index">返回首页</router-link>
	</div>
</template>

<script>
export default {
	name: 'error',
	components: {},
	data: () => {
		return {};
	},
	created() {},
	methods: {}
};
</script>
<style lang="scss" scoped>
.error-wrap {
	text-align: center;
	padding: 40px;
	position: absolute;
	top: 50%;
	left:50%;
	transform: translate(-50%,-50%);
	div {
		margin-bottom: 30px;
	}
	i{
		font-size: 60px;
	}
	h2{
		margin: 0 0 30px;
	}
	.go-back {
		margin-right: 10px;
		color: $base-color;
	}
	.go-index {
		color: $base-color;
	}
}
</style>
