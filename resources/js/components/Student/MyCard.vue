<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <div class="dashboard-container">
          <Menu />
          <el-row :gutter="20" class="mt my-card">
            <el-col :xs="24" :sm="24" :md="12">
              <div class="card-container">
                <img
                  :src="getImageUrl(personalDetails.card_front_image)"
                  alt="25% Off membership plus free trial at SecretStays"
                  class="offer-card-image" />

                <!--Profile picture -->
                <div
                  v-if="personalDetails.profile_image"
                  class="image-container">
                  <img
                    :src="getImageUrl(personalDetails.profile_image)"
                    alt="Profile Image"
                    class="profile-image" />
                </div>
                <!--    Dob -->
                <span class="card-user-dob">{{ personalDetails.dob }}</span>
                <!--user name -->
                <span class="card-user-name">{{ personalDetails.name }}</span>

                <!-- Expiry date -->
                <span class="card-user-expiry">{{
                  personalDetails.expire_date
                }}</span>
                <!-- Card Number -->
                <span class="card-user-card-no">{{
                  personalDetails.card_no
                }}</span>
              </div>
            </el-col>
            <el-col :xs="24" :sm="24" :md="12">
              <div>
                <h4>Your membership expires in</h4>
                <h4>{{ dayRemainingText }}</h4>
                <div>
                  <el-button
                    color="#87CFC3"
                    class="renew-now-button"
                    :disabled="!canRenew"
                    @click="navigateToRenewCard"
                    >Renew Now</el-button
                  >
                </div>
                <p>You can renew your card up to 30 days before expiry</p>
              </div>
              <el-divider />
            </el-col>
          </el-row>
        </div>
      </el-main>
    </el-container>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue';
import Menu from './Menu.vue';
import { loader } from '../../Composable/Loader';
import AlertMessage from '../../Composable/AlertMessage';
import { useAppHelper } from '../../Composable/appHelper';
import { computed } from 'vue';
import { useRouter } from 'vue-router';

export default {
  name: 'MyCard',
  components: {
    Menu,
  },
  setup() {
    const router = useRouter();
    const personalDetails = ref({
      name: '',
      email: '',
      dob: '',
      expire_date: '',
      profile_image: '',
      card_front_image: '',
      card_back_image: '',
      day_remaining: '',
      card_no: '',
    });

    const fileList = ref([]);
    const { error } = AlertMessage();
    const { startLoading, stopLoading } = loader();
    const { get } = useAppHelper();
    const app_vars = window.smart_auth_app_vars;
    const img_url = `${app_vars.image_url}/`;

    const fetchProfileInfo = async () => {
      startLoading();
      try {
        const response = await get('get-my-card');

        if (response.status === 'success') {
          personalDetails.value.name = response.data.name;
          personalDetails.value.email = response.data.email;
          personalDetails.value.dob = response.data.dob;
          personalDetails.value.expire_date = response.data.expire_date;
          personalDetails.value.profile_image = response.data.profile_image;
          personalDetails.value.card_front_image = response.data.card_front;
          personalDetails.value.card_back_image = response.data.card_back;
          personalDetails.value.day_remaining = response.data.day_remaining;
          personalDetails.value.card_no = response.data.card_no;
        } else {
          error(response.message);
        }
      } catch (err) {
        error(err.message);
      } finally {
        stopLoading();
      }
    };

    const getImageUrl = (file_name) => {
      const isUrl = /^(http|https):\/\//.test(file_name);
      //ii file is url
      if (isUrl) {
        return file_name;
      }
      return `${img_url}${file_name}`;
    };

    const dayRemainingText = computed(() => {
      return personalDetails.value.day_remaining == 1 ||
        personalDetails.value.day_remaining == -1
        ? 'today'
        : `${personalDetails.value.day_remaining} days`;
    });
    const canRenew = computed(() => {
      return personalDetails.value.day_remaining <= 30;
    });

    // Navigate to renew card page
    const navigateToRenewCard = () => {
      router.push('/choose-membership');
    };
    // Fetch data on mount
    onMounted(() => {
      fetchProfileInfo();
    });

    return {
      fileList,
      personalDetails,
      getImageUrl,
      dayRemainingText,
      canRenew,
      navigateToRenewCard,
    };
  },
};
</script>
<style></style>
