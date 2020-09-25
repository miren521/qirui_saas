<template>
    <div id="app">
        <transition name="slide"><router-view /></transition>
    </div>
</template>
<script>
    export default {
        name: "APP"
    }
</script>
<style lang="scss" scoped>
    /*每个页面公共css */
    @import url("assets/styles/iconfont.css");
    @import "assets/styles/main.scss";
</style>
