<template>
  <el-header>
    <div class="header-content">
      <el-menu
        :default-active="activeIndex"
        class="el-menu-demo"
        mode="horizontal"
        @select="handleSelect"
        :router="true">
        <el-menu-item index="general" :to="{ path: '/general' }"
          >General Settings</el-menu-item
        >
      </el-menu>
      <div class="h-6" />
    </div>
  </el-header>
</template>

<script>
import { ref, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

export default {
  name: 'Header',
  setup() {
    const route = useRoute(); // Get the current route
    const router = useRouter();
    const activeIndex = ref('general');

    // Set the active menu item based on the current route path
    const setActiveMenu = (path) => {
      if (path.includes('/general')) {
        activeIndex.value = 'general';
      } else {
        activeIndex.value = 'general';
      }
    };

    // Call setActiveMenu on component mount
    onMounted(() => {
      setActiveMenu(route.path);
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
