<template>
<div>
    <a-table :dataSource="data" rowKey="id"  :pagination="false">
        <a-table-column title="id" data-index="id"  />
        <a-table-column title="分类id" data-index="category_id"  >
            <template slot-scope="text, record, index">
                {{cate[text].name}}
            </template>
        </a-table-column>
        <a-table-column title="商品名" data-index="name"  />
        <a-table-column title="销量" data-index="sale_num"  />
        <a-table-column title="排序" data-index="sort"  />
        <a-table-column title="状态" data-index="status" >
            <template slot-scope="text, record, index">
                {{status[text-1]}}
            </template>
        </a-table-column>
        <a-table-column title="创建时间" data-index="created_at"  />
        <a-table-column title="更新时间" data-index="updated_at"  />

        <a-table-column title="操作"  data-index="action">
            <template slot-scope="text, record, index">
        <span>
          <a-button type="primary"  @click="edit(record.id)">编辑</a-button>
           <a-popconfirm title="是否删除该条数据?" @confirm="() =>  del(record.id)">
              <a-button type="danger" >删除</a-button>
           </a-popconfirm>
        </span>
            </template>
        </a-table-column>
    </a-table>
        <a-pagination @change="pageChange"  :pageSize="pageSize" :current="page" :total="total" />

</div>

</template>

<script>
    import qs from "qs";
    import api from '../../api'
    const status =['上架','下架'];
    export default {
        data() {
            return {
                data:[],
                cate:[],
                status,
                page:1,
                pageSize:2,
                total:null,
            };
        },
        methods: {
            getCate(){
                return new Promise((resolve, reject) => {
                    this.axios.get(api.CateGet).then(res => {
                        // console.log(res.data.data ,'分类信息');
                        if (res.data.status) {
                            res.data.data.data.forEach((val, key) => {
                                let json = JSON.parse(val.property);
                                resolve(
                                    this.cate[val.id] =
                                        {
                                            id: val.id,
                                            name: val.name,
                                            attr: json,
                                        }
                                )
                            })
                            console.log(this.cate);
                        }

                    }).catch(err => {
                        reject(console.log(err));
                    })
                })
            },
            getInfo(page,pageSize){
                // console.log(api.GetProduct);
                this.axios
                    .get(api.Product,{
                        params:{
                            'page':page,
                            'perpage':pageSize,
                        }
                    })
                    .then(response=>{
                        console.log(response);
                        this.total = response.data.data.total;
                        response.data.data.data.forEach((val,key) =>{
                            this.data.push(
                                {
                                    id: val.id,
                                    category_id: val.category_id,
                                    name:  val.name,
                                    sale_num: val.sale_num,
                                    // content: val.content,
                                    sort: val.sort,
                                    status: val.status,
                                    created_at: val.created_at,
                                    updated_at: val.updated_at
                                },
                            );
                        })

                    }).catch(err=>{
                    console.log(err);
                })
            },
            del(key){
                console.log(key);
                let data ={
                    'id':key,
                }
                this.axios.post(api.ProductDel,qs.stringify(data)).then(res=>{
                    console.log(res);
                    if(res.data.status){
                        // this.$router.go(0);
                        const dataSource = [...this.data];
                        this.data = dataSource.filter(item => item.id !== key);
                        this.$message.success('删除成功',2);
                    }else{
                        this.$message.error(res.data.msg,2);
                    }
                }).catch(err=>{

                    console.log(err);
                })
            },
          edit(id){
            console.log(id);
            this.$router.push({name:'update_product',query:{id:id}})
          },
          detail(key){
            this.$router.push({name:'detail_product',query:{id:key}})
          },
            pageChange(current) {
                this.page = current;
                this.data =[];
                this.getInfo(this.page,this.pageSize);
            },
        },
        created() {
            this.getCate().then(
                this.getInfo(this.page,this.pageSize),
            );
        }
    };
</script>
<style scoped lang="scss">
.ant-pagination{
    float:right;
    margin-top:10px !important;
}
</style>
