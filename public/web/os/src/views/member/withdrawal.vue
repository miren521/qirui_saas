<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<span>提现记录</span>
			</div>
		
			<div v-loading="loading" class="withdrawal-list">
				<el-table v-if="dataList.length > 0" :data="dataList" border>
					<el-table-column prop="transfer_type_name" label="账户类型" width="150"></el-table-column>
					<el-table-column prop="apply_money" label="提现金额" width="150"></el-table-column>
					<el-table-column prop="apply_time" label="提现时间"></el-table-column>
					<el-table-column prop="status_name" label="提现状态" width="150"></el-table-column>
					<el-table-column label="操作" width="150">
						<template slot-scope="scope">
							<el-button size="mini" @click="handleEdit(scope.$index, scope.row)">详情</el-button>
						</template>
					</el-table-column>
				</el-table>
				<div v-else-if="!loading && dataList.length == 0" class="ns-text-align">暂无提现记录</div>
			</div>
			<div class="pager">
				<el-pagination 
					background 
					:pager-count="5" 
					:total="total" 
					prev-text="上一页" 
					next-text="下一页" 
					:current-page.sync="currentPage" 
					:page-size.sync="pageSize" 
					@size-change="handlePageSizeChange" 
					@current-change="handleCurrentPageChange" 
					hide-on-single-page
				></el-pagination>
			</div>
		</el-card>
	</div>
</template>

<script>
import { withdrawList } from "@/api/member/account"

export default {
	name: 'withdrawal',
	components: {},
	data: () => {
		return {
			dataList: [],
			currentPage: 1,
			pageSize: 10,
			total: 0,
			loading: true,
			yes: true
		};
	},
	created() {
		this.getDateList();
	},
	mounted() {
		let self = this;
		setTimeout(function() {
			self.yes = false
		}, 300)
	},
	methods: {
		handlePageSizeChange(size) {
		    this.pageSize = size
		    this.refresh()
		},
		handleCurrentPageChange(page) {
		    this.currentPage = page
		    this.refresh()
		},
		refresh() {
		    this.loading = true
		    this.getDateList()
		},
		getDateList() {
			withdrawList({
				page_size: this.pageSize,
				page: this.currentPage
			}).then(res => {
				if (res.code == 0 && res.data) {
					this.dataList = res.data.list
					this.dataList.forEach(item => {
					    item.apply_time = this.$util.timeStampTurnTime(item.apply_time)
					})
					this.total = res.data.count
				}
				
				this.loading = false
			}).catch(err => {
				this.loading = false
			})
		},
		handleEdit(index, row) {
			this.$router.push({path: '/member/withdrawal_detail', query: {id: row.id}})
		}
	}
};
</script>
<style lang="scss" scoped>
	.box {
		width: 100%;
		position: relative;
	}
	
	.null-page {
		width: 100%;
		height: 730px;
		background-color: #FFFFFF;
		position: absolute;
		top: 0;
		left: 0;
		z-index: 9;
	}
	
	.el-card.is-always-shadow,
	.el-card.is-hover-shadow:focus,
	.el-card.is-hover-shadow:hover {
	    box-shadow: unset;
	}
	
	.el-card {
	    border: 0;
	}
	
	.ns-len-input {
	    width: 350px;
	}
	
	.el-select {
	    margin-right: 10px;
	}
	
	.page-wrap {
		margin-top: 10px;
	}
</style>
