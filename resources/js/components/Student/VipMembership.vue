<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <div class="dashboard-container">
          <Menu />
          <el-row :gutter="30">
            <div class="vip-content">
              <h2>Become an XO Student Discounts</h2>
              <h2>VIP Member</h2>
              <p>Get access to 100's of great deals plus</p>
              <div class="vip-list-offers">
                <ul>
                  <li>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="lucide lucide-crown">
                      <path
                        d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z" />
                      <path d="M5 21h14" />
                    </svg>
                    <span>Access to our exclusive wellness portal</span>
                  </li>

                  <li>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="lucide lucide-crown">
                      <path
                        d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z" />
                      <path d="M5 21h14" />
                    </svg>
                    <span>100's of Workout, mindfulness and yoga videos</span>
                  </li>
                  <li>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="lucide lucide-crown">
                      <path
                        d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z" />
                      <path d="M5 21h14" />
                    </svg>
                    <span>Mental health app & support</span>
                  </li>
                  <li>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="lucide lucide-crown">
                      <path
                        d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z" />
                      <path d="M5 21h14" />
                    </svg>
                    <span>Help with finances</span>
                  </li>
                </ul>
              </div>
              <div class="text-center">
                <button class="upgrade-now-button">Upgrade Now</button>
              </div>
            </div>
          </el-row>
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
import Menu from './Menu.vue';

export default {
  name: 'VipMembership',
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
<style>
.vip-content {
  background: radial-gradient(
    99.31% 115.31% at 61.95% -17.96%,
    #87cfc3 0%,
    #2e6668 100%
  );
  padding: 20px;
  color: white;
  margin: 30px auto;
  border-radius: 10px;
}
.vip-content h2 {
  color: white;
  text-align: center;
  margin-bottom: 10px;
}
.vip-content p {
  color: white;
  text-align: center;
  margin-bottom: 20px;
}
.vip-list-offers ul {
  list-style: none;
  padding: 0;
  padding-left: 0;
}
.vip-list-offers ul li {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}
.upgrade-now-button {
  color: #2e6668;
  background: white;
  border: 1px solid #2e6668;
  border-radius: 10px;
}
.upgrade-now-button:hover {
  background: #2e6668;
  color: white;
  border: 1px solid #fff;
}
.text-center {
  text-align: center;
  margin: 15px 0;
}
</style>
