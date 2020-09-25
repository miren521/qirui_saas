<template>
  <div class="notice-wrap">
    <el-breadcrumb separator="/" class="path">
      <el-breadcrumb-item :to="{ path: '/' }" class="path-home">
        <i class="n el-icon-s-home"></i>首页
      </el-breadcrumb-item>
      <el-breadcrumb-item class="path-help">公告</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="notice" v-loading="loading">
      <div class="menu">
        <div class="title">最新公告</div>
        <div class="item" v-for="item in noticeList" :key="item.id" @click="detil(item.id)">
          <div class="item-name" @click="menuOther(item.id)">{{ item.title }}</div>
        </div>
      </div>
      <div class="list-wrap">
        <div class="notice-title">商城公告</div>
        <div class="list" v-for="item in noticeList" :key="item.id" @click="detil(item.id)">
          <div class="item">{{ item.title }}</div>
          <div class="info">
            <div class="time">{{ $util.timeStampTurnTime(item.create_time) }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="pager">
        <el-pagination
			background
			:pager-count="5" 
			:total="total" 
			prev-text="上一页" 
			next-text="下一页" 
			:current-page.sync="queryinfo.page" 
			:page-size.sync="queryinfo.page_size"
			@size-change="handlePageSizeChange" 
			@current-change="handleCurrentPageChange" 
			hide-on-single-page
		></el-pagination>
      </div>
  </div>
</template>

<script>
import { noticesList } from "@/api/cms/notice";
export default {
  name: "notice",
  components: {},
  data: () => {
    return {
      queryinfo: {
        page: 1,
        page_size: 10,
		    receiving_type : "web"
      },
      noticeList: [],
      total: 0,
      loading: true
    };
  },
  created() {
    this.getList();
  },
  methods: {
    detil(id) {
      this.$router.push({ path: "/cms/notice-" + id });
    },
    getList() {
      noticesList(this.queryinfo)
        .then(res => {
          if (res.code == 0 && res.data) {
            this.noticeList = res.data.list;
            this.total = res.data.count;
          }
          this.loading = false;
        })
        .catch(err => {
          this.loading = false;
          this.$message.error(err.message);
        });
    },
    handlePageSizeChange(newsize) {
      this.queryinfo.page_size = newsize;
      this.getList();
    },
    handleCurrentPageChange(newnum) {
      this.queryinfo.page = newnum;
      this.getList();
    }
  }
};
</script>
<style lang="scss" scoped>
.notice-wrap {
  background: #ffffff;
  .path {
    padding: 15px 0;
  }
}
.notice {
  background-color: #ffffff;
  min-height: 300px;
  position: relative;
  display: flex;
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
      cursor: pointer;
      color: #666666;
      display: flex;
      align-items: center;
    }
    .item-name {
      font-size: $ns-font-size-base;
      cursor: pointer;
      line-height: 40px;
      border-top: 1px solid #f1f1f1;
      padding-left: 25px;
      padding-right: 10px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
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
  .list-wrap {
    padding: 0 40px;
    margin-left: 27px;
    border: 1px solid #f1f1f1;
    width: 100%;
    margin-bottom: 10px;
    .notice-title {
      padding: 37px 0 20px 0;
      font-size: 18px;
      border-bottom: 1px dotted #e9e9e9;
    }
    .list {
      display: flex;
      justify-content: space-between;
      align-items: center;
      &:last-of-type {
        border-bottom: initial;
      }
      &:nth-child(2) {
        margin-top: 10px;
      }
      .item {
        font-size: $ns-font-size-base;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        padding: 3px 0;
        &:hover {
          color: $base-color;
        }
      }
      .info {
        display: flex;
        font-size: $ns-font-size-base;
        .time {
          margin-right: 10px;
        }
      }
    }
  }
}
  .page {
    text-align: center;
  }
</style>
