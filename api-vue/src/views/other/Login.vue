<template>
<div class="login">
    <a-form
            id="components-form-demo-normal-login"
            :form="form"
            class="login-form"
            @submit="handleSubmit"
    >
        <a-form-item>
            <a-input
                    v-decorator="[
          'name',
          { rules: [{ required: true, message: '请输入有效用户名!' }] },
        ]"
                    placeholder="Username"
            >
                <a-icon slot="prefix" type="user" style="color: rgba(0,0,0,.25)" />
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-input
                    v-decorator="[
          'password',
          { rules: [{ required: true, message: '请输入有效密码!' }] },
        ]"
                    type="password"
                    placeholder="Password"
            >
                <a-icon slot="prefix" type="lock" style="color: rgba(0,0,0,.25)" />
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-button type="primary" html-type="submit" class="login-form-button">
                登录
            </a-button>

        </a-form-item>
    </a-form>
</div>
</template>
<script>
    import api from '../../api'
    import qs from 'qs'
    export default {

        beforeCreate() {
            this.form = this.$form.createForm(this, { name: 'normal_login' });
        },
        methods: {
            handleSubmit(e) {
                e.preventDefault();
                this.form.validateFields((err, values) => {
                    if (!err) {
                        // console.log('Received values of form: ', values);
                        let data = {
                            'name' : values['name'],
                            'password': values['password']
                        };
                        this.axios.post(api.Login,qs.stringify(data)).then(res=>{
                            console.log(res,'logininfo');
                            if(res.data.status){
                                localStorage.setItem('token',res.data.data.token);
                                localStorage.setItem('name',res.data.data.name);
                                this.$router.push('/table_product');
                            }else{
                                this.$message.error(res.data.msg,2);
                            }
                        }).catch(err=>{
                            console.log(err);
                        })
                    }
                });
            },
        },
    };
</script>
<style scoped lang="scss">
    #components-form-demo-normal-login {
        .login-form {
            max-width: 300px;
        }
        .login-form-forgot {
            float: right;
        }
        .login-form-button {
            width: 100%;
        }
    }
    .login{
        /*position:fixed;*/
        /*min-height:100% ;*/
        /*width: 100%;*/
        padding:80px 350px;
        box-sizing: border-box;;
    }

</style>
