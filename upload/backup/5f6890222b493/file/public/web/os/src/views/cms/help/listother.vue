<template>
  <div class="help-other">
    <div class="item-info">
      <div
        class="item"
        v-for="(item, index) in helpList.list"
        :key="index"
        @click="detail(item.id)"
      >
        <div class="item-title">{{ item.title }}</div>
        <div class="info">
          <div class="time">{{ $util.timeStampTurnTime(item.create_time) }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { helpList, helpOther } from "@/api/cms/help";
export default {
  name: "list_other",
  components: {},
  data: () => {
    return {
      helpList: [],
      id: null
    };
  },
  created() {
    this.id = this.$route.path.replace("/cms/help/listother-", "");
    this.getInfo();
  },
  watch: {
    $route(curr) {
      this.id = curr.params.pathMatch;
      this.getInfo();
    }
  },
  methods: {
    detail(id) {
      this.$router.push({ path: "/cms/help-" + id });
    },
    getInfo() {
      helpOther({
        class_id: this.id
      })
        .then(res => {
          if (res.code == 0 && res.data) {
            this.helpList = res.data;
          } else {
            this.$router.push({ path: "/cms/help" });
          }
        })
        .catch(err => {
          this.$message.error(err.message);
        });
    }
  }
};
</script>
<style lang="scss" scoped>
.help-other {
  .item-info {
    padding: 10px;
    background-color: #ffffff;
    height: 300px;
    border: 1px solid #e9e9e9;
    .item {
      border-bottom: 1px #f1f1f1 solid;
      padding: 10px 0;
      display: flex;
      justify-content: space-between;
      &:last-child {
        border-bottom: none;
      }
      &:first-child {
        padding-top: 0px;
      }
      .item-title {
        font-size: $ns-font-size-base;
        color: #333333;
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        overflow: hidden;
        cursor: pointer;
        &:hover {
          color: $base-color;
        }
      }
    }
  }
}
</style>
