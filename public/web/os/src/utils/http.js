import Config from "./config.js"
import JSEncrypt from "jsencrypt"
import axios from "axios"
import { getToken } from "./auth"

//路由判断 判断时http还是https
var pathTitle = location.protocol;
if(pathTitle == "https:" && Config.baseUrl.indexOf(pathTitle) == -1 || pathTitle == "http:" && Config.baseUrl.indexOf(pathTitle) == -1){
	var value = pathTitle == "https:" ? "http:" : "https:";
	Config.baseUrl = Config.baseUrl.split(value).join(pathTitle);
	Config.imgDomain = Config.imgDomain.split(value).join(pathTitle);
}
axios.defaults.baseURL = Config.baseUrl
axios.defaults.headers = {
    "X-Requested-With": "XMLHttpRequest",
    "content-type": "application/json"
}
axios.defaults.responseType = "json"
axios.defaults.timeout = 60 * 1000
// axios.defaults.timeout = 100;// 测试请求超时代码，勿删

/**
 * http单个请求
 * @param {object} params 请求参数
 * @param {integer} successCode 接口正常返回结果标识
 *
 * @returns Promise
 */
export default function request(params, successCode = 0, method = "POST") {

    var url = params.url // 请求路径
    var data = {
        app_type: "pc",
        app_type_name: "电脑端"
    }

    var token = getToken()
    if (token) data.token = token

    // 参数
    if (params.data != undefined) Object.assign(data, params.data)

    // 请求参数加密
    if (parseInt(Config.apiSecurity)) {
        let jsencrypt = new JSEncrypt()
        jsencrypt.setPublicKey(Config.publicKey)
        let encrypt = encodeURIComponent(jsencrypt.encryptLong(JSON.stringify(data)))
        data = {
            encrypt
        }
    }

    //异步
    return axios({ url: url, method: method, data })
        .then(res => {
            const { code } = res.data || {}
            if (code == successCode) return res.data
            else return Promise.reject(res.data)
        })
        .catch(error => {
            const { data } = error
            if (data === "AuthError") {
                vue.$store.dispatch("member/remove_token")
                if (params.forceLogin) {
                    vue.$router.push(`/login?redirect=${encodeURIComponent(vue.$router.history.current.fullPath)}`)
                }
            }

            return Promise.reject(error)
        })
}

/**
 * 并发请求
 * @param {array} params 并发请求参数数组，传入数组中对象的顺序要匹配 data 中 url
 * @var 该方法为并发请求，数据会在全部请求完之后才返回
 * 该方法不建议使用。
 */
export function conRequest(params) {
    if (Object.prototype.toString.call(params) != "[object Array]") {
        return Promise.reject({ code: -1, msg: "参数必须为数组" })
    }

    //同步并发
    var quest = []
    for (var i = 0; i < url.length; i++) {
        quest.push(
            axios({
                url: params[i].url,
                method: method,
                params: params[i].data
            })
        )
    }

    axios
        .all(quest)
        .then(
            axios.spread(() => {
                // 请求全部完成后执行
                var res = []
                for (let i = 0; i < arguments.length; i++) {
                    res.push(arguments[i].data)
                }

                return res
            })
        )
        .catch(error => {
            // '请求超时，请检查网络'
            vue.$message({
                message: error,
                type: "warning"
            })

            return Promise.reject(error)
        })
}
