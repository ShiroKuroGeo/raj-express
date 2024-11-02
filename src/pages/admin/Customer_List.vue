<template>
  <q-page padding>
    <q-table
      :rows="customers"
      :columns="columns"
      row-key="id"
      title="Users"
    >
      <template v-slot:body-cell-action="props">
        <q-btn
          flat
          icon="visibility"
          @click="viewCustomer(props.row.id)"
        />
        <q-btn
          flat
          color="red"
          icon="delete"
          @click="deleteCustomer(props.row.id)"
        />
      </template>
    </q-table>
  </q-page>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      customers: [],
      columns: [
        { name: "user_id", label: "User ID", align: "left", field: (row) => row.user_id },
        { name: "first_name", label: "First Name", align: "left", field: (row) => row.first_name },
        { name: "last_name", label: "Last Name", align: "left", field: (row) => row.last_name },
        { name: "address", label: "Address", align: "left", field: (row) => row.address },
        { name: "contact_number", label: "Contact Number", align: "left", field: (row) => row.contact_number },
        { name: "action", label: "Actions", align: "left" }
      ],
    };
  },
  methods: {
    async fetchCustomers() {
      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/adminController/userController/userList.php');
        this.customers = response.data.userDatas;
        console.log(response.data.userDatas);
      } catch (error) {
        console.log('Error in ' + error);
      }
    },
    viewCustomer(id) {
      alert(`Viewing customer with ID: ${id}`);
    },
  },
  mounted() {
    this.fetchCustomers();
  },
};
</script>

<style scoped>
.q-table__title {
  font-size: 1.2rem;
  font-weight: bold;
  color: #555;
}
</style>
