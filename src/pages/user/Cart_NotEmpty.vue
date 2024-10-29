<template>
  <q-page padding>
    <div class="q-pa-md" style="max-width: 400px; margin: 0 auto;">
      <div class="row items-center q-mb-md">
        <q-btn flat round icon="arrow_back" color="black" @click="goBack" />
        <div class="text-h6 q-ml-sm">Cart</div>
      </div>

      <q-list bordered separator>
        <q-item v-for="item in cartItems" :key="item.id" class="q-my-sm">
          <q-item-section avatar>
            <q-img :src="item.image" style="width: 80px; height: 80px;" />
          </q-item-section>

          <q-item-section>
            <q-item-label class="text-weight-bold">{{ item.name }}</q-item-label>
            <q-item-label caption>
              <q-rating
                v-model="item.rating"
                :max="5"
                size="1em"
                color="yellow"
                readonly
              />
              {{ item.rating }}
            </q-item-label>
            <q-item-label class="text-weight-bold">₱ {{ item.price }}</q-item-label>
          </q-item-section>

          <q-item-section side>
            <div class="column items-center">
              <q-btn-group flat>
                <q-btn flat dense icon="remove" @click="changeQuantity(item.id, -1)" />
                <q-btn flat dense disable>{{ item.quantity }}</q-btn>
                <q-btn flat dense icon="add" @click="changeQuantity(item.id, 1)" />
              </q-btn-group>
              <q-checkbox v-model="item.selected" @update:model-value="updateTotals" />
            </div>
          </q-item-section>
        </q-item>
      </q-list>

      <div class="q-mt-md">
        <q-checkbox v-model="selectAll" label="All" @update:model-value="toggleSelectAll" />
      </div>

      <div class="q-mt-md">
        <p>Total Items: {{ totalItems }}</p>
        <p>Total Price: ₱ {{ totalPrice.toFixed(2) }}</p>
      </div>

      <q-btn color="red" class="full-width q-mt-md" label="Proceed to checkout" @click="checkout" />
    </div>
  </q-page>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'

export default {
  setup() {
    const $q = useQuasar()
    const router = useRouter()
    const cartItems = ref([])
    const selectAll = ref(false)

    const fetchCartItems = async () => {
      try {
        const response = await fetch('get_cart_items.php')
        cartItems.value = await response.json()
      } catch (error) {
        console.error('Error:', error)
        $q.notify({
          color: 'negative',
          message: 'Failed to fetch cart items'
        })
      }
    }

    const changeQuantity = (itemId, change) => {
      const item = cartItems.value.find(i => i.id === itemId)
      if (item) {
        item.quantity = Math.max(0, item.quantity + change)
        updateTotals()
      }
    }

    const toggleSelectAll = () => {
      cartItems.value.forEach(item => item.selected = selectAll.value)
      updateTotals()
    }

    const totalItems = computed(() => {
      return cartItems.value.reduce((sum, item) => sum + (item.selected ? item.quantity : 0), 0)
    })

    const totalPrice = computed(() => {
      return cartItems.value.reduce((sum, item) => sum + (item.selected ? item.price * item.quantity : 0), 0)
    })

    const updateTotals = () => {
      selectAll.value = cartItems.value.every(item => item.selected)
    }

    const checkout = () => {
      $q.notify({
        color: 'positive',
        message: 'Proceeding to checkout'
      })
      // Implement checkout logic here
    }

    const goBack = () => {
      router.back()
    }

    onMounted(fetchCartItems)

    return {
      cartItems,
      selectAll,
      totalItems,
      totalPrice,
      changeQuantity,
      toggleSelectAll,
      updateTotals,
      checkout,
      goBack
    }
  }
}
</script>
