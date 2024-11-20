<template>
  <q-page class="q-pa-md">
    <q-card>
      <q-card-section>
        <div class="text-h4">Extra Lists</div>
      </q-card-section>

      <q-card-section>
        <div class="q-gutter-md row items-center">
          <q-btn label="Add" icon="add" color="orange" class="q-ml-md q-ml-auto" @click="openAddaddOnDialog" />
          <q-input v-model="searchQuery" placeholder="Search" filled>
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </q-card-section>
      <q-card-section>
        <q-table :rows="filteredAddsOns" :columns="columns" row-key="addOn_id" :rows-per-page-options="[10]" @request="onRequest" dense>
          <template v-slot:body-cell-action="props">
            <q-td :props="props" class="text-center">
              <q-btn flat dense icon="edit" color="orange" @click="openEditaddOnDialog(props.row)" />
              <q-btn flat dense icon="delete" color="red" @click="deleteaddOn(props.row.addOn_id)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
    
    <q-dialog v-model="dialogVisible">
      <q-card>
        <q-card-section>
          <div class="text-h6">{{ isEdit ? 'Edit Adds Ons' : 'Add New Adds Ons' }}</div>
        </q-card-section>

        <q-card-section>
          <q-input v-model="addOn.ao_name" label="addOn Name" outlined />
          <q-input v-model="addOn.ao_price" label="addOn Price" outlined />
          <q-select v-model="addOn.ao_status" label="Status" :options="['Available', 'Unavailable']" filled class="q-mt-md" />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" @click="closeDialog" />
          <q-btn flat label="Save" color="orange" @click="saveaddOn" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      AddsOns: [],
      searchQuery: '',
      columns: [
        { name: 'addOn_id', label: 'ID', align: 'left', field: (row) => row.addOn_id  },
        { name: 'addOn_name', label: 'Name', align: 'left', field: (row) => row.ao_name },
        { name: 'addOn_price', label: 'Price', align: 'center', field: (row) => row.ao_price },
        { name: 'action', label: 'Action', align: 'center' }
      ],

      dialogVisible: false,
      isEdit: false,
      addOn: { ao_name: null, ao_price: null, ao_status: null }
    };
  },
  computed: {
    filteredAddsOns() {
      if (!this.searchQuery.trim()) {
        return this.AddsOns;
      }
      return this.AddsOns.filter(addOn =>
        addOn.addOn_name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );

    }
  },
  methods: {
    async fetchAddsOns() {
      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/addOnController/get.php');
        this.AddsOns = response.data.addOnsItems;
      } catch (error) {
        console.error('AddOn error:', error);
      }
    },

    openAddaddOnDialog() {
      this.isEdit = false;
      this.addOn = { ao_name: null, ao_price: null, ao_status: null };
      this.dialogVisible = true;
    },

    openEditaddOnDialog(row) {
      this.isEdit = true;
      this.addOn = { ...row };
      this.dialogVisible = true;
    },

    closeDialog() {
      this.dialogVisible = false;
    },

    async saveaddOn() {
      const { ao_name, ao_price, ao_status } = this.addOn;

      try {
        const response = await axios.post('http://localhost/raj-express/backend/controller/addOnController/add.php', {
          aoname: ao_name,
          aoprice: ao_price,
          aostatus: ao_status,
        });
        
        if(response.data.success){
          this.fetchAddsOns();
        }else{
          alert('Data not added');
        }
      } catch (error) {
        console.error('AddOn error:', error);
      }
    },

    async deleteaddOn(addOn_id) {
      try {
        const response = await axios.delete('http://localhost/raj-express/backend/controller/pos_AddsOns.php', {
          data: { addOn_id }
        });

        if (response.data.success) {
          this.fetchAddsOns();
        } else {
          console.error('Error deleting addOn:', response.data.message);
        }
      } catch (error) {
        console.error('Error deleting addOn:', error);
      }
    },

    onRequest(props) {
      const { page, rowsPerPage } = props.pagination;
      this.pagination.page = page;
      this.pagination.rowsPerPage = rowsPerPage;
    }
  },

  created() {
    this.fetchAddsOns();
  }
};
</script>

<style scoped>
.q-page {
  padding: 20px;
}
</style>
