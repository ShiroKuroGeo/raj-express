<template>
    <q-layout view="lHh Lpr lFf">
      <q-page-container>
        <q-page class="q-pa-md">
          <!-- Title -->
          <div class="text-center text-h6 q-mt-md">My Order</div>

          <!-- Tabs for Ongoing and History Orders -->
          <q-tabs
            v-model="tab"
            class="q-mt-md q-mb-md"
            active-color="red"
            indicator-color="red"
            align="center"
            dense
          >
            <q-tab name="ongoing" label="Ongoing" />
            <q-tab name="history" label="History" />
          </q-tabs>

          <!-- Ongoing Orders -->
          <q-page v-if="tab === 'ongoing'">
            <div v-if="ongoingOrders.length">
              <!-- List of ongoing orders -->
              <q-list bordered>
                <q-item v-for="order in ongoingOrders" :key="order.id" clickable @click="viewOrderDetails(order.id)">
                  <q-item-section avatar>
                    <q-icon name="receipt_long" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Order #{{ order.id }}</q-item-label>
                    <q-item-label caption>Amount: {{ order.total_amount }}</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
            <div v-else class="text-center q-pa-md">
              <q-img src="/statics/empty-box.png" width="120px" height="120px" />
              <div class="text-h6 q-mt-md">No Ongoing Orders</div>
              <div class="text-subtitle2">You haven't placed any ongoing orders</div>
              <q-btn color="red" class="q-mt-md" @click="goToHome">Explore Menu</q-btn>
            </div>
          </q-page>

          <!-- History Orders -->
          <q-page v-if="tab === 'history'">
            <div v-if="historyOrders.length">
              <!-- List of history orders -->
              <q-list bordered>
                <q-item v-for="order in historyOrders" :key="order.id" clickable @click="viewOrderDetails(order.id)">
                  <q-item-section avatar>
                    <q-icon name="receipt_long" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>Order #{{ order.id }}</q-item-label>
                    <q-item-label caption>Amount: {{ order.total_amount }}</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
            <div v-else class="text-center q-pa-md">
              <q-img src="/statics/empty-box.png" width="120px" height="120px" />
              <div class="text-h6 q-mt-md">No Order History</div>
              <div class="text-subtitle2">You haven't made any purchases yet</div>
              <q-btn color="red" class="q-mt-md" @click="goToHome">Explore Menu</q-btn>
            </div>
          </q-page>
        </q-page>
      </q-page-container>
    </q-layout>
  </template>

  <script>
  export default {
    name: 'OrderPage',
    data() {
      return {
        tab: 'ongoing', // Initial tab selection
        ongoingOrders: [], // List of ongoing orders
        historyOrders: [], // List of past orders (history)
      };
    },
    mounted() {
      this.fetchOrders();
    },
    methods: {
      async fetchOrders() {
        // Fetch ongoing and history orders from backend
        try {
          const response = await fetch('http://localhost/AMBOT-KAPOYA-NA/raj-express/backend/controller/getOrders.php');
          const data = await response.json();
          if (data.success) {
            this.ongoingOrders = data.ongoingOrders;
            this.historyOrders = data.historyOrders;
          } else {
            console.error('Failed to fetch orders:', data.message);
          }
        } catch (error) {
          console.error('Error:', error);
        }
      },
      goToMenu() {
        this.$router.push('/menu');
      },
      viewOrderDetails(orderId) {
        this.$router.push(`/order-details/${orderId}`);
      },
      goToHome() {
      this.$router.push('/home');
    },
    goToFavorites() {
      this.$router.push('/favorites');
    },
    goToCart() {
      this.$router.push('/cart');
    },
    goToOrder() {
      this.$router.push('/order');
    },
    goToMenu() {
      this.$router.push('/menu');
      },
    },
  };
  </script>

  <style scoped>
  .q-footer {
    box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
  }

  .footer-fixed {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
  z-index: 999; /* Ensure the footer is above other content */

}
.q-tab-active {
  border-radius: 5px;
  padding: 10px;
  transform: translateY(-10px);
}

  </style>
