import Vue from 'vue'
import App from './App.vue'
import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/antd.css'
import router from './router'
import store from './store'
import axios from 'axios'
import VueQuillEditor from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

Vue.use(VueQuillEditor)
Vue.prototype.axios = axios;

Vue.use(Antd)
Vue.config.productionTip = false


// 添加请求拦截器
axios.interceptors.request.use(function (config) {
  // 在发送请求之前做些什么
  if(localStorage.getItem('token')){
    // console.log(config,'请求');
    config.url = config.url+'?token='+localStorage.getItem('token');
  }
  return config;
}, function (error) {
  // 对请求错误做些什么
  return Promise.reject(error);
});

// 添加响应拦截器
axios.interceptors.response.use(
    response => {
      // console.log(response.data,'相应');
      if(response.data.code == 402){
        if(localStorage.getItem('token')){
          localStorage.removeItem('token')
        }
        router.replace({
          path: '/login',
          query: { redirect: router.currentRoute.fullPath } // 将跳转的路由path作为参数，登录成功后跳转到该路由
        })
      }
      return response
    },
    error => {
      console.log('error')
      return Promise.reject(error.response)
    }
);



new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
