<template>
  <q-page class="q-pa-md">
    <div class="q-card__section q-card__section--vert">
      <q-card>
        <q-card-section>
          <h6>Recent Orders</h6>
          <q-table
            :rows="recentOrders"
            :columns="orderColumns"
            row-key="id"
            :pagination.sync="pagination"
            :rows-per-page-options="[10, 20, 30, 50, 0]"
          >
            <template v-slot:body-cell-actions="props">
              <q-td :props="props" class="q-gutter-sm">
                <q-btn flat round color="primary" icon="visibility" @click="viewOrderDetails(props.row)">
                  <q-tooltip>View Details</q-tooltip>
                </q-btn>
                <q-btn flat round color="green" icon="print" @click="printOrder(props.row)">
                  <q-tooltip>Print Order</q-tooltip>
                </q-btn>
              </q-td>
            </template>
            <template v-slot:bottom="props">
              <div class="row full-width">
                <div class="col-12 col-sm-6">
                  <span>
                    Showing {{ props.pagination.rowsPerPage * (props.pagination.page - 1) + 1 }}
                    to {{ Math.min(props.pagination.rowsPerPage * props.pagination.page, props.pagination.rowsNumber) }}
                    of {{ props.pagination.rowsNumber }} entries
                  </span>
                </div>
                <div class="col-12 col-sm-6 text-right">
                  <q-pagination
                    v-model="props.pagination.page"
                    :max="props.pagination.pagesNumber"
                    :max-pages="20"
                    :boundary-links="true"
                  />
                </div>
              </div>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </div>
    <OrderDetailsModal
      :visible="detailsModalVisible"
      :order="selectedOrder"
      @update:visible="detailsModalVisible = $event"
    />
    <q-dialog v-model="printModalVisible">
      <q-card style="min-width: 350px; max-width: 600px;">
        <q-card-section>
          <div class="text-h6">Print Invoice</div>
        </q-card-section>

        <q-card-section>
          <div id="printArea" class="print-invoice">
            <h2>R.A.J Food express</h2>
            <hr>
            <p>Order ID: {{ selectedOrder.order_number }}</p>
            <p>{{ formatDate(selectedOrder.created_at) }}</p>
            <hr>
            <table>
              <thead>
                <tr>
                  <th>QTY</th>
                  <th>Name & Desc</th>
                  <th>Price</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in selectedOrder.products" :key="product.product_id">
                  <td>{{ product.quantity }}</td>
                  <td>{{ product.product_name }}</td>
                  <td>{{ formatCurrency(product.price) }}</td>
                </tr>
              </tbody>
            </table>
            <hr>
            <p>Total Items: {{ selectedOrder.total_items }}</p>
            <p>Paid by: {{ selectedOrder.payment_method }}</p>
            <p><strong>Total Amount: {{ formatCurrency(selectedOrder.order_total) }}</strong></p>
            <hr>
            <p class="text-center">"THANK YOU"</p>
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Back" color="negative" v-close-popup />
          <q-btn flat label="Proceed, if printer is ready." color="positive" @click="proceedPrint" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref,  onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'PosOrders',

  setup() {
    const router = useRouter();
    const recentOrders = ref([]);
    const pagination = ref({
      sortBy: 'created_at',
      descending: true,
      page: 1,
      rowsPerPage: 0, // or any other number you prefer
      rowsNumber: 0
    });

    const orderColumns = ref([]);

    const fetchRecentOrders = async () => {
      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/posnewsale.php?action=get_recent_orders');
        if (response.data && response.data.success) {
          recentOrders.value = response.data.orders;
          pagination.value.rowsNumber = recentOrders.value.length;

          if (recentOrders.value.length > 0) {
            orderColumns.value = Object.keys(recentOrders.value[0]).map(key => ({
              name: key,
              label: key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' '),
              field: key,
              align: key === 'order_total' ? 'right' : 'left'
            }));

            orderColumns.value.push({
              name: 'actions',
              label: 'Actions',
              field: 'actions',
              align: 'center'
            });
          }
        } else {
          throw new Error(response.data.message || 'Failed to fetch recent orders');
        }
      } catch (error) {
        console.error('Error fetching recent orders:', error);
        alert('Failed to fetch recent orders.');
      }
    };

    const formatCurrency = (value) => {
      const number = parseFloat(value);
      return isNaN(number) ? '0.00' : number.toFixed(2);
    };

    const viewOrderDetails = (order) => {
      console.log('Order object:', order);
      if (order && order.order_number) {
        const encodedOrderNumber = encodeURIComponent(order.order_number);
        console.log('Encoded order number:', encodedOrderNumber);
        router.push({
          name: 'pos-order-details',
          params: { id: encodedOrderNumber }
        });
      } else {
        console.error('Invalid order or missing order_number:', order);
        alert('Unable to view order details. Invalid order data.');
      }
    };

    const printModalVisible = ref(false);
    const selectedOrder = ref(null);

    const printOrder = (order) => {
      if (order && order.order_number) {
        router.push({
          name: 'pos-print-order',
          params: { id: order.order_number }
        });
      } else {
        console.error('Invalid order or missing order_number:', order);
        alert('Unable to print order. Invalid order data.');
      }
    };

    const proceedPrint = () => {
      const printContent = document.getElementById('printArea').innerHTML;
      const originalContent = document.body.innerHTML;
      document.body.innerHTML = printContent;
      window.print();
      document.body.innerHTML = originalContent;
      printModalVisible.value = false;
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

    const detailsModalVisible = ref(false);

    onMounted(() => {
      fetchRecentOrders();
    });

    return {
      recentOrders,
      orderColumns,
      pagination,
      viewOrderDetails,
      printOrder,
      selectedOrder,
      detailsModalVisible,
      formatCurrency,
      printModalVisible,
      proceedPrint,
      formatDate
    };
  }
}
</script>

<style scoped>
.q-table__bottom {
  padding-top: 12px;
}

.print-invoice {
  font-family: Arial, sans-serif;
  padding: 20px;
}

.print-invoice h2 {
  text-align: center;
  margin-bottom: 10px;
}

.print-invoice hr {
  border: none;
  border-top: 1px dashed #000;
  margin: 10px 0;
}

.print-invoice table {
  width: 100%;
  border-collapse: collapse;
}

.print-invoice th, .print-invoice td {
  border: 1px solid #000;
  padding: 5px;
  text-align: left;
}

.print-invoice .text-center {
  text-align: center;
}

@media print {
  body * {
    visibility: hidden;
  }
  #printArea, #printArea * {
    visibility: visible;
  }
  #printArea {
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>
