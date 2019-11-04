<template>
    <div class="product">
        商品信息:
        <a-form :form="form" @submit="handleSubmit">
            <a-form-item label="商品名" v-bind="formItemLayout">
                <a-input
                        v-decorator="['product_name',{ rules: [{ required: true, message: '请填写有效商品名' }], initialValue:product_name },]"
                        placeholder="请填写商品名"
                />
            </a-form-item>
            <a-form-item label="排序" v-bind="formItemLayout">
                <a-input-number
                        style="width: 100%"
                        :min="1"
                        v-decorator="['sort',{ rules: [{ required: true, message: '请输入有效排序值' }], initialValue:1 },]"
                />
            </a-form-item>
            <a-form-item label="分类" v-bind="formItemLayout">
                <a-select style="width: 100%" @change="cateChange"
                          v-decorator="['category',{ rules: [{ required: true, message: '分类不可以省略选择' }], initialValue:0 },]">
                    <a-select-option :value="key" v-for="(val,key) in cate">{{val.name}}</a-select-option>
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
                        <a-form-item label="图片" v-bind="formItemLayout">
                            <div class="clearfix">
                                <a-upload
                                        listType="picture-card"
                                        :fileList="fileList"
                                        @preview="imagePreview"
                                        @change="imageChange"
                                        :customRequest="customRequest"
                                        :remove="Remove"
                                        v-decorator="['img',{ rules: [{ required: true, message: '商品图片不可缺少' }] },]"
                                >
                                    <div v-if="fileList.length < 1">
                                        <a-icon type="plus"/>
                                        <div class="ant-upload-text">上传</div>
                                    </div>
                                </a-upload>
                                <a-modal :visible="previewVisible" :footer="null" @cancel="imageCancel">
                                    <img alt="example" style="width: 100%" :src="previewImage"/>
                                </a-modal>
                            </div>
                        </a-form-item>
                        <a-form-item label="保质期" v-bind="formItemLayout">
                            <a-range-picker style="width: 100%" @change="dateChange" v-decorator="['date']"/>
                        </a-form-item>
                        <a-form-item label="促销宣传语" v-bind="formItemLayout">
                            <a-input placeholder="" v-decorator="['slogan']"/>
                        </a-form-item>
            库存信息
            <a-form-item label="库存" v-bind="formItemLayout">
                <a-button class="editable-add-btn" @click="handleAdd">Add</a-button>
                <a-table :columns="columns" :dataSource="data" bordered :pagination="false">
                    <template
                            v-for="col in ['original_price', 'price', 'attr1','attr2','attr3','quantity','sort','status']"
                            :slot="col"
                            slot-scope="text, record, index"
                    >
                        <div :key="col">
                            <a-input
                                    v-if="record.editable &&(col == 'attr1'||col=='attr2'||col=='attr3')"
                                    style="margin: -5px 0"
                                    :value="text"
                                    @change="e => handleChange(e.target.value, record.key, col)"/>
                            <a-input-number
                                    v-else-if="record.editable &&(col == 'price'||col=='original_price')"
                                    style="margin: -5px 0"
                                    :min="0" :max="9999999" :step="0.01" v-model="text"
                                    @change="e => handleChange(text, record.key, col)"/>
                            <a-input-number
                                    v-else-if="record.editable && (col=='sort'|| col=='quantity')"
                                    style="margin: -5px 0"
                                    :min="1" :max="10" v-model="text"
                                    @change="e => handleChange(text, record.key, col)"/>
                            <template v-else>
                                {{text}}
                            </template>
                        </div>
                    </template>
                    <template slot="operation" slot-scope="text, record, index">
                        <div class="editable-row-operations">
                            <span v-if="record.editable">
                                <a @click="() => save(record.key)">保存</a>
                                <a @click="()=>cancel(record.key)">取消</a>
                            </span>
                            <span v-else>
                                <a @click="() => edit(record.key)">编辑</a>
                                <a-popconfirm title="是否删除该条?" @confirm="() => onDelete(record.key)">
                                    <a>删除</a>
                                </a-popconfirm>
                            </span>
                        </div>
                    </template>
                </a-table>
            </a-form-item>
            <a-form-item v-bind="formTailLayout">
                <a-button type="primary" html-type="submit">
                    保存
                </a-button>
            </a-form-item>
        </a-form>

    </div>
