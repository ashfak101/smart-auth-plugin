<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <Menu />
        <el-row :gutter="30" class="student-profile">
          <!-- First Column: Profile Picture -->


          <!-- Second Column: Personal Details -->
          <el-col  :xs="24" :sm="24" :md="8" :lg="9" :xl="11" class="">
            <h3>Personal Details</h3>
            <el-form
              ref="personalForm"
              label-position="top"
              :model="personalDetails"
              :rules="personalDetailsRules"
              validate-on-blur
              validate-on-change>
              <el-form-item label="First Name" prop="first_name">
                <el-input v-model="personalDetails.first_name"></el-input>
              </el-form-item>
              <el-form-item label="Last Name" prop="last_name">
                <el-input v-model="personalDetails.last_name"></el-input>
              </el-form-item>
              <el-form-item label="Email" prop="email">
                <el-input v-model="personalDetails.email" disabled></el-input>
              </el-form-item>

            </el-form>
          </el-col>

          <!-- Third Column: Address -->
          <el-col  :xs="24" :sm="24" :md="8" :lg="9" :xl="11" class="m-l-dashed">
            <h3>Address</h3>
            <el-form
              ref="addressForm"
              label-position="top"
              :model="addressDetails"
              :rules="addressRules"
              validate-on-blur
              validate-on-change>
              <el-form-item class="m-b-35" prop="house_name">
                <el-input
                  v-model="addressDetails.house_name"
                  placeholder="House No/ Name"></el-input>
              </el-form-item>

              <el-form-item class="m-b-35" prop="address_line_two">
                <el-input
                  v-model="addressDetails.address_line_two"
                  placeholder="Address Line Two"></el-input>
              </el-form-item>

              <el-form-item class="m-b-35" prop="town">
                <el-input
                  v-model="addressDetails.town"
                  placeholder="Town"></el-input>
              </el-form-item>

              <el-form-item class="m-b-35" prop="postcode">
                <el-input
                  v-model="addressDetails.postcode"
                  placeholder="Postcode"></el-input>
              </el-form-item>

              <el-form-item prop="country">
                <el-input
                  v-model="addressDetails.country"
                  placeholder="Country"></el-input>
              </el-form-item>
            </el-form>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="12" class="text-left">
            <el-button
              type="primary"
              @click="validateAndSaveProfile"
              class="primary-button"
              >Save</el-button
            >
          </el-col>
        </el-row>
      </el-main>
    </el-container>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue';
import { useAppHelper } from '../../Composable/appHelper';
import AlertMessage from '../../Composable/AlertMessage';
import { loader } from '../../Composable/Loader';
import Menu from './Menu.vue';

