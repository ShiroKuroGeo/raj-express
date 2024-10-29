<template>
  <q-page class="q-pa-md">
    <h1 class="text-h4 q-mb-md">🎁 Product Review</h1>

    <div class="row items-center q-mb-md">
      <div class="col">
        <h2 class="text-h5">
          Review list
          <q-badge color="secondary" class="q-ml-sm">{{ reviews.length }}</q-badge>
        </h2>
      </div>
      <div class="col-auto">
        <q-input
          v-model="searchQuery"
          dense
          outlined
          placeholder="Search by product name"
          class="q-mr-sm"
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
        <q-btn color="primary" label="Search" @click="searchReviews" />
      </div>
    </div>

    <q-table
      :rows="reviews"
      :columns="columns"
      row-key="index"
      :pagination="{ rowsPerPage: 0 }"
    >
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td key="index" :props="props">
            {{ props.rowIndex + 1 }}
          </q-td>
          <q-td key="productName" :props="props">
            <div class="row items-center">
              <q-avatar size="40px" class="q-mr-sm">
                <img :src="props.row.productImage" :alt="props.row.productName">
              </q-avatar>
              <span>{{ props.row.productName }}</span>
            </div>
          </q-td>
          <q-td key="customerInfo" :props="props">
            <div>{{ props.row.customerName }}</div>
            <div class="text-caption">{{ maskPhoneNumber(props.row.customerPhone) }}</div>
          </q-td>
          <q-td key="reviewText" :props="props">
            {{ props.row.reviewText }}
          </q-td>
          <q-td key="rating" :props="props">
            <q-rating
              v-model="props.row.rating"
              size="1.5em"
              color="yellow"
              icon="star_border"
              icon-selected="star"
              readonly
            />
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </q-page>
</template>

<script>
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import axios from 'axios';

export default {
  setup() {
    const $q = useQuasar();
    const searchQuery = ref('');
    const reviews = ref([]);

    const columns = [
      { name: 'index', label: 'SL', field: 'index', align: 'left' },
      { name: 'productName', label: 'Product Name', field: 'productName', align: 'left' },
      { name: 'customerInfo', label: 'Customer Info', field: 'customerName', align: 'left' },
      { name: 'reviewText', label: 'Review', field: 'reviewText', align: 'left' },
      { name: 'rating', label: 'Rating', field: 'rating', align: 'left' },
    ];

    const fetchReviews = async () => {
      try {
        const response = await axios.get('/api/reviews.php');
        reviews.value = response.data;
      } catch (error) {
        console.error('Error fetching reviews:', error);
        $q.notify({
          color: 'negative',
          message: 'Failed to fetch reviews',
          icon: 'error'
        });
      }
    };

    const searchReviews = async () => {
      try {
        const response = await axios.get(`/api/reviews.php?search=${searchQuery.value}`);
        reviews.value = response.data;
      } catch (error) {
        console.error('Error searching reviews:', error);
        $q.notify({
          color: 'negative',
          message: 'Failed to search reviews',
          icon: 'error'
        });
      }
    };

    const maskPhoneNumber = (phone) => {
      return phone ? phone.slice(0, 3) + '*'.repeat(phone.length - 3) : '';
    };

    return {
      searchQuery,
      reviews,
      columns,
      fetchReviews,
      searchReviews,
      maskPhoneNumber
    };
  },
  mounted() {
    this.fetchReviews();
  }
};
</script>

<style scoped>
.product-review {
  font-family: Arial, sans-serif;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.review-count {
  background-color: #e0e0e0;
  padding: 2px 6px;
  border-radius: 12px;
}

.search-bar input {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.search-button {
  background-color: #ff6b6b;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.review-table {
  width: 100%;
  border-collapse: collapse;
}

.review-table th, .review-table td {
  border: 1px solid #e0e0e0;
  padding: 12px;
  text-align: left;
}

.product-info, .customer-info {
  display: flex;
  align-items: center;
}

.product-image {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
}

.rating {
  color: #4caf50;
}
</style>
