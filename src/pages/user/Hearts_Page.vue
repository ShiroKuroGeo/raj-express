

<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container>
      <q-page>
        <div class="text-center text-h6 q-mt-md">My Favorites</div>
          <div v-if="favorites.lenght > 0" class="text-center">
            <q-icon name="search_off" size="100px" class="q-mt-md text-red-7" style="padding: 100px;" />
          <div class="text-h6 q-mt-md">Nothing Found</div>
        </div>

        <div v-else>
          <q-list bordered padding>
            <!-- Kamo na bahala sa frontend ani iapil ang image, product name g kanang uban upat -->
            <q-item v-for="(favorite, index) in favorites" :key="index" clickable>
              <q-item-section>{{ favorite.product_name }}</q-item-section>
              <q-item-section>{{ 'http://localhost/raj-express/backend/' + favorite.product_image }}</q-item-section>
              <q-item-section>{{ favorite.product_description }}</q-item-section>
              <q-item-section>{{ favorite.product_status }}</q-item-section>
            </q-item>
          </q-list>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
  import { ref, onMounted } from 'vue'
  import { useRouter, useRoute } from 'vue-router'
  import axios from 'axios'

  export default {
    setup(){
      const router = useRouter()
      const route = useRoute()
      const favorites = ref([])

      const fetchWishlist = async () => {
        try {
          const token = localStorage.getItem('token');

          const response = await axios.get('http://localhost/raj-express/backend/controller/favorites.php', {
            headers: {
              'Authorization': token
            }
          });

          const data = response.data;

          if(data.success){
            favorites.value = data.favorites;
          }else{
            throw new Error(data.error || 'Failed to fetch products data');
          }
          
        } catch (error) {
          console.error('Error fetching specials:', error)
        }
      }

      onMounted(() => {
        fetchWishlist()
      })

      return {
        favorites,
      }
    }

  };
  </script>

  <style>
  /* Add any specific styles you need */
  .footer-fixed {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
  z-index: 999; /* Ensure the footer is above other content */

}
.q-tab-active {
  border-radius: 5px;
  padding: 10px;
  transform: translateY(-10px);
}
  </style>
