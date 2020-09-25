<template>
    <div>商品详情：{{ id }}</div>
</template>

<script>
    export default {
        name: "topic_detail",
        components: {},
        data: () => {
            return {
                id: 0
            }
        },
        created() {
            this.id = this.$route.path.replace("/promotion/topic-goods-", "")
        },
        methods: {}
    }
</script>
<style lang="scss" scoped></style>
