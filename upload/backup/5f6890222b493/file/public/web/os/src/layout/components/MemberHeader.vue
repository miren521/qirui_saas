<template>
    <div class="header">
        <ns-header-top />
        <ns-header-mid />
    </div>
</template>

<script>
    import NsHeaderTop from "./NsHeaderTop.vue"
    import NsHeaderMid from "./NsHeaderMid.vue"
    export default {
        components: {
            NsHeaderTop,
            NsHeaderMid
        },
        created() {
            this.$store.dispatch("cart/cart_count")
        }
    }
</script>

<style scoped lang="scss">
    .header {
        width: 100%;
        background-color: #fff;
        padding: 0;
    }
    .header-in {
        width: $width;
        height: 109px;
        margin: 20px auto 0;
        img {
            margin: 22px 120px auto 0;
            float: left;
        }
        .in-sousuo {
            width: 550px;
            float: left;
            margin: 10px 0 0 0;
            .sousuo-tag {
                display: block;
                height: 24px;
                span {
                    cursor: pointer;
                    font-size: 12px;
                    padding: 0 10px;
                    border-right: solid 1px #e1e1e1;
                    &:last-child {
                        border-right: none;
                    }
                }
                .span-font {
                    color: $base-color;
                }
            }
            .sousuo-box {
                width: 100%;
                height: 36px;
                border: 2px solid $base-color;
                box-sizing: border-box;
                input {
                    width: 400px;
                    height: 22px;
                    background: none;
                    outline: none;
                    border: none;
                    float: left;
                    margin: 4px;
                }
                .box-btn {
                    width: 80px;
                    height: 100%;
                    background-color: $base-color;
                    color: #fff;
                    float: right;
                    text-align: center;
                    line-height: 32px;
                    cursor: pointer;
                }
            }
            .sousuo-key {
                width: 100%;
                height: 20px;
                margin-top: 5px;
                font-size: 12px;
                span {
                    float: left;
                }
                ul {
                    margin: 0;
                    padding: 0;
                    float: left;
                    color: $ns-text-color-black;
                    li {
                        cursor: pointer;
                        list-style: none;
                        float: left;
                        margin-right: 10px;
                        color: $ns-text-color-black;
                    }
                }
            }
        }
        .car {
            float: left;
            width: 91px;
            height: 36px;
            margin-top: 34px;
            margin-left: 30px;
            padding: 0 28px 0 19px;
            border: 1px solid #dfdfdf;
            color: $base-color;
            font-size: 12px;
            span {
                cursor: pointer;
                line-height: 38px;
                margin-right: 10px;
            }
        }
    }
</style>
