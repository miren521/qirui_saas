import { apiOrderPay, apiOrderClose, apiOrderTakedelivery } from "@/api/order/order"
export default {
    methods: {
        /**
         * 订单支付
         * @param {Object} out_trade_no
         */
        orderPay(orderData) {
            if (orderData.adjust_money == 0) {
                apiOrderPay({
                    order_ids: orderData.order_id
                }).then(res => {
                    if (res.code >= 0) {
                        this.$router.push({
                            path: "/pay",
                            query: {
                                code: res.data
                            }
                        })
                    } else {
                        this.$message({
                            message: res.message,
                            type: "warning"
                        })
                    }
                })
            } else {
                this.$confirm("商家已将支付金额调整为" + orderData.pay_money + "元，是否继续支付？", "提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning"
                }).then(() => {
                    apiOrderPay({
                        order_ids: orderData.order_id
                    }).then(res => {
                        if (res.code >= 0) {
                            this.$router.push({
                                path: "/pay",
                                query: {
                                    code: res.data
                                }
                            })
                        } else {
                            this.$message({
                                message: res.message,
                                type: "warning"
                            })
                        }
                    })
                })
            }
        },
        /**
         * 关闭订单
         * @param {Object} order_id
         */
        orderClose(order_id, callback) {
            this.$confirm("您确定要关闭该订单吗？", "提示", {
                confirmButtonText: "确定",
                cancelButtonText: "取消",
                type: "warning"
            }).then(() => {
                apiOrderClose({
                    order_id
                }).then(res => {
                    this.$message({
                        message: "订单关闭成功",
                        type: "success"
                    })
                    typeof callback == "function" && callback()
                })
            })
        },
        /**
         * 订单收货
         * @param {Object} order_id
         */
        orderDelivery(order_id, callback) {
            this.$confirm("您确定已经收到货物了吗？", "提示", {
                confirmButtonText: "确定",
                cancelButtonText: "取消",
                type: "warning"
            }).then(() => {
                apiOrderTakedelivery({
                    order_id
                }).then(res => {
                    this.$message({
                        message: "订单收货成功",
                        type: "success"
                    })
                    typeof callback == "function" && callback()
                })
            })
        }
    }
}
