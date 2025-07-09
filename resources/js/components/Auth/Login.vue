<template>
  <div class="sa-student-profile-login">
    <div>
      <el-form
        :model="loginForm"
        ref="loginFormRef"
        :rules="rules"
        label-position="top"
        label-width="auto"
        class="login-form"
        validate-on-blur
        validate-on-change>
        <h3 class="title">Welcome</h3>
        <p class="subTitle">Please login to your account</p>
        <el-form-item label="Username or Email" prop="email">
          <el-input
            v-model="loginForm.email"
            placeholder="Enter Your Email Address Here"></el-input>
        </el-form-item>

        <el-form-item label="Password" prop="password">
          <el-input
            type="password"
            v-model="loginForm.password"
            placeholder="Enter Your Password Here"></el-input>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleLogin" class="login-button"
            >Login In</el-button
          >
        </el-form-item>

        <el-form-item prop="remember_me">
          <div class="form-actions">
            <el-checkbox v-model="loginForm.remember_me"
              >Remember Me</el-checkbox
            >
            <span class="forgot-password" @click="navigateToForgotPassword"
              >Forgot Password?</span
            >
          </div>
        </el-form-item>

        <el-form-item>
          <span
            >Don't have an account?
            <span class="register-link" @click="navigateToRegister"
              >Register Now</span
            ></span
          >
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAppHelper } from '../../Composable/appHelper';
import AlertMessage from '../../Composable/AlertMessage';
import { loader } from '../../Composable/Loader';

export default {
  name: 'Login',
  components: {},
  setup() {
    const router = useRouter();
    const { post } = useAppHelper();
    const { error, success } = AlertMessage();
    const { startLoading, stopLoading } = loader();
    const app_vars = window.smart_auth_app_vars;
    const is_logged_in = app_vars.is_logged_in;
    const lost_password_url = '/auth/forgot-password';
    const profile_page_url = app_vars.profile_page;
    const loginForm = ref({
      email: '',
      password: '',
      remember_me: false,
    });
    const loginFormRef = ref(null);
    const rules = {
      email: [
        {
          required: true,
          message: 'Please enter your email address',
          trigger: 'blur',
        },
        {
          type: 'text',
          message: 'Please enter a valid email address',
          trigger: 'blur',
        },
      ],
      password: [
        {
          required: true,
          message: 'Please enter your password',
          trigger: 'blur',
        },
      ],
    };

    const handleLogin = async () => {
      loginFormRef.value.validate((validData) => {
        if (validData) {
          handleLoginSubmission();
        } else {
          return false;
        }
      });
    };

    const handleLoginSubmission = async () => {
      startLoading();
      try {
        const formData = {
          ...loginForm.value,
        };
        const response = await post('login', formData);

        if (response.status === 'success') {
          window.location.href = profile_page_url;
        } else {
          error(response.message);
        }
      } catch (err) {
        error(err.message);
      } finally {
        stopLoading();
      }
    };

    const navigateToForgotPassword = async () => {
       router.push('/forgot-password');
    };

    const navigateToRegister = () => {
      router.push('/sign-up');
    };

    onMounted(() => {
      if (is_logged_in) {
        window.location.href = profile_page_url;
      }
    });

    return {
      loginForm,
      loginFormRef,
      rules,
      handleLogin,
      navigateToForgotPassword,
      navigateToRegister,
    };
  },
};
</script>

<style scoped>
.el-input__inner {
  height: 30px;
  border-radius: 5px;
}

input[type='number'],
input[type='search'],
input[type='time'],
input[type='text'],
input[type='password'],
input[type='email'],
input[type='url'],
input[type='image'],
textarea {
  border: none;
  padding: 5px;
}

.el-date-editor {
  width: fit-content;
  height: 36px;
}
</style>
