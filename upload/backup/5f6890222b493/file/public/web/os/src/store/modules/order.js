const state = {
    // 普通待付款订单数据
    orderCreateGoodsData: "",

    //团购待付款订单数据
    groupbuyOrderCreateData: "",

    //秒杀待付款订单数据
    seckillOrderCreateData: "",

    //组合套餐待付款订单数据
    comboOrderCreateData: ""
}

const mutations = {
    SET_ORDER_CREATE_DATA: (state, orderCreateData) => {
        state.orderCreateGoodsData = orderCreateData
    },
    SET_GROUPBUY_ORDER_CREATE_DATA: (state, groupbuyOrderCreateData) => {
        state.groupbuyOrderCreateData = groupbuyOrderCreateData
    },
    SET_SECKILL_ORDER_CREATE_DATA: (state, seckillOrderCreateData) => {
        state.seckillOrderCreateData = seckillOrderCreateData
    },
    SET_COMBO_ORDER_CREATE_DATA: (state, comboOrderCreateData) => {
        state.comboOrderCreateData = comboOrderCreateData
    }
}

const actions = {
    setOrderCreateData({ commit, state }, data) {
        commit("SET_ORDER_CREATE_DATA", data)
    },
    removeOrderCreateData({ commit }) {
        commit("SET_ORDER_CREATE_DATA", "")
    },
    setGroupbuyOrderCreateData({ commit, state }, data) {
        commit("SET_GROUPBUY_ORDER_CREATE_DATA", data)
    },
    removeGroupbuyOrderCreateData({ commit }) {
        commit("SET_GROUPBUY_ORDER_CREATE_DATA", "")
    },

    setSeckillOrderCreateData({ commit, state }, data) {
        commit("SET_SECKILL_ORDER_CREATE_DATA", data)
    },
    removeSeckillOrderCreateData({ commit }) {
        commit("SET_SECKILL_ORDER_CREATE_DATA", "")
    },
    setComboOrderCreateData({ commit, state }, data) {
        commit("SET_COMBO_ORDER_CREATE_DATA", data)
    },
    removeComboOrderCreateData({ commit }) {
        commit("SET_COMBO_ORDER_CREATE_DATA", "")
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}
