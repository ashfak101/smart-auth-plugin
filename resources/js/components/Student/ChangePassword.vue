<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <div class="dashboard-container">
          <Menu />
          <el-row :gutter="30" class="mt change-password">
            <el-col :span="12" :offset="6">
              <h3>Change Password</h3>
              <el-form
                ref="changePasswordForm"
                :model="form"
                :rules="rules"
                label-position="top"
                validate-on-blur
                validate-on-change>
                <el-form-item label="Current Password" prop="current_password">
                  <el-input
                    v-model="form.current_password"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    autocomplete="off">
                    <template #suffix>
                      <el-button
                        @click="toggleCurrentPasswordVisibility"
                        type="text">
                        <el-icon>
                          <View />
                        </el-icon>
                      </el-button>
                    </template>
                  </el-input>
                </el-form-item>
                <el-form-item label="New Password" prop="new_password">
                  <el-input
                    v-model="form.new_password"
                    :type="showNewPassword ? 'text' : 'password'"
                    autocomplete="off">
                    <template #suffix>
                      <el-button
                        @click="toggleNewPasswordVisibility"
                        type="text">
                        <el-icon>
                          <View />
                        </el-icon>
                      </el-button>
                    </template>
                  </el-input>
                </el-form-item>
                <el-form-item
                  label="Confirm New Password"
                  prop="confirm_new_password">
                  <el-input
                    v-model="form.confirm_new_password"
                    :type="showConfirmNewPassword ? 'text' : 'password'"
                    autocomplete="off">
                    <template #suffix>
                      <el-button
                        @click="toggleConfirmNewPasswordVisibility"
                        type="text">
                        <el-icon>
                          <View />
                        </el-icon>
                      </el-button>
                    </template>
                  </el-input>
                </el-form-item>
                <el-form-item class="m-t-35">
                  <el-button
                    type="primary"
                    @click="handleSubmit"
                    class="primary-button"
                    >Change Password</el-button
                  >
                </el-form-item>
              </el-form>
            </el-col>
          </el-row>
        </div>
      </el-main>
    </el-container>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useAppHelper } from '../../Composable/appHelper';
import AlertMessage from '../../Composable/AlertMessage';
import { loader } from '../../Composable/Loader';
import { View } from '@element-plus/icons-vue';
import Menu from './Menu.vue';

export default {
  name: 'ChangePassword',
  components: {
    View,
    Menu,
  },
  setup() {
    const { post } = useAppHelper();
    const { error, success } = AlertMessage();
    const { startLoading, stopLoading } = loader();
    const changePasswordForm = ref(null);
    const form = ref({
      current_password: '',
      new_password: '',
      confirm_new_password: '',
    });

    const showCurrentPassword = ref(false);
    const showNewPassword = ref(false);
    const showConfirmNewPassword = ref(false);

    const toggleCurrentPasswordVisibility = () => {
      showCurrentPassword.value = !showCurrentPassword.value;
    };

    const toggleNewPasswordVisibility = () => {
      showNewPassword.value = !showNewPassword.value;
    };

    const toggleConfirmNewPasswordVisibility = () => {
      showConfirmNewPassword.value = !showConfirmNewPassword.value;
    };

    const rules = {
      current_password: [
        {
          required: true,
          message: 'Please enter your current password',
          trigger: 'blur',
        },
      ],
      new_password: [
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
      confirm_new_password: [
        {
          required: true,
          message: 'Please confirm your new password',
          trigger: 'blur',
        },
        {
          validator: (rule, value, callback) => {
            if (value !== form.value.new_password) {
              callback(new Error('Confirm password does not match'));
            } else {
              callback();
            }
          },
          trigger: 'blur',
        },
      ],
    };

    const handleSubmit = () => {
      changePasswordForm.value.validate(async (valid) => {
        if (valid) {
          startLoading();
          try {
            const response = await post('change-password', form.value);
            if (response) {
              success('Password changed successfully');
              setTimeout(() => {
                window.location.reload();
              }, 2000);
            } else {
              throw new Error('Failed to change password');
            }
          } catch (err) {
            error(err.message);
          } finally {
            stopLoading();
          }
        } else {
          error('Please correct the errors in the form');
        }
      });
    };

    return {
      form,
      rules,
      changePasswordForm,
      handleSubmit,
      showCurrentPassword,
      showNewPassword,
      showConfirmNewPassword,
      toggleCurrentPasswordVisibility,
      toggleNewPasswordVisibility,
      toggleConfirmNewPasswordVisibility,
    };
  },
};
</script>

<style>
.el-input__inner {
  height: 36px;
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

.m-t-35 {
  margin-top: 35px;
}
</style>
