<template>
  <q-layout view="hHh lpR fFf">
    <!-- HEADER -->
    <q-header elevated class="bg-orange text-white">
      <q-toolbar>
        <!-- BURGER MENU TOGGLE -->
        <q-btn dense flat round icon="menu" @click="toggleDrawer" />

        <q-toolbar-title>
          <div style="display: flex; align-items: center;">
            <!-- Logo -->
            <img src="/icons/logo.png" alt="logo" style="height: 40px; margin-right: 10px;" />
            <!-- Name next to the Logo -->
            <span>R.A.J Food Express</span>
          </div>
        </q-toolbar-title>

        <!-- Dark Mode Toggle -->
        <q-btn flat round @click="toggleDarkMode">
          <q-icon :name="darkMode ? 'brightness_4' : 'brightness_7'" />
        </q-btn>


        <!-- User Avatar -->
        <div>
          <q-btn flat>{{ adminName }}</q-btn>
          <q-btn flat round @click="toggleMenu">
            <q-avatar>
              <img src="https://cdn.quasar.dev/img/avatar.png" alt="avatar" />
            </q-avatar>
          </q-btn>
          <q-menu v-model="menu" transition-show="scale" transition-hide="scale">
            <q-list>
              <q-item clickable @click="goToSettings">
                <q-item-section>Settings</q-item-section>
              </q-item>
              <q-item clickable @click="handleLogout">
                <q-item-section>Logout</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </div>
      </q-toolbar>
    </q-header>

    <!-- DRAWER / SIDEBAR -->
    <q-drawer v-model="drawer" width="250" bordered :class="drawerBgClass">
      <q-list padding>
        <!-- NAVIGATION TO PAGES -->
         <!-- Add search bar here -->
    <q-item>
      <q-input
        v-model="searchQuery"
        debounce="300"  
        @update:model-value="filterProducts"
        dense
        standout
        placeholder="Search"
      >
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </q-item>


        <q-item clickable v-ripple to="/dashboard">
          <q-item-section avatar>
            <q-icon name="dashboard" />
          </q-item-section>
          <q-item-section>Dashboard</q-item-section>
        </q-item>

        <!-- POS SYSTEM -->
        <q-expansion-item label="POS System" icon="shopping_cart">
          <q-list>
            <q-item clickable v-ripple to="/pos/new-sale">
              <q-item-section avatar>
                <q-icon name="point_of_sale" />
              </q-item-section>
              <q-item-section>New Sale</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/pos/orders">
              <q-item-section avatar>
                <q-icon name="receipt" />
              </q-item-section>
              <q-item-section>Order History</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/pos/product-add">
              <q-item-section avatar>
                <q-icon name="add_box" />
              </q-item-section>
              <q-item-section>Product Add</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/pos/categories">
              <q-item-section avatar>
                <q-icon name="category" />
              </q-item-section>
              <q-item-section>Categories</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/pos/products">
              <q-item-section avatar>
                <q-icon name="inventory" />
              </q-item-section>
              <q-item-section>Products List</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/pos/reports">
              <q-item-section avatar>
                <q-icon name="assessment" />
              </q-item-section>
              <q-item-section>Reports</q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>

        <!-- ORDER MANAGEMENT -->
        <q-expansion-item label="Order Management" icon="list_alt">
          <q-list>
            <q-item clickable v-ripple to="/orders/all">
              <q-item-section avatar>
                <q-icon name="list" />
              </q-item-section>
              <q-item-section>All Orders</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/orders/pending">
              <q-item-section avatar>
                <q-icon name="hourglass_empty" />
              </q-item-section>
              <q-item-section>Pending</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/orders/confirm">
              <q-item-section avatar>
                <q-icon name="check_circle" />
              </q-item-section>
              <q-item-section>Confirm</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/orders/processing">
              <q-item-section avatar>
                <q-icon name="autorenew" />
              </q-item-section>
              <q-item-section>Processing</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/orders/out-for-delivery">
              <q-item-section avatar>
                <q-icon name="local_shipping" />
              </q-item-section>
              <q-item-section>Out for Delivery</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/orders/delivered">
              <q-item-section avatar>
                <q-icon name="check" />
              </q-item-section>
              <q-item-section>Delivered</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/orders/returned">
              <q-item-section avatar>
                <q-icon name="undo" />
              </q-item-section>
              <q-item-section>Returned</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/orders/canceled">
              <q-item-section avatar>
                <q-icon name="cancel" />
              </q-item-section>
              <q-item-section>Canceled</q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>

        <!-- Product Management -->
        <q-expansion-item label="Product Management" icon="inventory">
          <q-list>
            <q-item clickable v-ripple to="/products/add">
              <q-item-section avatar>
                <q-icon name="add_box" />
              </q-item-section>
              <q-item-section>Add Product</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/products/list">
              <q-item-section avatar>
                <q-icon name="list" />
              </q-item-section>
              <q-item-section>Product List</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/products/history">
              <q-item-section avatar>
                <q-icon name="history" />
              </q-item-section>
              <q-item-section>Product History</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/products/categories">
              <q-item-section avatar>
                <q-icon name="category" />
              </q-item-section>
              <q-item-section>Categories</q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>


        <!-- Help & Support -->
        <q-expansion-item label="Help & Support" icon="help_outline">
          <q-list>
            <q-item clickable v-ripple to="/support/message">
              <q-item-section avatar>
                <q-icon name="message" />
              </q-item-section>
              <q-item-section>Message</q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>


        <!-- Report & Analytics -->
        <q-expansion-item label="Report & Analytics" icon="bar_chart">
          <q-list>
            <q-item clickable v-ripple to="/reports/earnings">
              <q-item-section avatar>
                <q-icon name="attach_money" />
              </q-item-section>
              <q-item-section>Earning Report</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/reports/orders">
              <q-item-section avatar>
                <q-icon name="receipt" />
              </q-item-section>
              <q-item-section>Order Report</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/reports/inventory">
              <q-item-section avatar>
                <q-icon name="inventory_2" />
              </q-item-section>
              <q-item-section>Inventory</q-item-section>
            </q-item>
            <q-item clickable v-ripple to="/reports/expenses">
              <q-item-section avatar>
                <q-icon name="money_off" />
              </q-item-section>
              <q-item-section>Expense</q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>

        <!-- User Management -->
        <q-expansion-item label="User Management" icon="people">
          <q-list>
            <q-item clickable v-ripple to="/users/customers">
              <q-item-section avatar>
                <q-icon name="group" />
              </q-item-section>
              <q-item-section>Customer List</q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>
      </q-list>
    </q-drawer>

    <!-- PAGE CONTENT - Dynamically Loaded Pages -->
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'

