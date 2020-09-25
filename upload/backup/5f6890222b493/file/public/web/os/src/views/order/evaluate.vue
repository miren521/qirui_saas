<template>
	<el-form class="ns-evalute" v-loading="loading">
		<div class="ns-eva-li" v-for="(item, index) in goodsList" :key="index">
			<!-- 商品信息 -->
			<div class="ns-eva-good">
				<el-image fit="scale-down" :src="$img(item.sku_image, { size: 'mid' })" @error="imageError(index)" @click="toGoodsDetail(item.sku_id)"></el-image>
				<p class="ns-eva-good-name" :title="item.sku_name" @click="toGoodsDetail(item.sku_id)">{{ item.sku_name }}</p>
				<p>￥{{ item.price }}</p>
			</div>

			<!-- 评价表单 -->
			<div class="ns-eva-form">
				<div class="block" v-if="!isEvaluate">
					<span class="demonstration">描述相符：</span>
					<el-rate v-model="goodsEvalList[index].scores" @change="setStar(index)"></el-rate>
					<div class="level">
						<i
							class="iconfont"
							:class="
								goodsEvalList[index].explain_type == '1'
									? 'iconhaoping1 ns-text-color'
									: goodsEvalList[index].explain_type == '2'
									? 'iconzhongchaping ns-text-color'
									: goodsEvalList[index].explain_type == '3'
									? 'iconzhongchaping'
									: ''
							"
						></i>
						<span>
							{{
								goodsEvalList[index].explain_type == '1'
									? '好评'
									: goodsEvalList[index].explain_type == '2'
									? '中评'
									: goodsEvalList[index].explain_type == '3'
									? '差评'
									: ''
							}}
						</span>
					</div>
				</div>

				<div class="ns-textarea">
					<el-input v-if="!isEvaluate" type="textarea" :rows="5" placeholder="请在此处输入您的评价" v-model="goodsEvalList[index].content" maxlength="200" show-word-limit></el-input>
					<el-input v-else type="textarea" :rows="5" placeholder="请在此处输入您的追评" v-model="goodsEvalList[index].again_content" maxlength="200" show-word-limit></el-input>
				</div>

				<el-upload
					ref="upload"
					:class="{ ishide: hide[index] }"
					:action="uploadActionUrl"
					list-type="picture-card"
					:on-success="
						(file, fileList) => {
							return handleSuccess(file, fileList, index);
						}
					"
					:on-preview="handlePictureCardPreview"
					:on-remove="
						(file, fileList) => {
							return handleRemove(file, fileList, index);
						}
					"
					:on-exceed="handleExceed"
				>
					<i class="el-icon-plus"></i>
				</el-upload>
				<el-dialog :visible.sync="dialogVisible"><img width="100%" :src="dialogImageUrl" alt="" /></el-dialog>
				<span>共6张，还能上传{{ imgList[index].length ? 6 - imgList[index].length : 6 }}张</span>
			</div>
		</div>

		<div class="ns-eva-public" v-if="!isEvaluate">
			<div class="ns-eva-wrap">
				<p>{{ siteName }}</p>
				<div class="block">
					<span class="demonstration">配送服务：</span>
					<el-rate v-model="shop_deliverycredit"></el-rate>
				</div>
				<div class="block">
					<span class="demonstration">描述相符：</span>
					<el-rate v-model="shop_desccredit"></el-rate>
				</div>
				<div class="block">
					<span class="demonstration">服务态度：</span>
					<el-rate v-model="shop_servicecredit"></el-rate>
				</div>
				<el-checkbox v-model="isAnonymous">匿名</el-checkbox>
			</div>
		</div>

		<div class="save-btn-wrap"><el-button @click="save" type="primary">提交</el-button></div>
	</el-form>
</template>

<script>
import { mapGetters } from 'vuex';
import { orderInfo, save, uploadImg } from '@/api/order/order';
import Config from '@/utils/config';

