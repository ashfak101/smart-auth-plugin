<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <div class="dashboard-container">
          <Menu />
          <el-space :gutter="30" wrap class="saved-offers-container">
            <SavedOffersCard v-for="item in saveData" :favourite_item="item" @removeOffer="removeOffer" />
          </el-space>
        </div>
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
import SavedOffersCard from './SavedOffersCard.vue';

export default {
  name: 'SavedOffer',
  components: {
    Menu,
    SavedOffersCard,
  },
  setup() {
    const { get, post } = useAppHelper();
    const { error } = AlertMessage();
    const { startLoading, stopLoading } = loader();
    const currentPage = ref(1);
    const pageSize = ref(5);
    const saveData = ref([]);

    const fetchOffers = async () => {
      startLoading();
      try {
        const response = await get(`get-saved-offers?page=${currentPage.value}&per_page=${pageSize.value}`);

        if (response) {
          if (response.status === 'success') {
            saveData.value = response.data;
          } else {
            error(response.message);
          }
        } else {
          throw new Error("Failed to save event");
        }
      } catch (err) {
        error(err.message);
      } finally {
        stopLoading();
      }
    };

    const removeOffer = async ( id ) => {
      startLoading();
      try {
        const response = await post(`remove-favourite/${id}`);

        if (response) {
          if (response.status === 'success') {
            fetchOffers();
          } else {
            error(response.message);
          }
        } else {
          throw new Error("Failed to save event");
        }
      } catch (err) {
        error(err.message);
      } finally {
        stopLoading();
      }
    };
    // Fetch data on mount
    onMounted(() => {
      fetchOffers();
    });

    return {
      saveData,
      removeOffer
    };
  },
};
</script>

<style>
.saved-offers-container {
  margin-top: 20px;
}
</style>