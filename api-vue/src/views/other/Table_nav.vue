<template>
    <div>
        <a-table :dataSource="data" :pagination="false" rowKey ="id">
            <a-table-column title="id" dataIndex="id" />
            <a-table-column title="位置" dataIndex="position_id" />
            <a-table-column title="导航标题" dataIndex="title" />
            <a-table-column title="导航图片" dataIndex="picture" >
                <template slot-scope="picture" v-if="picture">
                    <img :src="picture" alt="" style="width: 50px ;height: 50px">
                </template>
            </a-table-column>
            <a-table-column title="链接类型" dataIndex="link_type" />
            <a-table-column title="链接目标" dataIndex="link_target" />

            <a-table-column title="创建时间" dataIndex="created_at" />
            <a-table-column title="更新时间" dataIndex="updated_at" />

            <a-table-column title="操作"  dataIndex="action">
                <template slot-scope="text, record">
            <span>
              <a-button type="primary" @click="update(record.id)">修改</a-button>
              <a-popconfirm  title="你确定要删除该数据吗?" @confirm="() => onDelete(record.id)" >
                <a-icon slot="icon" type="question-circle-o" style="color: red"/>
                <a-button type="danger">删除</a-button>
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
    const link_type_list = ['商品分类页面', '商品购买页面', '商品活动页面', '店铺'];
    const position =['顶部导航', 'banner图', 'icon', '4张大图',];
    export default {
        data() {
            return {
                data: [],
                link_type_list,
                position,
                page:1,
                pageSize:2,
                total:null,
            };
        },
        methods: {
            getInfo(page,pageSize) {
                this.axios
                    .get(api.NavGet,{
                        params:{
                            'page':page,
                            'perpage':pageSize,
                        }
                    })
                    .then(response => {
                        console.log(response);
                        this.total = response.data.data.total;
                        response.data.data.data.forEach((val, key) => {
                            this.data.push(
                                {
                                    id: val.id,
                                    position_id: this.position[val.position_id-1],
                                    title: val.title,
                                    picture: val.picture,
                                    link_type: this.link_type_list[val.link_type-1],
                                    link_target: val.link_target,
                                    // status: val.status,
                                    created_at: val.created_at,
                                    updated_at: val.updated_at
                                },
                            );
                        })

                    }).catch(err => {
                    console.log(err);
                })
            },
            onDelete(key) {
                    let data ={
                        'id':key,
                    }
                    this.axios.post(api.NavDel,qs.stringify(data)).then(res=>{
                       if(res.data.status){
                           this.$message.success(res.data.msg, 3);
                           const dataSource = [...this.data];
                           this.data = dataSource.filter(item => item.key !== key);
                       }else{
                           this.$message.error(res.data.msg, 3);
                       }
                        console.log(response);
                    }).catch(err=>{
                        console.log(err);
                    })

            },
            update(id) {
                console.log(id);
                this.$router.push({name: 'update_nav', query: {id: id}});
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
    }
</script>
<style scoped lang="scss">
    .ant-pagination{
        float:right;
        margin-top:10px !important;
    }
</style>
