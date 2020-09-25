import { login, mobile_login } from "@/api/auth/login"
import { register } from "@/api/auth/register"
import { getToken, setToken, removeToken } from "@/utils/auth"
import { memberDetail } from "@/api/member/member"
import { memberInfo } from "@/api/member/index"

const state = {
    token: getToken(),
    autoLoginRange: 0,
    member: ""
}

const mutations = {
    SET_TOKEN: (state, token) => {
        state.token = token
    },
    SET_AUTOLOGIN_FLAG: (state, autologinRange) => {
        state.autoLogin = autologinRange
    },
    SET_MEMBER: (state, member) => {
        state.member = member
    }
}

const actions = {
    login({ commit }, userInfo) {
        const { username, password, captcha_id, captcha_code, autoLoginRange } = userInfo

        return new Promise((resolve, reject) => {
            return login({ username, password, captcha_id, captcha_code, autoLoginRange })
                .then(res => {
                    const { code, message, data } = res

                    if (code == 0) {
                        commit("SET_TOKEN", data.token)

                        if (userInfo.autoLoginRange !== undefined) {
                            commit("SET_AUTOLOGIN_FLAG", userInfo.autoLoginRange)
                        }

                        setToken(data.token, userInfo.autoLoginRange)

                        resolve(res)
                    }

                    reject()
                })
                .catch(_err => {
                    reject(_err)
                })
        })
    },

    mobile_login({ commit }, userInfo) {
        const { mobile, key, code } = userInfo

        return new Promise((resolve, reject) => {
            return mobile_login({ mobile, key, code })
                .then(res => {
                    const { code, message, data } = res

                    if (code == 0) {
                        commit("SET_TOKEN", data.token)

                        setToken(data.token, userInfo.autoLoginRange)

                        resolve(res)
                    }

                    reject()
                })
                .catch(_err => {
                    reject(_err)
                })
        })
    },

    remove_token({ commit }) {
        commit("SET_TOKEN", "")
        removeToken()
    },

    register_token({ commit }, userInfo) {
		const { username, password, captcha_id, captcha_code } = userInfo
		
		return new Promise((resolve, reject) => {
		    return register({ username, password, captcha_id, captcha_code })
		        .then(res => {
		            const { code, message, data } = res
		
		            if (code == 0) {
		                commit("SET_TOKEN", data.token)
		
		                setToken(data.token)
		
		                resolve(res)
		            }
		
		            reject()
		        })
		        .catch(_err => {
		            reject(_err)
		        })
		})
        commit("SET_TOKEN", "")
    },

    auto_login_range({ commit }, autologinRange) {
        commit("SET_AUTOLOGIN_FLAG", autologinRange)
    },

    logout({ commit }) {
        commit("SET_TOKEN", "")
        commit("SET_MEMBER", "")
        removeToken()
        // resolve()
    },

    /**
     * params refresh:1 表示强制请求会员信息
     */
    member_detail({ commit, state }, params) {
        if (!state.member || (params && params.refresh)) {
            return new Promise((resolve, reject) => {
                return memberDetail({ token: getToken() })
                    .then(res => {
                        const { data } = res
                        commit("SET_MEMBER", data)
                        return resolve(res)
                    })
                    .catch(err => {
                        return reject(err)
                    })
            })
        }
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}
