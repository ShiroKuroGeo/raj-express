<template>
  <q-page class="q-pa-md">
    <div class="text-h6 q-mb-md">Expenses</div>
    <q-card flat bordered class="q-pa-md">
      <q-card-section>
        <q-form @submit="addExpense">
          <div class="row q-col-gutter-md">
            <q-input v-model="newExpense.category" label="Category" outlined dense class="col" />
            <q-input v-model="newExpense.description" label="Description" outlined dense class="col" />
            <q-input v-model="newExpense.amount" label="Amount" type="number" outlined dense class="col" />
            <q-input v-model="newExpense.date" label="Date" type="date" outlined dense class="col" />
            <q-btn type="submit" label="Add Expense" color="primary" class="col-auto" />
          </div>
        </q-form>
      </q-card-section>

      <q-card-section class="q-mt-md">
        <q-table
          :rows="expenses"
          :columns="columns"
          row-key="expense_id"
          flat
          bordered
        >
          <template v-slot:top-right>
            <q-input round dense debounce="300" v-model="search" placeholder="Search" />
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  setup() {
    const expenses = ref([])
    const search = ref('')
    const newExpense = ref({
      category: '',
      description: '',
      amount: '',
      date: ''
    })

    const columns = [
      { name: 'category', label: 'Category', field: 'expense_category', align: 'left' },
      { name: 'description', label: 'Description', field: 'description', align: 'left' },
      { name: 'amount', label: 'Amount', field: 'amount', align: 'right' },
      { name: 'date', label: 'Date', field: 'expense_date', align: 'left' }
    ]

    const fetchExpenses = async () => {
      try {
        const response = await axios.get('/api/expenses.php')
        expenses.value = response.data
      } catch (error) {
        console.error('Failed to fetch expenses:', error)
      }
    }

    const addExpense = async () => {
      try {
        await axios.post('/api/expenses.php', newExpense.value)
        fetchExpenses()
        newExpense.value = { category: '', description: '', amount: '', date: '' }
      } catch (error) {
        console.error('Failed to add expense:', error)
      }
    }

    onMounted(fetchExpenses)

    return {
      expenses,
      search,
      newExpense,
      columns,
      fetchExpenses,
      addExpense
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
