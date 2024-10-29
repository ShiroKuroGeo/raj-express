const routes = [

  {
    path: '/login',
    component: () => import('pages/Login.vue')
  },

  {
    path: '/register',
    component: () => import('pages/Register.vue')
  },


  {
    path: '/',
    component: () => import('layouts/AdminLayout.vue'),
    children: [
      { path: '', component: () => import('pages/admin/Dashboard.vue') },
      { path: 'dashboard', component: () => import('pages/admin/Dashboard.vue') },
      { path: 'settings', component: () => import('pages/admin/Settings.vue') },
    ],
  },
    {
      path: '/pos', // Base path for POS section
      component: () => import('layouts/AdminLayout.vue'),
      children: [
        { path: 'new-sale',
          name: 'new-sale',
          component: () => import('pages/admin/PosNewSale.vue') },
        {
           path: 'product-add',
          name: 'product-add',
          component: () => import('pages/admin/PosProductAdd.vue') },
        {
          path: 'categories',
          component: () => import('pages/admin/PosCategory.vue') },
        {
          path: 'orders',
          name: 'orders',
          component: () => import('pages/admin/PosOrders.vue')
        },
        {
          path: 'details/:id',
          name: 'pos-order-details',
          component: () => import('pages/admin/PosViewDetails.vue'),

        },
        { path: 'products',
          name: 'products',
          component: () => import('pages/admin/PosProducts.vue') },

        {
          path: 'print-order/:id',
          name: 'pos-print-order',
          component: () => import('pages/admin/PosPrintOrder.vue')
        },
      ]
    },



  {
    path: '/user',
    component: () => import('layouts/UserLayout.vue'),
    children: [
      { path: '', component: () => import('pages/user/Home_Page.vue') },
      { path: '/home',
        name: 'user-home',
        component: () => import('pages/user/Home_Page.vue')
      },
      {
        path: '/favorites',
        name: 'favorites',
        component: () => import('pages/user/Hearts_Page.vue')
      },
      {
        path: '/cart',
        name: 'cart',
        component: () => import('pages/user/Cart_Page.vue')
      },
      {
        path: '/order',
        name: 'order',
        component: () => import('pages/user/Order_Page.vue')
      },
      {
        path: '/menu',
        name: 'menu',
        component: () => import('pages/user/Menu_Page.vue'),
        children: [
          {
            path: '/profile',
            name: '/profile',
            component: () => import('pages/user/UpdateProfile_Page.vue')
          },
          {
            path: '/my-order',
            component: () => import('pages/user/MyOrderPage.vue')
          },
          {
            path: '/order-details/:id',
            component: () => import('pages/user/OrderDetailsPage.vue')
          },
          {
            path: '/address',
            component: () => import('pages/user/AddressPage.vue')
          },
          {
            path: '/messages',
            component: () => import('pages/user/MessagesPage.vue')
          },
          {
            path: '/notifications',
            component: () => import('pages/user/NotificationsPage.vue')
          },
          {
            path: '/delete-account',
            component: () => import('pages/user/DeleteAccountPage.vue')
          },
          {
            path: '/logout',
            component: () => import('pages/user/LogoutPage.vue')
          },
        ]
      },

      // Other user routes...
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
