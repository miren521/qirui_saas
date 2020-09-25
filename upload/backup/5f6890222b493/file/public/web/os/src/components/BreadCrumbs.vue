<template>
    <el-breadcrumb class="app-breadcrumb" separator="/">
        <transition-group name="breadcrumb">
            <el-breadcrumb-item v-for="(item, index) in levelList" :key="item.path">
                <span v-if="index == 0">
                    <i class="el-icon-s-home"></i>
                </span>
                <span v-if="item.redirect === 'noRedirect' || index == levelList.length - 1" class="no-redirect">{{ item.meta.title }}</span>
                <a v-else @click.prevent="handleLink(item)">{{ item.meta.title }}</a>
            </el-breadcrumb-item>
            <el-breadcrumb-item key="ext_item" v-if="hasExtItem">
                <slot name="ext_item"></slot>
            </el-breadcrumb-item>
        </transition-group>
    </el-breadcrumb>
</template>

<script>
    import { compile } from "path-to-regexp"

    export default {
        props: {
            hasExtItem: false
        },
        data() {
            return {
                levelList: null
            }
        },
        watch: {
            $route() {
                this.getBreadcrumb()
            }
        },
        created() {
            this.getBreadcrumb()
        },
        methods: {
            getBreadcrumb() {
                // only show routes with meta.title
                let matched = this.$route.matched.filter(item => item.meta && item.meta.title)
                const first = matched[0]

                if (!this.isHome(first)) {
                    matched = [{ path: "/index", meta: { title: "首页" } }].concat(matched)
                }

                this.levelList = matched.filter(item => item.meta && item.meta.title && item.meta.breadcrumb !== false)
            },
            isHome(route) {
                const name = route && route.name
                if (!name) {
                    return false
                }
                return name.trim().toLocaleLowerCase() === "index".toLocaleLowerCase()
            },
            pathCompile(path) {
                const { params } = this.$route
                var toPath = compile(path)
                return toPath(params)
            },
            handleLink(item) {
                const { redirect, path } = item
                if (redirect) {
                    this.$router.push(redirect)
                    return
                }
                this.$router.push(this.pathCompile(path))
            }
        }
    }
</script>

<style lang="scss" scoped>
    .app-breadcrumb.el-breadcrumb {
        display: inline-block;
        font-size: 14px;
        margin: 10px auto 1px auto;
        width: 100%;

        border-width: 1px;
        border-style: solid;
        line-height: 30px;
        border-color: #eae9e9;

        .el-breadcrumb__item {
            padding-left: 10px;
        }

        .no-redirect {
            color: #97a8be;
            cursor: text;
        }
    }
</style>
