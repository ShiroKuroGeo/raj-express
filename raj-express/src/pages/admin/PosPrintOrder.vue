<template>
  <div class="invoice-container" v-if="order">
    <div class="invoice-header">
      <p>Print Invoice</p>
    </div>
    <div class="button-container">
      <button @click="printInvoice" class="print-button">Proceed, if printer is ready.</button>
      <button @click="goBack" class="back-button">Back</button>
    </div>
    <div class="divider"></div>

    <div class="invoice-content" id="printArea">
      <h5>R.A.J Food express</h5>
      <div class="divider"></div>
      <div class="order-info">
        <span>Order ID: {{ order.order_number }}</span>
        <span>{{ formatDate(order.created_at) }}</span>
      </div>
      <div class="divider"></div>
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>QTY</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in order.products" :key="item.product_id">

            <td>{{ item.product_name }}</td>
            <td>{{ formatCurrency(item.price) }}</td>
            <td>{{ item.quantity }}</td>
          </tr>
        </tbody>
      </table>
      <div class="divider"></div>
      <div class="order-summary">
        <p>Total Items: {{ order.total_items }}</p>
        <p>Paid by: {{ order.payment_method }}</p>
        <p class="total-amount">Total Amount: {{ formatCurrency(order.order_total) }}</p>
      </div>
      <div class="divider"></div>
      <p class="thank-you">"THANK YOU"</p>
    </div>
  </div>
  <div v-else>Loading order details...</div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'pos-print-order',
  setup() {
    const route = useRoute();
    const router = useRouter();
    const order = ref(null);

    const fetchOrderDetails = async () => {
      try {
        const orderNumber = route.params.id;
        const response = await axios.get('http://localhost/AMBOT-KAPOYA-NA/raj-express/backend/controller/pos_printorders.php', {
          params: {
            action: 'get_order_details',
            order_number: orderNumber
          }
        });

        if (response.data && !response.data.error) {
          order.value = response.data;
        } else {
          throw new Error(response.data.error || 'Failed to fetch order details');
        }
      } catch (error) {
        console.error('Error fetching order details:', error);
        // Handle error (e.g., show an error message to the user)
      }
    };

    onMounted(fetchOrderDetails);

    const formatDate = (dateString) => {
      if (!dateString) return '';
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

    const formatCurrency = (value) => {
      if (value == null) return '';
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'PHP'
      }).format(value);
    };

    const printInvoice = () => {
      window.print();
    };

    const goBack = () => {
      router.go(-1);
    };

    return {
      order,
      formatDate,
      formatCurrency,
      printInvoice,
      goBack
    };
  }
}
</script>



<style scoped>
.invoice-container {
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ccc;
  font-family: Arial, sans-serif;
}
.invoice-header {
  text-align: left;
  margin-bottom: 15px;
}
.invoice-header p {
  margin: 0;
  font-weight: bold;
}
.button-container {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
.print-button {
  background-color:#f27c22;
  color: white;
  border: none;
  padding: 10px 15px;
  cursor: pointer;
  font-size: 1em;
  border-radius: 5px;
  margin-left: 20px;
}
.back-button {
  background-color:#ef3a5d;
  color: white;
  border: none;
  padding: 10px 20px;
  cursor: pointer;
  font-size: 1em;
  border-radius: 5px;
  margin-right: 50px;


}
.invoice-content {
  text-align: center;
}
.invoice-content h5 {
  margin-bottom: 10px;
  font-weight: bold;
  font-size: 1.5em;
  margin-top: 10px;

}
.divider {
  border-top: 1px dashed #000;
  margin: 10px 0;
}
.order-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 10px;
}
th, td {
  border: 1px solid #000;
  padding: 5px;
  text-align: left;
}
.order-summary {
  text-align: left;
}
.total-amount {
  font-weight: bold;
}
.thank-you {
  font-weight: bold;
  margin-top: 10px;
}
@media print {
  /* Hide the header and any other elements you don't want to print */
  .invoice-header,
  .button-container,
  /* Assuming the header in the image has a class or id, add it here */
  .header-container {
    display: none !important;
  }

  .invoice-container {
    border: none;
    padding: 0;
    max-width: none;
  }

  .invoice-content {
    padding: 0;
  }

  /* Ensure the print starts from the top of the page */
  @page {
    margin-top: 0;
    margin-bottom: 0;
  }

  body {
    padding-top: 0;
  }
}

/* Add or modify these styles for better table layout */
th {
  background-color: #f2f2f2;
  font-weight: bold;
}

td {
  vertical-align: top;
}

</style>
