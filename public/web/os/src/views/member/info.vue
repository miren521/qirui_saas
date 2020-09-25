<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<div class="member-info" v-loading="loading">
			<el-tabs v-model="activeName" type="card" @tab-click="handleClick">
				<el-tab-pane label="基本信息" name="first">
					<el-form ref="infoRef" :model="memberInfo" :rules="infoRules" label-width="80px">
						<el-form-item label="账号" v-if="memberInfo.userName">
							<p>{{ memberInfo.userName }}</p>
						</el-form-item>
						<el-form-item label="邮箱">
							<p v-if="memberInfo.email">{{ memberInfo.email }}</p>
							<p v-else class="toBind" @click="$router.push({ path: '/member/security' })">去绑定</p>
						</el-form-item>
						<el-form-item label="手机号">
							<p v-if="memberInfo.tell">{{ memberInfo.tell }}</p>
							<p v-else class="toBind" @click="$router.push({ path: 'member/security' })">去绑定</p>
						</el-form-item>
						<el-form-item label="昵称" prop="nickName"><el-input v-model="memberInfo.nickName"></el-input></el-form-item>
					</el-form>
					<div class="btn"><el-button size="medium" type="primary" @click="saveInfo">保存</el-button></div>
				</el-tab-pane>
				<el-tab-pane label="头像照片" name="second">
					<div class="preview">
						<div class="title">头像预览</div>
						<div class="content">
							完善个人信息资料，上传头像图片有助于您结识更多的朋友。
							<br />
							头像最佳默认尺寸为120x120像素。
						</div>
					</div>
					<el-upload :action="uploadActionUrl" :show-file-list="false" :on-success="handleAvatarSuccess" class="upload">
						<div class="img-wrap"><img :src="$img(memberInfo.userHeadImg)" @error="memberInfo.userHeadImg = defaultHeadImage" /></div>
					</el-upload>
					<div class="btn"><el-button size="medium" type="primary" @click="uploadHeadImg">保存</el-button></div>
				</el-tab-pane>
			</el-tabs>
		</div>
	</div>
</template>

<script>
import { info, nickName, headImg } from '@/api/member/info';
import Config from '@/utils/config';
import { mapGetters } from 'vuex';
import { email } from '@/api/member/security';
export default {
	name: 'info',
	components: {},
	data: () => {
		return {
			memberInfo: {
				userHeadImg: '',
				userName: '', //账号
				nickName: '', //昵称
				email: '',
				tell: ''
			},
			infoRules: {
				nickName: [{ required: true, message: '请输入昵称', trigger: 'blur' }, { max: 30, message: '最大长度为30个字符', trigger: 'blur' }]
			},
			activeName: 'first',
			loading: true,
			uploadActionUrl: Config.baseUrl + '/api/upload/headimg',
			imgUrl: '',
			yes: true
		};
	},
	created() {
		this.getInfo();
	},
	mounted() {
		let self = this;
		setTimeout(function() {
			self.yes = false
		}, 300)
	},
	methods: {
		getInfo() {
			info()
				.then(res => {
					if (res.code == 0) {
						this.memberInfo.userHeadImg = res.data.headimg;
						this.memberInfo.userName = res.data.username;
						this.memberInfo.nickName = res.data.nickname;
						this.memberInfo.email = res.data.email;
						this.memberInfo.tell = res.data.mobile;
					}
					this.loading = false;
				})
				.catch(err => {
					this.loading = false;
					this.$message.error(err.message);
				});
		},
		handleClick(tab, event) {},
		saveInfo() {
			this.$refs.infoRef.validate(valid => {
				if (valid) {
					nickName({ nickname: this.memberInfo.nickName })
						.then(res => {
							if (res.code == 0) {
								this.$message({ message: '修改昵称成功', type: 'success' });
								this.getInfo;
								this.$store.dispatch('member/member_detail', { refresh: 1 });
							}
						})
						.catch(err => {
							this.$message.error(err.message);
						});
				} else {
					return false;
				}
			});
		},
		handleAvatarSuccess(res, file) {
			this.imgUrl = res.data.pic_path;
			this.memberInfo.userHeadImg = URL.createObjectURL(file.raw);
		},
		uploadHeadImg() {
			headImg({ headimg: this.imgUrl })
				.then(res => {
					if (res.code == 0) {
						this.$message({ message: '头像修改成功', type: 'success' });
						this.$store.dispatch('member/member_detail', { refresh: 1 });
					}
				})
				.catch(err => {
					this.$message.error(err.message);
				});
		}
	},
	computed: {
		...mapGetters(['defaultHeadImage'])
	}
};
</script>
<style>
.member-info .el-upload {
	display: flex;
	justify-content: center;
	margin-bottom: 20px;
}
</style>
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

.member-info {
	background: #ffffff;
	padding: 20px;
	.el-tab-pane {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-content: center;
		.preview {
			display: flex;
			justify-content: center;
			margin-bottom: 20px;
			.title {
				margin-right: 20px;
				line-height: 3;
			}
			.content {
				color: $base-color;
				line-height: 1.5;
			}
		}
		.upload {
			display: flex;
			justify-content: center;
		}
		.el-upload {
			width: 120px;
		}
		.img-wrap {
			width: 120px;
			height: 120px;
			display: block;
			line-height: 120px;
			overflow: hidden;
			border: 1px solid #f1f1f1;
			cursor: pointer;
			img {
				display: inline-block;
			}
		}
		.el-form {
			margin-top: 20px;
			width: 500px;
			margin-left: 200px;
			.toBind {
				cursor: pointer;
				&:hover {
					color: $base-color;
				}
			}
		}
		.btn {
			text-align: center;
		}
	}
}
</style>
