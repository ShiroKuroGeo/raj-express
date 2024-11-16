<template>
  <q-page class="q-pa-md">
    <q-card flat bordered>
      <!-- Order Details -->
      <q-card-section>
        <div class="text-h6">Order Details</div>
      </q-card-section>
      <q-card-section>
        <div class="row q-col-gutter-md">
          <div class="col-12 col-md-6">
            <q-list>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Order Number</q-item-label>
                  <q-item-label class="text-uppercase">{{ cusref || 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Date</q-item-label>
                  <q-item-label>{{ first_order_date ? formatDate(first_order_date) : 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Payment Method</q-item-label>
                  <q-item-label class="text-uppercase">{{ payment_method || 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
          <div class="col-12 col-md-6">
            <q-list>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Total Items</q-item-label>
                  <q-item-label>{{ total_orders || 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Order Total</q-item-label>
                  <q-item-label>{{ total_payment ? formatCurrency(total_payment) : 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
              <!-- <q-item>
                <q-item-section>
                  <q-item-label caption>Order Payment Status</q-item-label>
                  <q-item-label class="text-capitalize">
                    <q-select
                      v-model="payment_status"
                      class="q-mt-md text-capitalize"
                      label="Status"
                      :options="['pending', 'paid', payment_status]"
                      filled
                    />
                  </q-item-label>
                </q-item-section>
              </q-item> -->
              <!-- <q-item>
                <q-item-section>
                  <q-item-label caption>Order Status</q-item-label>
                  <q-item-label>
                    <q-select
                      v-model="status"
                      label="Status"
                      :options="['pending', 'confirm', 'processing', 'delivered', 'returned', 'canceled']"
                      filled
                      class="q-mt-md text-capitalize"
                    />
                  </q-item-label>
                </q-item-section>
              </q-item> -->
            </q-list>
          </div>
        </div>
      </q-card-section>

      <!-- Products Items Table -->
      <q-card-section>
        <div class="text-h6">Products Items</div>
        <q-table :rows="products" :columns="productsON" row-key="name" flat bordered>
          <template v-slot:body-cell-product_image="props">
            <q-td :props="props" align="left">
              <img :src="'http://localhost/raj-express/backend/uploads/' + props.row.product_image" alt="Product Image" style="width: 50px; height: auto;">
            </q-td>
          </template>
          <template v-slot:body-cell-price="props">
            <q-td :props="props">
              {{ formatCurrency(this.props) }}
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <!-- Adds On Items Table -->
      <q-card-section>
        <div class="text-h6">Adds On Items</div>
        <q-table :rows="extra" :columns="addsOns" row-key="name" flat bordered>
          <template v-slot:body-cell-price="props">
            <q-td :props="props">
              {{ formatCurrency(props.row.price) }}
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <q-card-section>
        <div class="row q-col-gutter-md">
          <div class="col-12">
            <q-card flat bordered>
              <q-card-section>
                <div class="text-h6">Delivery Address</div>
                <div id="map" style="position: relative; height: 400px; width: 100%;"></div>
              </q-card-section>
            </q-card>
          </div>
        </div>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat color="primary" label="Back" @click="goBack" />
        <q-btn color="primary" label="Save Status" @click="changeStatus" />
        <q-btn color="primary" label="Print" @click="printOrder" />
      </q-card-actions>
    </q-card>
  </q-page>
</template>


<script>
import axios from 'axios';
import L from 'leaflet';

export default {
  name: 'PosViewDetails',
  data() {
    return {
      orders: [],
      products: [],
      extra: [],
      cusref: '',
      payment_id: '',
      user_id: '',
      total_orders: '',
      product_names: '',
      status: '',
      first_order_date: '',
      addressContactPerson: '',
      addressContactNumber: '',
      deliveryAddress: '',
      latitude: 0,
      longitude: 0,
      streetNumber: '',
      landmark: '',
      payment_method: '',
      total_payment: '',
      payment_status: '',
      map: null,
      addsOns: [
        { name: 'name', label: 'Adds On', field: (row) => row.name, align: 'left' },
        { name: 'price', label: 'Price', field: (row) => row.price, align: 'right' },
        { name: 'quantity', label: 'Quantity', field: (row) => row.quantity, align: 'center' }
      ],
      productsON: [
        { name: 'product_image', label: 'Product Image', field: (row) => 'http://localhost/raj-express/backend/uploads/' + row.product_image, align: 'left' },
        { name: 'product_name', label: 'Product Name', field: (row) => row.product_name, align: 'center' },
        { name: 'product_price', label: 'Price', field: (row) => row.product_price, align: 'center' },
      ],
    };
  },
  methods: {
    formatCurrency(value) {
      const number = parseFloat(value);
      return isNaN(number) ? '0.00' : number.toFixed(2);
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
    },
    async setNotitication(){
      const content = 'Good Day, your has been '+this.status;
      const notificationData = {
        user_id: this.user_id,
        customer_ref: this.cusref,
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
          alert('Notification Sent!');
        }else{
          alert('The status is : '+response.status);
        }

      }catch(error){
        console.log('Error in '+ error);
      }

    },
    async changeStatus(){
      const token = this.$route.params.id;
      const data = {
        product_id: token,
        status: this.status,
        payment_id: this.payment_id,
        payment_status: this.payment_status
      };
      
      const response = await fetch("http://localhost/raj-express/backend/controller/admincontroller/orderController/changeStatusOrderController.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data)
      });

      if(response.status == 200){
        alert('Status Changed!');
        this.setNotitication();
      }else{
        alert('The status is : '+response.status);
      }
    },
    async fetchOrderDetails() {
      const token = this.$route.params.id;
      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/adminController/orderController/orderDetailsController.php', {
          headers: { 'Authorization': token }
        });
        Object.assign(this, response.data);
        this.extra = JSON.parse(response.data.extra);
        this.mapMark(); 
      } catch (error) {
        console.error('Error fetching order details:', error);
      }
    },
    async fetchProduct() {
      const token = this.$route.params.id;
      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/adminController/orderController/orderDetailsProductController.php', {
          headers: { 'Authorization': token }
        });
        this.products = response.data.orderDetails;
      } catch (error) {
        console.error('Error fetching product details:', error);
      }
    },
    goBack() {
      this.$router.push({ name: 'orders' });
    },
    printOrder() {
      this.$router.push({
        name: 'pos-print-order',
        params: {
          orderData: JSON.stringify(this.orders)
        }
      });
    },
    mapMark() {
  if (!this.latitude || !this.longitude) {
    console.error('Invalid latitude or longitude values.');
    return;
  }

  if (this.map) {
    this.map.remove();
  }

  const basakCoordinates = { lat: this.latitude, lng: this.longitude };

  this.map = L.map("map", {
    center: basakCoordinates,
    zoom: 14,
    minZoom: 14,
  });

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '© OpenStreetMap contributors'
  }).addTo(this.map);

  L.marker(basakCoordinates).addTo(this.map)
    .bindPopup('Customer Location') 
    .openPopup();
}

  },
  mounted() {
    this.fetchOrderDetails();
    this.fetchProduct();
    this.mapMark();
  }
};
</script>


<style scoped>
@import 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css';
@media print {
  .q-page {
    padding: 0 !important;
  }

  .q-card {
    box-shadow: none !important;
  }

  .q-btn {
    display: none !important;
  }
}
#map {
  width: 100%;
  height: 400px;
  overflow: hidden;
}

</style>