export default {
	name: 'evaluate',
	components: {},
	data: () => {
		return {
			loading: true,
			value1: 5,
			memberName: '',
			memberNeadimg: '',
			orderId: null,
			orderNo: '',
			isAnonymous: 0, //是否匿名发布  1.匿名，0.公开
			goodsList: [], //订单列表
			goodsEvalList: [], //评价列表
			imgList: [],
			isEvaluate: 0, //判断是否为追评
			flag: false, //防止重复点击

			siteName: '', // 店铺名称
			shop_deliverycredit: 5, // 配送服务分值
			shop_desccredit: 5, // 描述相符分值
			shop_servicecredit: 5, // 服务态度分值

			uploadActionUrl: Config.baseUrl + '/api/upload/evaluateimg',
			dialogImageUrl: '',
			dialogVisible: false,
			hide: []
		};
	},
	created() {
		this.orderId = this.$route.query.order_id;
		this.getUserInfo();
		if (this.orderId) {
			this.getOrderInfo();
		}
	},
	computed: {
		...mapGetters(['defaultGoodsImage'])
	},
	methods: {
		handleSuccess(file, fileList, index) {
			let arr = this.imgList[index];
			arr = arr.concat(file.data.pic_path);
			this.imgList[index] = [];
			this.$set(this.imgList, index, arr);
			if (this.isEvaluate) {
				this.goodsEvalList[index].again_images = this.imgList[index].toString();
			} else {
				this.goodsEvalList[index].images = this.imgList[index].toString();
			}

			if (this.imgList[index].length >= 6) {
				this.hide[index] = true;
			}
		},
		handleRemove(file, fileList, index) {
			let i = util.inArray(file.response.data.pic_path, this.imgList[index]);
			this.imgList[index].splice(i, 1);

			if (this.isEvaluate) {
				this.goodsEvalList[index].again_images = this.imgList[index].toString();
			} else {
				this.goodsEvalList[index].images = this.imgList[index].toString();
			}

			if (this.imgList[index].length < 6) {
				this.hide[index] = false;
			}
		},
		handleExceed(file, fileList) {
			// 图片数量大于6
			this.$message.warning('上传图片最大数量为6张');
		},
		handlePictureCardPreview(file) {
			// 点开大图
			this.dialogImageUrl = file.url;
			this.dialogVisible = true;
		},
		//获取用户信息
		getUserInfo() {
			this.$store
				.dispatch('member/member_detail', {
					refresh: 1
				})
				.then(res => {
					this.memberName = res.data.nickname;
					this.memberNeadimg = res.data.headimg;
				})
				.catch(err => {
					this.$message.error(err.message);
				});
		},
		//获取订单信息
		getOrderInfo() {
			//获取订单信息
			orderInfo({
				order_id: this.orderId
			})
				.then(res => {
					if (res.code == 0) {
						this.isEvaluate = res.data.evaluate_status;
						this.orderNo = res.data.list[0].order_no;
						this.goodsList = res.data.list;
						this.siteName = res.data.list[0].site_name;

						if (this.isEvaluate) {
							for (let i = 0; i < res.data.list.length; i++) {
								let array = [];
								this.imgList.push(array);
								this.hide.push(false);
								this.goodsEvalList.push({
									order_goods_id: res.data.list[i].order_goods_id,
									goods_id: res.data.list[i].goods_id,
									sku_id: res.data.list[i].sku_id,
									again_content: '',
									again_images: '',
									site_id: res.data.list[i].site_id
								});
							}
						} else {
							for (let i = 0; i < res.data.list.length; i++) {
								let array = [];
								this.imgList.push(array);
								this.goodsEvalList.push({
									content: '', // 评价内容
									images: '', //图片数组
									scores: 5, // 评分
									explain_type: 1, // 评价类型
									order_goods_id: res.data.list[i].order_goods_id,
									goods_id: res.data.list[i].goods_id,
									sku_id: res.data.list[i].sku_id,
									sku_name: res.data.list[i].sku_name,
									sku_price: res.data.list[i].price,
									sku_image: res.data.list[i].sku_image,
									site_id: res.data.list[i].site_id
								});
							}
						}
					}

					this.loading = false;
				})
				.catch(err => {
					this.$message.error(err.message);
					this.$router.push('/member/order_list');
					this.loading = false;
				});
		},
		//监听评分变化
		setStar(index) {
			if (this.goodsEvalList[index].scores >= 4) {
				this.goodsEvalList[index].explain_type = 1;
			} else if (1 < this.goodsEvalList[index].scores && this.goodsEvalList[index].scores < 4) {
				this.goodsEvalList[index].explain_type = 2;
			} else {
				this.goodsEvalList[index].explain_type = 3;
			}
		},
		imageError(index) {
			this.goodsList[index].sku_image = this.defaultGoodsImage;
		},
		/**
		 * 提交
		 */
		save() {
			for (let i = 0; i < this.goodsEvalList.length; i++) {
				if (this.isEvaluate) {
					if (!this.goodsEvalList[i].again_content.trim().length) {
						this.$message({ message: '商品的评价不能为空哦', type: 'warning' });
						return;
					}
				} else {
					if (!this.goodsEvalList[i].content.trim().length) {
						this.$message({ message: '商品的评价不能为空哦', type: 'warning' });
						return;
					}
				}
			}

			let goodsEvaluate = JSON.stringify(this.goodsEvalList);
			let data = {
				order_id: this.orderId,
				goods_evaluate: goodsEvaluate,
				isEvaluate: this.isEvaluate
			};
			if (!this.isEvaluate) {
				data.order_no = this.orderNo;
				data.member_name = this.memberName;
				data.member_headimg = this.memberNeadimg;
				data.is_anonymous = this.isAnonymous;
				data.shop_deliverycredit = this.shop_deliverycredit;
				data.shop_desccredit = this.shop_desccredit;
				data.shop_servicecredit = this.shop_servicecredit;
			}

			if (this.flag) return;
			this.flag = true;

			save(data)
				.then(res => {
					if (res.code == 0) {
						this.$message({
							message: '评价成功',
							type: 'success',
							duration: 2000,
							onClose: () => {
								this.$router.push({ path: '/member/order_list' });
							}
						});
					} else {
						this.$message({ message: res.message, type: 'warning' });
						this.flag = false;
					}
				})
				.catch(err => {
					this.$message.error(err.message);
					this.flag = false;
				});
		},
		/**
		 * 跳转到商品详情
		 */
		toGoodsDetail(id) {
			this.$router.pushToTab('sku-' + id);
		}
	}
};
</script>
<style lang="scss" scoped>
.ns-evalute {
	margin: 20px 0;
	background: #ffffff;
	padding: 30px;
	border-radius: 5px;
	.ns-eva-li {
		display: flex;
		padding: 20px 0;
		border-bottom: 1px solid #eeeeee;

		.ns-eva-good {
			width: 30%;
			text-align: center;
			.el-image {
				width: 125px;
				height: 125px;
				cursor: pointer;
			}
			.ns-eva-good-name {
				width: 250px;
				margin: 0 auto;
				overflow : hidden;
				text-overflow: ellipsis;
				display: -webkit-box;
				-webkit-line-clamp: 2;
				-webkit-box-orient: vertical;
				cursor: pointer;
			}
		}
	}

	.ns-eva-form {
		width: 70%;
		.ns-textarea {
			position: relative;
		}
		.level {
			display: inline-block;
			margin-left: 50px;
			i {
				margin-right: 5px;
				vertical-align: top;
			}
		}
		.el-textarea {
			margin: 10px 0;
		}
	}
	.el-rate {
		display: inline-block;
		vertical-align: middle;
	}
	.ns-eva-public {
		overflow: hidden;
		.ns-eva-wrap {
			width: 70%;
			float: right;
			margin-top: 20px;
			p {
				font-size: 16px;
				font-weight: 600;
			}
		}
	}
	.save-btn-wrap {
		text-align: center;
		margin-top: 20px;
	}

	.el-dialog {
		img {
			width: auto;
			margin: 0 auto;
		}
	}
}
</style>
<style lang="scss">
.ns-evalute {
	.el-upload--picture-card,
	.el-upload-list--picture-card .el-upload-list__item {
		width: 70px;
		height: 70px;
		line-height: 80px;
		position: relative;
	}
	.el-upload-list--picture-card .el-upload-list__item-thumbnail {
		width: 100%;
		height: auto;
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
	}
	.el-upload-list__item.is-success .el-upload-list__item-status-label {
		display: none;
	}
	.ishide .el-upload--picture-card {
		display: none;
	}
	.el-dialog {
		.el-dialog__body {
			text-align: center;
		}
	}
}
</style>
