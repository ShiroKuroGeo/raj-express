<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated class="bg-orange text-white">
      <q-toolbar>
        <q-btn dense flat round icon="menu" @click="toggleDrawer" />

        <q-toolbar-title>
          <div style="display: flex; align-items: center;">
            <img src="/backend/uploads/671a2b4a4fcf5_fried-chicken-recipe-1-500x433-removebg-preview.png" alt="logo" style="height: 40px; margin-right: 10px;" />
            <span>R.A.J Food Express</span>
          </div>
        </q-toolbar-title>

        <q-btn flat round @click="toggleDarkMode">
          <q-icon :name="darkMode ? 'brightness_4' : 'brightness_7'" />
        </q-btn>

        <div>
          <q-btn flat>{{ userFirstName }}</q-btn>
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

    <q-drawer v-model="drawer" width="250" bordered :class="drawerBgClass">
      <q-list padding>
        <q-item>
          <q-input
            v-model="searchQuery"
            debounce="300"
            @update:model-value="filterMenuItems"
            dense
            standout
            placeholder="Search"
          >
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </q-item>

        <template v-for="item in filteredMenuItems" :key="item.label">
          <q-item v-if="!item.children" clickable v-ripple :to="item.to">
            <q-item-section avatar>
              <q-icon :name="item.icon" />
            </q-item-section>
            <q-item-section>{{ item.label }}</q-item-section>
          </q-item>

          <q-expansion-item v-else :label="item.label" :icon="item.icon">
            <q-list>
              <q-item v-for="child in item.children" :key="child.label" clickable v-ripple :to="child.to">
                <q-item-section avatar>
                  <q-icon :name="child.icon" />
                </q-item-section>
                <q-item-section>{{ child.label }}</q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>
        </template>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'

const menuItems = [
  { label: 'Dashboard', icon: 'dashboard', to: '/dashboard' },
  {
    label: 'POS System',
    icon: 'shopping_cart',
    children: [
      { label: 'New Sale', icon: 'point_of_sale', to: '/pos/new-sale' },
      { label: 'Order History', icon: 'receipt', to: '/pos/orders' },
      { label: 'Reports', icon: 'assessment', to: '/pos/reports' }
    ]
  },
  {
    label: 'Order Management',
    icon: 'list_alt',
    children: [
      { label: 'All Orders', icon: 'list', to: '/orders/all' },
      { label: 'Pending', icon: 'hourglass_empty', to: '/orders/pending' },
      { label: 'Confirm', icon: 'check_circle', to: '/orders/confirm' },
      { label: 'Processing', icon: 'autorenew', to: '/orders/processing' },
      { label: 'Delivered', icon: 'check', to: '/orders/delivered' },
      { label: 'Returned', icon: 'undo', to: '/orders/returned' },
      { label: 'Canceled', icon: 'cancel', to: '/orders/canceled' }
    ]
  },
  {
    label: 'Product Management',
    icon: 'inventory',
    children: [
      { label: 'Add Product', icon: 'add_box', to: '/products/add' },
      { label: 'Product List', icon: 'list', to: '/products/list' },
      { label: 'Categories', icon: 'category', to: '/products/categories' },
      { label: 'Product Reviews', icon: 'reviews', to: '/products/reviews' }
    ]
  },
  {
    label: 'Help & Support',
    icon: 'help_outline',
    children: [
      { label: 'Message', icon: 'message', to: '/support/message' }
    ]
  },
  {
    label: 'Report & Analytics',
    icon: 'bar_chart',
    children: [
      { label: 'Earning Report', icon: 'attach_money', to: '/reports/earnings' },
      { label: 'Order Report', icon: 'receipt', to: '/reports/orders' },
      { label: 'Sale Report', icon: 'money_off', to: '/reports/sale' },
      { label: 'Expenses', icon: 'money_off', to: '/reports/expenses' },
      { label: 'Expenses Summary', icon: 'money_off', to: '/reports/summary' }
    ]
  },
  {
    label: 'User Management',
    icon: 'people',
    children: [
      { label: 'User Management', icon: 'group', to: '/list/customers' }
    ]
  }
]

export default {
  name: 'MainLayout',
  setup() {
    const $q = useQuasar()
    const router = useRouter()

    const drawer = ref(false)
    const menu = ref(false)
    const darkMode = ref($q.dark.isActive)
    const searchQuery = ref('')

    const userFirstName = computed(() => {
      return localStorage.getItem('userFirstName') || 'User'
    })

    const drawerBgClass = computed(() => {
      return darkMode.value ? 'bg-dark' : 'bg-white'
    })

    const filteredMenuItems = computed(() => {
      if (!searchQuery.value) return menuItems
      return menuItems.filter(item => {
        const matchesLabel = item.label.toLowerCase().includes(searchQuery.value.toLowerCase())
        if (item.children) {
          item.children = item.children.filter(child =>
            child.label.toLowerCase().includes(searchQuery.value.toLowerCase())
          )
          return matchesLabel || item.children.length > 0
        }
        return matchesLabel
      })
    })

    const toggleDrawer = () => {
      drawer.value = !drawer.value
    }

    const toggleMenu = () => {
      menu.value = menu.value ? true : false
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
      router.push('/')
      menu.value = false
    }

    const filterMenuItems = (query) => {
      searchQuery.value = query
    }

    return {
      drawer,
      menu,
      darkMode,
      userFirstName,
      drawerBgClass,
      searchQuery,
      filteredMenuItems,
      toggleDrawer,
      toggleMenu,
      toggleDarkMode,
      goToSettings,
      handleLogout,
      filterMenuItems
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
  width: 30%;
  height: 30%;
  object-fit: cover;
  border-radius: 50%;
}
</style>
