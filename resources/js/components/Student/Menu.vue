<template>
  <el-header>
    <div class="header-content">
      <el-menu
        :default-active="activeIndex"
        class="el-menu-demo"
        mode="horizontal"
        @select="handleSelect"
        :router="true">
        <el-menu-item index="my-profile" :to="{ path: '/my-profile' }"
          >My Profile</el-menu-item
        >
        <el-menu-item index="my-card" :to="{ path: '/my-card' }"
          >My Card</el-menu-item
        >
        <el-menu-item
          index="vip-access"
          :to="{ path: '/vip-access' }"
          v-if="!is_pro_membership"
          >VIP Membership</el-menu-item
        >
        <el-menu-item index="saved-offers" :to="{ path: '/saved-offers' }"
          >Saved Offers</el-menu-item
        >
        <el-menu-item index="history" :to="{ path: '/history' }"
          >History</el-menu-item
        >
        <!-- <el-menu-item index="billing" :to="{ path: '/billing' }">Billing</el-menu-item> -->
        <!-- <el-menu-item index="app-permission" :to="{ path: '/app-permission' }">Apps & Permissions</el-menu-item> -->
        <el-menu-item index="change-password" :to="{ path: '/change-password' }"
          >Change Password</el-menu-item
        >
      </el-menu>
      <div class="h-6" />
    </div>
  </el-header>
</template>

<script>
import { onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

export default {
  name: 'Menu',
  setup() {
    const route = useRoute(); // Get the current route
    const router = useRouter();
    const activeIndex = ref('my-profile');
    const app_vars = window.smart_auth_app_vars;
    const is_logged_in = `${app_vars.is_logged_in}`;
    const is_purchased = `${app_vars.is_purchased}`;
    const membership_type = `${app_vars.membership_type}`;
    const is_remaining_days = `${app_vars.remaining_days}`;
    const is_pro_membership = membership_type === 'pro' ? true : false;

    // Set the active menu item based on the current route path
    const setActiveMenu = (path) => {
      if (path.includes('/my-card')) {
        activeIndex.value = 'my-card';
      } else if (path.includes('/vip-access')) {
        activeIndex.value = 'vip-access';
      } else if (path.includes('/saved-offers')) {
        activeIndex.value = 'saved-offers';
      } else if (path.includes('/history')) {
        activeIndex.value = 'history';
      } else if (path.includes('/change-password')) {
        activeIndex.value = 'change-password';
      } else if (path.includes('billing')) {
        activeIndex.value = 'billing';
      } else if (path.includes('/app-permission')) {
        activeIndex.value = 'app-permission';
      } else {
        activeIndex.value = 'my-profile';
      }
    };

    // Call setActiveMenu on component mount
    onMounted(() => {
      if (!is_logged_in) {
        window.location.href = `${app_vars.auth_page}`;
      } else if (!is_purchased) {
        router.push('/choose-membership');
      } else {
        if ('/choose-membership' === route.path && is_remaining_days > 30) {
          router.push('/my-profile');
        } else {
          setActiveMenu(route.path);
        }
      }
    });

    // Watch the route for changes and update the active menu item accordingly
    watch(route, (newRoute) => {
      setActiveMenu(newRoute.path);
    });

    // Handle manual tab selection from the menu
    const handleSelect = (key) => {
      activeIndex.value = key;
      // Navigate to the selected route when a menu item is clicked
      router.push(`/${key}`);
    };

    return {
      activeIndex,
      handleSelect,
      is_pro_membership,
    };
  },
};
</script>