export default {
  name: 'MainLayout',
  setup() {
    const $q = useQuasar()
    const router = useRouter()

    const drawer = ref(false)
    const menu = ref(false)
    const darkMode = ref($q.dark.isActive)

    const userFirstName = computed(() => {
      // Replace this with actual user data retrieval
      return localStorage.getItem('userFirstName') || 'User'
    })

    const drawerBgClass = computed(() => {
      return darkMode.value ? 'bg-dark' : 'bg-white'
    })

    const toggleDrawer = () => {
      drawer.value = !drawer.value
    }

    const toggleMenu = () => {
      menu.value = !menu.value
    }

    const toggleDarkMode = () => {
      $q.dark.toggle()
      darkMode.value = $q.dark.isActive
    }

    const goToSettings = () => {
      router.push('/settings')
      menu.value = false
    }

    const handleLogout = () => {
      // Implement logout logic here
      // For example:
      router.push('/login')
      menu.value = false
    }

    return {
      drawer,
      menu,
      darkMode,
      userFirstName,
      drawerBgClass,
      toggleDrawer,
      toggleMenu,
      toggleDarkMode,
      goToSettings,
      handleLogout
    }
  },
  methods: {
    filterProducts(searchQuery) {
      // Implement your filtering logic here
      // This could involve filtering a local array of products
      // or making an API call to get filtered results from the server
    }
  }
}
</script>

<style scoped>
.bg-orange {
  background-color: #ff5722;
}
.bg-dark {
  background-color: #333;
}
.circle-img {
  width: 30%; /* Make the image fill the container */
  height: 30%; /* Make the image fill the container */
  object-fit: cover; /* Ensure the image covers the container without distortion */
  border-radius: 50%; /* Ensure the image itself is also circular */
}
</style>
