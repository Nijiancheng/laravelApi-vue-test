// const ip = 'http://localhost:8081/api';
// const ip = 'http://127.0.0.1:8081';
const  ip = 'http://api.com'
const url = ip+'/api';
//上传图片
const Upload = url + '/image/upload';
//登录
const Login = url + '/admin/login';
//注册
const Reg = url + '/admin/register';

// 添加库存
const SkuCreate = url + '/sku/create';
// 删除库存
const SkuDelete = url + '/sku/delete';
//获取商品主信息
const Product = url + '/product/index';
// 获取商品信息
const ProductGet = url + '/product';
// 删除商品
const ProductDel = url + '/product/delete';
// 修改商品信息
const ProductUpdate = url + '/product/update';
// 添加商品信息
const ProductCreate = url + '/product/create';

//获取分类信息
const CateGet = url + '/cate';
//添加分类信息
const CateAdd = url + '/cate/create'
//修改分类信息
const CateUpdate = url + '/cate/update'
//删除分类信息
const CateDel = url + '/cate/delete'

//获取导航信息
const NavGet = url + '/nav';
//添加分类信息
const NavAdd = url + '/nav/create'
//修改分类信息
const NavUpdate = url + '/nav/update'
//删除分类信息
const NavDel = url + '/nav/delete'

export default {
  Upload,
  Product,
  ProductDel,
  ProductGet,
  ProductUpdate,
  ProductCreate,
  CateGet,
  CateAdd,
  CateUpdate,
  CateDel,
  NavGet,
  NavAdd,
  NavUpdate,
  NavDel,

  SkuCreate,
  SkuDelete,
  Reg,
  Login,

  ip
}
