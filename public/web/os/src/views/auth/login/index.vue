<template>
	<div class="ns-login-wrap" :style="{background: backgroundColor}" v-loading="loadingAd">
		<div class="el-row-wrap el-row-wrap-login">
			<el-row>
				<el-col :span="13">
					<el-carousel height="460px" class="ns-login-bg" @change="handleChange">
						<el-carousel-item v-for="item in adList" :key="item.adv_id">
							<el-image :src="$img(item.adv_image)" fit="cover" @click="$router.pushToTab(item.adv_url.url)" />
						</el-carousel-item>
					</el-carousel>
				</el-col>
				<el-col :span="11" class="ns-login-form">
					<div class="grid-content bg-purple">
						<el-tabs v-model="activeName" @tab-click="handleClick">
							<el-tab-pane label="用户登录" name="first">
								<el-form v-if="activeName == 'first'" :model="formData" :rules="accountRules" ref="ruleForm">
									<el-form-item prop="account">
										<el-input v-model="formData.account" placeholder="请输入账号">
											<template slot="prepend">
												<i class="iconfont iconzhanghao"></i>
											</template>
										</el-input>
									</el-form-item>
									<el-form-item prop="password">
										<el-input type="password" v-model="formData.password" autocomplete="off" placeholder="请输入登录密码">
											<template slot="prepend">
												<i class="iconfont iconmima"></i>
											</template>
										</el-input>
									</el-form-item>
									<el-form-item prop="vercode">
										<el-input v-model="formData.vercode" autocomplete="off" placeholder="请输入验证码" maxlength="4">
											<template slot="prepend">
												<i class="iconfont iconyanzhengma"></i>
											</template>
											<template slot="append">
												<img :src="captcha.img" mode class="captcha" @click="getCaptcha" />
											</template>
										</el-input>
									</el-form-item>
									<el-form-item>
										<el-row>
											<el-col :span="12"><el-checkbox v-model="formData.checked">七天自动登录</el-checkbox></el-col>
											<el-col :span="12" class="ns-forget-pass"><router-link to="/find_pass" class>忘记密码</router-link></el-col>
										</el-row>
									</el-form-item>
									<el-form-item><el-button type="primary" @click="accountLogin('ruleForm')">登录</el-button></el-form-item>

									<el-form-item>
										<el-row>
											<el-col :span="24">
												<div class="bg-purple-light"><router-link to="/register">立即注册</router-link><i class="iconfont iconarrow-right"></i></div>
											</el-col>
										</el-row>
									</el-form-item>
								</el-form>
							</el-tab-pane>

							<el-tab-pane label="手机动态码登录" name="second" v-if="registerConfig.dynamic_code_login == 1">
								<el-form
									v-if="activeName == 'second'"
									:model="formData"
									:rules="mobileRules"
									ref="mobileRuleForm"
									class="ns-login-mobile"
								>
									<el-form-item prop="mobile">
										<el-input v-model="formData.mobile" placeholder="请输入手机号">
											<template slot="prepend">
												<i class="iconfont iconshouji-copy"></i>
											</template>
										</el-input>
									</el-form-item>

									<el-form-item prop="vercode">
										<el-input v-model="formData.vercode" autocomplete="off" placeholder="请输入验证码" maxlength="4">
											<template slot="prepend">
												<i class="iconfont iconyanzhengma"></i>
											</template>
											<template slot="append">
												<img :src="captcha.img" mode class="captcha" @click="getCaptcha" />
											</template>
										</el-input>
									</el-form-item>

									<el-form-item prop="dynacode">
										<el-input v-model="formData.dynacode" maxlength="4" placeholder="请输入短信动态码">
											<template slot="prepend">
												<i class="iconfont icondongtaima"></i>
											</template>
											<template slot="append">
												<div
													class="dynacode"
													:class="dynacodeData.seconds == 120 ? 'ns-text-color' : 'ns-text-color-gray'"
													@click="sendMobileCode('mobileRuleForm')"
												>
													{{ dynacodeData.codeText }}
												</div>
											</template>
										</el-input>
									</el-form-item>

									<el-form-item><el-button type="primary" @click="mobileLogin('mobileRuleForm')">登录</el-button></el-form-item>

									<el-form-item>
										<el-row>
											<el-col :span="24">
												<div class="bg-purple-light"><router-link to="/register">立即注册</router-link><i class="iconfont iconarrow-right"></i></div>
											</el-col>
										</el-row>
									</el-form-item>
								</el-form>
							</el-tab-pane>
						</el-tabs>
					</div>
				</el-col>
			</el-row>
		</div>
	</div>
</template>

<script>
import login from './login';
export default {
	name: 'login',
	mixins: [login]
};
</script>
<style lang="scss" scoped>
.ns-login-wrap {
	width: 100%;
	height: 500px;
	min-width: $width;
	.el-row-wrap-login {
		width: 1200px;
		margin: 0 auto;
		.ns-login-bg {
			margin-top: 40px;
		}

		.ns-login-form {
			width: 400px;
			margin-left: 50px;
			background: #ffffff;
			margin-top: 40px;

			.el-form {
				.captcha {
					vertical-align: top;
					max-width: inherit;
					max-height: 38px;
					line-height: 38px;
					cursor: pointer;
				}

				.dynacode {
					cursor: pointer;
				}

				[class*=' el-icon-'],
				[class^='el-icon-'] {
					font-size: 16px;
				}
			}
			.grid-content {
				padding: 10px 20px;
			}

			.el-form-item__error {
				padding-left: 50px;
			}

			button {
				width: 100%;
			}

			.ns-forget-pass {
				text-align: right;
			}
			
			i {
				font-size: 18px;
			}
			
			.bg-purple-light {
				display: flex;
				justify-content: flex-end;
				align-items: center;
				
				i {
					width: 16px;
					height: 16px;
					line-height: 16px;
					text-align: center;
					border-radius: 50%;
					background-color: $base-color;
					color: #FFFFFF;
					font-size: 12px;
					margin-left: 8px;
				}
			}
		}
	}
}
</style>

<style lang="scss">
.ns-login-form {
	.el-form-item__error {
		/* 错误提示信息 */
		padding-left: 57px;
	}

	.el-tabs__active-bar,
	.el-tabs__nav-wrap::after {
		/* 清除tab标签底部横线 */
		height: 0;
	}

	/* 立即注册 */
	.el-form-item__content {
		line-height: 20px;
	}
}
</style>
