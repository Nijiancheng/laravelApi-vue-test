<template>
    <a-form :form="form" @submit="handleSubmit">
        <a-form-item label="分类标题" v-bind="formItemLayout">
            <a-input placeholder="请输入标题名" v-decorator="[
          'cate_title',
          { rules: [{ required: true, message: '请输入分类标题' }]},
        ]"
                     style="width: 90%; margin-right: 8px"
            />
        </a-form-item>
        <a-form-item
                v-for="(k, index) in form.getFieldValue('keys')"
                :key="k"
                v-bind="formItemLayout"
                :label="'属性'"
                :required="false"
        >
            <a-input
                    v-decorator="[
          `attr[${k}]`,
          {
            rules: [
              {
                required: true,
                whitespace: true,
                message: '请输入有效属性值',
              },
            ],
          },
        ]"
                    style="width: 90%; margin-right: 8px"
            />
            <a-icon
                    v-if="form.getFieldValue('keys').length > 1"
                    class="dynamic-delete-button"
                    type="minus-circle-o"
                    :disabled="form.getFieldValue('keys').length === 1"
                    @click="() => remove(k)"
            />
        </a-form-item>
        <a-form-item :wrapper-col="{ span: 12, offset: 6 }" v-if="form.getFieldValue('keys').length < 3">
            <a-button type="dashed" @click="add" style="width: 75%;">
                <a-icon type="plus"/>
                添加属性
            </a-button>
        </a-form-item>
        <a-form-item label="排序" v-bind="formItemLayout">
            <a-input-number style="width: 90%;" :min="1" :max="10" v-model="sort" :defaultValue="sort" v-bind="formItemLayout"/>
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

    let id = 1;
    export default {
        data() {
            return {
                previewVisible: false,
                previewImage: '',
                formItemLayout: {
                    labelCol: {span: 6},
                    wrapperCol: {span: 10},
                },
                data: [],
                sort: 1,

            };
        },
        beforeCreate() {
            this.form = this.$form.createForm(this, {name: 'dynamic_form_item'});
            this.form.getFieldDecorator('keys', {initialValue: [], preserve: true});
        },
        methods: {

            handleSubmit(e) {
                e.preventDefault();
                this.form.validateFields((err, values) => {
                    if (!err) {
                        // console.log('Received values of form: ', values);
                        let property={};
                        let x = 1;
                        values.attr.forEach((v,k)=>{
                            console.log(v,k);
                                property[`attr${x}`] = v;
                                x++;
                        })
                        console.log(property);
                        let data = {
                            'name': values.cate_title,
                            'property': JSON.stringify(property),
                            'sort': this.sort
                        }
                        this.axios.post(api.CateAdd, qs.stringify(data)).then(res => {
                            if (res.data.status) {
                                this.$message.success('添加成功', 2);
                                this.$router.go(-1);
                            }else{
                                this.$message.error(res.data.msg,2);
                            }
                        }).catch(err => {
                            console.log(err);
                        })
                    }
                });
            },
            remove(k) {
                const {form} = this;
                // can use data-binding to get
                const keys = form.getFieldValue('keys');
                // We need at least one passenger
                if (keys.length === 1) {
                    return;
                }

                // can use data-binding to set
                form.setFieldsValue({
                    keys: keys.filter(key => key !== k),
                });
            },
            add() {
                const {form} = this;
                // can use data-binding to get
                const keys = form.getFieldValue('keys');
                const nextKeys = keys.concat(id++);
                // can use data-binding to set
                // important! notify form to detect changes
                form.setFieldsValue({
                    keys: nextKeys,
                });
            },
        },

        created() {
            this.add();
        }
    }
</script>
<style>

</style>
