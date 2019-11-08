<template>
  <a-layout id="components-layout-demo-top-side-2">
    <a-layout-header class="header">
      <div class="logo" >
        <div v-if="name" class="userName">
            {{name}}
        </div>
      </div>
        <a-icon class="logoutIcon" v-if="name" type="logout" @click="logout" title="登出"/>
    </a-layout-header>
    <a-layout>
      <a-layout-sider width="200" style="background: #fff" v-if="name">
        <a-menu
          mode="inline"
          :defaultSelectedKeys="['1']"
          :defaultOpenKeys="['sub1']"
          :style="{ height: '100%', borderRight: 0 }"
        >
          <a-sub-menu key="sub1">
            <span slot="title"><a-icon type="user" />商品管理</span>
            <a-menu-item key="1"><router-link to="table_product">商品列表</router-link></a-menu-item>
            <a-menu-item key="2"><router-link to="create_product">新增商品</router-link></a-menu-item>
          </a-sub-menu>
          <a-sub-menu key="sub2">
            <span slot="title"><a-icon type="bars" />导航管理</span>
            <a-menu-item key="5"><router-link to="table_nav">导航列表</router-link></a-menu-item>
            <a-menu-item key="6"><router-link to="create_nav">新增导航</router-link></a-menu-item>
          </a-sub-menu>
          <a-sub-menu key="sub3">
            <span slot="title"><a-icon type="bars" />分类管理</span>
            <a-menu-item key="9"><router-link to="table_cate">分类列表</router-link></a-menu-item>
            <a-menu-item key="10"><router-link to="create_cate">新增分类</router-link></a-menu-item>
          </a-sub-menu>
        </a-menu>
      </a-layout-sider>
      <a-layout style="padding: 0 24px 24px">
        <a-breadcrumb style="margin: 16px 0">
          <a-breadcrumb-item>Home</a-breadcrumb-item>
          <a-breadcrumb-item>List</a-breadcrumb-item>
          <a-breadcrumb-item>App</a-breadcrumb-item>
        </a-breadcrumb>
        <a-layout-content
          :style="{ background: '#fff', padding: '24px', margin: 0, minHeight: '500px' }"
        >
         <router-view></router-view>
        </a-layout-content>
      </a-layout>
    </a-layout>
  </a-layout>
</template>
<script>
    export default {
        data() {
            return {
                collapsed: false,
                name:'',
            };
        },
        methods:{
            getName(){
                this.name = localStorage.getItem('name')?localStorage.getItem('name'):false;
            },
            login(){
                this.$router.push('/login');
            },
            logout(){
                localStorage.clear();
                this.$router.push('/login');
            }
        },
        created(){
          this.getName();
        }
    };
</script>

<style scoped lang="scss">
  #components-layout-demo-top-side-2 .logo {
    width: 120px;
    height: 31px;
    background: rgba(255, 255, 255, 0.8);
    margin: 16px 28px 16px 0;
    float: left;
      display:flex;
      align-items: center;
      padding: 0 5px;
      justify-content: center;
      font-size:18px

  }
  #components-layout-demo-top-side-2 .header{
      display: flex;
      justify-content: space-between;
      align-items:center;
      .logoutIcon{
          color:red;
          font-size:20px
      }
  }
</style>