export default {
  name: 'MyProfile',
  components: {
    Menu,
  },
  setup() {
    const { get, post } = useAppHelper();
    const { error, success } = AlertMessage();
    const { startLoading, stopLoading } = loader();
    const app_vars = window.smart_auth_app_vars;
    const img_url = `${app_vars.image_url}/`;
    const fileList = ref([]);
    const personalForm = ref(null);
    const addressForm = ref(null);

    const personalDetails = ref({
      first_name: '',
      last_name: '',
      email: '',

    });

    const personalDetailsRules = {
      first_name: [
        {
          required: true,
          message: 'Please enter your name',
          trigger: 'blur',
        },
      ],
      last_name: [
        {
          required: true,
          message: 'Please enter your name',
          trigger: 'blur',
        },
      ],
      email: [
        {
          required: true,
          message: 'Please enter your email',
          trigger: 'blur',
        },
      ],

    };

    const addressDetails = ref({
      house_name: '',
      address_line_two: '',
      town: '',
      postcode: '',
      country: '',
    });

    // Validation rules for each form field
    const addressRules = {
      house_name: [
        {
          required: true,
          message: 'Please enter your House Name or No',
          trigger: 'blur',
        },
      ],
      town: [
        {
          required: true,
          message: 'Please enter your Town',
          trigger: 'blur',
        },
      ],
      postcode: [
        {
          required: true,
          message: 'Please enter your Postcode',
          trigger: 'blur',
        },
      ],
      country: [
        {
          required: true,
          message: 'Please enter your Country',
          trigger: 'blur',
        },
      ],
    };

    const validateAndSaveProfile = () => {
      personalForm.value.validate((validPersonal) => {
        addressForm.value.validate((validAddress) => {
          if (validPersonal && validAddress) {
            saveProfile();
          } else {
            error('Please correct the errors in the form');
          }
        });
      });
    };

    // Triggered when a file is added
    const onFileChange = (file, uploadedFiles) => {
      if (file.raw.type.startsWith('image/')) {
        // Upload the image to the server
        uploadImage(file.raw);
        fileList.value = uploadedFiles.slice(-1);
      } else {
        fileList.value = [];
        error('Invalid file type. Please upload an image or video.');
        return false;
      }
    };

    // Upload the image to the server
    const uploadImage = async (file) => {
      const formData = new FormData();
      formData.append('file', file);
      startLoading();
      try {
        const response = await post('profile-picture', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        if (response) {
          success(response);
        } else {
          throw new Error('Failed to save event');
        }
      } catch (err) {
        error(err.message);
      } finally {
        stopLoading();
      }
    };

    // Save profile data
    const saveProfile = async () => {
      const name =
        personalDetails.value.first_name +
        ' ' +
        personalDetails.value.last_name;

      const profileData = {
        ...personalDetails.value,
        ...addressDetails.value,
        name,
      };

      startLoading();
      try {
        const response = await post('save-profile', profileData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        if (response) {
          success(response);
        } else {
          throw new Error('Failed to save event');
        }
      } catch (err) {
        error(err.message);
      } finally {
        stopLoading();
      }
    };

    const fetchProfileInfo = async () => {
      startLoading();
      try {
        const response = await get('get-profile');

        if (response) {
          personalDetails.value.first_name = response.first_name;
          personalDetails.value.last_name = response.last_name;
          personalDetails.value.email = response.email;
          personalDetails.value.dob = response.dob;
          personalDetails.value.gender = response.gender;
          addressDetails.value.house_name = response.house_name;
          addressDetails.value.address_line_two = response.address_line_two;
          addressDetails.value.town = response.town;
          addressDetails.value.postcode = response.postcode;
          addressDetails.value.country = response.country;

          if (response.profile_picture) {
            fileList.value = [
              {
                name: 'Profile Picture',
                url: img_url + response.profile_picture,
              },
            ];
          }
        }
      } catch (err) {
        error(err.message);
      } finally {
        stopLoading();
      }
    };

    // Fetch data on mount
    onMounted(() => {
      fetchProfileInfo();
    });

    return {
      fileList,
      personalDetails,
      personalDetailsRules,
      addressDetails,
      addressRules,
      onFileChange,
      validateAndSaveProfile,
      personalForm,
      addressForm,
    };
  },
};
</script>
<style>
.el-tabs__nav {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  float: none;
  background-color: #23b7b7;
}

.el-tabs--border-card > .el-tabs__header .el-tabs__item,
.el-tabs--border-card > .el-tabs__header .el-tabs__item:hover {
  color: #fff;
}

.el-tabs--border-card > .el-tabs__header .el-tabs__item.is-active {
  /*background-color: #23b72d;
    color: #fff;*/
}

.el-tabs--border-card {
  background-color: #fff;
  border: 0.5px solid #ebeef5 !important;
  width: 100%;
}

h3 {
  margin-bottom: 20px;
}

.m-l-dashed {
  border-left: 3px dashed #ebeef5;
}

.el-form-item {
  margin-bottom: 0;
}

.upload-demo {
  width: 100%;
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  text-align: center;
  padding: 10px 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.el-upload-list--picture-card {
  display: flex;
  flex-direction: column-reverse;
}

.el-upload__input {
  display: none !important;
}

.el-input__wrapper {
  /*box-shadow: none;*/
}

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

.m-b-35 {
  margin-bottom: 35px;
}
</style>
