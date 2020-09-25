<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<span>编辑收货地址</span>
			</div>

			<div v-loading="loading" class="ns-member-address-list">
				<el-form :model="formData" :rules="rules" ref="ruleForm" label-width="80px">
					<el-form-item label="姓名" prop="name">
						<el-input v-model="formData.name" placeholder="收货人姓名" class="ns-len-input"></el-input>
					</el-form-item>

					<el-form-item label="手机" prop="mobile">
						<el-input v-model="formData.mobile" autocomplete="off" placeholder="收货人手机号" class="ns-len-input"></el-input>
					</el-form-item>

					<el-form-item label="电话">
						<el-input v-model.trim="formData.telephone" autocomplete="off" placeholder="收货人固定电话（选填）" class="ns-len-input"></el-input>
					</el-form-item>

					<el-form-item label="地址" prop="full_address">
						<el-select v-model="formData.province_id" placeholder="请选择省" @change="changeProvice(formData.province_id)">
							<el-option v-for="option in province" :key="option.id" :label="option.name" :value="option.id">{{ option.name }}</el-option>
						</el-select>
						<el-select v-model="formData.city_id" placeholder="请选择市" @change="changeCity(formData.city_id)">
							<el-option v-for="option in city" :key="option.id" :label="option.name" :value="option.id">{{ option.name }}</el-option>
						</el-select>
						<el-select v-model="formData.district_id" placeholder="请选择区/县" @change="changeDistrict(formData.district_id)">
							<el-option v-for="option in district" :key="option.id" :label="option.name" :value="option.id">{{ option.name }}</el-option>
						</el-select>
					</el-form-item>

					<el-form-item label="详细地址" prop="address">
						<el-input v-model.trim="formData.address" autocomplete="off" placeholder="定位到小区、街道、写字楼" class="ns-len-input"></el-input>
					</el-form-item>

					<el-form-item label="是否默认">
						<el-radio-group v-model="formData.is_default">
							<el-radio :label="1">是</el-radio>
							<el-radio :label="0">否</el-radio>
						</el-radio-group>
					</el-form-item>

					<el-form-item>
						<el-button type="primary" size="medium" @click="saveAddress('ruleForm')">保存</el-button>
					</el-form-item>
				</el-form>
			</div>
		</el-card>
	</div>
</template>

