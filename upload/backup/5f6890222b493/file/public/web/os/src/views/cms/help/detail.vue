<template>
	<div class="detail-wrap">
		<el-breadcrumb separator="/" class="path">
			<el-breadcrumb-item :to="{ path: '/' }" class="path-home">
				<i class="n el-icon-s-home"></i>
				首页
			</el-breadcrumb-item>
			<el-breadcrumb-item :to="{ path: '/cms/help' }">帮助列表</el-breadcrumb-item>
			<el-breadcrumb-item class="path-help">帮助详情</el-breadcrumb-item>
		</el-breadcrumb>
		<div class="help-detail" v-loading="loading">
			<div class="title">{{ detail.title }}</div>
			<div class="info">
				<div class="time">{{ $util.timeStampTurnTime(detail.create_time) }}</div>
			</div>
			<div class="content" v-html="detail.content"></div>
		</div>
	</div>
</template>

<script>
import { helpDetail } from '@/api/cms/help';
export default {
	name: 'help_detail',
	components: {},
	data: () => {
		return {
			detail: [],
			loading: true
		};
	},
	created() {
		this.id = this.$route.path.replace('/cms/help-', '');
		this.getDetail();
	},
	watch: {
		$route(curr) {
			this.id = curr.params.pathMatch;
			this.getDetail();
		}
	},
	methods: {
		getDetail() {
			helpDetail({
				id: this.id
			})
				.then(res => {
					if (res.code == 0) {
						if (res.data) {
							this.loading = false;
							this.detail = res.data;
						} else {
							this.$router.push({ path: '/cms/help' });
						}
					}
				})
				.catch(err => {
					this.loading = false;
					this.$message.error(err.message);
				});
		}
	}
};
</script>
<style lang="scss" scoped>
.help-detail {
	background-color: #ffffff;
	padding: 10px;
	border-radius: 5px;
	margin: 10px 0;
	.title {
		text-align: center;
		font-size: 18px;
		margin: 10px 0;
	}
	.info {
		margin: 0 43px;
		border-bottom: 1px dotted #e9e9e9;
		.time {
			text-align: center;
			color: #838383;
			margin-bottom: 17px;
		}
	}
	.content {
		padding-top: 10px;
		margin: 0 65px;
	}
}
.path {
	padding: 15px 0;
}
</style>
