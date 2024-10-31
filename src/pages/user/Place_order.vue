<template>
  <q-page padding>
    <div class="checkout-container">
      <div class="header">
        <q-btn flat round color="black" icon="arrow_back" @click="goBack" />
        <h2 class="text-h5 q-ml-md">Checkout</h2>
      </div>

      <q-card flat bordered class="delivery-address q-mt-md">
        <q-card-section>
          <div class="row justify-between items-center">
            <div class="text-subtitle1">Deliver to</div>
            <q-btn flat color="orange" label="Add Address" @click="addAddress" />
          </div>
          <div v-if="!addresses" class="text-red q-mt-sm">No info added</div>
          <div v-else>
            <div v-for="(a, index) in addresses" :key="a.address_id" class="text-capitalize">
              <!-- {{ a }} -->
              <div class="input-group">
                <!-- I enchange ni ang design kong mahimo  -->
                <input type="checkbox" @change="selectedIdForAddress(a.address_id)" v-model="isChecked"
                  :value="a.address_id"> {{ 1 + index++ }}. {{ a.deliveryAddress }}, {{ a.streetNumber }}, {{ a.landmark
                }}
              </div>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <q-card v-for="item in cartItems" :key="item.cart_id" flat bordered class="order-summary q-mt-md">
        <q-card-section horizontal>
          <q-img :src="`http://localhost/raj-express/backend/uploads/${item.product_image}`"
            style="width: 100px; height: 100px; object-fit: cover;" />
          <q-card-section>
            <div class="text-subtitle1">{{ item.product_name }} <small>x</small>{{ item.quantity }}</div>

            <div class="text-h6">₱ {{ parseInt(item.product_price) }}</div>
            <div class="">Adds On {{ parseInt(item.addOns) }}</div>
            <div class="">Total Amount: {{ parseInt(item.addOns) + parseInt(item.product_price * item.quantity) }}
            </div>
          </q-card-section>
        </q-card-section>
      </q-card>

      <div class="payment-method q-mt-lg">
        <div class="text-subtitle1 q-mb-sm">Payment Method:</div>
        <q-option-group v-model="paymentMethod" :options="paymentOptions" color="red" />
      </div>

      <div class="payment-details q-mt-lg">
        <div class="text-subtitle1 q-mb-sm">Payment Details:</div>
        <div v-for="item in cartItems" :key="item.id" class="row justify-between">
          <span>₱</span>
          <span>{{ parseInt(item.addOns) + parseInt(item.product_price * item.quantity) }}</span>
        </div>
      </div>

      <div class="total-payment q-mt-lg">
        <div class="row justify-between text-subtitle1">
          <span>Total Payment:</span>
          <span class="text-primary">₱ {{ totalPrice.toFixed(2) }}</span>
        </div>
      </div>

      <q-btn class="full-width q-mt-lg" color="red" label="Place Order" @click="placeOrder" />
    </div>
  </q-page>
</template>

<script>
import { ref, } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
  data() {
    return {
      address: '',
      paymentMethod: 'cash',
      paymentOptions: [{ label: 'Cash on Delivery', value: 'cash' }, { label: 'G-Cash', value: 'gcash' }],
      cartItems: [],
      addresses: [],
      selectedAddress: 0,
      isChecked: false,
    }
  },
  methods: {
    goBack() {
      this.$router.back();
    },
    selectedIdForAddress(id) {
      this.selectedAddress = id;
    },
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
      // { "cart_id": 2, "product_id": 30, "user_id": 12, "quantity": 1, "status": "pending", "created_at": "2024-10-28 14:09:01", "updated_at": "2024-10-28 14:09:01", "fullname": "123 123", "contact_number": "123", "email": "123@1", "product_name": "Pancit", "product_price": "30.00", "product_image": "6719bf8359381_pancit-bihon-10.jpg" }
    },
    async fetchAddresses() {
      try {
        const token = localStorage.getItem('token');

        const response = await axios.get('http://localhost/raj-express/backend/controller/addressController/getAllAddress.php', {
          headers: {
            'Authorization': token
          }
        });

        const data = response.data;
        this.addresses = data.addressItems;

      } catch (error) {
        console.error('Error fetching addresses:', error)
      }
    },
    async placeOrder() {
      try {
        if (this.selectedAddress != 0) {
          const token = localStorage.getItem('token');
          let orderData = [];

          this.cartItems.forEach(item => {
            orderData.push({
              user_id: token,
              cart_id: item.cart_id,
              product_id: item.product_id,
              address_id: this.selectedAddress,
              order_qty: item.quantity,
              extra: item.addOnsData,
              payment_method: this.paymentMethod,
              payment_total: parseInt(item.addOns) + parseInt(item.product_price * item.quantity),
              payment_status: this.paymentMethod === 'cash' ? 'pending' : 'pending on gcash',
            });
          });

          const response = await fetch("http://localhost/raj-express/backend/controller/orderController/add.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ orders: orderData })
          });

          if (!response.ok) {
            const errorMessage = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
          }

          const result = await response.json();

          if (result && result.success) {
            alert('Order Created!');
            // $q.notify({
            //   color: 'positive',
            //   message: result.success,
            // });
          } else {
            throw new Error(result.error || "Failed to add product to cart");
          }

        } else {
          console.log('No Selected Address.');
        }

      } catch (error) {
        console.log('Error in ' + error.message);
      }

    },
    addAddress() {
      this.$router.push('/address');
    }
  },
  computed: {
    totalPrice() {
      return this.cartItems.reduce((total, item) => {
        const productTotal = parseInt(item.product_price) * parseInt(item.quantity);

        const addOnsTotal = parseInt(item.addOns) || 0;

        return total + productTotal + addOnsTotal;
      }, 0);
    },
  },
  created() {
    this.fetchAddresses();
    this.fetchCartItems();
  }

}
</script>

<style scoped>
.checkout-container {
  max-width: 600px;
  margin: 0 auto;
}

.header {
  display: flex;
  align-items: center;
}

.delivery-address,
.order-summary {
  background-color: #fff;
}
</style>
