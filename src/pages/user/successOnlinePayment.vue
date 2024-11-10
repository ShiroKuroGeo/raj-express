<script setup>
import axios from 'src/boot/axios';
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const checkSuccessPayment = async () => {
    const token = route.params.id;
    const response = await fetch("http://localhost/raj-express/backend/controller/onlinePaymentController/paymentSuccess.php", {
        method: "POST",
        headers: {
            "Authorization": token,
        },
    });

    if (!response.ok) {
        const errorMessage = await response.text();
        throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
    }

    const result = await response.json();

    if (result && result.success) {
        router.push('/cart');
    } else {
        throw new Error(result.error || "Failed to submit a rating!");
    }
    
};

onMounted(() => {
    checkSuccessPayment();
});
</script>

<template>
  <div>
    <!-- <h1>Success Page</h1>
    <p>Source: {{ source }}</p>
    <p>ID: {{ id }}</p> -->
  </div>
</template>