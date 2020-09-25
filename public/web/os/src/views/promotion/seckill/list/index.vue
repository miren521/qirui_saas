<template>
	<div class="ns-seckill">
		<div class="ns-seckill-time-wrap" v-if="timeList.length > 0">
			<div class="ns-seckill-time-box">
				<span class="left-btn iconfont iconarrow-left-copy" @click="changeThumbImg('prev')"></span>
				<span class="right-btn iconfont iconarrow-right" @click="changeThumbImg('next')"></span>
				<div class="ns-seckill-time-list" ref="seckillTime">
					<ul class="seckill-time-ul" :style="{ left: thumbPosition + 'px' }">
						<!-- 商品缩率图 -->
						<li class="seckill-time-li" v-for="(item, key) in timeList" :key="key" @click="handleSelected(key)">
							<div slot="label" class="tab-li" :class="{ 'selected-tab': seckillId == item.seckill_id }">
								<div>{{ item.name }}</div>
								<div>
									<p v-if="key > index">敬请期待</p>
									<p v-if="key == index && !item.isNow">即将开始</p>
									<p v-if="key == index && item.isNow">抢购中</p>
									<p v-if="key < index">已结束</p>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<el-carousel height="400px" v-loading="loadingAd">
			<el-carousel-item v-for="item in adList" :key="item.adv_id"><el-image :src="$img(item.adv_image)" fit="cover" @click="$router.pushToTab(item.adv_url.url)" /></el-carousel-item>
		</el-carousel>

		<!-- 商品列表 -->
		<div class="ns-seckill-box" ref="seckillGoods" v-if="timeList.length > 0 && goodsList.length > 0">
			<div class="ns-seckill-title">
				<div>
					<i class="iconfont iconmiaosha1"></i>
					<span>{{ seckillName }}</span>
				</div>
				<div class="ns-seckill-end" v-if="seckillIndex == index && isTrue">
					{{ seckillText }}
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
			</div>

			<div v-loading="loading">
				<div class="goods-list">
					<div class="item" v-for="(item, key) in goodsList" :key="key">
						<div class="goods">
							<div class="img" v-if="seckillIndex == index && timeList[index].isNow" @click="toGoodsDetail(item.id)">
								<el-image fit="scale-down" :src="$img(item.sku_image, { size: 'mid' })" lazy @error="imageError(index)"></el-image>
							</div>
							<div class="img" v-else><el-image fit="scale-down" :src="$img(item.sku_image, { size: 'mid' })" lazy @error="imageError(index)"></el-image></div>
							<div class="name">
								<p :title="item.sku_name">{{ item.sku_name }}</p>
							</div>

							<!-- 价格展示区 -->
							<div class="price">
								<!-- 价格 -->
								<div>
									<p>
										<span>秒杀价</span>
										<span>￥</span>
										<span class="main_price">{{ item.seckill_price }}</span>
									</p>
									<span class="primary_price">￥{{ item.price }}</span>
								</div>
							</div>

							<el-button v-if="seckillIndex == index && timeList[index].isNow" @click="toGoodsDetail(item.id)">立即抢购</el-button>
						</div>
					</div>
				</div>
			</div>
			
			<div class="pager">
				<el-pagination
					background
					:pager-count="5"
					:total="total"
					prev-text="上一页"
					next-text="下一页"
					:current-page.sync="currentPage"
					:page-size.sync="pageSize"
					@size-change="handlePageSizeChange"
					@current-change="handleCurrentPageChange"
					hide-on-single-page
				></el-pagination>
			</div>
		</div>
		
		<div v-if="timeList.length <= 0 || goodsList.length <= 0" class="empty-wrap"><div class="ns-text-align">暂无正在进行秒杀的商品，去首页看看吧</div></div>
	</div>
</template>

<script>
    import list from "./list.js"
    export default {
        name: "seckill",
        components: {},
        mixins: [list]
    }
</script>
<style lang="scss" scoped>
    @import "./list.scss";
</style>

<style lang="scss">
.seckill-time {
	.el-tabs__nav-wrap {
		height: 56px;

		.el-tabs__nav {
			height: 56px;
		}

		.el-tabs__nav-next,
		.el-tabs__nav-prev {
			line-height: 56px;
		}

		.el-tabs__item {
			width: 150px;
			height: 56px;
			padding: 0;
		}
	}
	.el-tabs__nav-wrap::after {
		height: 0;
	}
}
.ns-seckill {
	.el-carousel {
		.el-image__inner {
			width: auto;
		}
	}
	.el-carousel__arrow--right{
		right: 60px;
	}

	.count-down {
		span {
			display: inline-block;
			width: 24px;
			height: 24px;
			line-height: 24px;
			text-align: center;
			background: #383838;
			color: #ffffff;
			border-radius: 2px;
		}
	}
}
</style>