<template>
  <el-card class="custom-card" shadow="hover" style="padding: 0">
    <div class="card-content">
      <div class="thumbnail-container">
        <img :src="favourite_item.thumbnail" alt="thumbnail" />
        <!-- Image Section -->
        <div class="saved-image-container">
          <div class="block">
            <el-avatar shape="square" :size="50" :src="favourite_item.logo" class="logo" />
          </div>
        </div>
      </div>
      <!-- Header with Favorite Icon -->
      <div class="header">
        <el-icon class="heart-icon" @click="handleDelete(favourite_item.id)">
          <el-icon :size="20" color="red">
            <!-- <i class="el-icon-star-on"></i> -->
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="red"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="lucide lucide-heart">
              <path
                d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
            </svg>
          </el-icon>
        </el-icon>
      </div>

      <!-- Main Text -->
      <div class="text-content">
        <p>
          <a :href="favourite_item.permalink" target="_blank" class="description"><strong>{{ favourite_item.title }}</strong></a>
        </p>
      </div>
    </div>
  </el-card>
</template>

<script>
export default {
  name: 'SavedOffersCard',
  props: {
    favourite_item: {
      type: Object,
      required: true,
    },
  },
  emits: ['removeOffer'],
  setup(_, { emit }) {
    const handleDelete = ( id ) => {
      emit('removeOffer', id);
    };
    return {
      handleDelete,
    };
  },
};
</script>

<style scoped>
.el-card__body {
  padding: 0 !important;
}
.custom-card {
  border-radius: 12px;
  text-align: center;
  max-width: 270px;

  position: relative;
}

.header {
  display: flex;
  justify-content: flex-end;
}

.heart-icon {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
  border: 1px solid #fff;
  padding: 15px;
  border-radius: 50%;
  background-color: #fff;
}
.thumbnail-container {
  position: relative;
  width: 100%;
  height: 200px;
}
.saved-image-container {
  position: absolute;
  top: 70px;
  left: 30%;
  padding: 5px;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.logo {
  width: 70px;
  height: auto;

}

.description {
  font-size: 14px;
  color: #333;
  margin: 0;
  text-decoration: none;
}
.description:hover {
  text-decoration: underline;
  color: #333;
}
</style>
