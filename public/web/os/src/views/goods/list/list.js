import { goodsSkuPage } from "@/api/goods/goods"
import { mapGetters } from "vuex"
import { relevanceinfo, goodsCategoryInfo } from "@/api/goods/goodscategory"
import { addCollect, deleteCollect, isCollect } from "@/api/goods/goods_collect"
export default {
    data: () => {
        return {
            cargoList: [],
            shopList: [], // 店铺列表
            brandList: [], // 品牌列表
            attributeList: [], // 属性列表
            brandInitialList: [],
            currentInitial: "", // 当前选择品牌分区
            choosedBrand: "", // 已选择的品牌,
            hasChoosedAttrs: false, // 忆选择的属性
            total: 0,
            keyword: "",
            catewords:'',
            currentPage: 1,
            pageSize: 12,
            is_free_shipping: 0,
            is_own: "",
            filters: {
                site_id: 0,
                category_id: 0,
                category_level: 0,
                brand_id: 0,
                min_price: "",
                max_price: "",
                order: "",
                sort: "desc",
                platform_coupon_type: 0
            },
            loading: true,
            whetherCollection: 0
        }
    },
    created() {
        this.keyword = this.$route.query.keyword || ""

        this.filters.category_id = this.$route.query.category_id || ""
        this.filters.category_level = this.$route.query.level || ""
        this.filters.brand_id = this.$route.query.brand_id || ""
        this.filters.platform_coupon_type = this.$route.query.platform_coupon_type || 0
        this.getGoodsList()
        if (this.filters.category_id) {
            this.getRelevanceinfo()
            this.categorySearch()
        }
    },
    computed: {
        ...mapGetters(["defaultGoodsImage"])
    },
    methods: {
        /**
         * 商品分类搜索
         */
        categorySearch() {
            goodsCategoryInfo({
                category_id: this.filters.category_id
            }).then(res => {
                if (res.code == 0 && res.data) {
                    this.catewords = res.data.category_full_name
                }
                
            }).catch(err => {

            })
        },
        addToCart(item) {
            this.$store
                .dispatch('cart/add_to_cart', item)
                .then(res => {
                    var data = res.data
                    if (data > 0) {
                        this.$message({
                            message: "加入购物车成功",
                            type: "success"
                        })
                    }
                })
                .catch(err => err);
        },
        async isCollect(item) {
            await isCollect({ goods_id: item.goods_id }).then(res => {
                this.whetherCollection = res.data
                if(this.whetherCollection == 0){
                    item.isCollection = false
                }else{
                    item.isCollection = true
                }
            })
                .catch(err => err);
        },
        async editCollection(item) {
           await this.isCollect(item)
            const { goods_id, sku_id, site_id, sku_name, sku_price, sku_image } = item;
            if (this.whetherCollection == 0) {
                await addCollect({ goods_id, sku_id, site_id, sku_name, sku_price, sku_image })
                    .then(res => {
                        this.$message({
                            message: '收藏成功',
                            type: 'success'
                        });
                        item.isCollection = true
                    })
                    .catch(err => err);
            } else {
                await deleteCollect({
                    goods_id
                }).then(res => {
                    if (res.data > 0) {
                        this.$message({
                            message: '取消收藏成功',
                            type: 'success'
                        });
                        item.isCollection = false
                    }
                })
                    .catch(err => err);
            }
        },
        getGoodsList() {
            const currentArr = []
            if (this.attributeList) {
                this.attributeList.forEach(item => {
                    if (item.child) {
                        item.child.forEach(subitem => {
                            if (subitem.selected) {
                                currentArr.push({
                                    attr_id: item.attr_id,
                                    attr_value_id: subitem.attr_value_id
                                })
                            }
                        })
                    }
                })
            }

            const params = {
                page: this.currentPage,
                page_size: this.pageSize,
                site_id: this.filters.siteId,
                keyword: this.keyword,
                attr: currentArr.length > 0 ? JSON.stringify(currentArr) : "",
                ...this.filters
            }
            goodsSkuPage(params || {})
                .then(res => {
                    const { count, page_count, list } = res.data
                    this.total = count
                    this.cargoList = list
                    this.loading = false
                })
                .catch(err => {
                    this.loading = false
                })
        },

        getRelevanceinfo() {
            const params = {
                category_id: this.filters.category_id,
                category_level: this.filters.category_level
            }
            relevanceinfo(params)
                .then(res => {
                    const { brand_list, attribute_list, brand_initial_list } = res.data
                    this.brandList = brand_list
                    this.attributeList = attribute_list
                    this.brandInitialList = brand_initial_list
                })
                .catch(err => err)
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
            item.child.forEach(subitem => {
                subitem.selected = false
            })

            item.isMuiltSelect = false

            this.getGoodsList()
        },

        handlePageSizeChange(size) {
            this.pageSize = size
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
        }
    },
    watch: {
        is_free_shipping: function (val) {
            this.filters.is_free_shipping = val ? 1 : ""
            this.getGoodsList()
        },
        is_own: function (val) {
            this.filters.is_own = val ? 1 : ""
            this.getGoodsList()
        },
        $route: function (curr) {
            if (curr.query.category_id == undefined) {
                this.catewords = ""
                this.currentPage = 1
                this.keyword = curr.query.keyword
                this.filters.category_id = curr.query.category_id || ""
                this.filters.category_level = curr.query.level || ""
                this.filters.brand_id = curr.query.brand_id || ""
                this.getGoodsList()
            }
        }
    }
}
