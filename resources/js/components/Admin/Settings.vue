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

                <el-form-item
                  label="Std Card Yearly Amount"
                  prop="standard_card_yearly_amount">
                  <el-input
                    v-model="form.standard_card_yearly_amount"></el-input>
                </el-form-item>

                <el-form-item
                  label="Std Card Biennial Amount"
                  prop="standard_card_biennial_amount">
                  <el-input
                    v-model="form.standard_card_biennial_amount"></el-input>
                </el-form-item>

                <el-form-item
                  label="VIP Card Yearly Amount"
                  prop="vip_card_yearly_amount">
                  <el-input v-model="form.vip_card_yearly_amount"></el-input>
                </el-form-item>

                <el-form-item
                  label="VIP Card Biennial Amount"
                  prop="vip_card_biennial_amount">
                  <el-input v-model="form.vip_card_biennial_amount"></el-input>
                </el-form-item>

                <el-form-item label="Currency" prop="selected_currency">
                  <el-select
                    v-model="form.selected_currency"
                    placeholder="Select Currency">
                    <el-option
                      v-for="item in currencies"
                      :key="item"
                      :label="item"
                      :value="item"></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="Card Front Image" prop="card_front_img">
                  <el-upload
                    class="upload-demo"
                    drag
                    :on-change="cardFrontFileChange"
                    :file-list="cardFrontFileList"
                    list-type="picture-card"
                    :auto-upload="false">
                    <div class="el-upload__text">
                      Drag and drop file here, or <em>click to upload</em>
                    </div>
                  </el-upload>
                </el-form-item>

                <el-form-item label="Card Back Image" prop="card_back_img">
                  <el-upload
                    class="upload-demo"
                    drag
                    :on-change="cardBackFileChange"
                    :file-list="cardBackFileList"
                    list-type="picture-card"
                    :auto-upload="false">
                    <div class="el-upload__text">
                      Drag and drop file here, or <em>click to upload</em>
                    </div>
                  </el-upload>
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
    const cardFrontFileList = ref([]);
    const cardBackFileList = ref([]);
    const app_vars = window.smart_auth_app_vars;
    console.log(app_vars);
    const url = `${app_vars.image_url}/`;
    const form = ref({
      auth_page_id: '',
      profile_page_id: '',
      card_front_img: '',
      card_back_img: '',
      standard_card_yearly_amount: '',
      standard_card_biennial_amount: '',
      vip_card_yearly_amount: '',
      vip_card_biennial_amount: '',
      selected_currency: '',
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
      card_front_img: [
        {
          required: true,
          message: 'Card Front Image is required',
          trigger: '',
        },
      ],
      card_back_img: [
        { required: true, message: 'Card Back Image is required', trigger: '' },
      ],
      standard_card_yearly_amount: [
        {
          required: true,
          message: 'Standard Card Yearly Amount is required',
          trigger: 'blur',
        },
      ],
      standard_card_biennial_amount: [
        {
          required: true,
          message: 'Standard Card Biennial Amount is required',
          trigger: 'blur',
        },
      ],
      vip_card_yearly_amount: [
        {
          required: true,
          message: 'VIP Card Yearly Amount is required',
          trigger: 'blur',
        },
      ],
      vip_card_biennial_amount: [
        {
          required: true,
          message: 'VIP Card Biennial Amount is required',
          trigger: 'blur',
        },
      ],
      selected_currency: [
        { required: true, message: 'Currency is required', trigger: 'change' },
      ],
    };

    const wp_pages = ref([]);
    const currencies = ref([]);

    const fetchSettings = async () => {
      startLoading();
      try {
        const response = await get('admin-settings');
        console.log(response);
        if (response.status === 'success') {
          const settings = response.settings;
          wp_pages.value = settings.wp_pages;
          currencies.value = settings.currencies;
          form.value.auth_page_id = String(settings.auth_page_id);
          form.value.profile_page_id = String(settings.profile_page_id);
          const isUrl = /^(http|https):\/\//.test(settings.card_front_img);

          if (isUrl) {
            form.value.card_front_img = settings.card_front_img;
            cardFrontFileList.value = [
              {
                name: settings.card_front_img,
                url: settings.card_front_img,
                status: 'finished',
              },
            ];
          } else {
            form.value.card_front_img = settings.card_front_img;
            cardFrontFileList.value = [
              {
                name: settings.card_front_img,
                url: url + settings.card_front_img,
                status: 'finished',
              },
            ];
          }
          const isUrl2 = /^(http|https):\/\//.test(settings.card_back_img);
          if (isUrl2) {
            form.value.card_back_img = settings.card_back_img;
            cardBackFileList.value = [
              {
                name: settings.card_back_img,
                url: settings.card_back_img,
                status: 'finished',
              },
            ];
          } else {
            form.value.card_back_img = settings.card_back_img;
            cardBackFileList.value = [
              {
                name: settings.card_back_img,
                url: url + settings.card_back_img,
                status: 'finished',
              },
            ];
          }
          form.value.standard_card_yearly_amount =
            settings.standard_card_yearly_amount;
          form.value.standard_card_biennial_amount =
            settings.standard_card_biennial_amount;
          form.value.vip_card_yearly_amount = settings.vip_card_yearly_amount;
          form.value.vip_card_biennial_amount =
            settings.vip_card_biennial_amount;
          form.value.selected_currency = settings.selected_currency;
        } else {
          error(response.message);
        }
      } catch (err) {
        console.error('Error fetching settings:', err);
        error('Error fetching settings:' + err.response);
      }
      stopLoading();
    };

    const cardFrontFileChange = (file, uploadedFiles) => {
      if (file.raw.type.startsWith('image/')) {
        form.value.card_front_img = file.raw;
        cardFrontFileList.value = uploadedFiles.slice(-1);
      } else {
        form.value.card_front_img = '';
        cardFrontFileList.value = [];
        error('Invalid file type. Please upload an image');
        return false;
      }
    };

    const cardBackFileChange = (file, uploadedFiles) => {
      if (file.raw.type.startsWith('image/')) {
        form.value.card_back_img = file.raw;
        cardBackFileList.value = uploadedFiles.slice(-1);
      } else {
        form.value.card_back_img = '';
        cardBackFileList.value = [];
        error('Invalid file type. Please upload an image');
        return false;
      }
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
        formData.append('card_front_img', form.value.card_front_img);
        formData.append('card_back_img', form.value.card_back_img);
        formData.append(
          'standard_card_yearly_amount',
          form.value.standard_card_yearly_amount
        );
        formData.append(
          'standard_card_biennial_amount',
          form.value.standard_card_biennial_amount
        );
        formData.append(
          'vip_card_yearly_amount',
          form.value.vip_card_yearly_amount
        );
        formData.append(
          'vip_card_biennial_amount',
          form.value.vip_card_biennial_amount
        );
        formData.append('selected_currency', form.value.selected_currency);

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
      currencies,
      onSubmit,
      formRef,
      cardFrontFileList,
      cardFrontFileChange,
      cardBackFileList,
      cardBackFileChange,
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
