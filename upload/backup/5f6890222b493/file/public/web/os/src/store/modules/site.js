import { wapQrcode, copyRight, websiteInfo, siteDefaultFiles } from "@/api/website"
import { addonisexit } from "@/api/addon"

const state = {
    // 网站商城二维码
    siteQrCode: "",
    copyRight: "",
    siteInfo: "",
    defaultFiles: "",
    addons: ""
}

const mutations = {
    SET_SITE_QRCODE: (state, qrcode) => {
        state.siteQrCode = qrcode
    },
    SET_COPY_RIGHT: (state, copyRight) => {
        state.copyRight = copyRight
    },
    SET_SITE_INFO: (state, siteInfo) => {
        state.siteInfo = siteInfo
    },
    SET_SITE_DEFAULT_FILES: (state, defaultFiles) => {
        state.defaultFiles = defaultFiles
    },
    SET_ADDONS: (state, addons) => {
        state.addons = addons
    }
}

const actions = {
    qrCodes({ commit, state }) {
        // if (!state.siteQrCode)
            return new Promise((resolve, reject) => {
                return wapQrcode({})
                    .then(res => {
                        const { code, message, data } = res

                        if (code == 0) {
                            commit("SET_SITE_QRCODE", data)
                            resolve(res)
                        }

                        reject({})
                    })
                    .catch(_err => {
                        reject(_err)
                    })
            })
    },
    copyRight({ commit, state }) {
        // if (!state.copyRight) {
            return new Promise((resolve, reject) => {
                return copyRight({})
                    .then(res => {
                        const { code, message, data } = res

                        if (code == 0) {
                            commit("SET_COPY_RIGHT", data)
                            resolve(res)
                        }

                        reject({})
                    })
                    .catch(_err => {
                        reject(_err)
                    })
            })
        // }
    },
    siteInfo({ commit, state }) {
        return new Promise((resolve, reject) => {
            return websiteInfo({})
                .then(res => {
                    const { code, message, data } = res

                    if (code == 0) {
                        commit("SET_SITE_INFO", data)
                        resolve(res)
                    }

                    reject({})
                })
                .catch(_err => {
                    reject(_err)
                })
        })
    },
    defaultFiles({ commit, state }) {
        // if (!state.defaultFiles) {
            return new Promise((resolve, reject) => {
                return siteDefaultFiles({})
                    .then(res => {
                        const { code, message, data } = res

                        if (code == 0) {
                            commit("SET_SITE_DEFAULT_FILES", data)
                            resolve(res)
                        }

                        reject({})
                    })
                    .catch(_err => {
                        reject(_err)
                    })
            })
        // }
    },
    addons({ commit, state }) {
        // if (!state.addons) {
            return new Promise((resolve, reject) => {
                return addonisexit({})
                    .then(res => {
                        const { code, message, data } = res

                        if (code == 0) {
                            commit("SET_ADDONS", data)
                            resolve(res)
                        }

                        reject({})
                    })
                    .catch(_err => {
                        reject(_err)
                    })
            })
        // }
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}
