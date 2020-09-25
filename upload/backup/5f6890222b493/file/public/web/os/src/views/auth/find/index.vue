<template>
	<div class="el-row-wrap find-pass">
		<ul>
			<li>
				<div>
					<p class="ns-bg-color">1</p>
					<p class="ns-text-color">输入手机号</p>
				</div>
				<span class="line-2 ns-border-color"></span>
			</li>
			<li id="current2">
				<span class="line-1" :class="step >= 2 ? 'ns-border-color' : 'ns-border-color-gray'"></span>
				<div>
					<p :class="step >= 2 ? 'ns-bg-color' : 'ns-bg-color-gray'">2</p>
					<p :class="step >= 2 ? 'ns-text-color' : 'ns-text-color-gray'">验证身份</p>
				</div>
				<span class="line-2" :class="step >= 2 ? 'ns-border-color' : 'ns-border-color-gray'"></span>
			</li>
			<li id="current3">
				<span class="line-1" :class="step >= 3 ? 'ns-border-color' : 'ns-border-color-gray'"></span>
				<div>
					<p :class="step >= 3 ? 'ns-bg-color' : 'ns-bg-color-gray'">3</p>
					<p :class="step >= 3 ? 'ns-text-color' : 'ns-text-color-gray'">重置密码</p>
				</div>
				<span class="line-2" :class="step >= 3 ? 'ns-border-color' : 'ns-border-color-gray'"></span>
			</li>
			<li id="current4">
				<span class="line-1" :class="step >= 4 ? 'ns-border-color' : 'ns-border-color-gray'"></span>
				<div>
					<p :class="step >= 4 ? 'ns-bg-color' : 'ns-bg-color-gray'">4</p>
					<p :class="step >= 4 ? 'ns-text-color' : 'ns-text-color-gray'">完成</p>
				</div>
			</li>
		</ul>

		<el-row>
			<el-col :span="12" :offset="6">
				<div class="grid-content bg-purple">
					<el-form :model="formData" :rules="rules" ref="ruleForm" class="ns-forget-pass-form">
						<div class="ns-forget-pass">
							<template v-if="step == 1">
								<el-form-item prop="mobile" key="1">
									<el-input v-model="formData.mobile" placeholder="请输入注册手机号">
										<template slot="prepend">
											<i class="el-icon-mobile-phone"></i>
										</template>
									</el-input>
								</el-form-item>

								<el-form-item><el-button type="primary" @click="nextStep('ruleForm')">下一步</el-button></el-form-item>
							</template>

							<template v-else-if="step == 2">
								<el-form-item prop="vercode" key="2">
									<el-input v-model="formData.vercode" autocomplete="off" placeholder="请输入验证码" maxlength="4">
										<template slot="prepend">
											<i class="el-icon-picture-outline"></i>
										</template>
										<template slot="append">
											<img :src="captcha.img" mode class="captcha" @click="getCaptcha" />
										</template>
									</el-input>
								</el-form-item>

								<el-form-item prop="dynacode" key="3">
									<el-input v-model="formData.dynacode" maxlength="4" autocomplete="off" placeholder="请输入短信动态码">
										<template slot="prepend">
											<i class="el-icon-mobile"></i>
										</template>
										<template slot="append">
											<div class="dynacode" :class="dynacodeData.seconds == 120 ? 'ns-text-color' : 'ns-text-color-gray'" @click="sendMobileCode('ruleForm')">
												{{ dynacodeData.codeText }}
											</div>
										</template>
									</el-input>
								</el-form-item>

								<el-form-item><el-button type="primary" @click="nextStepToSetPass('ruleForm')">下一步</el-button></el-form-item>
							</template>

							<template v-else-if="step == 3">
								<el-form-item prop="pass" key="4">
									<el-input v-model.trim="formData.pass" type="password" autocomplete="off" placeholder="请输入新的登录密码">
										<template slot="prepend">
											<i class="el-icon-lock"></i>
										</template>
									</el-input>
								</el-form-item>

								<el-form-item prop="repass" key="5">
									<el-input v-model.trim="formData.repass" type="password" autocomplete="off" placeholder="请再次输入新密码">
										<template slot="prepend">
											<i class="el-icon-lock"></i>
										</template>
									</el-input>
								</el-form-item>

								<el-form-item><el-button type="primary" @click="resetPass('ruleForm')">重置密码</el-button></el-form-item>
							</template>

							<template v-else-if="step == 4">
								<span class="ns-reset-success">重置密码成功</span>
								<el-form-item>
									<router-link to="/login"><el-button type="primary">重新登录</el-button></router-link>
								</el-form-item>
							</template>
						</div>
					</el-form>
				</div>
			</el-col>
		</el-row>
	</div>
</template>

<script>
import find from './find.js';
export default {
	name: 'find_pass',
	mixins: [find]
};
</script>
<style lang="scss" scoped>
.el-row-wrap {
	width: $width;
	margin: 0 auto;
	border: 1px solid #e5e5e5;
	ul {
		margin: 50px auto 0;
		width: 1032px;
		height: 51px;
		li {
			position: relative;
			float: left;
			width: 258px;
			height: 51px;
			text-align: center;
			div p:first-child {
				display: inline-block;
				width: 28px;
				height: 28px;
				line-height: 28px;
				color: #fff;
				border-radius: 28px;
			}
			div p:nth-child(2) {
				margin-top: 10px;
			}
			.line-1 {
				position: absolute;
				left: 0;
				top: 14px;
				display: inline-block;
				width: 100px;
				border-top: solid 2px;
			}
			.line-2 {
				position: absolute;
				right: 0;
				top: 14px;
				display: inline-block;
				width: 100px;
				border-top: solid 2px;
			}
			.ns-bg-color-gray {
				background-color: #b7b7b7;
			}
			.ns-text-color-gray {
				color: #898989;
			}
			.ns-border-color-gray {
				border-color: #e5e5e5 !important;
			}
		}
	}
}
.el-form {
	box-sizing: border-box;
	padding: 80px 0 50px;
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
	.ns-forget-pass {
		width: 60%;
		margin: 0 auto;
	}
	button {
		width: 100%;
	}
	.ns-reset-success {
		display: inline-block;
		font-size: 16px;
		text-align: center;
		width: 100%;
		height: 50px;
		line-height: 50px;
	}
}
</style>
<style lang="scss">
.find-pass {
	.ns-forget-pass-form {
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
}
</style>