</template>
<script>
    import api from '../../api'
    import qs from 'qs'
    import {quillEditor} from 'vue-quill-editor'

    // let id = 0;
    const formItemLayout = {
        labelCol: {span: 3},
        wrapperCol: {span: 20},
    };
    const formTailLayout = {
        labelCol: {span: 3},
        wrapperCol: {span: 20, offset: 3},
    };
    const tagType = ['保质期', '促销宣传语', '图片'];

    export default {
        data() {
            return {
                data: [
                    {
                        key: 1,
                        original_price: 0,
                        price: 0,
                        attr1: '',
                        attr2: '',
                        attr3: '',
                        quantity: 0,
                        sort: 1,
                    }
                ],
                previewVisible: false,
                previewImage: '',
                fileList: [],
                content: '',
                count: 2,
                tagArr: [],
                editorOption: {},
                columns: [
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
                        title: 'operation',
                        dataIndex: 'operation',
                        scopedSlots: {customRender: 'operation'},
                    },
                ],
                column: [],
                cate: [],
                tag: {},
                tagList: [],
                formItemLayout,
                formTailLayout,
                tagType,
                product_name: '',
                category_id: 1,
                sale_num: '',
                date: '',
                form: this.$form.createForm(this, {name: 'dynamic_rule'}),
            }
        },
        methods: {
            getCate() {
                this.axios.get(api.CateGet).then(res => {
                    console.log(res.data.data);
                    if (res.data.status) {
                        res.data.data.data.forEach((val, key) => {
                            let json = JSON.parse(val.property);
                            this.cate.push(
                                {
                                    id: val.id,
                                    name: val.name,
                                    attr: json,
                                }
                            )
                        })
                        console.log(this.cate);
                        this.columns.unshift(
                            {
                                title: this.cate[0].attr.attr1 || '暂无',
                                dataIndex: 'attr1',
                                width: '10%',
                                scopedSlots: {customRender: 'attr1'},
                            },
                            {
                                title: this.cate[0].attr.attr2 || '暂无',
                                dataIndex: 'attr2',
                                width: '10%',
                                scopedSlots: {customRender: 'attr2'},
                            },
                            {
                                title: this.cate[0].attr.attr3 || '暂无',
                                dataIndex: 'attr3',
                                width: '10%',
                                scopedSlots: {customRender: 'attr3'},
                            }
                        )
                    }
                }).catch(err => {
                    console.log(err);
                })
            },

            cateChange(value) {
                console.log(value);
                // this.category_id = value;
                this.columns.splice(0, 3);
                this.columns.unshift(
                    {
                        title: this.cate[value].attr.attr1,
                        dataIndex: 'attr1',
                        width: '10%',
                        scopedSlots: {customRender: 'attr1'},
                    },
                    {
                        title: this.cate[value].attr.attr2 || '暂无',
                        dataIndex: 'attr2',
                        width: '10%',
                        scopedSlots: {customRender: 'attr2'},
                    },
                    {
                        title: this.cate[value].attr.attr3 || '暂无',
                        dataIndex: 'attr3',
                        width: '10%',
                        scopedSlots: {customRender: 'attr3'},
                    }
                );
            },
            handleChange(value, key, column) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    target[column] = value;
                    this.data = newData;
                }
            },
            edit(key) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    target.editable = true;
                    this.data = newData;
                }
            },
            save(key) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    delete target.editable;
                    this.data = newData;
                    console.log(target);
                }
            },
            cancel(key) {
                const newData = [...this.data];
                const target = newData.filter(item => key === item.key)[0];
                if (target) {
                    // Object.assign(target, this.cacheData.filter(item => key === item.key)[0]);
                    delete target.editable;
                    this.data = newData;
                }
            },
            imageCancel() {
                this.previewVisible = false;
            },
            Remove(file) {
                // this.fileList = [];
                console.log(file);
            },
            customRequest(file) {
                const formData = new FormData();
                formData.append("imageFile", file.file);
                this.toUpload(formData);
            },
            imagePreview(file) {
                this.previewImage = file.url || file.thumbUrl;
                this.previewVisible = true;
            },
            imageChange({fileList}) {
                this.fileList = fileList;
            },
            toUpload(file) {
                let config = {
                    headers: {"Content-Type": "multipart/form-data"}
                }; //添加请求头
                this.axios
                    .post(api.Upload, file, config)
                    .then(res => {
                        if (res.data.status) {
                           this.fileList.push(
                               {
                                   uid: '1',
                                   name: 'xxx.png',
                                   status: 'done',
                                   url: res.data.data.path,
                               },
                           )
                            this.previewImage = res.data.data.path,
                            console.log(res);
                        } else {
                            this.$message.error("上传失败");
                        }
                    });
            },
            dateChange(date, dateString) {
                console.log(date, dateString);
                this.date = dateString[0] + '~' + dateString[1];
            },
            onEditorReady(editor) { // 准备编辑器
            },
            onEditorBlur(value) {
                console.log(value);
                console.log(this.content);
            }, // 失去焦点事件
            onEditorFocus(value) {
            }, // 获得焦点事件
            onEditorChange(value) {
            }, // 内容改变事件
            handleAdd() {
                this.data.push({
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
            onDelete(key) {
                if (this.data.length <= 1) {
                    this.$message.error('库存不能为空', 2);
                } else {
                    const dataSource = [...this.data];
                    this.data = dataSource.filter(item => item.key !== key);
                }

            },
            handleSubmit(e) {
                e.preventDefault();
                this.form.validateFields((err, values) => {
                    if (!err) {
                        console.log('Received values of form: ', values);
                        console.log([...this.data]);
                        let tag = [{
                            'type': '3',
                            'value': this.previewImage,
                        }];
                        if (values.date != null) {
                            tag.push({
                                'type': '1',
                                'value': this.date,
                            })
                        }
                        if (values.slogan != '') {
                            tag.push({
                                'type': '2',
                                'value': values.slogan,
                            })
                        }
                        let data = {
                            'category_id': this.cate[values.category].id,
                            'name': values.product_name,
                            'content': this.content,
                            'sort': values.sort,
                            'sku': [...this.data],
                            'tag': tag,
                        };
                        this.axios.post(api.ProductCreate, JSON.stringify(data)).then(res => {
                            console.log(res);
                            if (res.data.status) {
                                this.$router.go(-1);
                            }
                        }).catch(err => {
                            console.log(err);
                        })
                    }
                });
            },

        },
        created() {
            this.getCate();
        }
    }
</script>
