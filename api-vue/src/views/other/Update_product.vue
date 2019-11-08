<template>
    <div class="product">
        商品信息:
        <a-form :form="form"  @submit="handleSubmit">
            <a-form-item label="商品名" v-bind="formItemLayout">
                <a-input
                        v-decorator="['product_name',{ rules: [{ required: true, message: '请填写有效商品名' }], initialValue:product_name },]"
                        placeholder="请填写商品名"
                />
            </a-form-item>
            <a-form-item label="销量" v-bind="formItemLayout">
                <a-input disabled v-model="sale_num" style="width: 100%"/>
            </a-form-item>
            <a-form-item  label="排序" v-bind="formItemLayout">
                <a-input-number
                        style="width: 100%"
                        :min="1"
                        v-decorator="['sort',{ rules: [{ required: true, message: '请输入有效排序值' }], initialValue: sort },]"
                />
            </a-form-item>
            <a-form-item label="状态" v-bind="formItemLayout">
                <a-select
                        style="width: 100%"
                        v-decorator="['status',{ rules: [{ required: true, message: '状态不可以省略选择' }], initialValue:status },]"
                >
                    <a-select-option :value="index+1" v-for="(val,index) in statusList">{{val}}</a-select-option>
                </a-select>
            </a-form-item>
            <a-form-item label="分类" v-bind="formItemLayout">
                <a-select
                        disabled
                        style="width: 100%"
                        @change="cateChange"
                        v-decorator="['category',{ rules: [{ required: true, message: '分类不可以省略选择' }], initialValue:category_id },]"
                >
                    <a-select-option :value="val.id" v-for="val in cate">{{val.name}}</a-select-option>
                </a-select>
            </a-form-item>
            <a-form-item label="商品描述" v-bind="formItemLayout">
                <div class="edit_container">
                    <quill-editor
                            v-model="content"
                            ref="myQuillEditor"
                            :options="editorOption"
                            @blur="onEditorBlur($event)" @focus="onEditorFocus($event)"
                            @change="onEditorChange($event)">
                    </quill-editor>
                </div>
            </a-form-item>
            标签信息


            <div class="tag" >
                <a-form-item :label="'标签1'" v-bind="formItemLayout" >
                    <a-select  style="width: 100%"  disabled defaultValue="图片" >
                        <a-select-option value="3">图片</a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="图片" v-bind="formItemLayout" >
                    <div class="clearfix">
                        <a-upload
                                :customRequest="customRequest"
                                listType="picture-card"
                                :fileList="fileList"
                                @preview="imagePreview"
                                @change="imageChange"

                                v-decorator="['img',{ rules: [{ required: true, message: '商品图片不可缺少' }] ,initialValue:fileList},]"
                        >
                            <div v-if="fileList.length < 5">
                                <a-icon type="plus" />
                                <div class="ant-upload-text">上传</div>
                            </div>
                        </a-upload>
                        <a-modal :visible="previewVisible" :footer="null" @cancel="imageCancel">
                            <img alt="example" style="width: 100%" :src="previewImage" />
                        </a-modal>
                    </div>
                </a-form-item>
            </div>
            <div class="tag" v-for="(v,k) in tag" v-if="tag[k].type ==1||tag[k].type ==2">
                <a-form-item :label="'标签'+(k+2)" v-bind="formItemLayout" >
                    <a-select  style="width: 100%"   disabled  @change="handleSelectChange" v-model="tag[k].type">
                        <a-select-option :value="key+1" v-for="(val,key) in tagType">{{val}}</a-select-option>
                    </a-select>
                </a-form-item>
                <a-form-item label="保质期" v-bind="formItemLayout" v-if="tag[k].type ==1">
                    <a-date-picker style="width: 100%"   @change="dateChange" :format="dateFormat"   v-decorator="['date',{rules: [{ required: true, message: '值不能为空' }],initialValue:moment(date,dateFormat)}]"/>
                </a-form-item>
                <a-form-item label="促销宣传语" v-bind="formItemLayout" v-else-if="tag[k].type ==2">
                    <a-input placeholder="Basic usage"  v-decorator="['slogan',{rules: [{ required: true, message: '值不能为空' }],initialValue:slogan}]"/>
                </a-form-item>
            </div>


            库存信息
            <a-form-item label="库存" v-bind="formItemLayout">
                <a-table :columns="columns" :dataSource="data" bordered :pagination="false" rowKey="id">
                    <template
                            v-for="col in ['original_price', 'price', 'attr1','attr2','attr3','quantity','sort','status']"
                            :slot="col"
                            slot-scope="text, record, index"
                    >
                        <div :key="col">
                            <a-input
                                    v-if="record.editable && (col == 'attr1'||col=='attr2'||col=='attr3')"
                                    style="margin: -5px 0"
                                    :value="text"
                                    @change="e => handleChange(e.target.value, record.id, col)"/>
                            <a-input-number
                                    v-else-if="record.editable &&(col == 'price'||col=='original_price')"
                                    style="margin: -5px 0"
                                    :min="0" :max="9999999" :step="0.01" v-model="text"
                                    @change="e => handleChange(text, record.id, col)"/>
                            <a-input-number
                                    v-else-if="record.editable && (col=='sort'|| col=='quantity')"
                                    style="margin: -5px 0"
                                    :min="1" :max="10" v-model="text"
                                    @change="e => handleChange(text, record.id, col)"/>
                            <template v-else>
                                {{text}}
                            </template>
                        </div>
                    </template>
                    <template slot="operation" slot-scope="text, record, index">
                        <div class="editable-row-operations">
                            <span v-if="record.editable">
                                <a @click="() => save(record.id)">保存</a><br>
                                <a @click="()=>cancel(record.id)">取消</a>
                            </span>
                            <span v-else>
                                <a @click="() => edit(record.id)">编辑</a><br>
                                <a @click="() => cut(record.id)">统一删除</a><br>
                                <a-popconfirm title="是否删除该条?" @confirm="() => del(record.id)">
                                    <a>立即删除</a>
                                </a-popconfirm>
                            </span>
                        </div>
                    </template>
                </a-table>
            </a-form-item >
            <a-form-item v-bind="formTailLayout">
                <a-button type="primary" html-type="submit" >
                    保存
                </a-button>
            </a-form-item>
        </a-form>
        添加库存
        <a-form-item label="库存" v-bind="formItemLayout">
            <a-button class="editable-add-btn" @click="handleAdd">Add</a-button>
            <a-table :columns="columns" :dataSource="dataSource" bordered :pagination="false" >
                <template
                        v-for="col in ['original_price', 'price', 'attr1','attr2','attr3','quantity','sort','status']"
                        :slot="col"
                        slot-scope="text, record, index"
                >
                    <div :key="col">
                        <a-input
                                v-decorator="['${col}${index}', { rules: [{ required: true, message: '不能为空' }] }]"
                                v-if="record.editable &&(col == 'attr1'||col=='attr2'||col=='attr3')"
                                style="margin: -5px 0"
                                :value="text"
                                @change="e => addSkuChange(e.target.value, record.key, col)"/>
                        <a-input-number
                                v-else-if="record.editable &&(col == 'price'||col=='original_price')"
                                style="margin: -5px 0"
                                :min="0" :max="9999999" :step="0.01" v-model="text"
                                @change="e => addSkuChange(text, record.key, col)"/>
                        <a-input-number
                                v-else-if="record.editable && (col=='sort'|| col=='quantity')"
                                style="margin: -5px 0"
                                :min="1" :max="10" v-model="text"
                                @change="e => addSkuChange(text, record.key, col)"/>
                        <template v-else>
                            {{text}}
                        </template>
                    </div>
                </template>
                <template slot="operation" slot-scope="text, record, index">
                    <div class="editable-row-operations">
                            <span v-if="record.editable">
                                <a @click="() => skuSave(record.key)">提交</a><br>
                                <a @click="()=>skuCancel(record.key)">取消</a>
                            </span>
                        <span v-else>
                                <a @click="() => skuEdit(record.key)">编辑</a>&nbsp;
                                <a-popconfirm title="是否删除该条?" @confirm="() => skuDelete(record.key)">
                                    <a>删除</a>
                                </a-popconfirm>
                            </span>
                    </div>
                </template>
            </a-table>
        </a-form-item >
    </div>
