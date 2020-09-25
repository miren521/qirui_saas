<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<div class="my-point" v-loading="loading">
			<div class="member-point">
				<div class="title">我的积分</div>
				<div class="num">{{ memberPoint.point ? Math.ceil(memberPoint.point) : 0 }}</div>
			</div>
			<div class="detail">
				<el-table :data="pointList" border>
					<el-table-column prop="type_name" label="来源" width="150"></el-table-column>
					<el-table-column prop="pointNum" label="积分" width="150"></el-table-column>
					<el-table-column prop="remark" label="详细说明"></el-table-column>
					<el-table-column prop="time" label="时间" width="180"></el-table-column>
				</el-table>
			</div>
			<div class="pager">
				<el-pagination 
					background 
					:pager-count="5" 
					:total="total" 
					prev-text="上一页" 
					next-text="下一页" 
					:current-page.sync="pointInfo.page" 
					:page-size.sync="pointInfo.page_size" 
					@size-change="handlePageSizeChange" 
					@current-change="handleCurrentPageChange" 
					hide-on-single-page
				></el-pagination>
			</div>
		</div>
	</div>
</template>

<script>
    import { pointInfo, pointList } from "@/api/member/my_point"
    export default {
        name: "my_point",
        components: {},
        data: () => {
            return {
                pointInfo: {
                    page: 1,
                    page_size: 10,
                    account_type: "point"
                },
                pointList: [],
                memberPoint: {
                    point: 0
                },
                total: 0,
                loading: true,
				yes: true
            }
        },
        created() {
            this.getPointInfo(), this.getpointList()
        },
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
        methods: {
            getPointInfo() {
                pointInfo({ account_type: this.pointInfo.account_type })
                    .then(res => {
                        if (res.code == 0 && res.data) {
                            this.memberPoint = res.data
                        }
                        this.loading = false
                    })
                    .catch(err => {
                        this.loading = false
                        this.$message.error(err.message)
                    })
            },
            getpointList() {
                pointList(this.pointInfo)
                    .then(res => {
                        if (res.code == 0 && res.data) {
                            this.pointList = res.data.list
                            this.total = res.data.count
                            this.pointList.forEach(item => {
                                item.time = this.$util.timeStampTurnTime(item.create_time)
                                item.pointNum = item.account_data > 0 ? "+" + parseInt(item.account_data) : parseInt(item.account_data)
                            })
                        }
                    })
                    .catch(err => {
                        this.$message.error(err.message)
                    })
            },
            handlePageSizeChange(num) {
                this.pointInfo.page_size = num
                this.getpointList()
            },
            handleCurrentPageChange(page) {
                this.pointInfo.page = page
                this.getpointList()
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
	
    .my-point {
        background: #ffffff;
        padding: 20px;
        .member-point {
            font-size: $ns-font-size-base;
            font-weight: 600;
            margin-bottom: 10px;
            .num {
                font-size: 30px;
                font-weight: 600;
            }
        }
        .page {
            display: flex;
            justify-content: center;
            align-content: center;
            padding-top: 20px;
        }
    }
</style>
