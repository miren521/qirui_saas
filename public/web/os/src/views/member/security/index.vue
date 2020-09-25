<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<div class="security" v-loading="loading">
			<div class="item-wrap" v-if="type == 'all'">
				<div class="item">
					<div class="item-content">
						<i class="iconfont iconxiugaidenglumima"></i>
						<div class="name-wrap">
							<div class="name">登录密码</div>
							<div class="content">互联网账号存在被盗风险，建议您定期更改密码以保护账户安全</div>
						</div>
					</div>
					<div class="btn"><el-button type="primary" size="medium" @click="edit('password')">修改</el-button></div>
				</div>
				<div class="item">
					<div class="item-content">
						<i class="iconfont iconyouxiang1"></i>
						<div class="name-wrap">
							<div class="name">邮箱绑定</div>
							<div class="content">验证后，可用于快速找回登录密码及支付密码</div>
						</div>
					</div>
					<div class="btn"><el-button type="primary" size="medium" @click="edit('email')">修改</el-button></div>
				</div>
				<div class="item">
					<div class="item-content">
						<i class="iconfont iconshoujiyanzheng"></i>
						<div class="name-wrap">
							<div class="name">手机验证</div>
							<div class="content">验证后，可用于快速找回登录密码及支付密码，接收账户余额变动提醒</div>
						</div>
					</div>
					<div class="btn"><el-button type="primary" size="medium" @click="edit('tell')">修改</el-button></div>
				</div>
				<div class="item">
					<div class="item-content">
						<i class="el-icon-lock"></i>
						<div class="name-wrap">
							<div class="name">支付密码</div>
							<div class="content">互联网支付存在被盗风险，建议您定期更改支付密码以保护账户安全</div>
						</div>
					</div>
					<div class="btn"><el-button type="primary" size="medium" @click="edit('payPassWord')">修改</el-button></div>
				</div>
			</div>
			<div class="edit" v-if="type == 'password'">
				<div class="title">修改登录密码</div>
				<div v-if="memberInfo.password">
					<div class="pass-form">
						<el-form :model="passWordForm" :rules="passWordRules" ref="passWordRef" label-width="100px">
							<el-form-item label="原密码" prop="oldPass"><el-input type="password" placeholder="当前密码" v-model="passWordForm.oldPass"></el-input></el-form-item>
							<el-form-item label="新密码" prop="pass"><el-input type="password" placeholder="新密码" v-model="passWordForm.pass"></el-input></el-form-item>
							<el-form-item label="确认密码" prop="checkPass">
								<el-input type="password" placeholder="请确认新密码" v-model="passWordForm.checkPass"></el-input>
							</el-form-item>
						</el-form>
					</div>
					<div class="btn">
						<el-button type="primary" @click="save">保存</el-button>
						<el-button @click="type = 'all'">取消</el-button>
					</div>
				</div>
				<div v-else class="tell-pass">
					<el-form :model="tellPassForm" :rules="tellPassRules" ref="tellPassRef" label-width="100px">
						<el-form-item label="验证码" prop="code">
							<el-input placeholder="请输入验证码" maxlength="4" v-model="tellPassForm.code">
								<template slot="append">
									<img :src="captcha.img" mode class="captcha" @click="getcaptcha" />
								</template>
							</el-input>
						</el-form-item>
						<el-form-item label="动态码" prop="tellPassDynacode">
							<el-input placeholder="请输入动态码" v-model="tellPassForm.tellPassDynacode">
								<template slot="append">
									<el-button type="primary" @click="getTellPassCode">{{ tellPassForm.tellPassCodeText }}</el-button>
								</template>
							</el-input>
						</el-form-item>
						<p class="tell-code">点击“获取动态码”，将会向您已绑定的手机号{{ memberInfo.mobile | mobile }}发送验证码</p>
						<el-form-item label="新密码" prop="pass"><el-input type="password" placeholder="新密码" v-model="tellPassForm.pass"></el-input></el-form-item>
						<el-form-item label="确认密码" prop="checkPass"><el-input type="password" placeholder="请确认新密码" v-model="tellPassForm.checkPass"></el-input></el-form-item>
					</el-form>
					<div class="btn">
						<el-button type="primary" @click="tellPassSave">保存</el-button>
						<el-button @click="type = 'all'">取消</el-button>
					</div>
				</div>
			</div>
			<div class="edit" v-if="type == 'email'">
				<div class="title">绑定邮箱</div>
				<div class="pass-form">
					<el-form :model="emailForm" :rules="emailRules" ref="emailRef" label-width="100px">
						<el-form-item label="当前邮箱" prop="email" v-if="emailForm.currEmail">
							<p>{{ emailForm.currEmail }}</p>
						</el-form-item>
						<el-form-item label="邮箱" prop="email"><el-input type="email" placeholder="请输入邮箱" v-model="emailForm.email"></el-input></el-form-item>
						<el-form-item label="验证码" prop="code">
							<el-input placeholder="请输入验证码" maxlength="4" v-model="emailForm.code">
								<template slot="append">
									<img :src="captcha.img" mode class="captcha" @click="getcaptcha" />
								</template>
							</el-input>
						</el-form-item>
						<el-form-item label="动态码" prop="emailDynacode">
							<el-input placeholder="请输入动态码" v-model="emailForm.emailDynacode">
								<template slot="append">
									<el-button type="primary" @click="getEmailCode">{{ emailForm.emailCodeText }}</el-button>
								</template>
							</el-input>
						</el-form-item>
					</el-form>
				</div>
				<div class="btn">
					<el-button type="primary" @click="bindEmail">保存</el-button>
					<el-button @click="type = 'all'">取消</el-button>
				</div>
			</div>
			<div class="edit" v-if="type == 'tell'">
				<div class="title">绑定手机号</div>
				<div class="pass-form">
					<el-form :model="tellForm" :rules="tellRules" ref="tellRef" label-width="100px">
						<el-form-item label="当前手机号" prop="email" v-if="tellForm.currTell">
							<p>{{ tellForm.currTell }}</p>
						</el-form-item>
						<el-form-item label="手机号" prop="tell"><el-input type="tell" placeholder="请输入手机号" v-model="tellForm.tell"></el-input></el-form-item>
						<el-form-item label="验证码" prop="code">
							<el-input placeholder="请输入验证码" maxlength="4" v-model="tellForm.code">
								<template slot="append">
									<img :src="captcha.img" mode class="captcha" @click="getcaptcha" />
								</template>
							</el-input>
						</el-form-item>
						<el-form-item label="动态码" prop="tellDynacode">
							<el-input placeholder="请输入动态码" v-model="tellForm.tellDynacode">
								<template slot="append">
									<el-button type="primary" @click="gettellCode">{{ tellForm.tellCodeText }}</el-button>
								</template>
							</el-input>
						</el-form-item>
					</el-form>
				</div>
				<div class="btn">
					<el-button type="primary" @click="bindtell">保存</el-button>
					<el-button @click="type = 'all'">取消</el-button>
				</div>
			</div>
			<div class="edit-pay" v-if="type == 'payPassWord'">
				<div class="title">绑定支付密码</div>
				<div class="container">
					<div class="name" v-if="step != 0">请输入6位支付密码，建议不要使用重复或连续数字</div>
					<div class="name" v-else-if="isSend">验证码已发送至{{ tellForm.currTell | mobile }}</div>
					<div class="password-wrap">
						<el-input :maxlength="step == 0 ? 4 : 6" @change="input" ref="input" :auto-focus="true" v-model="payInput" :placeholder="palceText" v-if="step == 0"></el-input>
						<el-input
							:maxlength="step == 0 ? 4 : 6"
							@change="input"
							ref="input"
							:auto-focus="true"
							v-model="payInput"
							type="password"
							:placeholder="palceText"
							v-else
						></el-input>
						<div v-show="step == 0" class="dynacode" @click="sendMobileCode">{{ payCodeText }}</div>
					</div>
				</div>
				<div class="btn">
					<el-button type="primary" @click="bindPayPwd" :disabled="isClick">保存</el-button>
					<el-button @click="type = 'all'">取消</el-button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import security from './security';
