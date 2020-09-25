<template>
	<div class="register">
		<div class="box-card">
			<div class="register-title">用户注册</div>
			<div class="register-account">
				<el-form :model="registerForm" :rules="registerRules" ref="registerRef" label-width="80px" label-position="right" show-message>
					<el-form-item label="用户名" prop="username"><el-input v-model="registerForm.username" placeholder="请输入用户名"></el-input></el-form-item>
					<el-form-item label="密码" prop="password"><el-input v-model="registerForm.password" placeholder="请输入密码" type="password"></el-input></el-form-item>
					<el-form-item label="确认密码" prop="checkPass">
						<el-input v-model="registerForm.checkPass" placeholder="请输入确认密码" type="password"></el-input>
					</el-form-item>
					<el-form-item label="验证码" prop="code">
						<el-input v-model="registerForm.code" placeholder="请输入验证码" maxlength="4">
							<template slot="append">
								<img :src="captcha.img" mode class="captcha" @click="getCode" />
							</template>
						</el-input>
					</el-form-item>
				</el-form>
				<div class="xy" @click="check">
					<div class="xy-wrap">
						<div class="iconfont" :class="ischecked ? 'iconxuanze-duoxuan' : 'iconxuanze'"></div>
						<div class="content">
							阅读并同意
							<b @click.stop="getAggrement">《服务协议》</b>
						</div>
					</div>
					<div class="toLogin" @click="toLogin">已有账号，立即登录</div>
				</div>
				<el-button @click="register">立即注册</el-button>
			</div>
			<el-dialog :title="agreement.title" :visible.sync="aggrementVisible" width="60%" :before-close="aggrementClose" :lock-scroll="false" center>
				<div v-html="agreement.content" class="xyContent"></div>
			</el-dialog>
		</div>
	</div>
</template>

