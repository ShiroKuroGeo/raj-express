//resibo nis customer sir.
<template>
  <q-page class="flex flex-center">
    <q-card class="invoice-card">
      <q-card-section>
        <h6 class="text-center">Print Invoice</h6>
        <div class="row q-mt-md">
          <q-btn color="orange" label="Proceed, if printer is ready" class="col" @click="printInvoice" />
          <q-btn color="pink" label="Back" class="col q-ml-sm" @click="goBack" />
        </div>
      </q-card-section>

      <q-card-section>
        <h2 class="text-center">R.A.J Food express</h2>
        <q-separator class="q-my-md" />
        <div class="row justify-between items-center">
          <p><strong>Order ID:</strong> {{ orderNumber }}</p>
          <p>{{ orderDateTime }}</p>
        </div>
        <p><strong>Customer Name:</strong> {{ customerName }}</p>
        <p><strong>Phone Number:</strong> {{ phoneNumber }}</p>
        <p><strong>Address:</strong> {{ address }}</p>
        <q-separator class="q-my-md" />
      </q-card-section>

      <q-card-section>
        <q-table
          :rows="items"
          :columns="columns"
          row-key="name"
          hide-bottom
          flat
          bordered
        />
      </q-card-section>

      <q-card-section>
        <q-separator class="q-my-md" />
        <p>Total Items: {{ totalItems }}</p>
        <p>Paid by: {{ paymentMethod }}</p>
        <p class="text-weight-bold">Total Amount: ₱{{ formatCurrency(totalAmount) }}</p>
        <q-separator class="q-my-md" />
        <p class="text-center text-weight-bold">"THANK YOU"</p>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'InvoicePage',
  data () {
    return {
      orderNumber: '12345',
      orderDateTime: '12/Jun/2022 12:43 pm',
      customerName: 'John Doe',
      phoneNumber: '123-456-7890',
      address: '123 Main St, City',
      items: [
        { qty: 1, name: 'Bihon-Bam e: With Shrimp', price: 30 },
        { qty: 2, name: 'Mountain due', price: 30 }
      ],
      columns: [
        { name: 'qty', label: 'QTY', field: 'qty', align: 'left' },
        { name: 'name', label: 'Name & Desc', field: 'name', align: 'left' },
        { name: 'price', label: 'Price', field: 'price', align: 'left' }
      ],
      totalItems: 3,
      paymentMethod: 'Cash',
      totalAmount: 90
    }
  },
  methods: {
    printInvoice () {
      window.print()
    },
    goBack () {
      this.$router.go(-1)
    },
    formatCurrency (value) {
      return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
      }).format(value)
    }
  }
})
</script>

<style scoped>
.invoice-card {
  width: 100%;
  max-width: 400px;
}

@media print {
  .q-page {
    padding: 0;
  }
  .invoice-card {
    box-shadow: none;
    max-width: none;
  }
}
</style>
