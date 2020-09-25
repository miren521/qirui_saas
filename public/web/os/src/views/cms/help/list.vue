<template>
  <div class="help-wrap">
    <el-breadcrumb separator="/" class="path">
      <el-breadcrumb-item :to="{ path: '/' }" class="path-home"><i class="n el-icon-s-home"></i>首页</el-breadcrumb-item>
      <el-breadcrumb-item class="path-help">帮助</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="help" v-loading="loading">
      <div class="menu">
        <div class="title">帮助列表</div>
        <div class="item" v-for="(item, index) in helpList" :key="index">
          <div
            :class="currentId == item.class_id ? 'active item-name' : 'item-name'"
            @click="menuOther(item.class_id)"
          >{{ item.class_name }}</div>
        </div>
      </div>
      <div class="list-other">
        <transition name="slide">
          <router-view />
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
import { helpList } from "@/api/cms/help";
export default {
  name: "help",
  components: {},
  data: () => {
    return {
      helpList: [],
      currentId: 0,
      loading: true
    };
  },
  created() {
    this.getInfo();
  },
  methods: {
    menuOther(id) {
      this.currentId = id;
      this.$router.push({ path: "/cms/help/listother-" + id });
    },
    getInfo() {
      helpList({
        app_module: "admin"
      })
        .then(res => {
          if (res.code == 0 && res.data) {
            this.$router.push({
              path: "/cms/help/listother-" + res.data[0].class_id
            });
            this.currentId = res.data[0].class_id;
            this.helpList = res.data;
          }
          this.loading = false;
        })
        .catch(err => {
          this.loading = false;
          this.$message.error(err.message);
        });
    }
  }
};
</script>
<style lang="scss" scoped>
.help-wrap{
    background: #ffffff;
    .path{
        padding: 15px 0;
    }
}
.help {
  display: flex;
  padding-bottom: 20px;
  .menu {
    width: 210px;
    min-height: 300px;
    background: #ffffff;
    border: 1px solid #f1f1ff;
    flex-shrink: 0;
    .title {
      padding-left: 16px;
        background: #f8f8f8;
        font-size: $ns-font-size-base;
        height: 40px;
        line-height: 40px;
        cursor: pointer;
        color: #666666;
    }
    .item-name {
      font-size: $ns-font-size-base;
      cursor: pointer;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      line-height: 40px;
      border-top: 1px solid #f1f1f1;
      padding-left: 25px;
      padding-right: 10px;
      height: 40px;
      background: #ffffff;
      color: #666666;
      &:hover {
        color: $base-color;
      }
    }
    .active {
      color: $base-color;
    }
  }
}
.list-other {
  margin-left: 20px;
  width: 80%;
}
</style>
