<template>
<div>
    <a-table :dataSource="data" rowKey="id"  :pagination="false">
        <a-table-column title="id" dataIndex="id"  />
        <a-table-column title="分类id" dataIndex="category_id"  />
        <a-table-column title="商品名" dataIndex="name"  />
        <a-table-column title="销量" dataIndex="sale_num"  />
        <!--    <a-table-column title="商品描述" dataIndex="content" -->
        <a-table-column title="排序" dataIndex="sort"  />
        <a-table-column title="状态" dataIndex="status" />
        <a-table-column title="创建时间" dataIndex="created_at"  />
        <a-table-column title="更新时间" dataIndex="updated_at"  />

        <a-table-column title="操作"  dataindex="action">
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
                status,
                page:1,
                pageSize:2,
                total:null,
            };
        },
        methods: {
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
                                    status: this.status[val.status-1],
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
                this.axios.post(api.ProductDel,qs.stringify(data)).then(response=>{
                    console.log(response);
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
            this.getInfo(this.page,this.pageSize);
        }
    };
</script>
<style scoped lang="scss">
.ant-pagination{
    float:right;
    margin-top:10px !important;
}
</style>
