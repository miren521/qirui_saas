<template>
	<div class="box" v-loading="loading">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<span>收货地址</span>
			</div>

			<div>
				<div class="ns-member-address-list">
					<div class="text item ns-add-address" @click="addAddress('add')">
						<span>+ 添加收货地址</span>
					</div>

					<div class="text item" v-for="(item, index) in addressList" :key="index">
						<div class="text-name">
							<span>{{ item.name }}</span>
							<span v-if="item.is_default == 1" class="text-default">默认</span>
						</div>

						<div class="text-content">
							<p>{{ item.mobile }}</p>
							<p :title="item.full_address + item.address" class="ns-address-detail">{{ item.full_address }}{{ item.address }}</p>
						</div>

						<div class="text-operation">
							<span v-if="item.is_default != 1" @click="setDefault(item.id)">设为默认</span>
							<span @click="addAddress('edit', item.id)">编辑</span>
							<span v-if="item.is_default != 1" @click="delAddress(item.id, item.is_default)">删除</span>
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
    import { addressList, setDefault, deleteAddress } from "@/api/member/member"

    export default {
        name: "delivery_address",
        components: {},
        data: () => {
            return {
                addressList: [],
                total: 0,
                currentPage: 1,
                pageSize: 8,
                loading: true,
				yes: true
            }
        },
        created() {
            this.getListData()
        },
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
        methods: {
            getListData() {
                addressList({
                    page: this.currentPage,
                    page_size: this.pageSize
                })
                    .then(res => {
                        const { count, page_count, list } = res.data
                        this.total = count
                        this.addressList = list
                        this.loading = false
                    })
                    .catch(err => {
                        this.loading = false
                        this.$message.error(err.message)
                    })
            },
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
                this.getListData()
            },
            /**
             * 设为默认
             */
            setDefault(id) {
                setDefault({
                    id: id
                })
                    .then(res => {
                        this.refresh()
                        this.$message({
                            message: "修改默认地址成功",
                            type: "success"
                        })
                    })
                    .catch(err => {
                        this.$message.error(err.message)
                    })
            },

            /**
             * 添加/编辑地址
             */
            addAddress(type, id) {
                if (type == "edit") {
                    this.$router.push({ path: "/member/address_edit", query: { id: id } })
                } else {
                    this.$router.push({ path: "/member/address_edit" })
                }
            },

            /**
             * 删除地址
             */
            delAddress(id, is_default) {
                this.$confirm("确定要删除该地址吗?", "提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning"
                }).then(() => {
                    if (is_default == 1) {
                        this.$message({
                            type: "warning",
                            message: "默认地址，不能删除!"
                        })
                        return
                    }

                    deleteAddress({
                        id: id
                    })
                        .then(res => {
                            this.refresh()
                            this.$message({ message: res.message, type: "success" })
                        })
                        .catch(err => {
                            this.$message.error(err.message)
                        })
                })
            }
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

        .text {
            width: 32%;
            height: 140px;
            margin-right: 2%;
            border-radius: 5px;
            border: 1px solid #d8d8d8;
            margin-bottom: 20px;
            padding: 0 15px;
            box-sizing: border-box;

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
            }

            .ns-address-detail {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .text-operation {
                // 操作
                text-align: right;

                span {
                    margin: 0 5px;
                    color: #999999;
                    cursor: pointer;
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
            line-height: 140px;
            cursor: pointer;
        }

        .ns-add-address:hover {
            border-color: $base-color;
            color: $base-color;
        }
    }
</style>
