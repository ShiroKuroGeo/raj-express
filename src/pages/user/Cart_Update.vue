<template>
  <q-page padding>
    <q-card class="my-card">
      <q-img :src="`http://localhost/raj-express/backend/uploads/${cartItems.product_image}`">
        <div class="absolute-top-left q-pa-sm">
          <q-btn round color="white" text-color="black" icon="arrow_back" @click="goBack" />
        </div>
      </q-img>

      <q-card-section>
        <div class="text-h6">{{ cartItems.product_name }}</div>
        <div class="text-subtitle1">₱ {{ cartItems.product_price }}</div>
        <div class="text-yellow">
          <q-rating v-model="cartItems.rating" :max="5" size="1em" readonly />
          {{ cartItems.rating }}
        </div>
        <div class="text-caption">{{ cartItems.description }}</div>
        <q-btn-group flat>
          <q-item-section side>Quantity : </q-item-section> 
          <q-btn flat round icon="remove" @click="decrementQuantity(cartItems)" />
          <q-btn flat >{{ cartItems.quantity }}</q-btn>
          <q-btn flat round icon="add" @click="incrementQuantity(cartItems)"/>
        </q-btn-group>
      </q-card-section>

      <q-card-section>
        <div class="text-subtitle2">Add ons</div>
        <q-list>
            <q-item v-for="(addon, key) in addons" :key="addon.id">
                <q-item-section avatar>
                    <q-checkbox v-model="addon.selected" />
                </q-item-section>
                <q-item-section>{{ addon.ao_name }}</q-item-section>
                <q-item-section side>₱ {{ addon.ao_price }}</q-item-section>
                <q-item-section side>
                    <q-btn-group flat>
                        <q-btn flat round icon="remove" @click="changeQuantity(key, -1)" :disable="!addon.selected || addon.quantity === 0" />
                        <q-btn flat disable>{{ addon.quantity }}</q-btn>
                        <q-btn flat round icon="add" @click="changeQuantity(key, 1)" :disable="!addon.selected" />
                    </q-btn-group>
                </q-item-section>
            </q-item>
        </q-list>
      </q-card-section>

      <q-card-section>
        <div class="text-subtitle1">Total: ₱ {{ totalPrice }}</div>
      </q-card-section>

      <q-card-actions align="center">
        <q-btn
          :disable="totalPrice < 50"
          color="red"
          label="Update To Cart"
          @click="updateCart"
           style="width: 200px; margin-top: 20px"
        />
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import axios from 'axios'

export default {
  data(){
    return{
      quantity: 1,
      cartItems: [],
      extras: [],
      addons: [],
    }
  },
  methods:{
    async fetchCarts (){
      try {
        const token = localStorage.getItem('selectedCartId');
        const response = await axios.get('http://localhost/raj-express/backend/controller/cartController/getSelectedCart.php', {
          headers: {
            'Authorization': token
          }
        });
        
        this.cartItems = response.data.cartItems;
        this.extras = response.data.cartItems.extra ? JSON.parse(response.data.cartItems.extra) : [];
        this.extras = this.extras.map(addon => ({
          ...addon,
          selected: true, 
        }));
      } catch (error) {
        console.error('Error fetching specials:', error)
      }
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
    },
    async addToWishlist () {
      try {
        const token = localStorage.getItem('token');

        const addToCartData = {
          product_id: this.$route.params.id,
          user_id: token,
        };

        const response = await fetch("http://localhost/raj-express/backend/controller/addToWishlist.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(addToCartData)
        });

        if (!response.ok) {
          const errorMessage = await response.text();
          throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
        }

        const result = await response.json();
        console.log(result);

        if (result) {
          alert('Success');
          // $q.notify({
          //   color: 'positive',
          //   message: result.success
          // })
        } else {
          throw new Error(result.error || "Failed to add product to cart");
        }

      } catch (error) {
        console.error("Error adding to cart:", error.message || error);
      }
    },
    async updateCart () {
      try {
        let selectedAddons = this.addons
          .filter(addon => addon.selected)
          .map(addon => ({
              name: addon.ao_name,
              price: parseFloat(addon.ao_price), 
              quantity: addon.quantity
          }));

        const data = {
          quantity: this.cartItems.quantity,
          extra: selectedAddons,
          cart_id: this.cartItems.cart_id,
        };

        const response = await fetch("http://localhost/raj-express/backend/controller/cartController/updateCart.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data)
        });

        if (!response.ok) {
          const errorMessage = await response.text();
          throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
        }

        const result = await response.json();
        console.log(result);

        if (result) {
          alert('Success');
        } else {
          throw new Error(result.error || "Failed to add product to cart");
        }
      } catch (error) {
        console.log('Error in ' + error);
      }
    },
    goBack(){
      this.$router.back();
    },
    changeQuantity(index, delta) {
        const addon = this.addons[index];
        
        if (addon.selected) {
            addon.quantity = Math.max(0, addon.quantity + delta);
        }
    },
    async fetchAddsOn(){
      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/addOnController/get.php');
        const data = response.data;

        this.addons = data.addOnsItems.map(item => ({
          ...item,
          selected: false, 
          quantity: 1
        }));

      } catch (error) {
        console.error('Error fetching specials:', error);
      }
    }
  },
  created(){
    this.fetchCarts();
    this.fetchAddsOn();
  },
  computed: {
    totalPrice() {
      const productTotal = parseFloat(this.cartItems?.product_price) * parseFloat(this.cartItems?.quantity);
      const addOnsTotal = this.addons.reduce((sum, addon) => {
        if (addon.selected) {
          return sum + (parseInt(addon.ao_price) * addon.quantity);
        }
        return sum;
      }, 0);
      return productTotal + addOnsTotal;
    }
  }
}
</script>

<style scoped>
.my-card {
  max-width: 400px;
  margin: 20px auto;
}
</style>
