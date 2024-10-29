<template>
  <q-page class="q-pa-md">
    <q-card v-if="order">
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
                  <q-item-label>{{ order.order_number || 'N/A' }}</q-item-label>
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
                  <q-item-label>{{ order.total_items || 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label caption>Order Total</q-item-label>
                  <q-item-label>{{ order.order_total ? formatCurrency(order.order_total) : 'N/A' }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </div>
      </q-card-section>

      <q-card-section>
        <div class="text-h6">Order Items</div>
        <q-table
          :rows="order.products || []"
          :columns="productColumns"
          row-key="product_id"
          flat
          bordered
        >
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

    <div v-else class="text-center q-pa-md">
      <q-spinner color="primary" size="3em" />
      <p>Loading order details...</p>
    </div>
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
    const order = ref(null);

    const productColumns = [
      { name: 'product_name', label: 'Product', field: 'product_name', align: 'left' },
      { name: 'quantity', label: 'Quantity', field: 'quantity', align: 'center' },
      { name: 'price', label: 'Price', field: 'price', align: 'right' },
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
        const orderNumber = decodeURIComponent(route.params.id);
        console.log('Fetching order details for order number:', orderNumber);

        const url = '';
        const params = {
          action: 'get_order_details',
          order_number: orderNumber
        };
        console.log('Request URL:', url);
        console.log('Request params:', params);

        console.log('Sending request...');
        const response = await axios.get(url, { params });
        console.log('Received response');

        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        console.log('Response data:', response.data);

        if (response.data && response.data.products) {
          // Assuming the backend returns the order details directly
          order.value = {
            ...response.data,
            success: true // Add this flag manually
          };
          console.log('Order details:', order.value);
        } else {
          console.error('Invalid response format:', response.data);
          throw new Error('Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching order details:', error);
        if (axios.isAxiosError(error)) {
          console.error('Axios error:', error.toJSON());
          if (error.response) {
            console.error('Error data:', error.response.data);
            console.error('Error status:', error.response.status);
            console.error('Error headers:', error.response.headers);
          } else if (error.request) {
            console.error('Error request:', error.request);
          } else {
            console.error('Error message:', error.message);
          }
          console.error('Error config:', error.config);
        } else {
          console.error('Non-Axios error:', error);
        }
        alert('Failed to fetch order details. Please check the console for more information.');
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
          orderData: JSON.stringify(order.value)
        }
      });
    };

    onMounted(() => {
      fetchOrderDetails();
    });

    return {
      order,
      productColumns,
      formatCurrency,
      formatDate,
      goBack,
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
