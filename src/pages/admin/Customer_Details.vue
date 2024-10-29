<template>
  <q-page padding>
    <div class="row items-center q-mb-md">
      <q-avatar size="32px" class="q-mr-sm">
        <q-icon name="people" color="primary" />
      </q-avatar>
      <h5 class="q-my-none">Customer Details</h5>
    </div>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12">
        <h6 class="q-my-none">Customer ID #{{ customer.id }}</h6>
        <q-badge color="grey" class="q-mt-xs">
          Joined at: {{ customer.joined_at }}
        </q-badge>
      </div>
    </div>

    <div class="row q-mb-md">
      <div class="col-12 col-md-8">
        <div class="row items-center q-col-gutter-md">
          <div class="col-grow">
            <h6 class="q-my-none">Order List <q-badge color="primary" rounded>{{ customer.orders.length }}</q-badge></h6>
          </div>
          <div class="col-auto">
            <q-input v-model="searchOrderId" dense outlined placeholder="Search by order ID">
              <template v-slot:append>
                <q-btn color="primary" label="Search" @click="searchOrder" />
              </template>
            </q-input>
          </div>
        </div>

        <q-table
          :rows="customer.orders"
          :columns="orderColumns"
          row-key="id"
          flat
          bordered
        >
          <template v-slot:body-cell-action="props">
            <q-td :props="props">
              <q-btn flat round color="primary" icon="visibility" @click="viewOrder(props.row.id)" />
              <q-btn flat round color="primary" icon="receipt_long" @click="viewInvoice(props.row.id)" />
            </q-td>
          </template>
        </q-table>
      </div>

      <div class="col-12 col-md-4">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-h6">{{ customer.name }}</div>
            <q-item>
              <q-item-section avatar>
                <q-avatar>
                  <img :src="customer.avatar">
                </q-avatar>
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ customer.email }}</q-item-label>
                <q-item-label caption>{{ customer.phone }}</q-item-label>
                <q-item-label caption>{{ customer.orders.length }} Orders</q-item-label>
              </q-item-section>
            </q-item>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <div class="text-h6">Addresses</div>
            <q-list>
              <q-item v-for="address in customer.addresses" :key="address.id">
                <q-item-section avatar>
                  <q-icon :name="address.type === 'home' ? 'home' : 'business'" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ address.type === 'home' ? 'Home' : 'Work' }}</q-item-label>
                  <q-item-label caption>{{ address.phone }}</q-item-label>
                  <q-item-label caption>{{ address.full_address }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-md">
      <div class="col-auto">
        <q-btn color="primary" label="Dashboard" icon="dashboard" @click="goToDashboard" />
      </div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'

export default defineComponent({
  name: 'CustomerDetails',

  setup() {
    const $q = useQuasar()
    const customer = ref({})
    const searchOrderId = ref('')
    const orderColumns = [
      { name: 'id', label: 'NO.', field: 'id', sortable: true, align: 'left' },
      { name: 'order_id', label: 'Order ID', field: 'order_id', sortable: true, align: 'left' },
      { name: 'total_amount', label: 'Total Amount', field: 'total_amount', sortable: true, align: 'left' },
      { name: 'action', label: 'Action', field: 'action', align: 'center' }
    ]

    onMounted(async () => {
      try {
        const response = await fetch('/api/customer-details.php')
        customer.value = await response.json()
      } catch (error) {
        console.error('Error fetching customer details:', error)
        $q.notify({
          color: 'negative',
          message: 'Failed to load customer details'
        })
      }
    })

    const searchOrder = () => {
      // Implement order search functionality
    }

    const viewOrder = (orderId) => {
      // Implement view order functionality
    }

    const viewInvoice = (orderId) => {
      // Implement view invoice functionality
    }

    const goToDashboard = () => {
      // Implement navigation to dashboard
    }

    return {
      customer,
      searchOrderId,
      orderColumns,
      searchOrder,
      viewOrder,
      viewInvoice,
      goToDashboard
    }
  }
})
</script>
