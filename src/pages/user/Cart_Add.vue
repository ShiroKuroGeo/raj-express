// kani kay sa tanan product nga add cart sir. kanang ang customer sir ba
// kay dili ka place order kong 50 ra ilang total-amount

<template>
  <q-page padding>
    <q-card class="my-card">
      <q-img :src="`http://localhost/raj-express/backend/uploads/${product.image}`">
        <div class="absolute-top-left q-pa-sm">
          <q-btn round color="white" text-color="black" icon="arrow_back" @click="goBack" />
        </div>
        <div class="absolute-top-right q-pa-sm">
          <q-btn round color="white" text-color="red" icon="favorite" @click="addToWishlist" />
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
        <q-btn-group flat>
          <q-item-section side>Quantity : </q-item-section> 
          <q-btn flat round icon="remove" @click="updateQuantity(-1)" />
          <q-btn flat >{{ quantity }}</q-btn>
          <q-btn flat round icon="add" @click="updateQuantity(1)"/>
        </q-btn-group>
      </q-card-section>

      <q-card-section>
        <div class="text-subtitle2">Add ons</div>
        <q-list>
          <q-item v-for="(addon, key) in addons" :key="addon.id">
            <q-item-section avatar>
              <q-checkbox v-model="addon.selected" />
            </q-item-section>
            <q-item-section>{{ addon.ao_name }}</q-item-section>
            <q-item-section side>₱ {{ addon.ao_price }}</q-item-section>
            <q-item-section side>
              <q-btn-group flat>
                <q-btn flat round icon="remove" @click="changeQuantity(key, -1)" :disable="!addon.selected || addon.quantity === 0" />
                <q-btn flat disable>{{ addon.quantity }}</q-btn>
                <q-btn flat round icon="add" @click="changeQuantity(key, 1)" :disable="!addon.selected" />
              </q-btn-group>
            </q-item-section>
          </q-item>
        </q-list>

      </q-card-section>

      <q-card-section>
        <div class="text-subtitle1">Total: ₱ {{ totalPrice }}</div>
      </q-card-section>

      <q-card-actions align="center">
        <q-btn
          :disable="totalPrice <= 50"
          color="red"
          :label="totalPrice <= 50 ? 'Price must 50 above' : 'Add To Cart'"
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
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

export default {
  setup() {
    const $q = useQuasar()
    const router = useRouter()
    const quantity = ref(1)
    const route = useRoute()
    const product = ref([])
    
    const addons = ref([]);

    // const addons = ref({
    //   Rice: { id: 1, price: 10, quantity: 1, selected: false },
    //   Softdrinks: { id: 2, price: 25, quantity: 1, selected: false }
    // })

    const fetchProduct = async () => {

      try {

        const response = await axios.get('http://localhost/raj-express/backend/controller/productView.php', {
          headers: {
            'Authorization': route.params.id
          }
        });

        const data = response.success;

        if(!data){
          product.value = {
            id: response.data.product_id,
            name: response.data.product_name,
            price: response.data.product_price,
            rating: 5,
            description: response.data.product_description,
            image: response.data.product_image,
          }
        }else{
          throw new Error(data.error || 'Failed to fetch products data');
        }

        } catch (error) {
        console.error('Error fetching specials:', error)
        }

    }

    const fetchAddOns = async () => {

      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/addOnController/get.php');
        const data = response.data;

        console.log(data);

        addons.value = data.addOnsItems.map(item => ({
          ...item,
          selected: false, 
          quantity: 1
        }));

      } catch (error) {
        console.error('Error fetching specials:', error);
      }

    }

    const changeQuantity = (index, change) => {
      const addon = addons.value[index];
      if (addon.selected) {
        addon.quantity = Math.max(0, addon.quantity + change);
      }
    };

    const updateQuantity = (change) => {
      quantity.value = Math.max(1, quantity.value + change);
    };

    
    const totalPrice = computed(() => {
      let total = parseFloat(product.value.price) * quantity.value;

      for (const [item, data] of Object.entries(addons.value)) {
        if (data.selected) {
          total += parseFloat(data.ao_price) * quantity.value;
        }
      }

      return total;
    })

    const totalAddOns = computed(() => {
      let total = 0;
      for (const [item, data] of Object.entries(addons.value)) {
        if (data.selected) {
          total += data.ao_price * data.quantity;
        }
      }
      return total;
    });

    const addToWishlist = async () => {
      try {
        const token = localStorage.getItem('token');

        const addToCartData = {
          product_id: route.params.id,
          user_id: token,
        };

        const response = await fetch("http://localhost/raj-express/backend/controller/addToWishlist.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(addToCartData)
        });

        if (!response.ok) {
          const errorMessage = await response.text();
          throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
        }

        const result = await response.json();
        console.log(result);

        if (result) {
          $q.notify({
            color: 'positive',
            message: result.success
          })
        } else {
          throw new Error(result.error || "Failed to add product to cart");
        }

      } catch (error) {
        console.error("Error adding to cart:", error.message || error);
      }
    }

    const addToCart = async () => {

      try {
        const token = localStorage.getItem('token');

        const addToCartData = {
          product_id: route.params.id,
          user_id: token,
          quantity: quantity.value,
          addOns: totalAddOns.value,
          addOnsData: addons.value
            .filter(addon => addon.selected) 
            .map(addon => ({
              name: addon.ao_name, 
              price: addon.ao_price,
              quantity: addon.quantity
            })),
          pending: 'pending'
        };

        const response = await fetch("http://localhost/raj-express/backend/controller/cartController/add.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(addToCartData)
        });

        if (!response.ok) {
          const errorMessage = await response.text();
          throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
        }

        const result = await response.json();
        console.log(result);

        if (result) {
          $q.notify({
            color: 'positive',
            message: result.success
          })
        } else {
          throw new Error(result.error || "Failed to add product to cart");
        }

      } catch (error) {
        console.error("Error adding to cart:", error.message || error);
      }
    }

    const goBack = () => {
      router.go(-1)
    }

    onMounted(fetchProduct)
    onMounted(fetchAddOns)

    return {
      product,
      addons,
      changeQuantity,
      addToCart,
      quantity,
      fetchAddOns,
      totalPrice,
      addToWishlist,
      totalAddOns,
      updateQuantity,
      addToCart,
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
