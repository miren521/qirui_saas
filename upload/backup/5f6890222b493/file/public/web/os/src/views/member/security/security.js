import {
	passWord,
	emailCode,
	checkEmail,
	email,
	tellCode,
	tell,
	verifypaypwdcode,
	modifypaypassword,
	paypwdcode,
	pwdmobliecode
} from "@/api/member/security"
import {
	info
} from "@/api/member/info"
import {
	captcha
} from "@/api/website"
export default {
	name: "security",
	components: {},
	data() {
		var validatePass = (rule, value, callback) => {
			if (value === "") {
				callback(new Error("请输入新密码"))
			} else if (value == this.passWordForm.oldPass) {
				callback(new Error("新密码不能与原密码相同！"))
			} else {
				if (this.passWordForm.checkPass !== "") {
					this.$refs.passWordRef.validateField("checkPass")
				}
				callback()
			}
		}
		var validatePass2 = (rule, value, callback) => {
			if (value === "") {
				callback(new Error("请再次输入密码"))
			} else if (value !== this.passWordForm.pass) {
				callback(new Error("两次输入密码不一致!"))
			} else {
				callback()
			}
		}
		var validateTellPass = (rule, value, callback) => {
			if (value === "") {
				callback(new Error("请输入新密码"))
			} else if (value == this.tellPassForm.oldPass) {
				callback(new Error("新密码不能与原密码相同！"))
			} else {
				if (this.tellPassForm.checkPass !== "") {
					this.$refs.tellPassRef.validateField("checkPass")
				}
				callback()
			}
		}
		var validateTellPass2 = (rule, value, callback) => {
			if (value === "") {
				callback(new Error("请再次输入密码"))
			} else if (value !== this.tellPassForm.pass) {
				callback(new Error("两次输入密码不一致!"))
			} else {
				callback()
			}
		}
		var checkemail = (rules, value, callback) => {
			const regEmail = /^\w+@\w+(\.\w+)+$/
			if (regEmail.test(value)) {
				return callback()
			}
			callback(new Error("请输入正确的的邮箱"))
		}
		var checkTell = (rules, value, callback) => {
			const regMobile = /^1[3|4|5|6|7|8|9][0-9]{9}$/
			if (regMobile.test(value)) {
				return callback()
			}
			callback(new Error("请输入正确的手机号"))
		}
		return {
			type: "all",
			passWordForm: {
				oldPass: "",
				pass: "",
				checkPass: ""
			},
			emailForm: {
				email: "",
				code: "", //邮箱验证码
				emailDynacode: "", //邮箱动态验证码
				emailCodeText: "",
				key: "",
				currEmail: ""
			},
			passWordRules: {
				oldPass: [{
					required: true,
					message: "请输入原密码",
					trigger: "blur"
				}],
				pass: [{
					required: true,
					validator: validatePass,
					trigger: "blur"
				}],
				checkPass: [{
					required: true,
					validator: validatePass2,
					trigger: "blur"
				}]
			},
			emailRules: {
				email: [{
					required: true,
					message: "请输入正确的邮箱",
					trigger: "blur"
				},
				{
					validator: checkemail,
					trigger: "blur"
				}
				],
				code: [{
					required: true,
					message: "请输入验证码",
					trigger: "blur"
				}],
				emailDynacode: [{
					required: true,
					message: "请输入动态验证码",
					trigger: "blur"
				}]
			},
			captcha: {
				id: "",
				img: ""
			},
			seconds: 120,
			timer: null,
			isSend: false,
			tellForm: {
				tell: "",
				code: "", //邮箱验证码
				tellDynacode: "", //邮箱动态验证码
				tellCodeText: "",
				key: "",
				currTell: ""
			},
			tellRules: {
				tell: [{
					required: true,
					message: "请输入正确的手机号",
					trigger: "blur"
				},
				{
					validator: checkTell,
					trigger: "blur"
				}
				],
				code: [{
					required: true,
					message: "请输入验证码",
					trigger: "blur"
				}],
				tellDynacode: [{
					required: true,
					message: "请输入动态验证码",
					trigger: "blur"
				}]
			},
			isClick: true,
			payCodeText: '获取验证码',
			step: 0,
			payCode: '', // 动态码
			payPassword: '', // 支付密码
			payRepassword: '', // 重复支付密码
			payKey: '', // 短信key
			payInput: '',
			palceText: '输入短信验证码',
			memberInfo: {},
			tellPassForm: {
				code: "",
				tellPassCodeText: "",
				key: "",
				tellPassDynacode: "",
				pass: '',
				checkPass: ''
			},
			tellPassRules: {
				code: [{
					required: true,
					message: "请输入验证码",
					trigger: "blur"
				}],
				tellPassDynacode: [{
					required: true,
					message: "请输入动态验证码",
					trigger: "blur"
				}],
				pass: [{
					required: true,
					validator: validateTellPass,
					trigger: "blur"
				}],
				checkPass: [{
					required: true,
					validator: validateTellPass2,
					trigger: "blur"
				}]
			},
			loading: true,
			yes: true
		}
	},
	created() {
		this.getcaptcha()
		this.seconds = 120
		this.tellForm.tellCodeText = "获取动态码"
		this.emailForm.emailCodeText = "获取动态码"
		this.tellPassForm.tellPassCodeText = "获取动态码"
		this.isSend = false
		clearInterval(this.timer)
		this.getInfo()
	},
	mounted() {
		let self = this;
		setTimeout(function() {
			self.yes = false
		}, 300)
	},
	methods: {
		//获取个人信息
		async getInfo() {
			await info()
				.then(res => {
					if (res.code == 0) {
						this.memberInfo = res.data
						this.emailForm.currEmail = res.data.email
						this.tellForm.currTell = res.data.mobile
					}
					this.loading = false
				})
				.catch(err => {
					this.loading = false
					this.$message.error(err.message)
				})
		},
		async edit(type) {
			await this.getInfo()
			if (type == 'payPassWord') {
				if (!this.tellForm.currTell) {
					this.$confirm("你还未绑定手机号，请先绑定手机号？", "提示信息", {
						confirmButtonText: "确定",
						cancelButtonText: "取消",
						type: "warning"
					}).then(res => {
						if (res = 'confirm') {
							this.type = 'tell'
						} else {
							this.type = 'all'
						}
					})
				} else {
					this.type = type
				}
			} else {
				this.type = type
			}
		},
		//获取验证码
		getcaptcha() {
			captcha({
				captcha_id: this.captcha.id
			})
				.then(res => {
					if (res.code >= 0) {
						this.captcha = res.data
						this.captcha.img = this.captcha.img.replace(/\r\n/g, "")
					}
				})
				.catch(err => {
					this.$message.error(err.message)
				})
		},
		//修改密码
		save() {
			this.$refs.passWordRef.validate(valid => {
				if (valid) {
					passWord({
						new_password: this.passWordForm.pass,
						old_password: this.passWordForm.oldPass
					})
						.then(res => {
							this.$message({
								message: "修改密码成功",
								type: "success"
							})
							this.type = "all"
							this.$store.dispatch("member/member_detail", {
								refresh: 1
							})
							this.passWordForm.pass = ""
							this.passWordForm.oldPass = ""
							this.passWordForm.checkPass = ""
						})
						.catch(err => {
							this.$message.error(err.message)
						})
				} else {
					return false
				}
			})
		},
		// 检测邮箱是否存在
		async getCheckEmail() {
			let result = await checkEmail({
				email: this.emailForm.email
			})
				.then(res => {
					if (res.code != 0) {
						this.$message({
							message: res.message,
							type: "success"
						})
						return false
					}
					return true
				})
				.catch(err => {
					this.$message.error(err.message)
				})
			return result
		},
		//获取邮箱验证码
		async getEmailCode() {
			if (!(await this.getCheckEmail())) return
			if (!this.isSend) {
				this.isSend = true
				await emailCode({
					email: this.emailForm.email,
					captcha_id: this.captcha.id,
					captcha_code: this.emailForm.code
				})
					.then(res => {
						let data = res.data
						if (data.key) {
							if (this.seconds == 120 && this.timer == null) {
								this.timer = setInterval(() => {
									this.seconds--
									this.emailForm.emailCodeText = "已发送(" + this.seconds + "s)"
								}, 1000)
							}
							this.emailForm.key = data.key
						} else {
							this.$message({
								message: res.message,
								type: "warning"
							})
							this.isSend = false
						}
					})
					.catch(err => {
						this.getcaptcha()
						this.$message.error(err.message)
					})
			} else {
				this.$message({
					message: "请勿重复点击",
					type: "warning"
				})
			}
		},
		//绑定邮箱
		async bindEmail() {
			this.$refs.emailRef.validate(valid => {
				if (valid) {
					email({
						email: this.emailForm.email,
						captcha_id: this.captcha.id,
						captcha_code: this.emailForm.code,
						code: this.emailForm.emailDynacode,
						key: this.emailForm.key
					})
						.then(res => {
							if (res.code == 0) {
								this.$message({
									message: "邮箱绑定成功",
									type: "success"
								})
								this.type = "all"
								this.emailForm.email = ""
								this.emailForm.code = ""
								this.emailForm.emailDynacode = ""
								clearInterval(this.timer)
								this.getcaptcha()
							}
						})
						.catch(err => {
							this.getcaptcha()
							this.$message.error(err.message)
						})
				} else {
					return false
				}
			})
		},
		//获取手机验证码
		async gettellCode() {
			if (!this.isSend) {
				this.isSend = true
				await tellCode({
					mobile: this.tellForm.tell,
					captcha_id: this.captcha.id,
					captcha_code: this.tellForm.code
				})
					.then(res => {
						let data = res.data
						if (data.key) {
							if (this.seconds == 120 && this.timer == null) {
								this.timer = setInterval(() => {
									this.seconds--
									this.tellForm.tellCodeText = "已发送(" + this.seconds + "s)"
								}, 1000)
							}
							this.tellForm.key = data.key
						} else {
							this.$message({
								message: res.message,
								type: "warning"
							})
							this.isSend = false
						}
					})
					.catch(err => {
						this.getcaptcha()
						this.$message.error(err.message)
					})
			} else {
				this.$message({
					message: "请勿重复点击",
					type: "warning"
				})
			}
		},
		//绑定手机号
		bindtell() {
			this.$refs.tellRef.validate(valid => {
				if (valid) {
					tell({
						mobile: this.tellForm.tell,
						captcha_id: this.captcha.id,
						captcha_code: this.tellForm.code,
						code: this.tellForm.tellDynacode,
						key: this.tellForm.key
					})
						.then(res => {
							if (res.code == 0) {
								this.$message({
									message: "手机号绑定成功",
									type: "success"
								})
								this.type = "all"
								this.tellForm.email = ""
								this.tellForm.code = ""
								this.tellForm.emailDynacode = ""
								clearInterval(this.timer)
								this.getcaptcha()
							}
						})
						.catch(err => {
							this.getcaptcha()
							this.$message.error(err.message)
						})
				} else {
					return false
				}
			})
		},
		//获取输入框数据
		input(val) {
			this.isClick = false
			if (this.step == 0 && val.length == 4) {
				this.payCode = val;
			} else if (this.step == 1 && val.length == 6) {
				this.payPassword = val;
			} else if (val.length == 6) {
				this.payRepassword = val;
			}

		},
		//获取支付验证码
		sendMobileCode() {
			if (!this.isSend) {
				paypwdcode().then(res => {
					let data = res.data;
					if (data.key) {
						if (this.seconds == 120 && this.timer == null) {
							this.timer = setInterval(() => {
								this.seconds--;
								this.payCodeText =
									"已发送(" + this.seconds + "s)";
							}, 1000);
						}
						this.payKey = data.key;
					} else {
						this.$message({
							message: res.message,
							type: 'warning'
						});
						this.isSend = false;
					}
				})
					.catch(err => {
						this.$message.error(err.message);
					});
			}
		},
		//点击确定
		bindPayPwd() {
			clearInterval(this.timer)
			const reg = /^[0-9]*$/
			if (this.step == 0) {
				verifypaypwdcode({
					code: this.payCode,
					key: this.payKey
				}).then(res => {
					if (res.code == 0) {
						this.$refs.input.clear();
						this.step = 1;
						this.palceText = '请设置支付密码'
					}
				}).catch(err => {
					this.$message.error(err.message);
				})
			} else if (this.step == 1) {
				if (reg.test(this.$refs.input.value)) {
					this.$refs.input.clear();
					this.step = 2;
					this.palceText = '请再次输入'
				} else {
					this.$message.error('请输入数字')
					this.step = 1;
					this.$refs.input.clear();
				}
			} else {
				if (this.payPassword == this.payRepassword) {
					if (this.isSub) return;
					this.isSub = true;
					modifypaypassword({
						key: this.payKey,
						code: this.payCode,
						password: this.payPassword
					}).then(res => {
						if (res.code >= 0) {
							this.$message({
								message: "修改支付密码成功",
								type: "success"
							})
							this.type = 'all'
							this.step = 0,
								this.$refs.input.clear();
							clearInterval(this.timer)
						}
					}).catch(err => {
						this.$message.error(err.message);
					})
				} else {
					this.$message.error('两次密码输入不一样');
					this.initInfo();
				}
			}
		},
		//初始化信息
		initInfo() {
			this.step = 1;
			this.palceText = '请设置支付密码'
			this.password = '';
			this.repassword = '';
			this.oldpassword = '';
			this.isSub = false;
			this.$refs.input.clear();
		},
		//获取动态码
		getTellPassCode() {
			if (!this.isSend) {
				this.isSend = true
				pwdmobliecode({
					captcha_id: this.captcha.id,
					captcha_code: this.tellPassForm.code,
				})
					.then(res => {
						let data = res.data
						if (data.key) {
							if (this.seconds == 120 && this.timer == null) {
								this.timer = setInterval(() => {
									this.seconds--
									this.tellPassForm.tellPassCodeText = "已发送(" + this.seconds + "s)"
								}, 1000)
							}
							this.tellPassForm.key = data.key
						} else {
							this.$message({
								message: res.message,
								type: "warning"
							})
							this.isSend = false
						}
					})
					.catch(err => {
						this.getcaptcha()
						this.$message.error(err.message)
					})
			} else {
				this.$message({
					message: "请勿重复点击",
					type: "warning"
				})
			}
		},
		//修改密码
		tellPassSave() {
			this.$refs.tellPassRef.validate(valid => {
				if (valid) {
					passWord({
						new_password: this.tellPassForm.pass,
						code: this.tellPassForm.tellPassDynacode,
						key: this.tellPassForm.key,
					})
						.then(res => {
							this.$message({
								message: "修改密码成功",
								type: "success"
							})
							this.type = "all"
							this.$store.dispatch("member/member_detail", {
								refresh: 1
							})
							this.tellPassForm.pass = ""
							this.tellPassForm.checkPass = ""
							this.tellPassForm.key = ""
							this.tellPassForm.tellPassDynacode = ""
						})
						.catch(err => {
							this.$message.error(err.message)
						})
				} else {
					return false
				}
			})
		},
	},
	filters: {
		mobile(mobile) {
			return mobile.substring(0, 4 - 1) + '****' + mobile.substring(6 + 1);
		}
	},
}
