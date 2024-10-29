<template>
  <q-page padding>
    <q-table
      title="All Orders"
      :rows="orders"
      :columns="columns"
      row-key="id"
      :loading="loading"
    >
      <template v-slot:top-right>
        <q-input outlined v-model="search" dense debounce="300" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>

      <template v-slot:body-cell-actions="props">
        <q-td :props="props" class="q-gutter-sm">
          <q-btn
            color="primary"
            icon="visibility"
            size="sm"
            flat
            dense
            @click="viewOrder(props.row.id)"
          >
            <q-tooltip>View Order</q-tooltip>
          </q-btn>
          <q-btn
            color="secondary"
            icon="print"
            size="sm"
            flat
            dense
            @click="printOrder(props.row.id)"
          >
            <q-tooltip>Print Order</q-tooltip>
          </q-btn>
        </q-td>
      </template>
    </q-table>
  </q-page>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

export default {
  setup() {
    const orders = ref([]);
    const loading = ref(false);
    const search = ref('');
    const router = useRouter();

    const columns = ref([
      { name: 'id', required: true, label: 'Order ID', align: 'left', field: 'id', sortable: true },
      { name: 'delivery_date', align: 'left', label: 'Delivery Date', field: 'delivery_date', sortable: true },
      { name: 'customer_info', label: 'Customer Info', field: 'customer_info' },
      { name: 'total_amount', label: 'Total Amount', field: 'total_amount', sortable: true },
      { name: 'order_type', label: 'Order Type', field: 'order_type' },
      { name: 'actions', label: 'Actions', align: 'center' }
    ]);

    const fetchOrders = async () => {
      loading.value = true;
      try {
        const response = await axios.get('http://localhost/api.php');
        orders.value = response.data;
      } catch (error) {
        console.error('Failed to fetch orders:', error);
      } finally {
        loading.value = false;
      }
    };

    const viewOrder = (orderId) => {
      router.push({ name: 'CustomerViewOrders', params: { id: orderId } });
    };

    const printOrder = (orderId) => {
      router.push({ name: 'CustomerViewOrders', params: { id: orderId }, query: { print: true } });
    };

    fetchOrders();

    return { orders, columns, loading, search, viewOrder, printOrder };
  }
};
</script>
