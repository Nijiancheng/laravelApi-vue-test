<template>

    <a-layout id="components-layout-demo-top" class="layout">
        <a-layout-header>
            <a-menu
                    theme="dark"

                    :style="{ lineHeight: '64px' }"
            >
                登录
            </a-menu>
        </a-layout-header>
        <a-layout-content style="padding: 0 50px">
            <a-breadcrumb style="margin: 16px 0">

            </a-breadcrumb>
            <div :style="{ background: '#fff', padding: '24px', minHeight: '100%' }">
<!--                <router-view></router-view>-->
                <Login></Login>
            </div>
        </a-layout-content>
        <a-layout-footer style="text-align: center">
            Ant Design ©2018 Created by Ant UED
        </a-layout-footer>
    </a-layout>
</template>
<script>
    import Login from './other/Login.vue'
    export default {
        components:{
            Login
        }
    }
</script>
<style scoped lang="scss">
    #components-layout-demo-top .logo {
        width: 120px;
        height: 31px;
        background: rgba(255, 255, 255, 0.2);
        margin: 16px 24px 16px 0;
        float: left;
    }
</style>
