<template>
  <q-page padding>
    <q-card class="my-card">
      <q-img :src="product.image">
        <div class="absolute-top-left q-pa-sm">
          <q-btn round color="white" text-color="black" icon="arrow_back" @click="goBack" />
        </div>
      </q-img>

      <q-card-section>
        <div class="text-h6">{{ product.name }}</div>
        <div class="text-subtitle1">₱ {{ product.price }}</div>
        <div class="text-yellow">
          <q-rating v-model="product.rating" :max="5" size="1em" readonly />
          {{ product.rating }}
        </div>
        <div class="text-caption">{{ product.description }}</div>
      </q-card-section>

      <q-card-section>
        <div class="text-subtitle1">Quantity</div>
        <q-btn-group flat>
          <q-btn flat round icon="remove" @click="changeQuantity(-1)" :disable="quantity === 1" />
          <q-btn flat disable>{{ quantity }}</q-btn>
          <q-btn flat round icon="add" @click="changeQuantity(1)" />
        </q-btn-group>
      </q-card-section>

      <q-card-section>
        <div class="text-subtitle1">Total: ₱ {{ totalPrice }}</div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  setup() {
    const router = useRouter()

    const product = ref({
      id: 1,
      name: "",
      price: 0,
      rating: 0,
      description: "",
      image: "fried_chicken.jpg"
    })

    const quantity = ref(1)

    const fetchProduct = () => {
      product.value = {
        id: 1,
        name: "Fried Chicken",
        price: 60,
        rating: 5,
        description: "3 piece chicken",
        image: "fried_chicken.jpg"
      }
    }

    const changeQuantity = (change) => {
      quantity.value = Math.max(1, quantity.value + change)
    }

    const totalPrice = computed(() => {
      return product.value.price * quantity.value
    })

    const goBack = () => {
      router.go(-1)
    }

    onMounted(fetchProduct)

    return {
      product,
      quantity,
      changeQuantity,
      totalPrice,
      goBack
    }
  }
}
</script>

<style scoped>
.my-card {
  max-width: 400px;
  margin: 20px auto;
}
</style>
