import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import About from '../views/About.vue'
import TableProduct from '../views/other/Table_product.vue'
import TableNav from '../views/other/Table_nav.vue'
import TableCate from '../views/other/Table_cate.vue'
import UpdateNav from '../views/other/Update_nav.vue'
import UpdateProduct from '../views/other/Update_product.vue'
import CreateNav from '../views/other/Create_nav.vue'
import CreateProduct from '../views/other/Create_product.vue'
import CreateCate from '../views/other/Create_cate.vue'


Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'home',
    redirect:'table_product',
    component: Home
  },
  {
    path: '/about',
    name: 'about',
    component: About
  },
  {
    path:'/table_product',
    name:'table_product',
    component: TableProduct
  },
  {
    path:'/table_nav',
    name:'table_nav',
    component: TableNav
  },
  {
    path:'/table_cate',
    name:'table_cate',
    component: TableCate
  },
  {
    path:'/update_nav',
    name:'update_nav',
    component: UpdateNav
  },
  {
    path:'/update_product',
    name:'update_product',
    component: UpdateProduct
  },
  {
    path:'/create_nav',
    name:'create_nav',
    component: CreateNav
  },
  {
    path:'/create_cate',
    name:'create_cate',
    component: CreateCate
  },
  {
    path:'/create_product',
    name:'create_product',
    component: CreateProduct
  }
]

const router = new VueRouter({
  routes
})

export default router
