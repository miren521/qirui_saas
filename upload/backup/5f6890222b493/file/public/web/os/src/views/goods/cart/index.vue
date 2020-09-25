<template>
    <div class="cart" v-loading="loading">
        <template v-if="cartList.length || invalidGoods.length">
            <nav>
                <li><el-checkbox v-model="checkAll" @change="allElection"></el-checkbox></li>
                <li>商品信息</li>
                <li>价格</li>
                <li>数量</li>
                <li>小计</li>
                <li>操作</li>
            </nav>
            <div class="list" v-for="(siteItem, siteIndex) in cartList" :key="siteIndex">
                <div class="item">
                    <div class="head">
                        <el-checkbox v-model="siteItem.checked" @change="siteAllElection(siteIndex)"></el-checkbox>
                        <router-link to="/shop" target="_blank">{{ siteItem.siteName }}</router-link>
                        <el-tag size="small" v-if="siteItem.cartList[0].is_own == 1">自营</el-tag>
                    </div>
                    <ul v-for="(item, cartIndex) in siteItem.cartList" :key="cartIndex">
                        <li><el-checkbox v-model="item.checked" @change="singleElection(siteIndex, cartIndex)"></el-checkbox></li>
                        <li class="goods-info-wrap" @click="$router.pushToTab({ path: '/sku-' + item.sku_id })">
                            <div class="img-wrap"><img class="img-thumbnail" :src="$img(item.sku_image, { size: 'mid' })" @error="imageError(siteIndex, cartIndex)" /></div>
                            <div class="info-wrap">
                                <h5>{{ item.sku_name }}</h5>
                                <template v-if="item.sku_spec_format">
                                    <span v-for="(x, i) in item.sku_spec_format" :key="i"> {{ x.spec_name }}：{{ x.spec_value_name }} {{ i < item.sku_spec_format.length - 1 ? "；" : "" }} </span>
                                </template>
                            </div>
                        </li>
                        <li>
                            <span>￥{{ item.discount_price }}</span>
                        </li>
                        <li>
                            <el-input-number v-model="item.num" :step="modifyNum" size="mini" :min="1" :max="item.stock" @change="cartNumChange($event, { siteIndex, cartIndex })"></el-input-number>
                        </li>
                        <li>
                            <strong class="subtotal ns-text-color">￥{{ item.discount_price * item.num }}</strong>
                        </li>
                        <li><el-button type="text" @click="deleteCart(siteIndex, cartIndex)">删除</el-button></li>
                    </ul>
                </div>
            </div>

            <div class="lose-list" v-if="invalidGoods.length">
                <div class="head">
                    失效商品
                    <span class="ns-text-color">{{ invalidGoods.length }}</span>
                    件
                </div>
                <ul v-for="(goodsItem, goodsIndex) in invalidGoods" :key="goodsIndex">
                    <li><el-tag size="small" type="info">失效</el-tag></li>
                    <li class="goods-info-wrap">
                        <div class="img-wrap"><img class="img-thumbnail" :src="$img(goodsItem.sku_image, { size: 'mid' })" @error="imageErrorInvalid(goodsIndex)" /></div>
                        <div class="info-wrap">
                            <h5>{{ goodsItem.sku_name }}</h5>
                            <template v-if="goodsItem.sku_spec_format">
                                <span v-for="(x, i) in goodsItem.sku_spec_format" :key="i"> {{ x.spec_name }}：{{ x.spec_value_name }} {{ i < goodsItem.sku_spec_format.length - 1 ? "；" : "" }} </span>
                            </template>
                        </div>
                    </li>
                    <li>
                        <span>￥{{ goodsItem.discount_price }}</span>
                    </li>
                    <li>{{ goodsItem.num }}</li>
                    <li>
                        <strong class="subtotal">￥{{ goodsItem.discount_price * goodsItem.num }}</strong>
                    </li>
                </ul>
            </div>

            <footer>
                <el-checkbox v-model="checkAll" @change="allElection">全选</el-checkbox>
                <ul class="operation">
                    <li><el-button type="text" @click="deleteCartSelected">删除</el-button></li>
                    <li><el-button type="text" @click="clearInvalidGoods">清除失效宝贝</el-button></li>
                </ul>
                <div class="sum-wrap">
                    <div class="selected-sum">
                        <span>已选商品</span>
                        <em class="total-count">{{ totalCount }}</em>
                        <span>件</span>
                    </div>
                    <div class="price-wrap">
                        <span>合计（不含运费）：</span>
                        <strong class="ns-text-color">￥{{ totalPrice }}</strong>
                    </div>

                    <el-button type="primary" v-if="totalCount != 0" @click="settlement">结算</el-button>
                    <el-button type="info" v-else disabled @click="settlement">结算</el-button>
                </div>
            </footer>
        </template>
        <div class="empty-wrap" v-else-if="!loading && (!cartList.length || !invalidGoods.length)"><router-link to="/">您的购物车是空的，赶快去逛逛，挑选商品吧！</router-link></div>
    </div>
</template>

<script>
    import cart from "./cart"
    export default {
        name: "cart",
        mixins: [cart]
    }
</script>
<style lang="scss" scoped>
    @import "./cart.scss";
</style>
