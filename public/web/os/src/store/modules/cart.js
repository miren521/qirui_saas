import {
	addToCart,
	deleteCart,
	getCartCount,
	editCartNum
} from '@/api/goods/cart'

const state = {
	cartCount: 0
}

const mutations = {
	SET_CART_COUNT: (state, count) => {
		state.cartCount = count
	}
}

const actions = {
	add_to_cart({
		commit
	}, item) {
		return new Promise((resolve, reject) => {
			return addToCart({
					site_id: item.site_id,
					num: item.num || 1,
					sku_id: item.sku_id
				}).then(res => {
					getCartCount({}).then(res => {
						commit('SET_CART_COUNT', res.data)
					});
					resolve(res)
				})
				.catch(err => {
					reject(err)
				})
		})
	},
	delete_cart({
		commit
	}, item) {
		return new Promise((resolve, reject) => {
			return deleteCart({
				cart_id: item.cart_id
			}).then(res => {
				getCartCount({}).then(res => {
					commit('SET_CART_COUNT', res.data)
				});
				resolve(res)
			}).catch(err => {
				reject(err)
			})
		})
	},
	cart_count({
		commit
	}, item) {
		return new Promise((resolve, reject) => {
			return getCartCount({}).then(res => {
				commit('SET_CART_COUNT', res.data)
				resolve(res)
			}).catch(err => {
				reject(err)
			})
		})
	},
	edit_cart_num({
		commit
	}, item) {
		return new Promise((resolve, reject) => {
			return editCartNum({
				num: item.num,
				cart_id: item.cart_id
			}).then(res => {
				getCartCount({}).then(res => {
					commit('SET_CART_COUNT', res.data)
				});
				resolve(res)
			}).catch(err => {
				reject(err)
			})
		})
	},

}

export default {
	namespaced: true,
	state,
	mutations,
	actions
}
