<template>
	<el-card shadow="hover" :body-style="cardBody">
		<div class="shop" @click="handleToshop">
			<el-image :src="$img(shop.avatar || defaultShopImage)" fit="fill" @error="shop.avatar=defaultShopImage"></el-image>

			<div class="detail">
				<div class="site_name">{{ shop.site_name }}</div>
				<div class="addr">
					所在地 :
					<span class="addr_detail">{{ shop.address }}</span>
				</div>
				<div class="num">
					<span>
						销量:
						<span>{{ shop.shop_sales }}</span>
					</span>
					<span>
						关注会员:
						<span>{{ shop.sub_num }}</span>
					</span>
				</div>
			</div>

			<!-- <div class="btn"><el-button plain icon="el-icon-star-off" size="small" @click.stop="followShop(shop)">关注店铺</el-button></div> -->
		</div>
	</el-card>
</template>

<script>
import { mapGetters } from 'vuex';
import { addShopSubscribe } from '@/api/shop';

export default {
	props: {
		shop: {
			type: Object,
			required: true
		}
	},

	data() {
		return {
			cardBody: {
				padding: '5px'
			}
		};
	},

	created() {
		this.$store.dispatch('site/defaultFiles');
	},

	computed: {
		...mapGetters(['defaultShopImage'])
	},

	methods: {
		handleToshop() {
			this.$router.pushToTab(`/shop-${this.shop.site_id}`);
		},
		// followShop(shop) {
		// 	addShopSubscribe({
		// 		site_id: this.shop.site_id
		// 	})
		// 		.then(res => {
		// 			this.$messages.success('关注成功');
		// 		})
		// 		.catch(err => err);
		// }
	}
};
</script>

<style lang="scss" scoped>
.shop {
	display: flex;
	flex-direction: row;
	align-items: flex-start;

	.el-image {
		width: 130px;
		height: 130px;
		margin: 5px;

		flex-grow: 0;
	}

	.detail {
		height: 100%;
		margin: 5px 10px;

		.site_name {
			font-size: 20px;
			color: black;
			margin-top: 10px;
		}

		.addr {
			font-size: 16px;
			color: rgb(56, 52, 52);
			height: 59px;

			.addr_detail {
				font-size: 14px;
			}
		}

		.num {
			span {
				margin: auto 10px auto 0;
			}
		}
	}

	.btn {
		margin: 5px 10px;
	}
}
</style>
