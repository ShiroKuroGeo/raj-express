<template>
  <q-page class="q-pa-md">
    <q-card>
      <q-card-section>
        <h4>Add New Product</h4>
      </q-card-section>

      <q-card-section>
        <q-form @submit="handleSubmit">
          <!-- Category Information -->
          <q-select
            v-model="product.category_id"
            :options="categoryOptions"
            option-value="value"
            option-label="label"
            label="Category"
            outlined
            :rules="[val => !!val || 'Category is required']"
            class="q-mb-md"
          >
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey">
                  No results
                </q-item-section>
              </q-item>
            </template>
          </q-select>

          <!-- Product Name Input -->
          <q-input
            v-model="product.product_name"
            label="Product Name"
            placeholder="New Product"
            outlined
            required
          />

          <!-- Short Description -->
          <q-input
            v-model="product.product_description"
            label="Short Description"
            placeholder="Short description"
            outlined
            type="textarea"
            required
          />

          <!-- Price Information -->
          <q-input
            v-model="product.product_price"
            label="Default Price"
            placeholder="Type"
            type="number"
            outlined
            required
          />

          <!-- Product Status -->
          <q-select
            v-model="product.product_status"
            label="Status"
            :options="['Available', 'Not Available']"
            outlined
            required
          />

          <!-- Upload Image -->
          <q-uploader
            v-model="product.product_image"
            label="Upload Product Image"
            accept=".jpg, .png"
            :auto-upload="false"
            outlined
            square
            hide-upload-button
            @added="handleImageUpload">
            <div class="text-center q-pa-md">
              <q-icon name="cloud_upload" size="42px" />
              <span>Upload Image</span>
            </div>
          </q-uploader>

          <!-- Action Buttons -->
          <div class="row justify-end q-gutter-md">
            <q-btn label="Submit" type="submit" color="orange" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      product: {
        product_id: null,
        category_id: null, // Change this to null instead of an empty string
        product_name: '',
        product_description: '',
        product_price: 0,
        product_status: 'Available',
        product_image: null,
      },
      categoryOptions: [], // Changed from categories to categoryOptions
    };
  },
  methods: {
    // Fetch categories from the backend
    async fetchCategories() {
      try {
        const response = await axios.get('http://localhost/AMBOT-KAPOYA-NA/raj-express/backend/controller/pos_categories.php');
        console.log('API response:', response.data); // Log the entire response
        if (response.data && Array.isArray(response.data.categories)) {
          this.categoryOptions = response.data.categories.map(category => ({
            label: category.category_name,
            value: parseInt(category.category_id, 10), // Ensure category_id is a number
          }));
          console.log('Category options:', this.categoryOptions); // Log to check fetched categories
        } else {
          console.error('Unexpected response format:', response.data);
          this.categoryOptions = [];
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
        }
        this.categoryOptions = [];
      }
    },

    // Handle image upload
    handleImageUpload(file) {
      if (file && file.length > 0) {
        this.product.product_image = file[0]; // Store the uploaded image file
      }
    },

    // Save product and handle image upload
    async saveProduct() {
      console.log('Product object before saving:', JSON.stringify(this.product, null, 2));

      if (!this.product.category_id) {
        alert('Please select a category');
        return;
      }

      const formData = new FormData();
      for (const key in this.product) {
        if (this.product[key] !== null && this.product[key] !== undefined && this.product[key] !== '') {
          // For category_id, ensure we're sending the value, not the entire object
          if (key === 'category_id') {
            formData.append(key, this.product[key].value);
          } else {
            formData.append(key, this.product[key]);
          }
          console.log(`Appending to FormData: ${key} = ${formData.get(key)}`);
        } else {
          console.log(`Skipping empty value for: ${key}`);
        }
      }

      formData.append('is_edit', '0');

      console.log('FormData contents:');
      for (let [key, value] of formData.entries()) {
        console.log(key, value);
      }

      const url = 'http://localhost/AMBOT-KAPOYA-NA/raj-express/backend/controller/pos_product.php';

      try {
        const response = await axios.post(url, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        console.log('Server response:', response.data);
        alert('Product added successfully');
        this.handleReset();
        // Redirect to PosProducts.vue
        this.$router.push({ name: 'products' });
      } catch (error) {
        console.error('Error saving product:', error);
        if (error.response) {
          console.error('Error response data:', error.response.data);
          console.error('Error response status:', error.response.status);
          console.error('Error response headers:', error.response.headers);
        } else if (error.request) {
          console.error('No response received:', error.request);
        } else {
          console.error('Error setting up request:', error.message);
        }
        alert('Error saving product. Check console for details.');
      }
    },

    // Handle form submission
    handleSubmit(event) {
      event.preventDefault(); // Prevent default form submission
      console.log('Submitting form with product:', this.product);
      if (this.product.product_name && this.product.category_id && this.product.product_price) {
        this.saveProduct(); // Call saveProduct when form is submitted
      } else {
        console.error('Please fill in all required fields.');
        if (!this.product.category_id) {
          console.error('Category is not selected');
        }
      }
    },

    // Reset form after submission
    handleReset() {
      this.product = {
        category_id: null, // Reset to null instead of an empty string
        product_name: '',
        product_description: '',
        product_price: 0,
        product_status: 'Available',
        product_image: null,
      };
    },
  },

  // Fetch categories when the component is mounted
  mounted() {
    this.fetchCategories();
  },
};
</script>

<style scoped>
.q-uploader .q-uploader__header {
  display: none;
}
</style>
