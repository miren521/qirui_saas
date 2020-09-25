import {
	mobileCode,
	registerConfig
} from "@/api/auth/login"
import {
	adList,
	captcha
} from "@/api/website"

export default {
	data: () => {
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
			loginMode: "account",
			activeName: "first", // tab切换
			formData: {
				account: "",
				password: "",
				vercode: "",
				mobile: "",
				dynacode: "",
				key: "",
				checked: false,
				autoLoginRange: 7
			}, // 表单数据
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
			isSub: false, // 提交防重复
			registerConfig: {
				is_enable: 1
			},
			accountRules: {
				account: [{
					required: true,
					message: "请输入登录账号",
					trigger: "blur"
				}],
				password: [{
					required: true,
					message: "请输入登录密码",
					trigger: "blur"
				}],
				vercode: [{
					required: true,
					message: "请输入验证码",
					trigger: "blur"
				}]
			},
			mobileRules: {
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
				}]
			},
			codeRules: {
				mobile: [{
					required: true,
					validator: isMobile,
					trigger: "blur"
				}],
				vercode: [{
					required: true,
					message: "请输入验证码",
					trigger: "blur"
				}]
			},
			loadingAd: true,
			adList: [],
			backgroundColor: ''
		}
	},
	created() {
		this.getAdList()
		this.getCaptcha()
		this.getRegisterConfig()

		let that = this;
		document.onkeypress = function(e) {
			var keycode = document.all ? event.keyCode : e.which;
			if (keycode == 13) {
				if (that.activeName == "first") {
					that.accountLogin('ruleForm'); // 登录方法名
				} else {
					that.mobileLogin('mobileRuleForm'); // 登录方法名
				}

				return false;
			}
		};
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
		getAdList() {
			adList({
					keyword: "NS_PC_LOGIN"
				})
				.then(res => {
					if (res.code == 0 && res.data.adv_list) {
						this.adList = res.data.adv_list
						for (let i = 0; i < this.adList.length; i++) {
							if (this.adList[i].adv_url) this.adList[i].adv_url = JSON.parse(this.adList[i].adv_url)
						}
						this.backgroundColor = this.adList[0].background
					}

					this.loadingAd = false
				})
				.catch(err => {
					this.loadingAd = false
				})
		},
		handleClick(tab, event) {
			if (this.activeName == "first") {
				this.loginMode = "account"
			} else {
				this.loginMode = "mobile"
			}
		},
		handleChange(curr, pre) {
			this.backgroundColor = this.adList[curr].background
		},
		/**
		 * 账号登录
		 */
		accountLogin(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					var data = {
						username: this.formData.account,
						password: this.formData.password
					}

					if (this.captcha.id != "") {
						data.captcha_id = this.captcha.id
						data.captcha_code = this.formData.vercode
					}

					if (this.formData.checked) {
						data.autoLoginRange = this.formData.autoLoginRange
					}

					if (this.isSub) return
					this.isSub = true

					this.$store
						.dispatch("member/login", data)
						.then(res => {
							if (res.code >= 0) {
								this.$message({
									message: "登录成功！",
									type: "success"
								})
								if (this.$route.query.redirect) {
									const a = this.$route.query.redirect
									const b = this.$route.query
									this.$router.push(this.$route.query.redirect)
								} else {
									this.$router.push({
										name: "member"
									})
								}
							} else {
								this.isSub = false
								this.getCaptcha()
								this.$message({
									message: res.message,
									type: "warning"
								})
							}
						})
						.catch(err => {
							this.isSub = false
							this.$message.error(err.message)
							this.getCaptcha()
						})
				} else {
					return false
				}
			})
		},

		/**
		 * 手机号登录
		 */
		mobileLogin(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					var data = {
						mobile: this.formData.mobile,
						key: this.formData.key,
						code: this.formData.dynacode
					}

					if (this.captcha.id != "") {
						data.captcha_id = this.captcha.id
						data.captcha_code = this.formData.vercode
					}

					if (this.isSub) return
					this.isSub = true

					this.$store
						.dispatch("member/mobile_login", data)
						.then(res => {
							if (res.code >= 0) {
								this.$message({
									message: "登录成功！",
									type: "success"
								})
								if (this.$route.query.redirect) {
									this.$router.push(this.$route.query.redirect)
								} else {
									this.$router.push({
										name: "member"
									})
								}
							} else {
								this.isSub = false
								this.getCaptcha()
								this.$message({
									message: res.message,
									type: "warning"
								})
							}
						})
						.catch(err => {
							this.isSub = false
							this.$message.error(err.message)
							this.getCaptcha()
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
						if (this.registerConfig.dynamic_code_login == 1) this.loginMode = "mobile"
						else this.loginMode = "account"
					}
				})
				.catch(err => {
					console.log(err.message)
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

			this.$refs[formName].validateField("mobile", valid => {
				if (valid) {
					return false
				}
			})
			this.$refs[formName].validateField("vercode", valid => {
				if (!valid) {
					mobileCode({
							mobile: this.formData.mobile,
							captcha_id: this.captcha.id,
							captcha_code: this.formData.vercode
						})
						.then(res => {
							if (res.code >= 0) {
								this.formData.key = res.data.key
								if (this.dynacodeData.seconds == 120 && this.dynacodeData.timer == null) {
									this.dynacodeData.timer = setInterval(() => {
										this.dynacodeData.seconds--
										this.dynacodeData.codeText = this.dynacodeData.seconds + "s后可重新获取"
									}, 1000)
								}
							}
						})
						.catch(err => {
							this.$message.error(err.message)
						})
				} else {
					return false
				}
			})
		}
	}
}
