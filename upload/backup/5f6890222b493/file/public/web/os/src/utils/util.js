import Config from "./config.js"

export default {
    /**
     * 图片路径转换
     * @param {String} img_path 图片地址
     * @param {Object} params 参数，针对商品、相册里面的图片区分大中小，size: big、mid、small
     */
    img(img_path, params) {
        var path = ""
        if (img_path != undefined && img_path != "") {
            if (params && img_path != vue.$store.getters.defaultGoodsImage) {
                // 过滤默认图
                let arr = img_path.split(".")
                let suffix = arr[arr.length - 1]
                arr.pop()
                arr[arr.length - 1] = arr[arr.length - 1] + "_" + params.size
                arr.push(suffix)
                img_path = arr.join(".")
            }
            if (img_path.indexOf("http://") == -1 && img_path.indexOf("https://") == -1) {
                path = Config.imgDomain + "/" + img_path
            } else {
                path = img_path
            }
        }
        return path
    },
    /**
     * 时间戳转日期格式
     * @param {Object} timeStamp
     */
    timeStampTurnTime(timeStamp) {
        if (timeStamp != undefined && timeStamp != "" && timeStamp > 0) {
            var date = new Date()
            date.setTime(timeStamp * 1000)
            var y = date.getFullYear()
            var m = date.getMonth() + 1
            m = m < 10 ? "0" + m : m
            var d = date.getDate()
            d = d < 10 ? "0" + d : d
            var h = date.getHours()
            h = h < 10 ? "0" + h : h
            var minute = date.getMinutes()
            var second = date.getSeconds()
            minute = minute < 10 ? "0" + minute : minute
            second = second < 10 ? "0" + second : second
            return y + "-" + m + "-" + d + " " + h + ":" + minute + ":" + second
        } else {
            return ""
        }
    },
    /**
     * 倒计时
     * @param {Object} seconds 秒
     */
    countDown(seconds) {
        let [day, hour, minute, second] = [0, 0, 0, 0]
        if (seconds > 0) {
            day = Math.floor(seconds / (60 * 60 * 24))
            hour = Math.floor(seconds / (60 * 60)) - day * 24
            minute = Math.floor(seconds / 60) - day * 24 * 60 - hour * 60
            second = Math.floor(seconds) - day * 24 * 60 * 60 - hour * 60 * 60 - minute * 60
        }
        if (day < 10) {
            // day = '0' + day
        }
        if (hour < 10) {
            // hour = '0' + hour
        }
        if (minute < 10) {
            // minute = '0' + minute
        }
        if (second < 10) {
            // second = '0' + second
        }
        return {
            d: day,
            h: hour,
            i: minute,
            s: second
        }
    },
    /**
     * 数值去重
     * @param {Array} arr 数组
     * @param {string} field 字段
     */
    unique(arr, field) {
        const res = new Map()
        return arr.filter(a => !res.has(a[field]) && res.set(a[field], 1))
    },
    /**
     * 判断值是否在数组中
     * @param {Object} elem
     * @param {Object} arr
     * @param {Object} i
     */
    inArray: function(elem, arr) {
        return arr == null ? -1 : arr.indexOf(elem)
    },
    /**
     * 获取某天日期
     * @param {Object} day
     */
    getDay: function(day) {
        var today = new Date()
        var targetday_milliseconds = today.getTime() + 1000 * 60 * 60 * 24 * day
        today.setTime(targetday_milliseconds)

        const doHandleMonth = function(month) {
            var m = month
            if (month.toString().length == 1) {
                m = "0" + month
            }
            return m
        }

        var tYear = today.getFullYear()
        var tMonth = today.getMonth()
        var tDate = today.getDate()
        var tWeek = today.getDay()
        var time = parseInt(today.getTime() / 1000)
        tMonth = doHandleMonth(tMonth + 1)
        tDate = doHandleMonth(tDate)

        const week = ["周日", "周一", "周二", "周三", "周四", "周五", "周六"]
        return {
            t: time,
            y: tYear,
            m: tMonth,
            d: tDate,
            w: week[tWeek]
        }
    },
    /**
     * 复制
     * @param {Object} message
     * @param {Object} callback
     */
    copy(value, callback) {
        var oInput = document.createElement("input") //创建一个隐藏input（重要！）
        oInput.value = value //赋值
        document.body.appendChild(oInput)
        oInput.select() // 选择对象
        document.execCommand("Copy") // 执行浏览器复制命令
        oInput.className = "oInput"
        oInput.style.display = "none"
        vue.$message({
            message: "复制成功",
            type: "success"
        })
        typeof callback == "function" && callback()
    },
    /**
     * 深度拷贝对象
     * @param {Object} obj
     */
    deepClone(obj) {
        const isObject = function(obj) {
            return typeof obj == "object"
        }

        if (!isObject(obj)) {
            throw new Error("obj 不是一个对象！")
        }
        //判断传进来的是对象还是数组
        let isArray = Array.isArray(obj)
        let cloneObj = isArray ? [] : {}
        //通过for...in来拷贝
        for (let key in obj) {
            cloneObj[key] = isObject(obj[key]) ? this.deepClone(obj[key]) : obj[key]
        }
        return cloneObj
    }
}
