<template>
<div class="reg">
    <a-form :form="form" @submit="handleSubmit">
        <a-form-item v-bind="formItemLayout" label="用户名">
            <a-input
                    v-decorator="[
          'name',
          {
            rules: [{ required: true, message: '请输入有效用户名!', whitespace: true }],
          },
        ]"
            />
        </a-form-item>
        <a-form-item v-bind="formItemLayout" label="邮箱">
            <a-input
                    v-decorator="[
          'email',
          {
            rules: [
              {
                type: 'email',
                message: '请输入有效的邮箱地址!',
              },
              {
                required: true,
                message: '请输入你的邮箱地址!',
              },
            ],
          },
        ]"
            />
        </a-form-item>
        <a-form-item v-bind="formItemLayout" label="密码">
            <a-input
                    v-decorator="[
          'password',
          {
            rules: [
              {
                required: true,
                message: '请输入你的密码!',
              },
              {
                validator: validateToNextPassword,
              },
            ],
          },
        ]"
                    type="password"
            />
        </a-form-item>
        <a-form-item v-bind="formItemLayout" label="重复密码">
            <a-input
                    v-decorator="[
          'confirm',
          {
            rules: [
              {
                required: true,
                message: '请再次输入你的密码!',
              },
              {
                validator: compareToFirstPassword,
              },
            ],
          },
        ]"
                    type="password"
                    @blur="handleConfirmBlur"
            />
        </a-form-item>

<!--        <a-form-item v-bind="formItemLayout" label="Phone Number">-->
<!--            <a-input-->
<!--                    v-decorator="[-->
<!--          'phone',-->
<!--          {-->
<!--            rules: [{ required: true, message: 'Please input your phone number!' }],-->
<!--          },-->
<!--        ]"-->
<!--                    style="width: 100%"-->
<!--            >-->
<!--                <a-select-->
<!--                        slot="addonBefore"-->
<!--                        v-decorator="['prefix', { initialValue: '86' }]"-->
<!--                        style="width: 70px"-->
<!--                >-->
<!--                    <a-select-option value="86">-->
<!--                        +86-->
<!--                    </a-select-option>-->
<!--                    <a-select-option value="87">-->
<!--                        +87-->
<!--                    </a-select-option>-->
<!--                </a-select>-->
<!--            </a-input>-->
<!--        </a-form-item>-->
        <a-form-item v-bind="tailFormItemLayout">
            <a-button type="primary" html-type="submit">
                注册
            </a-button>
        </a-form-item>
    </a-form>
</div>
</template>
<script>
    import api from '../../api'
    import qs from 'qs'
    export default {
        data() {
            return {
                confirmDirty: false,
                formItemLayout: {
                    labelCol: {
                        xs: { span: 20 },
                        sm: { span: 8 },
                    },
                    wrapperCol: {
                        xs: { span: 8 },
                        sm: { span: 8 },
                    },
                },
                tailFormItemLayout: {
                    wrapperCol: {
                        xs: {
                            span: 24,
                            offset: 0,
                        },
                        sm: {
                            span: 16,
                            offset: 8,
                        },
                    },
                },
            };
        },
        beforeCreate() {
            this.form = this.$form.createForm(this, { name: 'register' });
        },
        methods: {
            handleSubmit(e) {
                e.preventDefault();
                this.form.validateFieldsAndScroll((err, values) => {
                    if (!err) {
                        console.log('Received values of form: ', values);
                        let data = {
                            'name':values['name'],
                            'email':values['email'],
                            'password':values['password']
                        }
                        this.axios.post(api.Reg,qs.stringify(data)).then(res=>{
                            console.log(res);
                            if(res.data.status){
                                localStorage.setItem('token',res.data.data.token);
                                this.$message.success('注册成功',2);
                                this.$router.push('/');
                            }else{
                                this.$message.error(res.data.msg,2);
                            }
                        }).catch(err=>{
                            console.log(err.response);
                            if (err.response.data.error){

                            }
                        })
                    }
                });
            },
            handleConfirmBlur(e) {
                const value = e.target.value;
                this.confirmDirty = this.confirmDirty || !!value;
            },
            compareToFirstPassword(rule, value, callback) {
                const form = this.form;
                if (value && value !== form.getFieldValue('password')) {
                    callback('两次输入密码不一致!');
                } else {
                    callback();
                }
            },
            validateToNextPassword(rule, value, callback) {
                const form = this.form;
                if (value && this.confirmDirty) {
                    form.validateFields(['confirm'], { force: true });
                }
                callback();
            }
        },
    };
</script>
