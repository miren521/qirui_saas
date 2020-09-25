import { shopInfo, isFollow, unFollow, follow, tree } from "@/api/shop/index"
import { goodsSkuPage } from "@/api/goods/goods"
import { mapGetters } from "vuex"

export default {
    data: () => {
        return {
            cateGoodsList: [],
            total: 0,
            keyword: "",
            currentPage: 1,
            pageSize: 12,
            is_free_shipping: 0,
            is_own: "",
            filters: {
                site_id: 0,
                category_id: 0,
                min_price: "",
                max_price: "",
                order: "",
                sort: "desc",
                category_level: "",
            },
            shopInfo: {}, //店铺信息
            hasFollow: false, //是否关注
            categoryList: [],
            loading: true
        }
    },
    created() {
        this.filters.coupon_type = this.$route.query.coupon_type || 0
        this.filters.platform_coupon_type = this.$route.query.platform_coupon_type || 0
        this.keyword = this.$route.query.keyword || ""
        this.filters.shop_category_id = this.$route.query.shop_category_id
        this.filters.category_level = this.$route.query.category_level

        if (this.$route.query.site_id) {
            this.filters.site_id = this.$route.query.site_id
            this.getGoodsList()
            this.getShopInfo()
            this.getCategoryList()
            this.getfollow()
        } else {
            this.$message.error("缺少店铺id")
            this.$router.push("/index")
        }
    },
    watch: {
        is_free_shipping: function (val) {
            this.filters.is_free_shipping = val ? 1 : ""
            this.getGoodsList()
        },
        $route: function (curr) {
            if (curr.fullPath) {
                this.filters.site_id = curr.query.site_id
                this.filters.shop_category_id = curr.query.shop_category_id
                this.filters.category_level = curr.query.category_level
                this.getGoodsList()
            }
            if (this.keyword !== curr.query.keyword) {
                this.currentPage = 1
                this.keyword = curr.query.keyword
                this.filters.shop_category_id = curr.query.category_id || "";
                // this.filters.category_level = curr.query.level || "";
                this.getGoodsList()
            }
        }
    },
    computed: {
        ...mapGetters(["token", "defaultShopImage", "addonIsExit", "defaultGoodsImage"])
    },
    methods: {
        tabCate(item) {
            this.$router.push({
                path: "/shop_list",
                query: {
                    site_id: this.filters.site_id,
                    shop_category_id: item.category_id,
                    category_level: item.level
                }
            })
            this.filters.shop_category_id = item.category_id
            this.filters.category_level = item.level
            this.getGoodsList()
        },
        getShopInfo() {
            shopInfo({ site_id: this.filters.site_id })
                .then(res => {
                    if (res.code == 0) {
                        this.shopInfo = res.data
                    }
                })
                .catch(err => {
                    this.$message.error(err.message)
                })
        },
        getfollow() {
            isFollow({
                site_id: this.filters.site_id
            })
                .then(res => {
                    this.hasFollow = res.data
                })
                .catch(err => {
                })
        },
        //获取店铺商品分类列表
        getCategoryList() {
            tree({ site_id: this.filters.site_id })
                .then(res => {
                    this.categoryList = res.data
                })
                .catch(err => {
                    this.$message.error(err.message)
                })
        },
        //关注与取消
        getIsFollow() {
            if (this.hasFollow) {
                unFollow({ site_id: this.filters.site_id }).then(res => {
                    if (res.code == 0 && res.data) {
                        this.hasFollow = !this.hasFollow
                        this.$message({
                            message: "取消关注成功",
                            type: "success"
                        })
                    }
                })
            } else {
                follow({ site_id: this.filters.site_id }).then(res => {
                    if (res.code == 0 && res.data) {
                        this.hasFollow = !this.hasFollow
                        this.$message({
                            message: "关注成功",
                            type: "success"
                        })
                    }
                })
            }
        },
        getGoodsList() {
            const currentArr = []
            const params = {
                page: this.currentPage,
                page_size: this.pageSize,
                site_id: this.filters.site_id,
                keyword: this.keyword,
                category_level: this.filters.category_level,
                ...this.filters
            }
            goodsSkuPage(params || {})
                .then(res => {
                    const { count, page_count, list } = res.data
                    this.total = count
                    this.cateGoodsList = list
                    this.loading = false
                })
                .catch(err => {
                    this.loading = false
                })
        },

        onChooseBrand(brand) {
            this.choosedBrand = brand
            this.filters.brand_id = brand.id
            this.getGoodsList()
        },

        setMuiltChoose(attr) {
            this.$set(attr, "isMuiltSelect", !attr.isMuiltSelect)
            this.getGoodsList()
        },

        setAttrSelected(item, subitem) {
            if (item.child) {
                item.child.forEach(attr => {
                    this.$set(attr, "selected", false)
                })
            }

            this.$set(subitem, "selected", true)
            this.$set(item, "selectedValue", subitem.attr_value_name)
            this.getGoodsList()
        },

        setAttrSelectedMuilt(item, subitem) {
            this.$set(subitem, "selected", !subitem.selected)
            var selectedValueArray = []

            if (subitem.selected) {
                const selectedValue = item.selectedValue || ""
                selectedValueArray = selectedValue.split(",")
                if (selectedValueArray[0] == "") selectedValueArray.pop(0)

                if (selectedValueArray.indexOf(subitem.attr_value_name) == -1) {
                    selectedValueArray.push(subitem.attr_value_name)
                }
            } else {
                const selectedValue = item.selectedValue || ""
                selectedValueArray = selectedValue.split(",")
                if (selectedValueArray[0] == "") selectedValueArray.pop(0)

                if (selectedValueArray.indexOf(subitem.attr_value_name) !== -1) {
                    selectedValueArray.splice(selectedValueArray.indexOf(subitem.attr_value_name), 1)
                }
            }

            this.$set(item, "selectedValue", selectedValueArray.toString())
            this.getGoodsList()
        },

        colseBrand() {
            this.choosedBrand = ""
            this.filters.brand_id = ""
            this.getGoodsList()
        },

        colseAttr(item) {
            item.selectedValue = ""
            item.child.forEach(item => {
                this.$set(item, "selected", false)
            })
            this.getGoodsList()
        },
        handlePageSizeChange(num) {
            this.pageSize = num
            this.getGoodsList()
        },
        handleCurrentPageChange(page) {
            this.currentPage = page
            this.getGoodsList()
        },

        handlePriceRange() {
            this.getGoodsList()
        },
        handleChangeInitial(initial) {
            this.currentInitial = initial
        },
        changeSort(type) {
            if (this.filters.order === type) {
                this.$set(this.filters, "sort", this.filters.sort === "desc" ? "asc" : "desc")
            } else {
                this.$set(this.filters, "order", type)
                this.$set(this.filters, "sort", "desc")
            }

            this.getGoodsList()
        },
        imageError(index) {
            this.cateGoodsList[index].sku_image = this.defaultGoodsImage
        }
    }
}
