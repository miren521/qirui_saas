<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<span>账户列表</span>
			</div>
		
			<div v-loading="loading">
				<div class="ns-member-address-list">
					<div class="text item ns-add-address" @click="addAccount('add')">
						<span>+ 新增账户</span>
					</div>
		
					<div class="text item ns-account-list" v-for="(item, index) in dataList" :key="index" @click="setDefault(item.id)">
						<div class="text-name">
							<span>{{ item.realname }}</span>
							<span v-if="item.is_default == 1" class="text-default">默认</span>
						</div>
		
						<div class="text-content">
							<p>手机号码：{{ item.mobile }}</p>
							<p v-if="item.withdraw_type == 'alipay'">提现账号：{{ item.bank_account }}</p>
							<p>账号类型：{{ item.withdraw_type_name }}</p>
							<p v-if="item.withdraw_type == 'bank'">银行名称：{{ item.branch_bank_name }}</p>
						</div>
		
						<div class="text-operation">
							<span v-if="item.is_default != 1" @click="setDefault(item.id)">设为默认</span>
							<span @click.stop="addAccount('edit', item.id)">编辑</span>
							<span v-if="item.is_default != 1" @click.stop="delAccount(item.id, item.is_default)">删除</span>
						</div>
					</div>
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
			</div>
		</el-card>
	</div>
</template>

<script>
	import { accountList, accountDefault, delAccount } from "@/api/member/member"
	export default {
	    name: "account_list",
	    components: {},
	    data: () => {
	        return {
				dataList: [],
				total: 0,
				currentPage: 1,
				pageSize: 8,
				loading: true,
				isSub: false,
				yes: true
	        }
	    },
	    created() {
			this.back = this.$route.query.back
			this.getAccountList()
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
	            this.getAccountList()
	        },
			/**
			 * 获取账户列表
			 */
			getAccountList() {
				accountList({
					page_size: this.pageSize,
					page: this.currentPage
				}).then(res => {
					this.dataList = res.data.list
					this.total = res.data.count
					
					let withdrawType = {
						'bank': '银行',
						'alipay': '支付宝',
						'wechatpay': '微信'
					};
					this.dataList.forEach(item => {
						item.withdraw_type_name = withdrawType[item.withdraw_type] ? withdrawType[item.withdraw_type] : '';
					})
					
					this.loading = false
				}).catch(err => {
					this.loading = false
				})
			},
			setDefault(id) {
				if (this.isSub) return;
				this.isSub = true;
				
				accountDefault({
					id: id
				}).then(res => {
					if (this.back) {
						this.$router.push(this.back);
					} else {
						this.refresh()
						this.$message.success('修改默认账户成功')
					}
					this.isSub = false;
				}).catch(err => {
					this.isSub = false;
					this.$message.error(err.message)
				})
			},
			delAccount(id) {
				this.$confirm("确定要删除该账户吗?", "提示", {
				    confirmButtonText: "确定",
				    cancelButtonText: "取消",
				    type: "warning"
				}).then(() => {
				    delAccount({
				        id: id
				    }).then(res => {
						this.refresh()
						this.$message.success(res.message)
					}).catch(err => {
						this.$message.error(err.message)
					})
				})
			},
			/**
			 * 添加/编辑地址
			 */
			addAccount(type, id) {
			    if (type == "edit") {
			        this.$router.push({ path: "/member/account_edit", query: { id: id } })
			    } else {
			        this.$router.push({ path: "/member/account_edit" })
			    }
			},
	    }
	}
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
	
	.ns-member-address-list {
	    display: flex;
	    flex-wrap: wrap;
		
		.ns-account-list {
			cursor: pointer;
		}
		
	    .text {
	        width: 32%;
	        height: 170px;
	        margin-right: 2%;
	        border-radius: 5px;
	        border: 1px solid #d8d8d8;
	        margin-bottom: 20px;
	        padding: 0 15px;
	        box-sizing: border-box;
			position: relative;
	
	        .text-name {
	            height: 37px;
	            line-height: 40px;
	            padding: 0 10px;
	            border-bottom: 1px solid #eeeeee;
	        }
	
	        .text-default {
	            display: inline-block;
	            margin-left: 10px;
	            background: $base-color;
	            color: #ffffff;
	            width: 35px;
	            height: 20px;
	            line-height: 20px;
	            text-align: center;
	            border-radius: 3px;
	        }
	
	        .text-content {
	            padding: 10px;
				
				p {
					font-size: 12px;
				}
	        }
	
	        .ns-address-detail {
	            overflow: hidden;
	            text-overflow: ellipsis;
	            white-space: nowrap;
	        }
	
	        .text-operation {
	            // 操作
				position: absolute;
				right: 12px;
				bottom: 5px;
	
	            span {
	                margin: 0 5px;
	                color: #999999;
	                cursor: pointer;
					font-size: 12px;
	            }
	
	            span:hover {
	                color: $base-color;
	            }
	        }
	    }
	
	    .text:nth-child(3n) {
	        margin-right: 0;
	    }
	
	    .ns-add-address {
	        border: 1px dashed #d8d8d8;
	        text-align: center;
	        color: #999999;
	        line-height: 170px;
	        cursor: pointer;
	    }
	
	    .ns-add-address:hover {
	        border-color: $base-color;
	        color: $base-color;
	    }
	}
</style>
