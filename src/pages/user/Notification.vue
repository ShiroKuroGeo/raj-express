//diri sir kay ma notify ang customer kung naay new food na added.


<template>
  <q-page>
    <q-header class="bg-pink-1">
      <q-toolbar>
        <q-btn flat dense icon="arrow_back" color="red" @click="$router.back()" />
        <q-toolbar-title class="text-center text-purple-9">Notification</q-toolbar-title>
      </q-toolbar>
    </q-header>

    <div class="q-pa-md">
      <q-card v-for="notification in notifications" :key="notification.id" class="q-mb-md bg-pink-2">
        <q-card-section class="row no-wrap items-center">
          <q-img
            :src="notification.image"
            spinner-color="white"
            style="height: 100px; width: 100px"
            class="rounded-borders"
          />
          <q-card-section class="q-ml-md col">
            <div class="text-h6">{{ notification.title }}</div>
            <div class="text-caption">{{ notification.datetime }}</div>
          </q-card-section>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';

export default defineComponent({
  name: 'NotificationPage',
  setup() {
    const notifications = ref([]);

    const fetchNotifications = async () => {
      try {
        const response = await fetch('http://your-backend-url/get_notifications.php');
        const data = await response.json();
        notifications.value = data;
      } catch (error) {
        console.error('Error fetching notifications:', error);
      }
    };

    onMounted(() => {
      fetchNotifications();
    });

    return {
      notifications
    };
  }
});
</script>

<style lang="scss" scoped>
.q-toolbar {
  min-height: 56px;
}

.q-toolbar-title {
  font-size: 1.2rem;
  font-weight: 500;
}

.q-btn {
  margin-left: -8px; // Adjust to align the back arrow with the edge
}
</style>
