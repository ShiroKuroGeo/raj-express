<template>
  <q-page class="q-pa-md">
    <div class="text-h6 q-mb-md">Earning Report</div>
    <q-card flat bordered class="q-pa-md">
      <q-card-section class="row items-center">
        <q-avatar size="lg" class="q-mr-md">
          <img src="https://cdn.quasar.dev/img/avatar.png" alt="Earning Icon">
        </q-avatar>
        <div>
          <div class="text-h6">Earning Report Overview</div>
          <div>Admin: <span class="text-primary">admin</span></div>
          <div>Date: ( {{ startDate }} - {{ endDate }} )</div>
        </div>
        <q-btn icon="home" flat round class="q-ml-auto" />
      </q-card-section>

      <q-card-section class="q-mt-md">
        <div class="row">
          <q-input
            v-model="startDate"
            mask="####-##-##"
            label="Start Date"
            outlined
            dense
            type="date"
            class="col-12"
            style="margin-top: 10px"
          />
          <q-input
            v-model="endDate"
            mask="####-##-##"
            label="End Date"
            outlined
            dense
            type="date"
            class="col-12"
            style="margin-top: 10px"
          />
          <q-btn color="orange" label="Show" @click="fetchReports" class="col-12" style="margin-top: 10px" />
        </div>
      </q-card-section>

      <q-card-section class="q-mt-md">
        <div class="row">
          <q-card flat bordered class="col">
            <q-card-section>
              <div class="text-subtitle1">Total Sold</div>
              <div class="text-h6">{{ totalSold }}$ <q-icon name="attach_money" /></div>
              <div>{{ soldPercent }}%</div>
            </q-card-section>
          </q-card>

        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
  setup() {
    const startDate = ref('')
    const endDate = ref('')
    const totalSold = ref(0)
    const totalTax = ref(0)

    const soldPercent = ref(0)
    const taxPercent = ref(0)

    const fetchReports = async () => {
      try {
        const response = await axios.get('/api/earnings_reports.php', {
          params: { start_date: startDate.value, end_date: endDate.value }
        })
        const data = response.data
        totalSold.value = data.total_sold
        totalTax.value = data.total_tax

        const total = totalSold.value + totalTax.value
        soldPercent.value = total ? ((totalSold.value / total) * 100).toFixed(2) : 0
        taxPercent.value = total ? ((totalTax.value / total) * 100).toFixed(2) : 0
      } catch (error) {
        console.error('Failed to fetch reports:', error)
      }
    }

    return {
      startDate,
      endDate,
      totalSold,
      soldPercent,
      fetchReports
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
