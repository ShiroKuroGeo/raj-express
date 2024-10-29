<template>
  <q-page>
    <q-header class="bg-red text-primary">
      <q-toolbar>
        <q-btn flat round dense icon="arrow_back" class="text-white" @click="$router.back()" />
        <q-toolbar-title class="text-bold text-white">Today's Special</q-toolbar-title>
      </q-toolbar>
    </q-header>

    <div class="q-pa-md">
      <div :key="specialItems.product_id" class="special-item q-mb-md">
        <q-card flat bordered class="bg-pink-1">
          <q-item>
            <q-item-section>
              <q-img
                :src="`http://localhost/raj-express/backend/uploads/${specialItems.product_image}`"
                :ratio="16/9"
                style="border-radius: 8px"
              />
              <div class="row items-center q-mt-sm">
                <div class="text-subtitle1 text-weight-bold">{{ specialItems.product_name }}</div>
                <q-space />
                <q-btn
                  @click="goToAddProduct(specialItems.product_id)"
                  round
                  flat
                  color="red"
                  icon="add"
                  size="sm"
                />
              </div>
              <div class="row items-center">
                <!-- <q-rating
                  size="1em"
                  color="yellow"
                  icon="star"
                  readonly
                /> -->
                <div class="q-ml-sm text-subtitle2">{{ specialItems.rating }}</div>
              </div>
              <div class="text-subtitle1 text-weight-bold">₱ {{ specialItems.product_price }}</div>
              <div class="text-subtitle1 text-weight-normal">{{ specialItems.product_description }}</div>
            </q-item-section>
          </q-item>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

export default {
  setup () {
    const router = useRouter()
    const route = useRoute()
    const specialItems = ref({
      product_id : '',
      category_id : '',
      product_name : '',
      product_description : '',
      product_price : '',
      product_status : '',
      product_image : '',
      created_at : '',
      updated_at : '',
    })

    const addToCart = async () => {
      try {
        const token = localStorage.getItem('token');

        const addToCartData = {
          product_id: route.params.id,
          user_id: token,
          address_payment: '',
          quantity: 1,
          mop: 'Cash on Delivery',
          pending: 'pending'
        };

        const response = await fetch("http://localhost/raj-express/backend/controller/addToCart.php", {
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

        if (result.status === 200) {
          console.log("Added Successfully!");
        } else {
          throw new Error(result.error || "Failed to add product to cart");
        }

      } catch (error) {
        console.error("Error adding to cart:", error.message || error);
      }
    };

    const fetchSpecials = async () => {
      try {

        const response = await axios.get('http://localhost/raj-express/backend/controller/productView.php', {
          headers: {
            'Authorization': route.params.id
          }
        });

        const data = response.success;

        if(!data){
          specialItems.value = {
            product_id: response.data.product_id,
            category_id: response.data.category_id,
            product_name: response.data.product_name,
            product_description: response.data.product_description,
            product_price: response.data.product_price,
            product_status: response.data.product_status,
            product_image: response.data.product_image,
            created_at: response.data.created_at,
            updated_at: response.data.updated_at,
          }
        }else{
          throw new Error(data.error || 'Failed to fetch products data');
        }
        
      } catch (error) {
        console.error('Error fetching specials:', error)
      }
    }

    const goToAddProduct = (productId) => {
      router.push({ name: 'addProduct', params: { id: productId } });
    };

    onMounted(() => {
      fetchSpecials()
    })

    return {
      addToCart,
      goToAddProduct,
      specialItems
    }
  }
}
</script>

<style lang="scss" scoped>
.special-item {
  max-width: 400px;
  margin: 0 auto;
}
</style>
