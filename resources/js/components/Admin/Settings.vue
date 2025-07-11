<template>
  <el-container class="full-height">
    <el-main class="main-center">
      <Header />
      <div class="dashboard-container">
        <h1>General Settings</h1>

        <el-card>
          <el-form
            :model="form"
            :rules="rules"
            ref="formRef"
            label-width="200px"
            class="dashboard-setting-form-container">
            <el-row>
              <el-col :span="12">
                <el-form-item label="Auth Page" prop="auth_page_id">
                  <el-select
                    v-model="form.auth_page_id"
                    placeholder="Select Auth Page">
                    <el-option
                      v-for="item in wp_pages"
                      :key="item.id"
                      :label="item.title"
                      :value="item.id"></el-option>
                  </el-select>
                </el-form-item>

                <el-form-item label="Profile Page" prop="profile_page_id">
                  <el-select
                    v-model="form.profile_page_id"
                    placeholder="Select Profile Page">
                    <el-option
                      v-for="item in wp_pages"
                      :key="item.id"
                      :label="item.title"
                      :value="item.id"></el-option>
                  </el-select>
                </el-form-item>


              </el-col>

            </el-row>
            <el-row>
              <el-col :span="24">
                <el-button
                  type="primary"
                  @click="onSubmit"
                  class="setting-button"
                  >Save</el-button
                >
              </el-col>
            </el-row>
          </el-form>
        </el-card>
      </div>
    </el-main>
    <el-footer>
      <Footer />
    </el-footer>
  </el-container>
</template>

<script>
import { onMounted, ref } from 'vue';
import AlertMessage from '../../Composable/AlertMessage';
import { useAppHelper } from '../../Composable/appHelper';
import { loader } from '../../Composable/Loader';
import Header from '../Header';
export default {
  name: 'Settings',
  components: {
    Header,
  },
  setup() {
    const { get, post } = useAppHelper();
    const { success, error } = AlertMessage();
    const { startLoading, stopLoading } = loader();
    const formRef = ref(null);

    const app_vars = window.smart_auth_app_vars;
    console.log(app_vars);

    const form = ref({
      auth_page_id: '',
      profile_page_id: '',

    });

    // Validation rules
    const rules = {
      auth_page_id: [
        { required: true, message: 'Auth page is required, trigger: "change"' },
      ],
      profile_page_id: [
        {
          required: true,
          message: 'Profile Page is required',
          trigger: 'change',
        },
      ],

    };

    const wp_pages = ref([]);


    const fetchSettings = async () => {
      startLoading();
      try {
        const response = await get('admin-settings');
        console.log(response);
        if (response.status === 'success') {
          const settings = response.settings;
          wp_pages.value = settings.wp_pages;
          form.value.auth_page_id = String(settings.auth_page_id);
          form.value.profile_page_id = String(settings.profile_page_id);
        } else {
          error(response.message);
        }
      } catch (err) {
        console.error('Error fetching settings:', err);
        error('Error fetching settings:' + err.response);
      }
      stopLoading();
    };



    // Validate and submit the form
    const onSubmit = async () => {
      formRef.value.validate((valid) => {
        if (valid) {
          updateAdminsettings();
        } else {
          console.error('Validation failed');
          return false;
        }
      });
    };

    const updateAdminsettings = async () => {
      startLoading();
      try {
        const formData = new FormData();
        formData.append('auth_page_id', form.value.auth_page_id);
        formData.append('profile_page_id', form.value.profile_page_id);


        const response = await post('update-settings', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        success(response.message);

        if (response.status === 'success') {
          success(response.message);
        } else {
          error(response.message);
        }
      } catch (error) {
        error('Error updating settings:' + err.response.data.message);
      }
      stopLoading();
    };

    onMounted(() => {
      fetchSettings();
    });

    return {
      form,
      rules,
      wp_pages,

      onSubmit,
      formRef,

    };
  },
};
</script>
<style scoped>
.dashboard-container {
  padding: 20px;
  max-width: 100%;
}

.full-height {
  min-height: 100vh;
}

.el-col {
  padding: 10px;
}
</style>
