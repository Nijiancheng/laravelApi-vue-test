<template>
    <a-form :form="form" @submit="handleSubmit">
        <a-form-item label="分类标题" v-bind="formItemLayout">
            <a-input  placeholder="请输入标题名"  v-decorator="[
          'cate_title',
          { rules: [{ required: true, message: '请输入分类标题' }]},
        ]"/>
        </a-form-item>
        <a-form-item label="属性1" v-bind="formItemLayout">
            <a-input  placeholder="请输入标题名" v-model="attr1"/>
        </a-form-item>
        <a-form-item label="属性2" v-bind="formItemLayout">
            <a-input  placeholder="请输入标题名" v-model="attr2"/>
        </a-form-item>
        <a-form-item label="属性2" v-bind="formItemLayout">
            <a-input  placeholder="请输入标题名" v-model="attr3"/>
        </a-form-item>
        <a-form-item label="排序" v-bind="formItemLayout">
            <a-input-number :min="1" :max="10" v-model="sort" :defaultValue="sort" v-bind="formItemLayout"/>
        </a-form-item>
        <a-form-item :wrapper-col="{ span: 12, offset: 6 }">
            <a-button  type="primary" html-type="submit">
                提交
            </a-button>
        </a-form-item>
    </a-form>
</template>
<script>
    import qs from "qs";
    import api from '../../api';

    export default {
        data() {
            return {
                previewVisible: false,
                previewImage: '',
                form: this.$form.createForm(this, {name: 'dynamic_rule'}),
                formItemLayout: {
                    labelCol: {span: 6},
                    wrapperCol: {span: 8},
                },
                data: [],
                cate_title:'',
                attr1:'',
                attr2:'',
                attr3:'',
                sort:1,
            };
        },
        methods: {
            changeTitle(value){
                console.log(value);
            },
            handleSubmit(e) {
                e.preventDefault();
                this.form.validateFields((err, values) => {
                    if (!err) {
                        console.log('Received values of form: ', values);
                        let property = {
                            'attr1':this.attr1,
                            'attr2':this.attr2,
                            'attr3':this.attr3
                        };
                        let data={
                                    'name':values.cate_title,
                                    'property': JSON.stringify(property),
                                    'sort':this.sort
                                }
                                this.axios.post(api.CateAdd,qs.stringify(data)).then(res=>{
                                    if(res.data.status){
                                        this.$message.success(res.data.msg, 3);
                                        this.$router.go(-1);
                                    }
                                }).catch(err=>{
                                    console.log(err);
                                })
                    }
                });
            },
        },

        created() {
        }
    }
</script>
<style>

</style>
