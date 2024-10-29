<template>
  <q-layout>
    <div v-if="isLoading">Loading...</div>
    <div v-else>
      <!-- Categories Section -->
      <div v-for="category in categories" :key="category.category_id">
        <h5 style="margin-left: 20px; margin-bottom: 5px">
          <span style="font-weight: bold">{{ category.category_name }}</span>
        </h5>
        <div class="category-items">
          <div v-if="getProductsByCategory(category.category_id).length === 0">
            No products available in this category.
          </div>
          <div v-else class="row q-col-gutter-md">
            <div v-for="product in getProductsByCategory(category.category_id)" :key="product.product_id" class="col-xs-6 col-sm-4 col-md-3">
              <q-card class="product-card">
                <!-- <q-img :src="`http://localhost/raj-express/backend/uploads/${product.image_path}`" :ratio="1" style="height: 200px;" /> -->
                <q-card-section>
                  <div class="text-h6">{{ product.product_name }}s</div>
                  <div class="text-h6">{{ product.image_path }}</div>
                  <div class="text-subtitle2">₱{{ product.price }}</div>
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
export default {
  data() {
    return {
      categories: [],
      products: [],
      isLoading: true,
    };
  },
  mounted() {
    this.fetchAllData();
  },
  methods: {
    async fetchAllData() {
      this.isLoading = true;
      try {
        await Promise.all([
          this.fetchCategories(),
          this.fetchProducts()
        ]);
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        this.isLoading = false;
      }
    },
    async fetchCategories() {
      try {
        const response = await fetch('http://localhost/AMBOT-KAPOYA-NA/raj-express/backend/controller/pos_categories.php');
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
    async fetchProducts() {
      try {
        const response = await fetch('http://localhost/AMBOT-KAPOYA-NA/raj-express/backend/controller/pos_products.php');
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        const data = await response.json();
        console.log('Products data:', data); // Log the raw data
        if (data && Array.isArray(data.products)) {
          this.products = data.products;
          console.log('Processed products:', this.products); // Log the processed products
        } else {
          console.error('Unexpected response format:', data);
        }
      } catch (error) {
        console.error("Error fetching products:", error);
      }
    },
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
  z-index: 1000; /* Ensure it's above other content */
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

@media (max-width: 700px) {
  .search {
    width: 70%; /* Adjust width for mobile */
    height: 15vw; /* Shorter height on mobile */
  }
}

.content-container {
  padding-top: 80px; /* To avoid overlap with fixed search bar */
  padding-bottom: 80px; /* To avoid overlap with fixed footer */
}


.specialty {
  width: 1230px;
  height: 300px;
  /* border: 1px solid black; */
  margin-left: 20px;
  border-radius: 30px;
}
@media (max-width: 700px) {
  .specialty {
    width: 95%; /* Adjust width for mobile */
    height: 40vw; /* Shorter height on mobile */
    margin-left: 10px; /* Adjust left margin for mobile */
  }
}
.daily {
  width: 1230px;
  height: 600px;
  border: 1px solid black;
  margin-left: 20px;
  border-radius: 30px;
}
@media (max-width: 700px) {
  .daily {
    width: 95%; /* Adjust width for mobile */
    height: 60vw; /* Shorter height on mobile */
    margin-left: 10px; /* Adjust left margin for mobile */
  }
}
.drinks {
  width: 1230px;
  height: 600px;
  border: 1px solid black;
  margin-left: 20px;
  border-radius: 30px;
}
@media (max-width: 700px) {
  .drinks {
    width: 95%; /* Adjust width for mobile */
    height: 60vw; /* Shorter height on mobile */
    margin-left: 10px; /* Adjust left margin for mobile */
  }
}
.snacks {
  width: 1230px;
  height: 600px;
  border: 1px solid black;
  margin-left: 20px;
  border-radius: 30px;
}
@media (max-width: 700px) {
  .snacks {
    width: 95%; /* Adjust width for mobile */
    height: 60vw; /* Shorter height on mobile */
    margin-left: 10px; /* Adjust left margin for mobile */
  }
}

.bg-orange {
  background-color: #ffa500 !important;
}

.text-orange {
  color: #ffa500 !important;
}
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

.category-items {
  width: 95%;
  min-height: 100px;
  border: 1px solid black;
  margin-left: 20px;
  border-radius: 30px;
  padding: 20px;
}

.product-card {
  width: 100%;
  transition: transform 0.3s;
}

.product-card:hover {
  transform: scale(1.05);
}

@media (max-width: 700px) {
  .category-items {
    width: 90%;
    margin-left: 5%;
  }
}
</style>
