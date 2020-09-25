module.exports = {
    publicPath: "/web",
    assetsDir: "assets",
    chainWebpack: config => {
        const oneOfsMap = config.module.rule("scss").oneOfs.store
        oneOfsMap.forEach(item => {
            item.use("sass-resources-loader")
                .loader("sass-resources-loader")
                .options({
                    // 全局变量资源路径
                    resources: "./src/assets/styles/main.scss"
                    // 也可以选择全局变量路径数组, 如果你有多个文件需要成为全局,就可以采用这种方法
                    // resources: ['./path/to/vars.scss', './path/to/mixins.scss']
                    // 或者你可以将多个scss文件@import到一个main.scss用第一种方法，都是可以的
                })
                .end()
        })
    }
}
