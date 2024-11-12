<template>
  <q-layout>
    <div v-if="isLoading">Loading...</div>
    <div v-else>
      <div v-for="category in categories" :key="category.category_id">
        <h5 style="margin-left: 20px; margin-bottom: 5px">
          <span style="font-weight: bold">{{ category.category_name }}</span>
        </h5>
        <div class="category-items">
          <div v-if="getProductsByCategory(category.category_id).length === 0">
            No products available in this category.
          </div>
          <div v-else class="row q-col-gutter-md">
            <div v-for="product in getProductsByCategory(category.category_id)" :key="product.product_id"
              class="col-xs-6 col-sm-4 col-md-3">
              <q-card class="product-card cursor-pointer" @click="goToProductDetails(product.product_id)">
                <q-img :src="`http://localhost/raj-express/backend/uploads/${product.product_image}`" :ratio="1"
                  style="height: 150px;" />
                <q-card-section>
                  <div class="text-h6">{{ product.product_name }}</div>
                  <div class="text-h6">
                    <div v-if="ratings.length">
                      <small> <q-rating :value="calculateAverageRating(product.product_id)" :readonly="true" color="yellow" icon="star" size="10px" :max="5" /> ({{ calculateAverageRating(product.product_id) }})</small>
                    </div>
                    <div v-else>
                      No ratings available for this product.
                    </div>
                  </div>
                  <div class="text-h6">{{ product.product_price }}</div>
                </q-card-section>
              </q-card>
            </div>
          </div>
        </div>
      </div>
    </div>
  </q-layout>
</template>

<script>
import axios from 'axios'
export default {
  data() {
    return {
      categories: [],
      products: [],
      ratings: [], 
      isLoading: true,
      search: '',
    };
  },
  mounted() {
    this.fetchAllData();
    this.fetchRateProduct();
  },
  methods: {
    async fetchAllData() {
      this.isLoading = true;
      try {
        await this.fetchCategories();
        await this.fetchProductsForCategories();
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        this.isLoading = false;
      }
    },
    async fetchCategories() {
      try {
        const response = await fetch('http://localhost/raj-express/backend/controller/pos_categories.php');
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        const data = await response.json();
        if (data && Array.isArray(data.categories)) {
          this.categories = data.categories;
        } else {
          console.error('Unexpected response format:', data);
        }
      } catch (error) {
        console.error("Error fetching categories:", error);
      }
    },
    async fetchProductsForCategories() {
      try {
        const productPromises = this.categories.map(async (category) => {
          const response = await fetch(`http://localhost/raj-express/backend/controller/category_product.php?category_id=${category.category_id}`);
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          const data = await response.json();
          if (Array.isArray(data.products)) {
            this.products = [...this.products, ...data.products];
          }
        });
        await Promise.all(productPromises);
      } catch (error) {
        console.error("Error fetching products by category:", error);
      }
    },
    goToProductDetails(productId) {
      this.$router.push({ name: 'productDetails', params: { id: productId } });
    },
    async fetchRateProduct() {
      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/ratingController/getRateController.php');
        this.ratings = response.data.ratings;
      } catch (error) {
        console.log('Error in fetchRateProduct:', error);
      }
    },
    calculateAverageRating(productId) {
      const productRatings = this.ratings.filter(rating => rating.product_id === productId);
      
      if (productRatings.length === 0) return 0; 

      const total = productRatings.reduce((sum, rating) => sum + parseInt(rating.feedback), 0);
      return parseInt(total / productRatings.length);
    }
  },
  computed: {
    getProductsByCategory() {
      return (categoryId) => {
        return this.products.filter(product => parseInt(product.category_id) === parseInt(categoryId));
      };
    }
  }
};
</script>

<style scoped>
.search {
  width: 50%;
  border: 2px solid black;
  border-radius: 50px;
}

.search-fixed {
  position: fixed;
  top: 0;
  width: 100%;
  padding: 10px;
  z-index: 1000;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

.content-container {
  padding-top: 80px;
  padding-bottom: 80px;
}

.category-items {
  width: 95%;
  min-height: 100px;
  margin-left: 20px;
  border-radius: 30px;
  padding: 20px;
}

.product-card {
  width: 100%;
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
}

.product-card:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>
