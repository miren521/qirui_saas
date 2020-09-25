import {
	rePass,
	nextStep,
	smsCode,
	registerConfig
} from "@/api/auth/login"
import {
	captcha
} from "@/api/website"

export default {
	data() {
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

		let self = this
		var setPass = function(rule, value, callback) {
			let regConfig = self.registerConfig
			if (!value) {
				return callback(new Error("请输入新的登录密码"))
			} else {
				if (regConfig.pwd_len > 0) {
					if (value.length < regConfig.pwd_len) {
						return callback(new Error("密码长度不能小于" + regConfig.pwd_len + "位"))
					} else {
						callback()
					}
				} else {
					if (regConfig.pwd_complexity != "") {
						let passwordErrorMsg = "密码需包含",
							reg = ""
						if (regConfig.pwd_complexity.indexOf("number") != -1) {
							reg += "(?=.*?[0-9])"
							passwordErrorMsg += "数字"
						} else if (regConfig.pwd_complexity.indexOf("letter") != -1) {
							reg += "(?=.*?[a-z])"
							passwordErrorMsg += "、小写字母"
						} else if (regConfig.pwd_complexity.indexOf("upper_case") != -1) {
							reg += "(?=.*?[A-Z])"
							passwordErrorMsg += "、大写字母"
						} else if (regConfig.pwd_complexity.indexOf("symbol") != -1) {
							reg += "(?=.*?[#?!@$%^&*-])"
							passwordErrorMsg += "、特殊字符"
						} else {
							reg += ""
							passwordErrorMsg += ""
						}

						if (reg.test(value)) {
							return callback(new Error(passwordErrorMsg))
						} else {
							callback()
						}
					}
				}
			}
		}

		var checkPass = function(rule, value, callback) {
			if (!value) {
				return callback(new Error("请输入确认密码"))
			} else {
				if (value !== self.formData.pass) {
					callback(new Error("两次密码输入不一致"))
				} else {
					callback()
				}
			}
		}

		return {
			formData: {
				mobile: "",
				vercode: "",
				dynacode: "",
				pass: "",
				repass: "",
				key: ""
			},
			step: 1,
			activeName: "first",
			isShowPhone: "",
			captcha: {
				id: "",
				img: ""
			}, // 验证码
			dynacodeData: {
				seconds: 120,
				timer: null,
				codeText: "获取动态码",
				isSend: false
			}, // 动态码
			registerConfig: {},
			rules: {
				mobile: [{
					required: true,
					validator: isMobile,
					trigger: "blur"
				}],
				vercode: [{
					required: true,
					message: "请输入验证码",
					trigger: "blur"
				}],
				dynacode: [{
					required: true,
					message: "请输入短信动态码",
					trigger: "blur"
				}],
				pass: [{
					required: true,
					validator: setPass,
					trigger: "blur"
				}],
				repass: [{
					required: true,
					validator: checkPass,
					trigger: "blur"
				}]
			}
		}
	},
	created() {
		this.getCaptcha()
		this.getRegisterConfig()
	},
	watch: {
		"dynacodeData.seconds": {
			handler(newValue, oldValue) {
				if (newValue == 0) {
					clearInterval(this.dynacodeData.timer)
					this.dynacodeData = {
						seconds: 120,
						timer: null,
						codeText: "获取动态码",
						isSend: false
					}
				}
			},
			immediate: true,
			deep: true
		}
	},
	methods: {
		/**
		 * 下一步
		 */
		nextStep(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					nextStep({
							mobile: this.formData.mobile
						})
						.then(res => {
							if (res.code == -1) {
								this.step = 2
							} else {
								this.$message({
									message: res.message,
									type: "warning"
								})
							}
						})
						.catch(err => {
							if (err.code == 0) {
								this.$message({
									message: "该手机号未注册",
									type: "warning"
								})
							} else {
								this.$message.error(err.message)
							}
						})
				} else {
					return false
				}
			})
		},
		/**
		 * 获取动态验证码 下一步
		 */
		nextStepToSetPass(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					this.step = 3
				} else {
					return false
				}
			})
		},
		/**
		 * 重置密码
		 */
		resetPass(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					rePass({
							password: this.formData.pass,
							code: this.formData.dynacode,
							key: this.formData.key,
							mobile: this.formData.mobile
						})
						.then(res => {
							if (res.code >= 0) {
								this.step = 4
								this.$message({
									message: res.message,
									type: "success"
								})
							}
						})
						.catch(err => {
							this.$message.error(err.message)
						})
				} else {
					return false
				}
			})
		},
		/**
		 * 获取验证码
		 */
		getCaptcha() {
			captcha({
					captcha_id: this.captcha.id
				})
				.then(res => {
					if (res.code >= 0) {
						this.captcha.id = res.data.id
						this.captcha.img = res.data.img
						this.captcha.img = this.captcha.img.replace(/\r\n/g, "")
					}
				})
				.catch(err => {
					this.$message.error(err.message)
				})
		},
		/**
		 * 发送手机动态码
		 */
		sendMobileCode(formName) {
			if (this.dynacodeData.seconds != 120) return
			this.$refs[formName].clearValidate("dynacode")
			this.$refs[formName].validateField("vercode", valid => {
				if (!valid) {
					if (this.isSend) return
					this.isSend = true

					smsCode({
							captcha_id: this.captcha.id,
							captcha_code: this.formData.vercode,
							mobile: this.formData.mobile
						})
						.then(res => {
							if (res.code >= 0) {
								this.formData.key = res.data.key

								if (this.dynacodeData.seconds == 120 && this.dynacodeData.timer == null) {
									this.dynacodeData.timer = setInterval(() => {
										this.dynacodeData.seconds--
										this.dynacodeData.codeText = this.dynacodeData.seconds + "s后可重新获取"
									}, 1000)
								} else {
									this.$message({
										message: res.message,
										type: "warning"
									})
									this.isSend = false
									this.getCaptcha()
								}
							}
						})
						.catch(err => {
							this.isSend = false
							this.getCaptcha()
							this.$message.error(err.message)
						})
				} else {
					return false
				}
			})
		},
		/**
		 * 获取注册配置
		 */
		getRegisterConfig() {
			registerConfig()
				.then(res => {
					if (res.code >= 0) {
						this.registerConfig = res.data.value
					}
				})
				.catch(err => {
					console.log(err.message)
				})
		}
	}
}
