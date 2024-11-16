<template>
  <q-page class="q-pa-md">
    <q-card>
      <q-card-section>
        <div class="text-h4">Category List</div>
      </q-card-section>

      <q-card-section>
        <div class="q-gutter-md row items-center">
          <q-btn label="Add" icon="add" color="orange" class="q-ml-md q-ml-auto" @click="openAddCategoryDialog" />
          <q-input v-model="searchQuery" placeholder="Search" filled>
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </q-card-section>
      <q-card-section>
        <q-table :rows="filteredCategories" :columns="columns" row-key="category_id" :rows-per-page-options="[10]"
          @request="onRequest" dense>
          <template v-slot:body-cell-category_status="props">
            <q-td :props="props" class="text-center">
              <q-chip :color="props.row.category_status === 'Active' ? 'green' : 'red'" text-color="white" square>
                {{ props.row.category_status }}
              </q-chip>
            </q-td>
          </template>

          <template v-slot:body-cell-action="props">
            <q-td :props="props" class="text-center">
              <q-btn flat dense icon="edit" color="orange" @click="openEditCategoryDialog(props.row)" />
              <q-btn flat dense icon="delete" color="red" @click="deleteCategory(props.row.category_id)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Dialog for Add/Edit Category -->
    <q-dialog v-model="dialogVisible">
      <q-card>
        <q-card-section>
          <div class="text-h6">{{ isEdit ? 'Edit Category' : 'Add New Category' }}</div>
        </q-card-section>

        <q-card-section>
          <q-input v-model="category.category_name" label="Category Name" outlined />
          <q-select v-model="category.category_status" label="Status" :options="['Active', 'Inactive']" filled
            class="q-mt-md" />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" @click="closeDialog" />
          <q-btn flat label="Save" color="orange" @click="saveCategory" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import axios from 'axios';

// Configure axios baseURL if not already done globally
axios.defaults.baseURL = 'http://localhost/raj-express/backend/controller/';

export default {
  data() {
    return {
      categories: [],
      searchQuery: '',
      columns: [
        { name: 'category_id', label: 'ID', align: 'left', field: 'category_id' },
        { name: 'category_name', label: 'Name', align: 'left', field: 'category_name' },
        { name: 'category_status', label: 'Status', align: 'center', field: 'category_status' },
        { name: 'action', label: 'Action', align: 'center' }
      ],

      dialogVisible: false,
      isEdit: false,
      category: { category_id: null, category_name: '', category_status: null }
    };
  },
  computed: {
    filteredCategories() {
      // return this.categories.filter(category =>
      //   category.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      // );
      if (!this.searchQuery.trim()) {
        return this.categories;
      }
      return this.categories.filter(category =>
        category.category_name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );

    }
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get('/pos_categories.php');
        console.log('Categories response:', response);

        if (response.data && Array.isArray(response.data.categories)) {
          this.categories = response.data.categories;
          console.log('Categories fetched successfully:', this.categories);
        } else {
          console.error('Unexpected response format:', response.data);
          this.categories = [];
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
        if (error.response) {
          console.error('Error response data:', error.response.data);
          console.error('Error response status:', error.response.status);
          console.error('Error response headers:', error.response.headers);
        } else if (error.request) {
          console.error('No response received:', error.request);
        } else {
          console.error('Error setting up request:', error.message);
        }
        this.categories = [];
      }
    },
    openAddCategoryDialog() {
      this.isEdit = false;
      this.category = { category_id: null, category_name: '', category_status: '' };
      this.dialogVisible = true;
    },

    openEditCategoryDialog(row) {
      this.isEdit = true;
      this.category = { ...row };
      this.dialogVisible = true;
    },

    closeDialog() {
      this.dialogVisible = false;
    },
    async saveCategory() {
      try {
        const response = await axios({
          method: this.isEdit ? 'put' : 'post',
          url: '/pos_categories.php',
          data: {
            category_id: this.category.category_id,
            category_name: this.category.category_name,
            category_status: this.category.category_status
          }
        });

        if (response.data.success) {
          this.fetchCategories(); // Refresh the category list
          this.closeDialog();
        } else {
          console.error('Error saving category:', response.data.message);
        }
      } catch (error) {
        console.error('Error saving category:', error);
      }
    },

    async deleteCategory(category_id) {
      try {
        const response = await axios.delete('http://localhost/raj-express/backend/controller/pos_categories.php', {
          data: { category_id }
        });

        if (response.data.success) {
          this.fetchCategories();
        } else {
          console.error('Error deleting category:', response.data.message);
        }
      } catch (error) {
        console.error('Error deleting category:', error);
      }
    },

    onRequest(props) {
      const { page, rowsPerPage } = props.pagination;
      this.pagination.page = page;
      this.pagination.rowsPerPage = rowsPerPage;
    }
  },

  mounted() {
    this.fetchCategories();
  }
};
</script>

<style scoped>
.q-page {
  padding: 20px;
}
</style>
