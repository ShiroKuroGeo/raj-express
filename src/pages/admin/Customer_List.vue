<template>
  <q-page padding>
    <q-table
      :rows="customers"
      :columns="columns"
      row-key="id"
      title="Customers"
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
        { name: "id", label: "ID", align: "left", field: (row) => row.id },
        {
          name: "name",
          label: "Customer Name",
          align: "left",
          field: (row) => row.name,
        },
        {
          name: "phone",
          label: "Customer Info",
          align: "left",
          field: (row) => row.phone,
        },
        {
          name: "orders",
          label: "Total Orders",
          align: "left",
          field: (row) => row.orders,
        },
        {
          name: "amount",
          label: "Total Order Amount",
          align: "left",
          field: (row) => row.amount,
        },
        {
          name: "status",
          label: "Status",
          align: "left",
          field: (row) => row.status,
        },
        { name: "action", label: "Actions", align: "left" },
      ],
    };
  },
  methods: {
    async fetchCustomers() {
      try {
        const response = await axios.get('http://localhost/raj-express/backend/controller/adminController/userController/userList.php');

        // this.customers = response

      } catch (error) {
        console.log('Error in ' + error);
      }
    },
    viewCustomer(id) {
      alert(`Viewing customer with ID: ${id}`);
    },
    // deleteCustomer(id) {
    //   if (confirm("Are you sure you want to delete this customer?")) {
    //     axios
    //       .post("http://localhost/api/delete_customer.php", { id: id })
    //       .then(() => {
    //         this.fetchCustomers();
    //       })
    //       .catch((error) => {
    //         console.error(error);
    //       });
    //   }
    // },
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