<script>
    import { addressInfo, saveAddress } from "@/api/member/member"
    import { getArea } from "@/api/address"

    export default {
        name: "address_edit",
        components: {},
        data() {
            let self = this
            var isMobile = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error("手机号不能为空"))
                } else {
                    const reg = /^1[3|4|5|6|7|8|9][0-9]{9}$/

                    if (reg.test(value)) {
                        callback()
                    } else {
                        callback(new Error("请输入正确的手机号"))
                    }
                }
            }

            var fullAddress = (rule, value, callback) => {
                if (self.formData.province_id) {
                    if (self.formData.city_id) {
                        if (self.formData.district_id) {
                            return callback()
                        } else {
                            return callback(new Error("请选择区/县"))
                        }
                    } else {
                        return callback(new Error("请选择市"))
                    }
                } else {
                    return callback(new Error("请选择省"))
                }
            }

            return {
                formData: {
                    id: 0,
                    name: "",
                    mobile: "",
                    telephone: "",
                    province_id: "",
                    city_id: "",
                    district_id: "",
                    community_id: "",
                    address: "",
                    full_address: "",
                    latitude: 0,
                    longitude: 0,
                    is_default: 1
                },
                addressValue: "",
                flag: false, //防重复标识
                defaultRegions: [],
                rules: {
                    name: [{ required: true, message: "请输入收货人姓名", trigger: "blur" }],
                    mobile: [{ required: true, validator: isMobile, trigger: "blur" }],
                    address: [{ required: true, message: "请输入详细地址", trigger: "blur" }],
                    full_address: [{ required: true, validator: fullAddress, trigger: "blur" }]
                },
                province: [],
                city: [],
                district: [],
                pickerValueArray: [],
                multiIndex: [0, 0, 0],
                isInitMultiArray: false,
                // 是否加载完默认地区
                isLoadDefaultAreas: false,
                loading: true,
				yes: true
            }
        },
        created() {
            this.formData.id = this.$route.query.id
            this.getAddressDetail()
            this.getDefaultAreas(0, {
                level: 0
            })
        },
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
        watch: {
            defaultRegions: {
                handler(arr, oldArr = []) {
                    // 避免传的是字面量的时候重复触发
                    if (arr.length !== 3 || arr.join("") === oldArr.join("")) return
                    this.handleDefaultRegions()
                },
                immediate: true
            }
        },
        computed: {
            pickedArr() {
                // 进行初始化
                if (this.isInitMultiArray) {
                    return [this.pickerValueArray[0], this.pickerValueArray[1], this.pickerValueArray[2]]
                }
                return [this.pickerValueArray[0], this.city, this.district]
            }
        },
        methods: {
            /**
             * 改变省
             */
            changeProvice(id) {
                this.getAreas(id, data => (this.city = data))
                let obj = {}
                obj = this.province.find(item => {
                    //这里的province就是上面遍历的数据源
                    return item.id === id //筛选出匹配数据
                })
                this.formData.city_id = ""
                this.formData.district_id = ""
                this.formData.full_address = obj.name // 设置选中的地址
            },
            /**
             * 改变市
             */
            changeCity(id) {
                this.getAreas(id, data => (this.district = data))
                let obj = {}
                obj = this.city.find(item => {
                    //这里的province就是上面遍历的数据源
                    return item.id === id //筛选出匹配数据
                })
                this.formData.district_id = ""
                this.formData.full_address = this.formData.full_address + "-" + obj.name
            },
            /**
             * 改变区
             */
            changeDistrict(id) {
                let obj = {}
                obj = this.district.find(item => {
                    //这里的province就是上面遍历的数据源
                    return item.id === id //筛选出匹配数据
                })
                this.formData.full_address = this.formData.full_address + "-" + obj.name
            },
            /**
             * 获取地址信息
             */
            getAddressDetail() {
                addressInfo({
                    id: this.formData.id
                })
                    .then(res => {
                        let data = res.data
                        if (data != null) {
                            this.formData.name = data.name
                            this.formData.mobile = data.mobile
                            this.formData.telephone = data.telephone
                            this.formData.address = data.address
                            this.formData.full_address = data.full_address
                            this.formData.latitude = data.latitude
                            this.formData.longitude = data.longitude
                            this.formData.is_default = data.is_default
                            this.formData.province_id = data.province_id
                            this.formData.city_id = data.city_id
                            this.formData.district_id = data.district_id
                            this.defaultRegions = [data.province_id, data.city_id, data.district_id]
                        }
                    })
                    .catch(err => {})
            },
            // 异步获取地区
            getAreas(pid, callback) {
                getArea({
                    pid: pid
                })
                    .then(res => {
                        if (res.code == 0) {
                            var data = []
                            res.data.forEach((item, index) => {
                                data.push(item)
                            })
                            if (callback) callback(data)
                        }
                    })
                    .catch(err => {})
            },

            /**
             * 获取省市区列表
             */
            getDefaultAreas(pid, obj) {
                getArea({
                    pid: pid
                })
                    .then(res => {
                        if (res.code == 0) {
                            var data = []
                            var selected = undefined
                            res.data.forEach((item, index) => {
                                if (obj != undefined) {
                                    if (obj.level == 0 && obj.province_id != undefined) {
                                        selected = obj.province_id
                                    } else if (obj.level == 1 && obj.city_id != undefined) {
                                        selected = obj.city_id
                                    } else if (obj.level == 2 && obj.district_id != undefined) {
                                        selected = obj.district_id
                                    }
                                }

                                if (selected == undefined && index == 0) {
                                    selected = item.id
                                }
                                data.push(item)
                            })

                            this.pickerValueArray[obj.level] = data
                            if (obj.level + 1 < 3) {
                                obj.level++
                                this.getDefaultAreas(selected, obj)
                            } else {
                                this.isInitMultiArray = true
                                this.isLoadDefaultAreas = true
                            }

                            this.province = this.pickerValueArray[0]
                        }
                        this.loading = false
                    })
                    .catch(err => {
                        this.loading = false
                    })
            },

            /**
             * 渲染默认值
             */
            handleDefaultRegions() {
                var time = setInterval(() => {
                    if (!this.isLoadDefaultAreas) return
                    this.isInitMultiArray = false

                    for (let i = 0; i < this.defaultRegions.length; i++) {
                        for (let j = 0; j < this.pickerValueArray[i].length; j++) {
                            this.province = this.pickerValueArray[0]

                            // 匹配省
                            if (this.defaultRegions[i] == this.pickerValueArray[i][j].id) {
                                // 设置选中省
                                this.$set(this.multiIndex, i, j)
                                // 查询市
                                this.getAreas(this.pickerValueArray[i][j].id, data => {
                                    this.city = data

                                    for (let k = 0; k < this.city.length; k++) {
                                        if (this.defaultRegions[1] == this.city[k].id) {
                                            // 设置选中市
                                            this.$set(this.multiIndex, 1, k)

                                            // 查询区县
                                            this.getAreas(this.city[k].id, data => {
                                                this.district = data

                                                // 设置选中区县
                                                for (let u = 0; u < this.district.length; u++) {
                                                    if (this.defaultRegions[2] == this.district[u].id) {
                                                        this.$set(this.multiIndex, 2, u)
                                                        this.handleValueChange({
                                                            detail: {
                                                                value: [j, k, u]
                                                            }
                                                        })
                                                        break
                                                    }
                                                }
                                            })

                                            break
                                        }
                                    }
                                })
                            }
                        }
                    }
                    if (this.isLoadDefaultAreas) clearInterval(time)
                }, 100)
            },
            handleValueChange(e) {
                // 结构赋值
                let [index0, index1, index2] = e.detail.value
                let [arr0, arr1, arr2] = this.pickedArr
                let address = [arr0[index0], arr1[index1], arr2[index2]]

                this.formData.full_address = ""
                for (let i = 0; i < address.length; i++) {
                    if (this.formData.full_address) {
                        this.formData.full_address = this.formData.full_address + "-" + address[i].name
                    } else {
                        this.formData.full_address = this.formData.full_address + address[i].name
                    }
                }
            },

            /**
             * 保存地址
             */
            saveAddress(formName) {
                this.$refs[formName].validate(valid => {
                    if (valid) {
                        var data = {
                            name: this.formData.name,
                            mobile: this.formData.mobile,
                            telephone: this.formData.telephone,
                            province_id: this.formData.province_id,
                            city_id: this.formData.city_id,
                            district_id: this.formData.district_id,
                            community_id: "",
                            address: this.formData.address,
                            full_address: this.formData.full_address,
                            latitude: this.formData.latitude,
                            longitude: this.formData.longitude,
                            is_default: this.formData.is_default
                        }

                        data.url = "add"
                        if (this.formData.id) {
                            data.url = "edit"
                            data.id = this.formData.id
                        }
                        if (this.flag) return
                        this.flag = true

                        saveAddress(data)
                            .then(res => {
                                if (res.code == 0) {
                                    this.$router.push({ path: "/member/delivery_address" })
                                } else {
                                    this.flag = false
                                    this.$message({ message: res.message, type: "warning" })
                                }
                            })
                            .catch(err => {
                                this.flag = false
                                this.$message.error(err.message)
                            })
                    } else {
                        return false
                    }
                })
            }
        }
    }
</script>
<style lang="scss" scoped>
	.box {
		width: 100%;
		position: relative;
	}
	
	.null-page {
		width: 100%;
		height: 730px;
		background-color: #FFFFFF;
		position: absolute;
		top: 0;
		left: 0;
		z-index: 9;
	}
	
    .el-card.is-always-shadow,
    .el-card.is-hover-shadow:focus,
    .el-card.is-hover-shadow:hover {
        box-shadow: unset;
    }

    .el-card {
        border: 0;
    }

    .ns-len-input {
        width: 350px;
    }

    .el-select {
        margin-right: 10px;
    }
</style>
