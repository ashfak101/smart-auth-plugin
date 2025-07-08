<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <div class="dashboard-container">
          <Menu />
          <el-row :gutter="30"> Billing </el-row>
        </div>
      </el-main>
    </el-container>
  </div>
</template>

<script>
import { onMounted, ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAppHelper } from '../../Composable/appHelper';
import AlertMessage from '../../Composable/AlertMessage';
import { loader } from '../../Composable/Loader';
import Menu from './Menu';

export default {
  name: 'History',
  components: {
    Menu,
  },
  setup() {
    const { get } = useAppHelper();
    const { error } = AlertMessage();
    const { startLoading, stopLoading } = loader();
    const route = useRoute();
    const items = ref([]);
    const app_vars = window.smart_auth_app_vars;

    // Fetch data on mount
    onMounted(() => {});

    return {};
  },
};
</script>
