<template>
  <q-layout>
    <q-page-container>
      <q-page class="flex flex-center">
        <div class="q-pa-md q-ma-md" style="max-width: 500px; width: 100%">
          <div class="q-card-section row justify-center">
            <q-img src="/icons/logo.png" alt="Logo" class="circle-img"></q-img>
          </div>

          <!--  <div class="q-mx-md">
            Add Font Awesome bell icon here
            <i class="fa-solid fa-bell"></i>
          </div> -->

          <div class="q-card-section">
            <q-form class="q-gutter-md" @submit.prevent="login">
              <p style="margin-bottom: 0">Email</p>
              <q-input
                v-model="email"
                label="Email"
                type="email"
                dense
                outlined
                style="margin-top: 0"
              ></q-input>

              <p style="margin-bottom: 0">Password</p>
              <q-input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                label="Password"
                dense
                outlined
                style="margin-top: 0"
              >
                <template v-slot:append>
                  <q-icon
                    :name="showPassword ? 'visibility_off' : 'visibility'"
                    @click="togglePasswordVisibility"
                    class="cursor-pointer"
                  />
                </template>
              </q-input>

              <div class="q-mt-md" style="font-style: italic">
                <p>
                  Forgot Password?
                  <q-btn flat @click="goToResetPassword">Click Here</q-btn>
                </p>
              </div>

              <div class="column items-center">
                <q-btn
                  type="submit"
                  label="Sign In"
                  color="primary"
                  class="q-mt-md"
                ></q-btn>
              </div>

              <div class="q-mt-md" style="font-style: italic">
                <p>
                  Don't have an account?
                  <q-btn flat @click="goToSignUp">Click Here</q-btn>
                </p>
              </div>
            </q-form>
          </div>
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>


<style scoped>
.circle-img {
  width: 30%; /* Make the image fill the container */
  height: 30%; /* Make the image fill the container */
  object-fit: cover; /* Ensure the image covers the container without distortion */
  border-radius: 50%; /* Ensure the image itself is also circular */
}
</style>


<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import axios from 'axios';

export default {
  name: 'SignInPage',
  setup() {
    const router = useRouter();
    const $q = useQuasar();

    const email = ref('');
    const password = ref('');
    const showPassword = ref(false);

    const login = async () => {
      try {
        const loginData = { email: email.value, password: password.value };
        console.log('Sending login data:', loginData);

        const response = await axios.post('http://localhost/raj-express/backend/auth/signin.php',loginData,
          {
            headers: {
              'Content-Type': 'application/json',
            },
          }
        );

        if (response.data.success) {
          $q.notify({
            type: 'positive',
            message: 'Login successful!'
          });

          localStorage.setItem('customer', JSON.stringify({
            id: response.data.user_id,
            firstName: response.data.first_name,
            lastName: response.data.last_name,
            userType: response.data.user_type
          }));

          if (response.data.user_type === 'admin') {
            router.push('/admin-dashboard');
          } else if (response.data.user_type === 'customer') {
            router.push('/home');
          } else {
            $q.notify({
              type: 'warning',
              message: 'Unknown user type. Please contact support.'
            });
          }

          localStorage.setItem('token', response.data.user_id);
        } else {
          throw new Error(response.data.error || 'Login failed');
        }
      } catch (error) {
        if (error.response) {

        } else if (error.request) {
          console.error('No response received:', error.request);
        } else {
          console.error('Error setting up request:', error.message);
        }
        if (error.response) {
          $q.notify({
            type: 'negative',
            message: error.response.data.error || 'An error occurred during login. Please try again.'
          });
        } else if (error.request) {
          $q.notify({
            type: 'negative',
            message: 'No response received from the server. Please try again later.'
          });
        } else {
          $q.notify({
            type: 'negative',
            message: 'An error occurred while setting up the request. Please try again.'
          });
        }
      }
    };

    const togglePasswordVisibility = () => {
      showPassword.value = !showPassword.value;
    };

    const goToResetPassword = () => {
      router.push('/reset-password');
    };

    const goToSignUp = () => {
      router.push('/register');
    };

    return {
      email,
      password,
      showPassword,
      login,
      togglePasswordVisibility,
      goToResetPassword,
      goToSignUp
    };
  }
};
</script>

