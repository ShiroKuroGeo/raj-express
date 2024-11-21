<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md">
      <div class="col col-12">
        <q-card class="pt-0">
          <q-card-section>
            <span class="mt-1 pt-1" style="font-size: 20px; font-weight: bold">Product Section</span>
            <div class="row q-col-gutter-md q-mb-md">
              <div class="col-12">
                <q-select v-model="selectedCategory" :options="categories" label="All Categories" dense @update:model-value="filterProducts" />
              </div>
              <div class="col-12">
                <q-input v-model="searchQuery" dense placeholder="Search here" outlined clearable debounce="300" @update:model-value="filterProducts" >
                  <template v-slot:append>
                    <q-icon name="search" />
                  </template>
                </q-input>
              </div>
            </div>

            <div class="row q-col-gutter-md">
              <div v-for="product in filteredProducts" :key="product.id" class="col-12 q-mb-md" >
                <q-card @click="addToOrder(product)" class="product-card q-hoverable q-pa-sm" :class="{ 'unavailable': !product.available }" >
                  <q-img :src="product.image" :alt="product.name" style="height: 150px; object-fit: cover;" >
                    <template v-slot:error>
                      <div class="absolute-full flex flex-center bg-negative text-white">
                        Image not available
                      </div>
                    </template>
                    <div class="absolute-bottom text-subtitle2 text-center bg-black bg-opacity-50 text-white" v-if="!product.available">
                      Out of Stock
                    </div>
                  </q-img>
                  <q-card-section>
                    <div class="text-subtitle1">{{ product.name }}</div>
                    <div class="text-caption">₱{{ formatPrice(product.price) }}</div>
                  </q-card-section>
                </q-card>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col col-12">
        <q-card>
          <q-card-section class="row items-center justify-between">
            <div class="text-h6">Order</div>
            <q-btn color="primary" label="New Order" @click="newOrder" />
          </q-card-section>

          <q-card-section>
            <q-table :rows="order" :columns="columns" row-key="id" hide-pagination :pagination="{ rowsPerPage: 0 }" flat bordered >
              <template v-slot:body="props">
                <q-tr :props="props">
                  <q-td key="name" :props="props">
                    {{ props.row.name }}
                  </q-td>
                  <q-td key="qty" :props="props">
                    <q-input v-model.number="props.row.qty" type="number" dense outlined min="1" style="width: 60px" @update:model-value="updateOrder(props.row)" />
                  </q-td>
                  <q-td key="price" :props="props">
                    ₱{{ formatPrice(props.row.price * props.row.qty) }}
                  </q-td>
                  <q-td key="actions" :props="props">
                    <q-btn flat round color="negative" icon="close" size="sm" @click="removeItem(props.rowIndex)" />
                  </q-td>
                </q-tr>
              </template>
            </q-table>

            <q-list class="q-mt-md">
              <q-item>
                <q-item-section>
                  <q-item-label class="text-weight-bold">Total</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-item-label class="text-weight-bold">₱{{ formatPrice(totalAmount) }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>

          <q-card-section>
            <div class="q-mb-md">
              <div class="text-subtitle2">Payment Method:</div>
              <q-btn-toggle v-model="paymentMethod" :options="[   {label: 'Cash', value: 'cash'},   {label: 'G-Cash', value: 'gcash'} ]" color="grey" toggle-color="green" />
            </div>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn color="red" label="Cancel Order" @click="cancelOrder" :disable="order.length === 0" class="q-mr-sm" />
            <q-btn color="green" label="Place Order" @click="placeOrder" :disable="order.length === 0" />
          </q-card-actions>
        </q-card>
      </div>
    </div>
    <q-dialog v-model="showConfirmDialog" persistent>
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Order Summary</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <p>Total Items: {{ order.length }}</p>
          <p>Total Amount: ₱{{ formatPrice(totalAmount) }}</p>
          <p>Payment Method: {{ paymentMethod }}</p>
          <div>Products Name:
            <ul>
              <li v-for="item in order" :key="item.id">
                {{ item.name }} (x{{ item.qty }}) - ₱{{ formatPrice(item.price * item.qty) }}
              </li>
            </ul>
          </div>
          <p>Do you want to confirm this order?</p>
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup @click="cancelOrder" />
          <q-btn flat label="OK" v-close-popup @click="confirmOrder" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: "PosNewSale",
  setup() {
    const selectedCategory = ref("All Categories");
    const searchQuery = ref("");
    const categories = ref(["All Categories"]);
    const products = ref([]);
    const filteredProducts = ref([]);
    const order = ref([]);
    const paymentMethod = ref('cash');
    const showConfirmDialog = ref(false);
    const columns = [
      { name: 'name', label: 'Item Name', field: 'name', align: 'left' },
      { name: 'qty', label: 'Qty', field: 'qty', align: 'center' },
      { name: 'price', label: 'Price', field: row => row.price * row.qty, align: 'right' },
      { name: 'actions', label: '', field: 'actions', align: 'center' }
    ];

    const totalAmount = computed(() => {
      return order.value.reduce((sum, item) => sum + item.price * item.qty, 0);
    });

    const fetchProducts = async () => {
      try {
        const response = await axios.get("http://localhost/raj-express/backend/controller/posnewsale.php");
        console.log("Response from backend:", response.data);
        products.value = response.data.products || [];
        filteredProducts.value = products.value;

        const uniqueCategories = [...new Set(products.value.map(product => product.category))];
        categories.value = ["All Categories", ...uniqueCategories];
      } catch (error) {
        console.error("Error fetching products:", error);
      }
    };

    const filterProducts = () => {
      filteredProducts.value = products.value.filter(product => {
        const categoryMatch = selectedCategory.value === "All Categories" || product.category === selectedCategory.value;
        const searchMatch = !searchQuery.value || product.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        return categoryMatch && searchMatch;
      });
    };

    const addToOrder = (product) => {
      if (!product.available) {
        console.log('This product is currently unavailable.');
        return;
      }
      const existingItem = order.value.find((item) => item.id === product.id);
      if (existingItem) {
        existingItem.qty++;
      } else {
        order.value.push({ ...product, qty: 1 });
      }
    };

    const updateOrder = (item) => {
      item.qty = Math.max(1, item.qty);
    };

    const removeItem = (index) => {
      order.value.splice(index, 1);
    };

    const cancelOrder = () => {
      console.log('Order cancelled');
    };

    const newOrder = () => {
      order.value = [];
      paymentMethod.value = 'cash';
    };

    const placeOrder = () => {
      if (order.value.length === 0) {
        console.log('Cannot place an empty order.');
        return;
      }
      showConfirmDialog.value = true;
    };

    const confirmOrder = async () => {
      let orderData = [];

      order.value.map(item => {
        orderData.push({
          product_id: item.id,
          product_name: item.name,
          quantity: item.qty,
          price: item.price * item.qty,
          payment_status: paymentMethod.value,
          status: 'Paid',
          extra: '',
        });
      });

      const response = await fetch("http://localhost/raj-express/backend/controller/adminController/posController/addOrderController.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ orders: orderData })
      });

      console.log(response.data);

      alert('Success Order!');
      
    };

    const formatPrice = (price) => {
      return typeof price === 'number' ? price.toFixed(2) : '0.00';
    };

    onMounted(() => {
      fetchProducts();
    });

    return {
      selectedCategory,
      searchQuery,
      categories,
      filteredProducts,
      order,
      paymentMethod,
      columns,
      totalAmount,
      addToOrder,
      updateOrder,
      removeItem,
      filterProducts,
      cancelOrder,
      newOrder,
      placeOrder,
      formatPrice,
      confirmOrder,
      showConfirmDialog,
    };
  }
};
</script>

<style scoped>
.product-card {
  cursor: pointer;
  transition: all 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.product-card.unavailable {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
