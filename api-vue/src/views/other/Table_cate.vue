<template>
    <div>
        <a-table :columns="columns" :dataSource="data" bordered :pagination="false" rowKey="id">
            <template
                    v-for="col in ['name', 'attr1','attr2','attr3','sort']"
                    :slot="col"
                    slot-scope="text, record, index"
            >
                <div :key="col">
                    <a-input
                            v-if="record.editable && col!='sort'"
                            style="margin: -5px 0"
                            :value="text"
                            @change="e => handleChange(e.target.value, record.id, col)"
                    />
                    <a-input-number
                            v-else-if="record.editable && col=='sort'"
                            style="margin: -5px 0"
                            :min="1" :max="10" v-model="text"
                            @change="e => handleChange(text, record.id, col)"/>
                    <template v-else
                    >{{text}}
                    </template
                    >
                </div>
            </template>

            <template slot="operation" slot-scope="text, record, index">
                <div class="editable-row-operations">
        <span v-if="record.editable">
            <a-button type="primary" @click="() => save(record.id)">保存</a-button>
            <a-button @click="()=>cancel(record.id)">取消</a-button>

        </span>
                    <span v-else>
         <a-button type="primary" @click="update(record.id)">修改</a-button>
          <a-popconfirm title="是否删除该条数据?" @confirm="() =>  del(record.id)">
               <a-icon slot="icon" type="question-circle-o" style="color: red"/>
              <a-button type="danger">删除</a-button>
          </a-popconfirm>
        </span>
                </div>
            </template>
        </a-table>
        <a-pagination @change="pageChange" :pageSize="pageSize" :current="page" :total="total"/>
    </div>
</template>
<script>
    const columns = [
        {
            title: '分类id',
            dataIndex: 'id',
            width: '8%',
            scopedSlots: {customRender: 'id'},
        },
        {
            title: '分类名',
            dataIndex: 'name',
            width: '13%',
            scopedSlots: {customRender: 'name'},
        },
        {
            title: '属性1',
            dataIndex: 'attr1',
            width: '15%',
            scopedSlots: {customRender: 'attr1'},
        },
        {
            title: '属性2',
            dataIndex: 'attr2',
            width: '15%',
            scopedSlots: {customRender: 'attr2'},
        },
        {
            title: '属性3',
            dataIndex: 'attr3',
            width: '15%',
            scopedSlots: {customRender: 'attr3'},
        },
        {
            title: '排序',
            dataIndex: 'sort',
            width: '8%',
            scopedSlots: {customRender: 'sort'},
        },
        {
            title: '操作',
            dataIndex: 'operation',
            scopedSlots: {customRender: 'operation'},
        },
    ];
    import qs from 'qs';
    import api from '../../api';

    var data = [];
    export default {
        data() {
            return {
                data,
                cacheData:[],
                columns,
                page: 1,
                pageSize: 2,
                total: null,
            };
        },
        methods: {
            getCate(page, pageSize) {
                console.log('aaaa');
                this.axios.get(api.CateGet, {
                    params: {
                        'page': page,
                        'perpage': pageSize,
                    }
                }).then(res => {
                    console.log(res.data.data);
                    if (res.data.status) {
                        this.total = res.data.data.total;
                        this.data = [];
                        res.data.data.data.forEach((val, key) => {
                            let json = JSON.parse(val.property);
                            this.data.push(
                                {
                                    id: val.id,
                                    name: val.name,
                                    attr1: json.attr1 ? json.attr1 : null,
                                    attr2: json.attr2 ? json.attr2 : null,
                                    attr3: json.attr3 ? json.attr3 : null,
                                    sort: val.sort,
                                    // status: val.status,
                                },
                            );
                        })
                        // this.cacheData = this.data;
                        this.cacheData = this.data.map(item => ({...item}));
                    }
                }).catch(err => {
                    console.log(err)
                })

            },
            handleChange(value, key, column) {
                // console.log(value,key,column,'ceshi');
                if(column == 'name' &&value == '' ){
                    this.$message.error('分类名不能为空');
                }
                const newData = [...this.data];
                const target = newData.filter(item => key === item.id)[0];
                if (target) {
                    target[column] = value;
                    this.data = newData;
                }
            },
            update(key) {
                const newData = [...this.data];
                // console.log(newData.item,'newData');
                const target = newData.filter(item => key === item.id)[0];
                if (target) {
                    target.editable = true;
                    this.data = newData;
                }
            },
            del(key) {
                console.log(key);
                let data = {
                    'id': key,
                }
                this.axios.post(api.CateDel, qs.stringify(data)).then(res => {
                    console.log(res);
                    if (res.data.status) {
                        this.$message.success(res.data.msg, 3);
                        const dataSource = [...this.data];
                        this.data = dataSource.filter(item => item.id !== key);
                    } else {
                        this.$message.error(res.data.msg, 3);
                    }
                }).catch(err => {
                    console.log(err);
                })
            },
            save(key) {
                console.log('save');
                const newData = [...this.data];
                const target = newData.filter(item => key === item.id)[0];
                if (target) {
                    if (this.toUpload(target)) {
                        delete target.editable;
                        this.data = newData;
                        this.cacheData = newData.map(item => ({...item}));
                    } else {
                        delete target.editable;
                        this.data = newData;
                    }
                }
            },
            cancel(key) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.id)[0];
                if (target) {
                    Object.assign(target, this.cacheData.filter(item => key === item.id)[0]);
                    // console.log(this.cacheData);
                    delete target.editable;
                    this.data = newData;
                }
            },
            toUpload(target) {
                if(target.name == ''){
                    this.$message.error('分类名不能为空');
                }else{
                    let property = JSON.stringify({
                        'attr1': target.attr1,
                        'attr2': target.attr2,
                        'attr3': target.attr3
                    });
                    let data = {
                        'id': target.id,
                        'property': property,
                        'name': target.name,
                        'sort': target.sort,
                        'status': target.status
                    };
                    this.axios.post(api.CateUpdate, qs.stringify(data)).then(res => {
                        console.log(res);
                        if (res.data.status) {
                            this.$message.success(res.data.msg, 3);
                            return true;
                        } else {
                            this.$message.error(res.data.msg, 3);
                            return false;
                        }
                    }).catch(err => {
                        console.log(err);
                    })
                }
            },
            pageChange(current) {
                this.page = current;
                this.data = [];
                this.getCate(this.page, this.pageSize);
            },
        },
        created() {
            this.getCate(this.page, this.pageSize);
        }
    };
</script>
<style scoped>
    .editable-row-operations a {
        margin-right: 8px;
    }

    .editable-add-btn {
        margin-bottom: 8px;
    }

    .ant-pagination {
        float: right;
        margin-top: 10px !important;
    }
</style>
