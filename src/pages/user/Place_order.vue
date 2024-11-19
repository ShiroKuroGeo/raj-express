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
            <div class="text-capitalize">
              <q-radio
                v-for="(a, index) in addresses"
                :key="index"
                :label="a.landmark"
                :val="a.address_id"
                v-model="selectedAddress"
              /> <br>
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
            <div class="" v-for="ex in item.extra">Adds On - {{ ex.name }} - (P {{ ex.price }}.00)</div>
            <div class="">Total Amount: {{ parseInt(item.product_price * item.quantity) }}
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
        <div class="between">
          <small>Adds On Total</small>
          <span class="float-end">₱ {{ extrasTotal }}</span>
        </div>
        <div class="between">
          <small>Product Total</small>
          <span class="float-end">₱ {{ totalProduct.toFixed(2) }}</span>
        </div>
      </div>

      <div class="total-payment q-mt-lg">
        <div class="row justify-between text-subtitle1">
          <span>Total Payment: </span>
          <span class="text-primary">₱ {{ extrasTotal + totalProduct }}</span>
        </div>
      </div>

      <q-btn v-if="isNightTime" disabled class="full-width q-mt-lg" color="red" label="We're Closed" @click="placeOrder" />
      <q-btn v-else class="full-width q-mt-lg" color="red" label="Place Order" @click="placeOrder" />
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
      isNightTime: false,
      paymentMethod: 'cash',
      paymentOptions: [{ label: 'Cash on Delivery', value: 'cash' }, { label: 'Online Payment', value: 'online' }],
      cartItems: [],
      addresses: [],
      addons: [],
      extras: [],
      selectedAddress: 0,
    }
  },
  methods: {
    goBack() {
      this.$router.back();
    },
    selectedIdForAddress(id) {
      this.selectedAddress = id;
    },
    checkTimeRange() {
      const now = new Date();
      const phTime = new Date(
        now.toLocaleString("en-US", { timeZone: "Asia/Manila" })
      );

      const hour = phTime.getHours(); 
      this.isNightTime = hour >= 20 || hour < 6;
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

        this.cartItems = data.cartItems.map(item => ({
          ...item,
          extra: JSON.parse(item.extra)
        }));

        this.extras = data.cartItems.flatMap(item => JSON.parse(item.extra));
      } catch (error) {
        console.error('Error fetching cart items:', error);
      }
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
    async setNotitication(result){
      const content = 'Order Placed';
      const notificationData = {
        user_id: 1,
        customer_ref: 'placedOrder',
        content: content
      };

      try{
        const response = await fetch("http://localhost/raj-express/backend/controller/admincontroller/notificationController/setNotificationController.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(notificationData)
        });

        if(response.status == 200){
          if(result == 1){
            alert('Order Placed!');
            this.$router.push('/home');
          }else{
            window.location.href = result;
          }
        }else{
          alert('The status is : '+response.status);
        }

      }catch(error){
        console.log('Error in '+ error);
      }

    },
    async placeOrder() {
      try {
        
        if (this.selectedAddress != 0) {
          if(this.paymentMethod === 'cash'){

            const token = localStorage.getItem('token');
            let orderData = [];
  
            this.cartItems.forEach(item => {
              const productTotal = parseFloat(item.product_price) * parseInt(item.quantity);
  
              const addonsTotal = this.extras
                  .filter(addon => addon.selected)
                  .reduce((total, addon) => {
                      return total + (parseFloat(addon.price) * (addon.quantity || 1)); 
                  }, 0);
  
              const paymentTotal = (productTotal + addonsTotal).toFixed(2);
  
              orderData.push({
                user_id: token,
                cart_id: item.cart_id,
                product_id: item.product_id,
                address_id: this.selectedAddress,
                order_qty: item.quantity,
                totalExtra: this.extras.length > 0 ? this.extras : null,
                extra: item.extra.length > 0 ? item.extra : null,
                payment_method: this.paymentMethod,
                payment_total: paymentTotal,
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
              const home = 1;
              this.setNotitication(home);
            } else {
              throw new Error(result.error || "Failed to add product to cart");
            }
          }else if(this.paymentMethod === 'online'){
            const token = localStorage.getItem('token');
            let orderData = [];

            this.cartItems.forEach(item => {
              const productTotal = parseFloat(item.product_price) * parseInt(item.quantity);
  
              const addonsTotal = this.extras
                  .filter(addon => addon.selected)
                  .reduce((total, addon) => {
                      return total + (parseFloat(addon.price) * (addon.quantity || 1)); 
                  }, 0);
  
              const paymentTotal = (productTotal + addonsTotal).toFixed(2);
              let onlineTotal = (productTotal + addonsTotal) * 100;

              // Now data
              item.extra.forEach((addon) => {
                  orderData.push({
                      user_id: token,
                      cart_id: item.cart_id,
                      product_id: item.product_id, // Use the product ID or add-on-specific ID if available
                      address_id: this.selectedAddress,
                      order_qty: addon.quantity,
                      totalExtra: null, // Addons don't have nested extras
                      extra: null, // Addons are treated as individual items
                      payment_method: this.paymentMethod,
                      payment_total: parseInt(paymentTotal),
                      payment_status: this.paymentMethod === 'cash' ? 'pending' : 'pending on gcash',
                      onlineTotal: onlineTotal,
                      productTotal: addon.price * 100, // Add-on total
                      addonTotal: 0, // No nested add-ons
                      description: addon.name, // Add-on name or description
                      name: addon.name,
                      quantity: addon.quantity,
                  });
              });

              // Add the main product after processing add-ons
              orderData.push({
                  user_id: token,
                  cart_id: item.cart_id,
                  product_id: item.product_id,
                  address_id: this.selectedAddress,
                  order_qty: item.quantity,
                  totalExtra: this.extras.length > 0 ? this.extras : null,
                  extra: item.extra.length > 0 ? item.extra : null,
                  payment_method: this.paymentMethod,
                  payment_total: parseInt(paymentTotal),
                  payment_status: this.paymentMethod === 'cash' ? 'pending' : 'pending on gcash',
                  onlineTotal: onlineTotal,
                  productTotal: parseInt(item.product_price) * 100,
                  addonTotal: item.extra.reduce((total, addon) => total + parseInt(addon.price || 0), 0), // Sum add-ons
                  description: item.extra.length > 0 ? item.extra : 'no extra',
                  name: item.product_name,
                  quantity: item.quantity,
              });
            });

            try {
                const response = await fetch("http://localhost/raj-express/backend/controller/onlinePaymentController/gcashPaymentController.php", {
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
                    // alert('You are redirected to PayMongo for online payment!');
                    this.setNotitication(result.success);
                } else {
                    throw new Error(result.error || "Failed to add product to cart or missing checkout URL");
                }

            } catch (error) {
                console.error("Payment process error:", error);
                alert("An error occurred during the payment process. Please try again.");
            }
          }else{
            alert('No payment method selected!');
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
    },
  },
  computed: {
    totalPrice() {
      return this.cartItems.reduce((total, item) => {
        const productTotal = parseInt(item.product_price) * parseInt(item.quantity);
        
        return total + productTotal;
      },0)
    },
    
    totalProduct(){
      return this.cartItems.reduce((total, item) => {
        const productTotal = parseInt(item.product_price) * parseInt(item.quantity);
        
        return total + productTotal;
      },0)
    },
    
    extrasTotal(){
      return this.extras.reduce((total, item) => {
        const extrasTotal = parseInt(item.price) * parseInt(item.quantity);
        
        return total + extrasTotal;
      },0)
    },

  },
  created() {
    this.fetchAddresses();
    this.fetchCartItems();
    this.checkTimeRange();
    setInterval(this.checkTimeRange, 60000);
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

.between {
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-top: 8px; 
}

.between small {
    font-size: 1rem;
    color: #333; 
}

.between span {
    font-size: 1rem; 
    font-weight: bold; 
    color: #2c3e50;
}

</style>