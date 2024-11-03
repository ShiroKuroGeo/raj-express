<template>
  <q-page class="q-pa-md">
    <q-card v-for="order in orders">
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
                  <q-item-label>{{ order.order_id || 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Date</q-item-label>
                  <q-item-label>{{ order.created_at ? formatDate(order.created_at) : 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Payment Method</q-item-label>
                  <q-item-label>{{ order.payment_method || 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
          <div class="col-12 col-md-6">
            <q-list>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Total Items</q-item-label>
                  <q-item-label>{{ order.qty || 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Order Total</q-item-label>
                  <q-item-label>{{ order.payment_total ? formatCurrency(order.payment_total) : 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </div>
      </q-card-section>

      <q-card-section>
        <div class="text-h6">Products Items</div>
        <q-table :rows="products" :columns="productsON" row-key="name" flat bordered>
          <template v-slot:body-cell-product_image="props">
            <q-td :props="props" align="left">
              <img :src="'http://localhost/raj-express/backend/uploads/'+props.row.product_image" alt="Product Image" style="width: 50px; height: auto;">
            </q-td>
          </template>
          <template v-slot:body-cell-price="props">
            <q-td :props="props">
              {{ formatCurrency(props.value) }}
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <q-card-section>
        <div class="text-h6">Adds On Items</div>
        <q-table :rows="order.extra" :columns="addsOns" row-key="name" flat bordered>
          <template v-slot:body-cell-price="props">
            <q-td :props="props">
              {{ formatCurrency(props.value) }}
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat color="primary" label="Back" @click="goBack" />
        <q-btn color="primary" label="Print" @click="printOrder" />
      </q-card-actions>
    </q-card>

  </q-page>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'PosViewDetails',

  setup() {
    const route = useRoute();
    const router = useRouter();
    const orders = ref([]);
    const products = ref([]);

    const addsOns = [
      { name: 'addson', label: 'Adds On', field: (row) => row.name, align: 'left' },
      { name: 'quantity', label: 'Quantity', field: (row) => row.quantity, align: 'center' },
      { name: 'price', label: 'Price', field: (row) => row.price, align: 'right' },
    ];

    const productsON = [
      { name: 'product_image', label: 'Product Image', field: row => 'http://localhost/raj-express/backend/uploads/' + row.product_image, align: 'left' },
      { name: 'product_name', label: 'Product Name', field: (row) => row.product_name, align: 'center' },
      { name: 'product_price', label: 'Price', field: (row) => row.product_price, align: 'center' },
      { name: 'product_id', label: 'Product Id', field: (row) => row.product_id, align: 'right' },
    ];

    const formatCurrency = (value) => {
      const number = parseFloat(value);
      return isNaN(number) ? '0.00' : number.toFixed(2);
    };

    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
    };

    const fetchOrderDetails = async () => {
      try {
        const token = route.params.id;

        const response = await axios.get('http://localhost/raj-express/backend/controller/adminController/orderController/orderDetailsController.php',{
          headers:{
            'Authorization': token
          }
        });
        
        orders.value = response.data.orderDetails;
      } catch (error) {
        console.log('Error in ' + error);
      }
    };

    const fetchProduct = async () => {
      try {
        const token = route.params.id;

        const response = await axios.get('http://localhost/raj-express/backend/controller/adminController/orderController/orderDetailsProductController.php',{
          headers:{
            'Authorization': token
          }
        });
        
        products.value = response.data.orderDetails;
      } catch (error) {
        console.log('Error in ' + error);
      }
    };

    const goBack = () => {
      router.push({ name: 'orders' });  // Change 'pos-orders' to 'orders'
    };

    const printOrder = () => {
      // Instead of window.print(), we'll navigate to the PosPrintOrder component
      router.push({
        name: 'pos-print-order',
        params: {
          orderData: JSON.stringify(orders.value)
        }
      });
    };

    onMounted(() => {
      fetchOrderDetails();
      fetchProduct();
    });

    return {
      orders,
      addsOns,
      formatCurrency,
      formatDate,
      fetchProduct,
      goBack,
      products,
      productsON,
      printOrder
    };
  }
}
</script>

<style scoped>
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
</style>
