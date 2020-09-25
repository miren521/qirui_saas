<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<span>编辑账户</span>
			</div>
		
			<div v-loading="loading">
				<el-form :model="formData" :rules="rules" ref="ruleForm" label-width="80px">
					<el-form-item label="姓名" prop="realname">
						<el-input v-model="formData.realname" placeholder="请输入真实姓名" class="ns-len-input"></el-input>
					</el-form-item>
		
					<el-form-item label="手机" prop="mobile">
						<el-input v-model="formData.mobile" autocomplete="off" placeholder="请输入手机号" maxlength="11" class="ns-len-input"></el-input>
					</el-form-item>
		
					<el-form-item label="账号类型" prop="withdraw_type">
						<el-select v-model="formData.withdraw_type" placeholder="请选择账号类型">
							<el-option v-for="(value, key) in transferType" :key="key" :label="value" :value="key" :disabled="key == 'wechatpay'"></el-option>
						</el-select>
					</el-form-item>
		
					<el-form-item label="银行名称" prop="branch_bank_name" v-if="formData.withdraw_type == 'bank'">
						<el-input v-model="formData.branch_bank_name" autocomplete="off" placeholder="请输入银行名称" maxlength="50" class="ns-len-input"></el-input>
					</el-form-item>
					
					<el-form-item label="提现账号" prop="bank_account" v-if="formData.withdraw_type != 'wechatpay' && formData.withdraw_type">
						<el-input v-model="formData.bank_account" autocomplete="off" placeholder="请输入提现账号" maxlength="30" class="ns-len-input"></el-input>
					</el-form-item>
		
					<el-form-item>
						<el-button size="medium" type="primary" @click="saveAccount('ruleForm')">保存</el-button>
					</el-form-item>
				</el-form>
			</div>
		</el-card>
	</div>
</template>

<script>
	import { transferType, accountDetail, saveAccount } from "@/api/member/member"
	export default {
		name: "account_edit",
	    components: {},
	    data () {
			let self = this
			var isMobile = (rule, value, callback) => {
			    if (!value) {
			        return callback(new Error("手机号不能为空"))
			    } else {
			        const reg = /^1[3|4|5|6|7|8|9][0-9]{9}$/
			
			        if (reg.test(value)) {
			            callback()
			        } else {
			            callback(new Error("请输入正确的手机号"))
			        }
			    }
			}
	        return {
				formData: {
					id: '',
					realname: '',
					mobile: '',
					withdraw_type: '',
					bank_account: '',
					branch_bank_name: ''
				},
				flag: false, //防重复标识
				payList: [],
				loading: true,
				index: 0,
				transferType: [],
				rules: {
					realname: [{ required: true, message: "请输入真实姓名", trigger: "blur" }],
					mobile: [{ required: true, validator: isMobile, trigger: "blur" }],
					withdraw_type: [{ required: true, message: '请选择账号类型', trigger: 'change' }],
					branch_bank_name: [{ required: true, message: "请输入银行名称", trigger: "blur" }],
					bank_account: [{ required: true, message: "请输入提现账号", trigger: "blur" }],
				},
				yes: true
	        }
	    },
	    created() {
			this.formData.id = this.$route.query.id
			this.getTransferType();
			if (this.formData.id) {
				this.getAccountDetail(this.formData.id)
			}
	    },
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
	    methods: {
			/**
			 * 获取转账方式
			 */
			getTransferType() {
				transferType().then(res => {
					this.transferType = res.data;
					
					if (!this.formData.id) { // id为空，即添加时在此结束加载
						this.loading = false
					}
				}).catch(err => {
					if (!this.formData.id) {
						this.loading = false
					}
				})
			},
			/**
			 * 获取账户详情（编辑）
			 */
			getAccountDetail(id) {
				accountDetail({id: this.formData.id}).then(res => {
					if (res.code == 0 && res.data) {
						this.formData.realname = res.data.realname;
						this.formData.mobile = res.data.mobile;
						this.formData.bank_account = res.data.bank_account;
						this.formData.branch_bank_name = res.data.branch_bank_name;
						this.formData.withdraw_type = res.data.withdraw_type;
					}
					this.loading = false
				}).catch(err => {
					this.loading = false
				})
			},
			/**
			 * 保存
			 */
			saveAccount(formName) {
				this.$refs[formName].validate(valid => {
				    if (valid) {
				        var data = {
							id: this.formData.id,
							realname: this.formData.realname,
							mobile: this.formData.mobile,
							withdraw_type: this.formData.withdraw_type,
							bank_account: this.formData.bank_account,
							branch_bank_name: this.formData.branch_bank_name
				        }
				
				        data.url = "add"
				        if (this.formData.id) {
				            data.url = "edit"
				        }
						
				        if (this.flag) return
				        this.flag = true
				
				        saveAccount(data)
				            .then(res => {
				                if (res.code == 0) {
				                    this.$router.push({ path: "/member/account_list" })
				                } else {
				                    this.flag = false
				                    this.$message({ message: res.message, type: "warning" })
				                }
				            })
				            .catch(err => {
				                this.flag = false
				                this.$message.error(err.message)
				            })
				    } else {
				        return false
				    }
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
	
	.ns-len-input {
	    width: 350px;
	}
	
	.el-select {
	    margin-right: 10px;
	}
</style>
