
<template>
  <q-page class="q-pa-md">
    <div class="text-h6 q-mb-md">Order Report</div>
    <q-card flat bordered class="q-pa-md">
      <q-card-section class="row items-center">
        <q-avatar size="lg" class="q-mr-md">
          <img src="https://cdn.quasar.dev/img/avatar.png" alt="Order Icon">
        </q-avatar>
        <div>
          <div class="text-h6">Order Report Overview</div>
          <div>Admin: <span class="text-primary">admin</span></div>
          <div>Date: ( {{ startDate }} - {{ endDate }} )</div>
        </div>
      </q-card-section>

      <q-card-section class="q-mt-md">
        <div class="row q-col-gutter-md items-center">
          <q-input
            v-model="startDate"
            mask="####-##-##"
            label="Start Date"
            outlined
            dense
            type="date"
            class="col"
          />
          <q-input
            v-model="endDate"
            mask="####-##-##"
            label="End Date"
            outlined
            dense
            type="date"
            class="col"
          />
          <q-btn color="orange" label="Show" @click="fetchReports" class="col-auto" />
        </div>
      </q-card-section>

      <q-card-section class="q-mt-md">
        <div class="row q-col-gutter-md">
          <q-card flat bordered class="col">
            <q-card-section>
              <div class="text-subtitle1">Delivered</div>
              <div class="text-h6">{{ delivered }} <q-icon name="shopping_cart" /></div>
              <div>{{ deliveredPercent }}%</div>
            </q-card-section>
          </q-card>
          <q-card flat bordered class="col">
            <q-card-section>
              <div class="text-subtitle1">Returned</div>
              <div class="text-h6">{{ returned }} <q-icon name="undo" /></div>
              <div>{{ returnedPercent }}%</div>
            </q-card-section>
          </q-card>
          <q-card flat bordered class="col">
            <q-card-section>
              <div class="text-subtitle1">Canceled</div>
              <div class="text-h6">{{ canceled }} <q-icon name="cancel" /></div>
              <div>{{ canceledPercent }}%</div>
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
    const delivered = ref(0)
    const returned = ref(0)
    const canceled = ref(0)

    const deliveredPercent = ref(0)
    const returnedPercent = ref(0)
    const canceledPercent = ref(0)

    const fetchReports = async () => {
      try {
        const response = await axios.get('/api/order_reports.php', {
          params: { start_date: startDate.value, end_date: endDate.value }
        })
        const data = response.data
        delivered.value = data.delivered
        returned.value = data.returned
        canceled.value = data.canceled

        const total = delivered.value + returned.value + failed.value + canceled.value
        deliveredPercent.value = total ? ((delivered.value / total) * 100).toFixed(2) : 0
        returnedPercent.value = total ? ((returned.value / total) * 100).toFixed(2) : 0
        canceledPercent.value = total ? ((canceled.value / total) * 100).toFixed(2) : 0
      } catch (error) {
        console.error('Failed to fetch reports:', error)
      }
    }

    return {
      startDate,
      endDate,
      delivered,
      returned,
      canceled,
      deliveredPercent,
      returnedPercent,
      canceledPercent,
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