</template>
<script>
    import api from '../../api'
    import qs from 'qs'
    import {quillEditor} from 'vue-quill-editor'
    import moment from 'moment'

    const formItemLayout = {
        labelCol: {span: 3},
        wrapperCol: {span: 20},
    };
    const formTailLayout = {
        labelCol: {span: 3},
        wrapperCol: {span: 20, offset: 3},
    };
    const tagType=['保质期','促销宣传语','图片'];
    const statusList=['上架','下架'];
    // var data =[];
    // var cacheData=[];
    export default {
        data() {
            return {
                id: '',
                data:[],
                cacheData:[],
                columns:[
                    {
                        title: '原价',
                        dataIndex: 'original_price',
                        width: '14%',
                        scopedSlots: {customRender: 'original_price'},
                    },
                    {
                        title: '售价',
                        dataIndex: 'price',
                        width: '14%',
                        scopedSlots: {customRender: 'price'},
                    },

                    {
                        title: '库存',
                        dataIndex: 'quantity',
                        width: '14%',
                        scopedSlots: {customRender: 'quantity'},
                    },
                    {
                        title: '排序',
                        dataIndex: 'sort',
                        width: '14%',
                        scopedSlots: {customRender: 'sort'},
                    },
                    {
                        title: '操作',
                        dataIndex: 'operation',
                        scopedSlots: {customRender: 'operation'},
                    },
                ],
                dataSource:[],
                cacheDataSource:[],
                count:2,
                previewVisible: false,
                previewImage: '',
                fileList: [],
                content: '',
                editorOption: {},
                dateFormat:'YYYY-MM-DD',
                column: [],
                cate: {},
                tag:{},
                tagList:[],
                formItemLayout,
                formTailLayout,
                tagType,
                statusList,
                product_name: '',
                category_id: '',
                sale_num:'',
                sort:'',
                slogan:'',
                status:'',
                date:null,
                form: this.$form.createForm(this, {name: 'dynamic_rule'}),
                imageId:-1,
            }
        },
        methods: {
            moment,
            getCate() {
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
                            // console.log(this.cate);
                        }

                    }).catch(err => {
                        reject(console.log(err));
                    })
                })
            },
            getProduct() {
                this.id = this.$route.query.id;
                this.axios.get(api.ProductGet, {
                    params: {
                        'id': this.id,
                    }
                }).then(res => {
                    if (res.data.status) {
                        let data = res.data.data[0];
                        this.product_name = data.name;
                        this.category_id = data.category_id;
                        this.sale_num = data.sale_num;
                        this.content = data.content;
                        this.tag = data.tag;
                        this.sort = data.sort;
                        this.status = Number(data.status);
                        data.tag.forEach((v,k)=>{
                            // console.log(v,'图片');
                            if(v.type == 3){
                                this.fileList.push(
                                    {
                                        uid: this.imageId,
                                        id:v.id,
                                        name: 'xxx.png',
                                        status: 'done',
                                        url: v.value,
                                        fileKey:'',
                                    }
                                )
                                this.imageId--;
                            }else if(v.type ==2){
                                this.slogan = v.value;
                            }else if(v.type ==1){
                                this.date =v.value;
                            }
                        })
                        for(let k in this.cate[data.category_id].attr){
                            if(this.cate[data.category_id].attr[k] !='' && this.cate[data.category_id].attr[k] !=null){
                                this.columns.unshift(
                                    {
                                        title: this.cate[data.category_id].attr[k],
                                        dataIndex: k,
                                        width: '10%',
                                        scopedSlots: {customRender: k},
                                    },
                                )
                            }
                        }
                        data.sku.forEach((val, key) => {
                            this.data = data.sku;
                        })
                        this.dataSource.push({
                            key: 1,
                            original_price: 0,
                            price: 0,
                            quantity: 0,
                            sort: 1,
                        })
                        this.cacheData = this.data.map(item => ({...item}));
                        this.cacheDataSource = this.dataSource.map(item => ({...item}));

1                    }
                }).catch(err => {
                    console.log(err);
                })
            },
            cateChange(value){},
            handleChange(value, key, column) {
                    const newData = [...this.data];
                    const target = newData.filter(item => key === item.id)[0];
                console.log(this.data);
                console.log(this.cacheData);
                    if (target) {
                        target[column] = value;
                        this.data = newData;
                    }
            },
            handleSelectChange(value) {},
            edit(key) {
                // console.log(key);
                const newData = [...this.data];
                const target = newData.filter(item => key === item.id)[0];
                if (target) {
                    target.editable = true;
                    this.data = newData;
                }
            },
            del(key){
                console.log(key);
                if(this.data.length <=1){
                    this.$message.error('库存不能为空',2);
                }else{
                    let data={
                        'id':key,
                    }
                    this.axios.post(api.SkuDelete,qs.stringify(data)).then(res=>{
                        console.log(res);
                        if(res.data.status){
                            this.$message.success('库存删除成功',2);
                            const dataSource = [...this.data];
                            this.data = dataSource.filter(item => item.id !== key);
                        }else{
                            this.$message.error(res.data.msg,2);
                        }
                    }).catch(err=>{
                        console.log(err);
                    })

                }
            },
            cut(key){
                console.log(key);
                if(this.data.length <1){
                    this.$message.error('库存不能为空');
                }else{
                    const dataSource = [...this.data];
                    this.data = dataSource.filter(item => item.id !== key);
                }

            },
            save(key) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.id)[0];
                // console.log(target);
                if(target){
                    delete target.editable;
                    this.data = newData;
                    this.cacheData = newData.map(item => ({ ...item }));
                }
            },

            cancel(key) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.id)[0];
                console.log(this.data,'data');
                console.log(this.cacheData,'cacheData');
                if (target) {
                    Object.assign(target, this.cacheData.filter(item => key === item.id)[0]);
                    delete target.editable;
                    this.data = newData;
                }
            },
            addSkuChange(value, key, column) {
                const newData = [...this.dataSource];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    target[column] = value;
                    this.dataSource = newData;
                }
            },
            skuEdit(key) {
                const newData = [...this.dataSource];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    target.editable = true;
                    this.dataSource = newData;
                }
            },
            skuSave(key) {
                const newData = [...this.dataSource];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    delete target.editable;
                    this.dataSource = newData;
                    this.cacheDataSource = newData.map(item => ({ ...item }));
                    // console.log(target);
                    let data ={
                        'product_id':this.id,
                        'original_price':target.original_price,
                        'price':target.price,
                        'quantity':target.quantity,
                        'sort':target.sort,
                        'attr1':target.attr1,
                        'attr2':target.attr2,
                        'attr3':target.attr3,
                    }
                    this.axios.post(api.SkuCreate,qs.stringify(data)).then(res=>{
                        // console.log(res);
                        if(res.data.status){
                            this.$message.success('库存新增成功',2);
                            this.data.push(
                               res.data.data
                            );
                        }else{
                            this.$message.error('库存新增失败',2);
                        }
                    }).catch(err=>{
                        console.log(err);
                    })
                }
            },
            skuCancel(key) {
                console.log(key);
                const newData = [...this.dataSource];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    Object.assign(target, this.cacheDataSource.filter(item => key === item.key)[0]);
                    delete target.editable;
                    this.dataSource = newData;
                }
            },
            skuDelete(key) {
                if(this.dataSource.length <=1){
                    this.$message.error('库存不能为空',2);
                }else{
                    const dataSource = [...this.dataSource];
                    this.dataSource = dataSource.filter(item => item.key !== key);
                }

            },
            handleAdd() {
                this.dataSource.push({
                    key: this.count,
                    original_price: 0,
                    price: 0,
                    attr1: '',
                    attr2: '',
                    attr3: '',
                    quantity: 0,
                    sort: 1,
                });
                this.count = this.count + 1;
            },
            submitSku(key){
                const newData = [...this.data];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    delete target.editable;
                    this.data = newData;
                }
            },
            imageCancel() {
                this.previewVisible = false;
            },
            customRequest(file) {
                const formData = new FormData();
                formData.append("imageFile", file.file);
                this.toUpload(formData);
            },
            //查看原图
            imagePreview(file) {
                this.previewImage = file.url || file.thumbUrl;
                this.previewVisible = true;
            },
            //上传值改变
            imageChange({fileList,file,event}) {
                this.fileList = fileList;
                console.log(this.fileList);
            },
            toUpload(file) {
                let config = {
                    headers: {"Content-Type":"multipart/form-data"}
                }; //添加请求头
                this.axios
                    .post(api.Upload, file, config)
                    .then(res => {
                        if (res.data.status) {
                            this.fileList.pop();
                            this.fileList.push({
                                'id':-1,
                                'uid':this.imageId,
                                'name':res.data.data.originalName,
                                'statdus':"done",
                                'url':res.data.data.path,
                                'fileKey':res.data.data.fileKey,
                            })
                            this.imageId--;
                            this.$message.success('图片上传成功',2);
                        } else {
                            this.$message.error("上传失败");
                        }
                    });
            },
            // 时间选择器变化
            dateChange(date, dateString) {
                this.date = dateString;
            },
            onEditorReady(editor) {},// 准备编辑器
            onEditorBlur(value){}, // 失去焦点事件
            onEditorFocus(value){}, // 获得焦点事件
            onEditorChange(value){}, // 内容改变事件
            handleSubmit(e) {
                e.preventDefault();
                this.form.validateFields((err, values) => {
                    if (!err) {
                        let tag = [];
                        this.tag.forEach((v,k)=>{
                            if(v.type == 1){
                                tag.push({
                                    'id' : v.id,
                                    'product_id':v.product_id,
                                    'value':this.date,
                                    'type' : 1,
                                });
                            }else if(v.type == 2){
                                tag.push({
                                    'id' : v.id,
                                    'product_id':v.product_id,
                                    'value':this.slogan,
                                    'type' : 2,
                                });
                            }
                        })
                        this.fileList.forEach((val,key)=>{
                            tag.push({
                                'id' : val.id,
                                'value': val.url,
                                'product_id': this.id,
                                'type':3,
                                'fileKey':val.fileKey,
                            })
                        })
                        let data = {
                            'id':this.id,
                            'category_id': this.cate[values.category].id,
                            'name':values.product_name,
                            'content':this.content,
                            'status':values.status,
                            'sort':values.sort,
                            'sku':[...this.data],
                            'tag':tag,
                        };
                        this.axios.post(api.ProductUpdate ,JSON.stringify(data)).then(res=>{
                            // console.log(res);
                            if(res.data.status){
                                this.$message.success('编辑成功',2);
                                this.$router.go(-1);
                            }else{
                                this.$message.error(res.data.msg,2);
                            }
                        }).catch(err=>{
                            this.$message.error('编辑失败',2);
                            console.log(err);
                        })
                    }
                });
            },
        },

        created() {
            this.getCate().then(
                this.getProduct()
            )
        }
    }
</script>
