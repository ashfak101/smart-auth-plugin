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

    // Set the active menu item based on the current route path
    const setActiveMenu = (path) => {
      if (path.includes('/')) {
        activeIndex.value = 'my-profile';
      } else {
        activeIndex.value = 'my-profile';
      }
    };

    // Call setActiveMenu on component mount
    onMounted(() => {
      if (!is_logged_in) {
        window.location.href = `${app_vars.auth_page}`;
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
    };
  },
};
</script>
