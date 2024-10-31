<template>
  <q-page padding>
    <h4 class="q-mb-md">Welcome, {{ adminName }}.</h4>
    <p class="text-grey-8">Here’s what’s happening with your R.A.J. business today.</p>

    <div class="row q-col-gutter-md">
      <div class="col-md-8 col-sm-12">
        <q-card>
          <q-card-section>
            <div class="text-h6">Order Statistics</div>
            <q-btn-toggle v-model="orderStatsTimeframe" flat stretch toggle-color="primary"
              :options="[
                {label: 'This Month', value: 'month'},
                {label: 'This Week', value: 'week'},
                {label: 'Today', value: 'today'}
              ]"
            />
            <!-- Add your chart component here -->
          </q-card-section>
        </q-card>
      </div>
      <div class="col-md-4 col-sm-12">
        <q-card>
          <q-card-section>
            <div class="text-h6">Order Status Statistic</div>
            <!-- Add your order status statistics here -->
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-md q-mt-md">
      <div class="col-md-8 col-sm-12">
        <q-card>
          <q-card-section>
            <div class="text-h6">Earning Statistics</div>
            <q-btn-toggle
              v-model="earningStatsTimeframe"
              flat
              stretch
              toggle-color="primary"
              :options="[
                {label: 'This Month', value: 'month'},
                {label: 'This Week', value: 'week'},
                {label: 'Today', value: 'today'}
              ]"
            />
            <!-- Add your earnings chart component here -->
          </q-card-section>
        </q-card>
      </div>
      <div class="col-md-4 col-sm-12">
        <q-card>
          <q-card-section class="row items-center justify-between">
            <div class="text-h6">Recent Order</div>
            <q-btn flat color="primary" label="View All" />
          </q-card-section>
          <!-- Add your recent orders list here -->
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-md q-mt-md">
      <div class="col-md-4 col-sm-12">
        <q-card>
          <q-card-section class="row items-center justify-between">
            <div class="text-h6">Top Selling Product</div>
            <q-btn flat color="primary" label="View All" />
          </q-card-section>
          <!-- Add your top selling products list here -->
        </q-card>
      </div>
      <div class="col-md-4 col-sm-12">
        <q-card>
          <q-card-section class="row items-center justify-between">
            <div class="text-h6">Most Selling Product</div>
            <q-btn flat color="primary" label="View All" />
          </q-card-section>
          <!-- Add your most selling products list here -->
        </q-card>
      </div>
      <div class="col-md-4 col-sm-12">
        <q-card>
          <q-card-section class="row items-center justify-between">
            <div class="text-h6">Top Customer</div>
            <q-btn flat color="primary" label="View All" />
          </q-card-section>
          <!-- Add your top customers list here -->
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'AdminDashboard',
  data () {
    return {
      adminName: 'Seleno Shiro',
      orderStatsTimeframe: 'month',
      earningStatsTimeframe: 'month'
    }
  },
  methods:{

  },
  created(){
    
  }
}
</script>
