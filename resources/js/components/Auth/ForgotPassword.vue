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
        <h3 class="title">Forgot Password</h3>
        <p class="subTitle">
          Enter your email address or username below. We'll send you a link to
          reset your password.
        </p>
        <el-form-item label=" Email" prop="email">
          <el-input
            v-model="loginForm.email"
            placeholder="Enter Your Email Address Here"></el-input>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleLogin" class="login-button"
            >Reset Password</el-button
          >
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
  name: 'ForgotPassword',
  components: {},
  setup() {
    const router = useRouter();
    const { post } = useAppHelper();
    const { error, success } = AlertMessage();
    const { startLoading, stopLoading } = loader();
    const app_vars = window.smart_auth_app_vars;
    const is_logged_in = app_vars.is_logged_in;
    const lost_password_url = app_vars.lost_password;
    const profile_page_url = app_vars.profile_page;
    const loginForm = ref({
      email: '',
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
          type: 'email',
          message: 'Please enter a valid email address',
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
        const response = await post('forgot-password', formData);

        if (response.status === 'success') {
          success(response.message);
          // Redirect to the login page or show a success message
        //   router.push('/sign-in');
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
      window.location.href = lost_password_url;
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
.forgot-password {
  max-width: 400px;
  margin: 2rem auto;
  padding: 2rem;
  border: 1px solid #eee;
  border-radius: 8px;
  background: #fff;
}
.form-group {
  margin-bottom: 1rem;
}
</style>
