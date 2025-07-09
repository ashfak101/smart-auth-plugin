<template>
  <div class="sa-student-profile-login">
    <div>
      <el-form
        :model="passwordForm"
        ref="passwordFormRef"
        :rules="rules"
        label-position="top"
        label-width="auto"
        class="login-form"
        validate-on-blur
        validate-on-change>
        <h3 class="title">Reset Password</h3>
        <p class="subTitle">
          Please enter your new password below. Make sure it is at least 6
          characters long.
        </p>
        <el-form-item label=" Password" prop="Password">
          <el-input
            type="password"
            v-model="passwordForm.password"
            placeholder="Enter Your New Password Here"></el-input>
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
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAppHelper } from '../../Composable/appHelper';
import AlertMessage from '../../Composable/AlertMessage';
import { loader } from '../../Composable/Loader';

export default {
  name: 'NewPassword',
  setup() {
    const route = useRoute();
    const router = useRouter();
    const { post } = useAppHelper();
    const { error, success } = AlertMessage();
    const { startLoading, stopLoading } = loader();

    const token = route.query.token || ''; // Extract token from URL query
    const login = route.query.login || ''; // Extract email from URL query
    const passwordForm = ref({
      password: '',
    });
    const passwordFormRef = ref(null);

    const rules = {
      password: [
        {
          required: true,
          message: 'Please enter your new password',
          trigger: 'blur',
        },
        {
          min: 6,
          message: 'Password must be at least 6 characters',
          trigger: 'blur',
        },
      ],
    };
    const handleLogin = async () => {
      passwordFormRef.value.validate((validData) => {
        if (validData) {
          submitNewPassword();
        } else {
          return false;
        }
      });
    };
    const submitNewPassword = async () => {
      startLoading();
      try {
        const formData = {
          token,
          password: passwordForm.value.password,
          login,
        };
        const response = await post('reset-password', formData);
        if (response.status === 'success') {
          success('Password reset successfully!');
          router.push('/sign-in'); // Redirect to login page
        } else {
          error(response.message || 'Failed to reset password');
        }
      } catch (err) {
        error(err.message || 'An error occurred');
      } finally {
        stopLoading();
        // Clear the form after submission
        passwordForm.value.password = '';
      }
    };

    const navigateToRegister = () => {
      router.push('/sign-up');
    };
    return {
      passwordForm,
      passwordFormRef,
      rules,

      navigateToRegister,
      handleLogin,
    };
  },
};
</script>

<style scoped>
.new-password {
  max-width: 400px;
  margin: 2rem auto;
}
.mt-2 {
  margin-top: 1rem;
}
</style>
