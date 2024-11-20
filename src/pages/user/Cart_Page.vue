cart page

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
          <q-list padding class="q-px-md">
            <q-item
              v-for="(item, index) in cartItems"
              :key="index"
              clickable
              @click="goToCartAdd(item.cart_id)"
              class="product-card q-mb-md"
            >
              <div class="row full-width items-center">
                <div class="col-4">
                  <q-img
                    :src="'http://localhost/raj-express/backend/uploads/' + item.product_image"
                    :ratio="1"
                    class="rounded-borders"
                  />
                </div>
                <div class="col-8 q-pl-md">
                  <div class="text-h6">{{ item.product_name }}</div>
                  <div class="text-h5">₱{{ (item.product_price * item.quantity).toFixed(2) }}</div>
                  <div class="text-caption text-grey">
                    <q-rating
                      v-model="item.rating"
                      size="1.5em"
                      color="orange"
                      readonly
                      :max="5"
                    />
                  </div>
                  <div class="row items-center justify-end q-mt-sm">
                    <q-btn round flat icon="remove" color="grey" @click.stop="decrementQuantity(item)" />
                    <div class="q-px-md">{{ item.quantity || 1 }}</div>
                    <q-btn round flat icon="add" color="red" @click.stop="incrementQuantity(item)" />
                  </div>
                </div>
              </div>
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
        this.cartItems = data.cartItems.map(item => ({
          ...item,
          quantity: item.quantity || 1,
          rating: item.average_rating || 5.0,
          price: parseFloat(item.price || 0)
        }));
      } catch (error) {
        console.error('Error fetching cart items:', error);
      }
    },
    purchase(){
      this.$router.push('/place-order');
    },
    goToCartAdd(cart_id){
      localStorage.setItem('selectedCartId', cart_id);
      this.$router.push('/update-cart/' + cart_id);
    },
    goToHome() {
      this.$router.push('/home');
    },
    async incrementQuantity(item) {
      if (!item.quantity) item.quantity = 1;
      item.quantity++;

      this.onChangeQuantity(item.quantity, item.cart_id);
    },
    async decrementQuantity(item) {
      if (!item.quantity) item.quantity = 1;
      if (item.quantity > 1) {
        item.quantity--;
      }
      
      this.onChangeQuantity(item.quantity, item.cart_id);
    },
    async onChangeQuantity(quantity, id){
      try {
        const datas = {
          quantity: quantity,
          id: id
        };

        const response = await fetch("http://localhost/raj-express/backend/controller/cartController/updateQuantity.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(datas)
        });

        const result = await response.json();
        console.log(result);

        if (result) {
          console.log('Quantity Updated!');
          // $q.notify({
          //   color: 'positive',
          //   message: result.success
          // })
        } else {
          throw new Error(result.error || "Failed to add product to cart");
        }
      } catch (error) {
        console.log('Error in ' + error);
      }
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

.product-card {
  background: #fff1f1;
  border-radius: 12px;
  padding: 12px;
  margin-bottom: 16px;
  transition: all 0.3s ease;
}

.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.text-h6 {
  font-weight: bold;
  margin-bottom: 8px;
}

.text-h5 {
  font-weight: bold;
  color: #000;
}

/* Add this new style for the rating display */
.rating-display {
  display: flex;
  align-items: center;
  gap: 4px;
}
</style>
