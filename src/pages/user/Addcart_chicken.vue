//  diri dapita sir kay for chicken nga add cart page. lahi and uban pero nga add cart.

<!-- FriedChickenItem.vue -->
<template>
  <q-page padding>
    <q-card class="my-card">
      <q-img src="fried_chicken.jpg">
        <div class="absolute-top-left q-pa-sm">
          <q-btn round color="white" text-color="black" icon="arrow_back" />
        </div>
        <div class="absolute-top-right q-pa-sm">
          <q-btn round color="white" text-color="red" icon="favorite" />
        </div>
      </q-img>

      <q-card-section>
        <div class="text-h6">Fried Chicken</div>
        <div class="text-subtitle1">₱ {{ item.price }}</div>
        <div class="text-yellow">
          <q-rating v-model="item.rating" :max="5" size="1em" readonly />
          {{ item.rating }}
        </div>
        <div class="text-caption">{{ item.description }}</div>
      </q-card-section>

      <q-card-section>
        <div class="text-subtitle2">Add ons</div>
        <q-list>
          <q-item v-for="addon in addons" :key="addon.id">
            <q-item-section avatar>
              <q-checkbox v-model="addon.selected" />
            </q-item-section>
            <q-item-section>{{ addon.name }}</q-item-section>
            <q-item-section side>₱ {{ addon.price }}</q-item-section>
            <q-item-section side>
              <q-btn-group flat>
                <q-btn flat round icon="remove" @click="decrementAddon(addon)" />
                <q-btn flat disable>{{ addon.quantity }}</q-btn>
                <q-btn flat round icon="add" @click="incrementAddon(addon)" />
              </q-btn-group>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>

      <q-card-section>
        <div class="text-subtitle2">Combos</div>
        <q-option-group
          v-model="selectedCombo"
          :options="combos"
          type="radio"
        />
      </q-card-section>

      <q-card-section>
        <div class="text-subtitle1">Total: ₱ {{ totalPrice }}</div>
      </q-card-section>

      <q-card-actions align="center">
        <q-btn
          color="red"
          label="Add to cart"
          @click="addToCart"
           style="width: 200px; margin-top: 20px"
        />
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'

export default {
  setup() {
    const $q = useQuasar()
    const item = ref({})
    const addons = ref([])
    const combos = ref([])
    const selectedCombo = ref(null)

    const fetchData = async () => {
      try {
        const response = await fetch('/api/fried-chicken-item.php')
        const data = await response.json()
        item.value = data.item
        addons.value = data.addons.map(addon => ({ ...addon, selected: false, quantity: 0 }))
        combos.value = data.combos.map(combo => ({ label: combo.name, value: combo.id, price: combo.price }))
      } catch (error) {
        console.error('Error fetching data:', error)
        $q.notify({
          color: 'negative',
          message: 'Failed to load item details'
        })
      }
    }

    onMounted(fetchData)

    const incrementAddon = (addon) => {
      addon.quantity++
    }

    const decrementAddon = (addon) => {
      if (addon.quantity > 0) addon.quantity--
    }

    const totalPrice = computed(() => {
      let total = item.value.price || 0
      addons.value.forEach(addon => {
        if (addon.selected) total += addon.price * addon.quantity
      })
      if (selectedCombo.value) {
        const combo = combos.value.find(c => c.value === selectedCombo.value)
        if (combo) total = combo.price
      }
      return total
    })

    const addToCart = () => {
      // Implement add to cart functionality
      $q.notify({
        color: 'positive',
        message: 'Added to cart'
      })
    }

    return {
      item,
      addons,
      combos,
      selectedCombo,
      incrementAddon,
      decrementAddon,
      totalPrice,
      addToCart
    }
  }
}
</script>
