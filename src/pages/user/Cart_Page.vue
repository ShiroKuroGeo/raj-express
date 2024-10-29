// kaning part sir kay if wlay sud ang cart mao ning porma.

<template>
  <q-layout>
    <q-page-container>
      <q-page>
        <div class="text-center text-h6 q-mt-md">My Cart</div>
        <div v-if="cartItems.length === 0" class="text-center">
          <h3 style="padding: 50px;" >Your cart is empty!</h3>
          <p>Please add some items from the menu</p>
          <q-btn
            label="Explore Menu"
            color="red"
            @click="goToHome"
            style="width: 200px; margin-top: 20px"
          />
        </div>
        <div v-else>
          <q-list bordered padding>
            <q-item v-for="(item, index) in cartItems" :key="index" clickable>
              <q-item-section>{{ item.product_name }}</q-item-section>
            </q-item>
          </q-list>
          <div class="text-center">
            <q-btn
            label="Purchase Cart"
            color="red"
            @click="purchase"
            style="width: 200px; margin-top: 20px"
          />
          </div>
        </div>

      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      darkMode: false,
      cartItems: [],
      selectedOptions: [],
    };
  },
  methods: {
    async fetchCartItems() {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://localhost/raj-express/backend/controller/cartController/get.php', {
          headers: {
            'Authorization': token
          }
        });
        const data = response.data;
        this.cartItems = data.cartItems; 
      } catch (error) {
        console.error('Error fetching cart items:', error);
      }
    },
    purchase(){
      this.$router.push('/place-order');
    },
    goToHome() {
      this.$router.push('/home');
    }
  },
  mounted() {
    this.fetchCartItems();
  }
};
</script>

<style scoped>
.text-center {
  text-align: center;
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
