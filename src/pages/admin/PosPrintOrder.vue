<template>
  <div class="invoice-container" v-if="orders">
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
        <span>Order ID: <span class="text-uppercase"> {{ cusref }}</span></span>
        <span>{{ formatDate( first_order_date ) }}</span>
      </div>
      <div class="divider"></div>
      <q-card-section>
        <div class="text-h6">Products Items</div>
        <q-table :rows="products" :columns="productsON" row-key="name" flat bordered>
          <template v-slot:body-cell-price="props">
            <q-td :props="props">
              {{ formatCurrency(this.props) }}
            </q-td>
          </template>
        </q-table>
      </q-card-section>
      <div class="divider"></div>
      <div class="order-summary">
        <p>Total Items: {{ total_orders }}</p>
        <p>Paid by: {{ payment_method }}</p>
        <p class="total-amount">Total Amount: {{ formatCurrency(order_total) }}</p>
      </div>
      <div class="divider"></div>
      <p class="thank-you">"THANK YOU"</p>
    </div>
  </div>
  <div v-else>Loading order details...</div>
</template>

<script>
import axios from 'axios';

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
      qty: '',
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
        { name: 'product_name', label: 'Product Name', field: (row) => row.product_name, align: 'center' },
        { name: 'product_price', label: 'Price', field: (row) => row.product_price, align: 'center' },
        { name: 'Quantity', label: 'Quantity', field: (row) => row.qty, align: 'left' },
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
    async fetchOrderDetails() {
      const token = this.$route.params.id;
      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/adminController/orderController/orderDetailsController.php', {
          headers: { 'Authorization': token }
        });
        Object.assign(this, response.data);
        this.extra = response.data.extra != 'null' || response.data.extra != '' ? JSON.parse(response.data.extra) : 'No Extra';
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

  },
  mounted() {
    this.fetchOrderDetails();
    this.fetchProduct();
  }
};
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
  background-color: #f27c22;
  color: white;
  border: none;
  padding: 10px 15px;
  cursor: pointer;
  font-size: 1em;
  border-radius: 5px;
  margin-left: 20px;
}

.back-button {
  background-color: #ef3a5d;
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

th,
td {
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
