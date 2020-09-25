import Vue from "vue"
import Vuex from "vuex"
import getters from "./getters"
import createPersistedState from "vuex-persistedstate"
import app from "./modules/app"
import member from "./modules/member"
import site from "./modules/site"
import cart from "./modules/cart"
import order from "./modules/order"

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        app,
        member,
        site,
        cart,
        order
    },

    getters,

    plugins: [
        createPersistedState({
            storage: window.localStorage,
            reducer(val) {
                const { app, site, order } = val
                return { app, site, order }
            }
        })
    ]
})

export default store