<script>
import { getRegisiterAggrement, register, registerConfig } from '@/api/auth/register';
import { captcha } from '@/api/website';
export default {
	name: 'register',
	components: {},
	data() {
		var checkPassValidata = (rule, value, callback) => {
			if (value === '') {
				callback(new Error('请再次输入密码'));
			} else if (value !== this.registerForm.password) {
				callback(new Error('两次输入密码不一致!'));
			} else {
				callback();
			}
		};
		let self = this;
		var passwordValidata = function(rule, value, callback) {
			let regConfig = self.registerConfig;
			if (!value) {
				return callback(new Error('请输入密码'));
			} else {
				if (regConfig.pwd_len > 0) {
					if (value.length < regConfig.pwd_len) {
						return callback(new Error('密码长度不能小于' + regConfig.pwd_len + '位'));
					} else {
						callback();
					}
				} else {
					if (regConfig.pwd_complexity != '') {
						let passwordErrorMsg = '密码需包含',
							reg = '';
						if (regConfig.pwd_complexity.indexOf('number') != -1) {
							reg += '(?=.*?[0-9])';
							passwordErrorMsg += '数字';
						} else if (regConfig.pwd_complexity.indexOf('letter') != -1) {
							reg += '(?=.*?[a-z])';
							passwordErrorMsg += '、小写字母';
						} else if (regConfig.pwd_complexity.indexOf('upper_case') != -1) {
							reg += '(?=.*?[A-Z])';
							passwordErrorMsg += '、大写字母';
						} else if (regConfig.pwd_complexity.indexOf('symbol') != -1) {
							reg += '(?=.*?[#?!@$%^&*-])';
							passwordErrorMsg += '、特殊字符';
						} else {
							reg += '';
							passwordErrorMsg += '';
						}

						if (reg.test(value)) {
							return callback(new Error(passwordErrorMsg));
						} else {
							callback();
						}
					}
				}
			}
		};
		return {
			registerForm: {
				username: '',
				password: '',
				checkPass: '',
				code: ''
			},
			registerRules: {
				username: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
				password: [
					{
						required: true,
						validator: passwordValidata,
						trigger: 'blur'
					}
				],
				checkPass: [{ required: true, validator: checkPassValidata, trigger: 'blur' }],
				code: [{ required: true, message: '请输入验证码', trigger: 'blur' }]
			},
			ischecked: false,
			agreement: '',
			aggrementVisible: false,
			captcha: {
				// 验证码
				id: '',
				img: ''
			},
			registerConfig: {}
		};
	},
	created() {
		this.getCode();
		this.regisiterAggrement();
		this.getRegisterConfig();
	},
	methods: {
		check() {
			this.ischecked = !this.ischecked;
		},
		toLogin() {
			this.$router.push('/login');
		},
		//  获取注册配置
		getRegisterConfig() {
			registerConfig()
				.then(res => {
					if (res.code >= 0) {
						this.registerConfig = res.data.value;
						if (this.registerConfig.is_enable != 1) {
							this.$message({
								message: '平台未启用注册',
								type: 'warning',
								duration: 2000,
								onClose: () => {
									this.$router.push('/');
								}
							});
						}
					}
				})
				.catch(err => {
					console.log(err.message)
				});
		},
		// 注册
		register() {
			this.$refs.registerRef.validate(valid => {
				if (valid) {
					if (!this.ischecked) {
						return this.$message({
							message: '请先阅读协议并勾选',
							type: 'warning'
						});
					}
					var data = {
						username: this.registerForm.username.trim(),
						password: this.registerForm.password
					};
					if (this.captcha.id != '') {
						data.captcha_id = this.captcha.id;
						data.captcha_code = this.registerForm.code;
					}
					this.$store
						.dispatch('member/register_token', data)
						.then(res => {
							if (res.code >= 0) {
								this.$router.push('/member/index');
							}
						})
						.catch(err => {
							this.$message.error(err.message);
							this.getCode();
						});
				} else {
					return false;
				}
			});
		},
		aggrementClose() {
			this.aggrementVisible = false;
		},
		// 获取协议
		regisiterAggrement() {
			getRegisiterAggrement()
				.then(res => {
					if (res.code >= 0) {
						this.agreement = res.data;
					}
				})
				.catch(err => {
					console.log(err.message)
				});
		},
		getAggrement() {
			this.aggrementVisible = true;
		},
		// 获取验证码
		getCode() {
			captcha({ captcha_id: 'this.captcha.id' })
				.then(res => {
					if (res.code >= 0) {
						this.captcha = res.data;
						this.captcha.img = this.captcha.img.replace(/\r\n/g, '');
					}
				})
				.catch(err => {
					this.$message.error(err.message);
				});
		}
	}
};
</script>
<style lang="scss" scoped>
.register {
	width: 100%;
	height: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
	margin: 20px 0;
}
.box-card {
	width: 500px;
	margin: 0 auto;
	display: flex;
	background-color: #ffffff;
	padding: 0 30px 30px 30px;
	flex-direction: column;

	.register-title {
		border-bottom: 1px solid #f1f1f1;
		text-align: left;
		margin-bottom: 20px;
		font-size: 16px;
		color: $base-color;
		padding: 10px 0;
	}
	.register-account {
		width: 100%;
		text-align: center;
	}
	.code {
		width: 80%;
		text-align: left;
	}
	.el-form {
		margin: 0 30px;
		.captcha {
			vertical-align: top;
			max-width: inherit;
			max-height: 38px;
			line-height: 38px;
			cursor: pointer;
		}
	}
	.xyContent {
		height: 600px;
		overflow-y: scroll;
	}
	.xy {
		margin-left: 110px;
		display: flex;
		justify-content: space-between;
		align-items: center;
		text-align: left;
		margin-right: 30px;
		.toLogin {
			cursor: pointer;
		}
		.xy-wrap {
			display: flex;
			align-items: center;
			font-size: $ns-font-size-base;
			cursor: pointer;
			.iconfont {
				display: flex;
				align-content: center;
			}
			.content {
				margin-left: 3px;
				b {
					color: $base-color;
				}
			}
		}
		.iconxuanze-duoxuan {
			color: $base-color;
		}
	}
	.el-button {
		margin-top: 20px;
		background-color: $base-color;
		color: #ffffff;
		width: calc(100% - 60px);
	}
}
</style>
