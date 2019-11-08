<template>
    <a-form :form="form" @submit="handleSubmit">
        <a-form-item label="导航标题" v-bind="formItemLayout">
            <a-input  placeholder="请输入标题名"
                      v-decorator="['nav_title',{ rules: [{ required: true, message: '请输入分类标题' }]},]"
            />
        </a-form-item>
        <a-form-item  v-bind="formItemLayout" label="导航位置" has-feedback >
            <a-select style="width: 100%"
                      v-decorator="['position_id',{ rules: [{ required: true, message: '请选择导航位置' }],initialValue:position_id},]"
            >
                <a-select-option :value="index+1"  v-for="(posName,index) in position" :key="index">{{posName}}</a-select-option>
            </a-select>
        </a-form-item>
        <!--图片-->
        <a-form-item label="图片" v-bind="formItemLayout">
            <a-upload
                    listType="picture-card"
                    :fileList="fileList"
                    @preview="handlePreview"
                    @change="imageChange"
                    :customRequest="customRequest"
            >
                <div v-if="fileList.length < 1">
                    <a-icon type="plus"/>
                    <div class="ant-upload-text">上传</div>
                </div>
            </a-upload>
            <a-modal :visible="previewVisible" :footer="null" @cancel="handleCancel">
                <img alt="example" style="width: 100%" :src="previewImage"/>
            </a-modal>
        </a-form-item>

        <!--链接类型-->
        <a-form-item   v-bind="formItemLayout" label="链接类型" has-feedback>
            <a-select  style="width: 100%" @change="handleProvinceChange"
                      v-decorator="['link_type',{ rules: [{ required: true, message: '请选择链接类型' }],initialValue:1},]"
            >
                <a-select-option  v-for="(province,index) in link_type_list" :key="province" :value="index+1"
                >
                    {{province}}
                </a-select-option
                >
            </a-select>
        </a-form-item>

        <a-form-item v-bind="formItemLayout" label="链接目标" has-feedback v-if="link_target_list">
            <a-select  style="width: 100%"
                       v-decorator="['link_target',{ rules: [{ required: true, message: '请选择链接类型' }],initialValue:1},]"
            >
                <a-select-option  v-for="(target,index) in link_target_list" :value="target.id" :key="index">
                    {{target.name}}</a-select-option>
            </a-select>
        </a-form-item>
        <a-form-item :wrapper-col="{ span: 12, offset: 6 }">
            <a-button type="primary" html-type="submit">
                提交
            </a-button>
        </a-form-item>
    </a-form>
</template>
<script>
    import qs from "qs";
    import api from '../../api';

    const link_type_list = ['商品分类页面', '商品购买页面', '商品活动页面', '店铺'];
    const position =['顶部导航', 'banner图', 'icon', '4张大图',];
    export default {
        data() {
            return {
                previewVisible: false,
                previewImage: '',
                form: this.$form.createForm(this, {name: 'dynamic_rule'}),
                formItemLayout: {
                    labelCol: {span: 6},
                    wrapperCol: {span: 10},
                },
                fileList: [],
                link_type_list,
                link_target_list:[],
                link:[],
                position,
                data: [],
                position_id:1,
                link_type: 1,
                link_target: '',
            };
        },
        methods: {
            getCate() {
                // 获取分类
                return new Promise((resolve, reject) => {
                    this.axios.get(api.CateGet).then(res => {
                        resolve(
                            this.link[1] = res.data.data.data,
                            this.link_target_list = this.link[1],
                        );
                    }).catch(err => {
                        reject(console.log(err));
                    })
                })
            },
            getProduct() {
                //获取商品
                return new Promise((resolve, reject) => {
                    this.axios.get(api.Product).then(res => {
                        console.log(res,'商品');
                        resolve(this.link[2] = res.data.data.data);
                        console.log(this.link[this.link_type].length);
                    }).catch(err => {
                        reject(console.log(err));
                    })
                })
            },
            handleCancel() {
                this.previewVisible = false;
            },
            customRequest(file) {
                const formData = new FormData();
                formData.append("imageFile", file.file);
                this.toUpload(formData);
            },
            handlePreview(file) {
                this.previewImage = file.url || file.thumbUrl;
                this.previewVisible = true;
            },
            imageChange({fileList}) {
                this.fileList = fileList;
                // console.log(fileList);
            },
            toUpload(file) {
                let config = {
                    headers: {"Content-Type":"multipart/form-data"}
                }; //添加请求头
                this.axios
                    .post(api.Upload, file, config)
                    .then(res => {
                        if (res.data.status) {
                            console.log(res.data.data);
                            this.fileList = [];
                            this.fileList.push({
                                uid:-1,
                                status : "done",
                                url:res.data.data.path,
                                fileKey:res.data.data.fileKey,
                            })
                        } else {
                            this.$message.error("上传失败");
                        }
                    });
            },
            handleProvinceChange(value) {
                console.log(value);

                this.link_type = value;
                this.link_target = 1;
                this.link_target_list = this.link[value];
            },
            handleSubmit(e) {
                e.preventDefault();
                this.form.validateFields((err, values) => {
                    if (!err) {
                        console.log('Received values of form: ', values);
                        let data={
                            'position_id':values.position_id,
                            'title':values.nav_title,
                            'picture':this.fileList.length>0?this.fileList[0].fileKey:'',
                            'link_type':values.link_type,
                            'link_target':this.link_target
                        }
                        this.axios.post(api.NavAdd,qs.stringify(data)).then(res=>{
                            if(res.data.status){
                                this.$router.push('/table_nav');
                            }
                        }).catch(err=>{
                            console.log(err);
                        })
                    }
                });
            },

        },
        created() {
            this.getCate()
                .then(() => {
                    this.getProduct();
                })

        }
    }
</script>
<style>

</style>
