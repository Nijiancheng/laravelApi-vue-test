// const ip = 'http://localhost:8081/api';
const ip = 'http://127.0.0.1:8081/api';
//上传图片
const Upload = ip + '/image/upload';
//登录
const Login = ip + '/admin/login';
//注册
const Reg = ip + '/admin/register';

// 添加库存
const SkuCreate = ip + '/sku/create';
// 删除库存
const SkuDelete = ip + '/sku/delete';
//获取商品主信息
const Product = ip + '/product/index';
// 获取商品信息
const ProductGet = ip + '/product';
// 删除商品
const ProductDel = ip + '/product/delete';
// 修改商品信息
const ProductUpdate = ip + '/product/update';
// 添加商品信息
const ProductCreate = ip + '/product/create';

//获取分类信息
const CateGet = ip + '/cate';
//添加分类信息
const CateAdd = ip + '/cate/create'
//修改分类信息
const CateUpdate = ip + '/cate/update'
//删除分类信息
const CateDel = ip + '/cate/delete'

//获取导航信息
const NavGet = ip + '/nav';
//添加分类信息
const NavAdd = ip + '/nav/create'
//修改分类信息
const NavUpdate = ip + '/nav/update'
//删除分类信息
const NavDel = ip + '/nav/delete'

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
  Login
}
