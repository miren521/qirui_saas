<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		<el-card class="box-card order-list">
			<div slot="header" class="clearfix"><span>核销台</span></div>
			<div class="ns-verification">
				<div class="ns-verification-flow">
					<div class="ns-verification-icon">
						<div><i class="iconfont iconshurutianxiebi"></i></div>
						<p>输入核销码</p>
					</div>
					<div><i class="iconfont iconjiang-copy"></i></div>
					<div class="ns-verification-icon">
						<div><i class="iconfont iconhexiao"></i></div>
						<p>核销</p>
					</div>
				</div>
				<div class="ns-verification-wrap">
					<el-input v-model="verify_code" placeholder="请输入核销码"></el-input>
					<el-button @click="confirm">确认</el-button>
				</div>
			</div>
		</el-card>
	</div>
</template>

<script>
    import { checkisverifier, verifyInfo } from "@/api/order/verification"

    export default {
        name: "verification",
        components: {},
        data: () => {
            return {
                verify_code: "",
				yes: true
            }
        },
        created() {},
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
        methods: {
            confirm() {
                var reg = /[\S]+/
                if (!reg.test(this.verify_code)) {
                    this.$message({ message: "请输入核销码", type: "warning" })
                    return false
                }
                verifyInfo({
                    verify_code: this.verify_code
                })
                    .then(res => {
                        if (res.code >= 0) {
                            this.$router.push({ path: "/member/verification_detail", query: { code: this.verify_code } })
                        } else {
                            this.$message({ message: res.message, type: "warning" })
                        }
                    })
                    .catch(err => {
                        this.$message.error(err.message)
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
	
    .ns-verification {
        margin: 20px 0;
        background: #ffffff;
        padding: 80px;
        text-align: center;
        .ns-verification-flow {
            display: flex;
            justify-content: center;
            .ns-verification-icon {
                width: 150px;
                margin: 0 20px;
                div {
                    display: inline-block;
                    background: #eee;
                    width: 60px;
                    height: 60px;
                    text-align: center;
                    line-height: 60px;
                    border-radius: 50%;
                }
                p {
                    color: #999999;
                    margin-top: 5px;
                }
            }
            i {
                font-size: 30px;
                color: #999999;
            }
        }
        .ns-verification-wrap {
            display: inline-block;
            width: 500px;
            margin-top: 50px;

            .el-button {
                margin-top: 50px;
                background: $base-color;
                color: #ffffff;
            }
        }
    }
</style>
