<template>
	<div>
		<!-- 首页固定区 -->
		<div class="index-wrap" :style="{ background: backgroundColor }">
			<div class="index">
				<div class="banner">
					<el-carousel height="430px" arrow="hover" v-loading="loadingAd" @change="handleChange">
						<el-carousel-item v-for="item in adList" :key="item.adv_id">
							<el-image :src="$img(item.adv_image)" fit="cover" @click="$router.pushToTab(item.adv_url.url)" />
						</el-carousel-item>
					</el-carousel>
				</div>
				<div class="news">
					<div class="login-wrap">
						<div class="avtar">
							<router-link to="/member/info">
								<img v-if="member.headimg" :src="$img(member.headimg)" @error="member.headimg = defaultHeadImage" />
								<img v-else :src="$img(defaultHeadImage)" />
							</router-link>
						</div>
						<div class="btn" v-if="!member">
							<router-link to="/login" class="login">登录</router-link>
							<router-link to="/register" class="register">注册</router-link>
						</div>
						<div class="memeber-name" v-else>{{ member.nickname }}</div>
					</div>
					<!-- 商城快讯 -->
					<div class="notice-wrap">
						<div class="notice">
							<div class="notice-name">商城资讯</div>
							<router-link to="/cms/notice" class="notice-more">
								更多
								<i class="iconfont iconarrow-right"></i>
							</router-link>
						</div>
						<div v-for="item in noticeList" :key="item.id" class="item">
							<router-link :to="'/cms/notice-' + item.id" target="_blank" tag="a">
								<div class="notice-title">{{ item.title }}</div>
							</router-link>
						</div>
					</div>
					<div class="xian"></div>

					<!-- 商城服务 -->
					<div class="server-wrap">
						<div class="server-title">商城服务</div>
						<div class="item-wrap">
							<div class="item">
								<a :href="shopApplyUrl" target="_blank">
									<i class="iconfont iconshang" />
									<span>招商入驻</span>
								</a>
							</div>
							<div class="item">
								<a :href="shopCenterUrl" target="_blank">
									<i class="iconfont iconshangjiazhongxin" />
									<span>商家中心</span>
								</a>
							</div>
							<div class="item" v-if="addonIsExit.store == 1">
								<a :href="storeUrl" target="_blank">
									<i class="iconfont iconshangjiazhongxin-" />
									<span>门店管理</span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="content">
			<!-- 领券中心 -->
			<div class="content-div" v-if="addonIsExit.coupon == 1 && couponList.length > 0">
				<div class="coupon">
					<div class="coupon-title">
						<p class="coupon-font">领券中心</p>
						<p class="coupon-en">coupon</p>
						<p class="coupon-more" @click="$router.push('/coupon')">
							<span>更多</span>
							<i class="iconfont iconarrow-right"></i>
						</p>
					</div>
					<ul class="coupon-list">
						<li v-for="(item, index) in couponList" :key="index">
							<p class="coupon-price ns-text-color">
								￥
								<span>{{ item.money }}</span>
							</p>
							<p class="coupon-term">满{{ item.at_least }}可用</p>
							<p class="coupon-receive ns-text-color" @click="couponTap(item, index)">
								<span v-if="item.useState == 0">立即领取</span>
								<span v-else>去使用</span>
								<i class="iconfont iconarrow-right"></i>
							</p>
						</li>
					</ul>
				</div>
			</div>

			<!-- 广告 -->
			<div class="content-div" v-if="adLeftList.length > 0 || adRightList.length > 0">
				<div class="ad-wrap">
					<div class="ad-big">
						<div class="ad-big-img" v-for="(item, index) in adLeftList" :key="index">
							<el-image :src="$img(item.adv_image)" fit="cover" @error="adLeftImageError(index)" @click="$router.pushToTab(item.adv_url.url)"></el-image>
						</div>
					</div>
					<div class="ad-small">
						<div class="ad-small-img" v-for="(item, index) in adRightList" :key="index">
							<el-image :src="$img(item.adv_image)" fit="cover" @error="adRightImageError(index)" @click="$router.pushToTab(item.adv_url.url)"></el-image>
						</div>
					</div>
				</div>
			</div>

			<!-- 限时秒杀 -->
			<div class="content-div" v-if="addonIsExit.seckill == 1 && listData.length > 0">
				<div class="seckill-wrap">
					<div class="seckill-time">
						<div class="seckill-time-left">
							<i class="iconfont iconmiaosha1 ns-text-color"></i>
							<span class="seckill-time-title ns-text-color">限时秒杀</span>
							<span>{{ seckillText }}</span>
							<count-down
								class="count-down"
								v-on:start_callback="countDownS_cb()"
								v-on:end_callback="countDownE_cb()"
								:currentTime="seckillTimeMachine.currentTime"
								:startTime="seckillTimeMachine.startTime"
								:endTime="seckillTimeMachine.endTime"
								:dayTxt="'：'"
								:hourTxt="'：'"
								:minutesTxt="'：'"
								:secondsTxt="''"
							></count-down>
						</div>
						<div class="seckill-time-right" @click="$router.push('/promotion/seckill')">
							<span>更多商品</span>
							<i class="iconfont iconarrow-right"></i>
						</div>
					</div>
					<div class="seckill-content">
						<vue-seamless-scroll :data="listData" :class-option="optionLeft" class="seamless-warp2">
							<ul class="item" :style="{ width: 250 * listData.length + 'px' }">
								<li v-for="(item, index) in listData" :key="index">
									<div class="seckill-goods" @click="$router.pushToTab('/promotion/seckill-' + item.id)">
										<div class="seckill-goods-img"><img :src="$img(item.sku_image, { size: 'mid' })" @error="imageError(index)" /></div>
										<p>{{ item.sku_name }}</p>
										<div class="seckill-price-wrap">
											<p class="ns-text-color">
												￥
												<span>{{ item.seckill_price }}</span>
											</p>
											<p class="primary-price">￥{{ item.price }}</p>
										</div>
									</div>
								</li>
							</ul>
						</vue-seamless-scroll>
					</div>
				</div>
			</div>

			<!-- 楼层区 -->
			<div class="content-div">
				<div class="floor">
					<div v-for="(item, index) in floorList" :key="index" class="floor_item">
						<floor-style-1 v-if="item.block_name == 'floor-style-1'" :data="item" />
						<floor-style-2 v-if="item.block_name == 'floor-style-2'" :data="item" />
						<floor-style-3 v-if="item.block_name == 'floor-style-3'" :data="item" />
					</div>
				</div>
			</div>
			
			<!-- 浮层区 -->
			<div class="floatLayer-wrap" v-if="floatLayer.is_show">
				<div class="floatLayer">
					<img :src="$img(floatLayer.img_url)" @click="$router.pushToTab(floatLayer.link.url)" />
					<i class="el-icon-circle-close" @click="closeFloat"></i>
				</div>
			</div>

			<!-- 悬浮搜索 -->
			<div class="fixed-box" :style="{ display: isShow ? 'block' : 'none' }">
				<div class="header-search">
					<el-row>
						<el-col :span="6">
							<router-link to="/" class="logo-wrap"><img :src="$img(siteInfo.logo)" /></router-link>
						</el-col>
						<el-col :span="13">
							<div class="in-sousuo">
								<div class="sousuo-box">
									<el-dropdown @command="handleCommand" trigger="click">
										<span class="el-dropdown-link">
											{{ searchTypeText }}
											<i class="el-icon-arrow-down"></i>
										</span>
										<el-dropdown-menu slot="dropdown">
											<el-dropdown-item command="goods">商品</el-dropdown-item>
											<el-dropdown-item command="shop">店铺</el-dropdown-item>
										</el-dropdown-menu>
									</el-dropdown>
									<input type="text" :placeholder="defaultSearchWords" v-model="keyword" @keyup.enter="search" maxlength="50" />
									<el-button type="primary" size="small" @click="search">搜索</el-button>
								</div>
							</div>
						</el-col>
						<el-col :span="5">
							<div class="cart-wrap">
								<router-link class="cart" to="/cart">
									<span>我的购物车</span>
									<el-badge v-if="cartCount" :value="cartCount" type="primary"><i class="iconfont icongouwuche"></i></el-badge>
									<i v-else class="iconfont icongouwuche"></i>
								</router-link>
							</div>
						</el-col>
					</el-row>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import floorStyle1 from './components/floor-style-1.vue';
import floorStyle2 from './components/floor-style-2.vue';
import floorStyle3 from './components/floor-style-3.vue';
import index from './_index/index.js';

export default {
	name: 'index',
	components: { floorStyle1,floorStyle2,floorStyle3 },
	mixins: [index]
};
</script>

<style lang="scss" scoped>
@import './_index/index.scss';
</style>

<style lang="scss">
.count-down {
	span {
		display: inline-block;
		width: 22px;
		height: 22px;
		line-height: 22px;
		text-align: center;
		background: #383838;
		color: #ffffff;
		border-radius: 2px;
	}
}
</style>
