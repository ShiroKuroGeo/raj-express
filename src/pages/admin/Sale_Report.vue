<template>
  <q-page class="q-pa-md">
    <div class="text-h6 q-mb-md">Sale Report</div>
    <q-card flat bordered class="q-pa-md">
      <q-card-section class="row items-center">
        <q-avatar size="lg" class="q-mr-md">
          <img src="https://cdn.quasar.dev/img/avatar.png" alt="Sale Icon">
        </q-avatar>
        <div>
          <div class="text-h6">Sale Report Overview</div>
          <div>Admin: <span class="text-primary">admin</span></div>
        </div>
        <q-btn icon="home" flat round class="q-ml-auto" @click="goToDashboard" />
      </q-card-section>

      <q-card-section class="q-mt-md">
        <div class="row q-col-gutter-md items-center">
          <q-select v-model="filter" :options="filterOptions" label="Filter" outlined dense class="col" />
          <q-input v-model="startDate" mask="####-##-##" label="Start Date" outlined dense type="date" class="col" />
          <q-input v-model="endDate" mask="####-##-##" label="End Date" outlined dense type="date" class="col" />
          <q-btn color="orange" label="Show" @click="fetchReports" class="col-auto" />
        </div>
      </q-card-section>

      <q-card-section class="q-mt-md">
        <div>Total Orders: {{ totalOrders }}</div>
        <div>Total Item Qty: {{ totalQty }}</div>
        <div>Total Amount: {{ totalAmount }}</div>
      </q-card-section>

      <q-card-section class="q-mt-md">
        <div class="row q-col-gutter-md">
          <q-btn label="Print" color="primary" />
        </div>
      </q-card-section>

      <q-table :rows="rows" :columns="columns" row-key="id" class="q-mt-md" flat bordered >
        <template v-slot:top-right>
          <q-input round dense debounce="300" v-model="search" placeholder="Search" />
        </template>
      </q-table>
    </q-card>
  </q-page>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
  setup() {
    const filter = ref('All')
    const filterOptions = ['All', 'Option 1', 'Option 2']
    const startDate = ref('')
    const endDate = ref('')
    const totalOrders = ref(0)
    const totalQty = ref(0)
    const totalAmount = ref(0)
    const search = ref('')
    const rows = ref([])
    const columns = [
      { name: 'id', label: 'No.', field: 'id', align: 'left' },
      { name: 'order', label: 'Order', field: 'order', align: 'left' },
      { name: 'date', label: 'Date', field: 'date', align: 'left' },
      { name: 'qty', label: 'Qty', field: 'qty', align: 'right' },
      { name: 'amount', label: 'Amount', field: 'amount', align: 'right' }
    ]

    const fetchReports = async () => {
      try {
        const response = await axios.get('/api/sale_reports.php', {
          params: { filter: filter.value, start_date: startDate.value, end_date: endDate.value }
        })
        const data = response.data
        rows.value = data.rows
        totalOrders.value = data.totalOrders
        totalQty.value = data.totalQty
        totalAmount.value = data.totalAmount
      } catch (error) {
        console.error('Failed to fetch reports:', error)
      }
    }

    const goToDashboard = () => {
      
    }

    return {
      filter,
      filterOptions,
      startDate,
      endDate,
      totalOrders,
      totalQty,
      totalAmount,
      search,
      rows,
      columns,
      fetchReports,
      goToDashboard
    }
  }
}
</script>

<style scoped>
.q-page {
  max-width: 1000px;
  margin: auto;
}
.q-card-section {
  display: flex;
  align-items: center;
}
.q-card {
  text-align: center;
}
</style>