export default {
	name: 'security',
	mixins: [security]
};
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

.security {
	background: #ffffff;
	.el-form {
		margin: 0 30px;
		.captcha {
			vertical-align: top;
			max-width: inherit;
			width: 74px;
			max-height: 38px;
			line-height: 38px;
			cursor: pointer;
		}
	}
	.item {
		border-bottom: 1px solid #f1f1ff;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 20px;
		.item-content {
			display: flex;
			align-items: center;
			i {
				font-size: 40px;
			}
			.name-wrap {
				margin-left: 20px;
			}
		}
	}
	.edit {
		padding: 20px;
		.title {
			padding-bottom: 5px;
			font-size: $ns-font-size-base;
			border-bottom: 1px solid #f1f1f1;
		}
		.pass-form {
			margin-top: 20px;
			display: flex;
			justify-content: center;
			.el-form {
				width: 500px;
			}
		}
		.btn {
			display: flex;
			justify-content: center;
		}
	}
	.tell-pass {
		margin-top: 20px;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		.el-form {
			width: 500px;
		}
		.tell-code {
			margin-left: 66px;
			margin-bottom: 20px;
		}
	}
	.edit-pay {
		padding: 20px;
		text-align: center;
		.title {
			font-size: $ns-font-size-base;
			border-bottom: 1px solid #f1f1f1;
			text-align: left;
			padding-bottom: 5px;
		}
		.dynacode {
			cursor: pointer;
			text-align: right;
			margin-right: 329px;
			color: $base-color;
			margin-bottom: 20px;
		}
		.el-input {
			width: 300px;
			margin-top: 20px;
		}
		.btn {
			margin-top: 20px;
		}
		.name {
			margin-top: 10px;
		}
	}
}
</style>
