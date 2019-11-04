<template>
<a-form :form="form" >
    <a-form-item label="导航标题" v-bind="formItemLayout">
        <a-input  placeholder="请输入分类名" v-model="nav_title"/>
    </a-form-item>
    <a-form-item v-bind="formItemLayout" label="导航位置" has-feedback >
        <a-select style="width: 100%" v-model="position_id">
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
                :remove="Remove"
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
    <a-form-item v-bind="formItemLayout" label="链接类型" has-feedback>
        <a-select  style="width: 100%" @change="handleProvinceChange" v-model="link_type">
            <a-select-option  v-for="(province,index) in link_type_list" :key="province" :value="index+1"
            >{{province}}</a-select-option
            >
        </a-select>
    </a-form-item>
    <a-form-item v-bind="formItemLayout" label="链接目标" has-feedback >
        <a-select  style="width: 100%" v-model="link_target" v-if="link[link_type]">
            <a-select-option :value="target.id" v-for="(target,index) in link[link_type]" :key="index">{{target.name}}</a-select-option>
        </a-select>
    </a-form-item>

    <a-form-item :wrapper-col="{ span: 12, offset: 6 }">
        <a-button  type="primary" @click="submitInfo">
            修改
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
                id: '',
                previewVisible: false,
                previewImage: '',
                form: this.$form.createForm(this, {name: 'dynamic_rule'}),
                formItemLayout: {
                    labelCol: {span: 6},
                    wrapperCol: {span: 14},
                },
                fileList: [],
                link_type_list,
                link_target_list:[],
                link:[],
                position,
                data: [],
                nav_title:'',
                position_id:'',
                img_path:'',
                link_type: '',
                link_target: '',
            };
        },
        methods: {
            getNav() {
                return new Promise((resolve, reject) => {

                    console.log(this.$route.query.id);
                    this.id = this.id == '' ? this.$route.query.id : this.id;

                    this.axios.get(api.NavUpdate, {
                        params: {
                            'id': this.id
                        }
                    }).then(res => {
                        console.log(res,'信息');
                        resolve(
                            this.data = res.data.data,
                            this.nav_title = res.data.data.title,
                            this.link_type = res.data.data.link_type,
                            this.link_target = res.data.data.link_target,
                            this.img_path = res.data.data.picture,
                            this.position_id = res.data.data.position_id,
                            res.data.data.picture ==null?'':this.fileList['0']=  {
                                uid: '1',
                                name: 'xxx.png',
                                status: 'done',
                                url: res.data.data.picture,
                            }
                        );
                        // console.log(this.data);
                    }).catch(err => {
                        reject(console.log(err));
                    })
                })
            },
            getCate() {
                // 获取分类
                return new Promise((resolve, reject) => {

                    this.axios.get(api.CateGet).then(res => {
                        console.log(res.data.data.data,'fenlei');
                        resolve(this.link['1'] = res.data.data.data);
                    }).catch(err => {
                        reject(console.log(err));
                    })
                })
            },
            getProduct() {
                //获取商品
                return new Promise((resolve, reject) => {
                    this.axios.get(api.Product).then(res => {
// console.log(res);                        console.log(res.data.data.data,'商品');
                        resolve(this.link['2'] = res.data.data.data);
                        console.log(this.link);
                    }).catch(err => {
                        reject(console.log(err));
                    })
                })
            },
            handleCancel() {
                this.previewVisible = false;
            },
            Remove() {
                this.fileList = [];
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
                            this.fileList[0].status = "done";
                            this.fileList[0].url = res.data.data.path;
                            // this.fileList[0].name = res.data.data.originalName;
                            this.previewImage = res.data.data.path;
                            this.img_path = res.data.data.path;
                            console.log(res);
                        } else {
                            this.$message.error("上传失败");
                        }
                    });
            },
            handleProvinceChange(value) {
                console.log(value);
                this.link_type = value;
                this.link_target = 1;
                this.link_target_list = this.link[value-1];
            },
            submitInfo(){
                let data={
                    'id':this.id,
                    'position_id':this.position_id,
                    'title':this.nav_title,
                    'picture':this.img_path,
                    'link_type':this.link_type,
                    'link_target':this.link_target
                }
                this.axios.post(api.NavUpdate,qs.stringify(data)).then(res=>{
                        if(res.data.status){
                            this.$router.go(-1);
                        }
                }).catch(err=>{
                    console.log(err);
                })

            }
        },
        created() {
            this.getCate()
                .then(() => {
                    this.getProduct();
                }).then(() => {
                this.getNav();
            })

        }
    }
</script>
<style>

</style>
