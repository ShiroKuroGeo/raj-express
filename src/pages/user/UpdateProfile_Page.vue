<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-red text-white">
      <q-toolbar>
        <q-btn flat dense round icon="arrow_back" @click="goToMenu" />
        <q-toolbar-title>My Profile</q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container >
      <q-page class="bg-white q-pt-lg">
        <div class="profile-card bg-white q-ma-sm q-pa-md rounded-borders">
          <div class="profile-picture-container q-mb-md">
            <q-avatar size="100px" class="bg-grey-3">
              <img :src="profilePicture">
              <q-btn round color="red" icon="edit" size="sm" class="edit-icon" @click="uploadImage" />
            </q-avatar>
          </div>

          <q-input v-model="first_name" label="First Name" outlined dense class="q-mb-sm">
            <template v-slot:prepend>
              <q-icon name="person" />
            </template>
          </q-input>

          <q-input v-model="last_name" label="Last Name" outlined dense class="q-mb-sm">
            <template v-slot:prepend>
              <q-icon name="person" />
            </template>
          </q-input>

          <q-input v-model="email" label="Email" outlined dense class="q-mb-sm">
            <template v-slot:prepend>
              <q-icon name="email" />
            </template>
          </q-input>

          <q-input v-model="contact_number" label="Phone Number" outlined dense class="q-mb-sm">
            <template v-slot:prepend>
              <q-icon name="phone" />
            </template>
            <template v-slot:append>
              <q-icon name="warning" color="orange" />
            </template>
          </q-input>

          <q-input v-model="password" :type="showPassword ? 'text' : 'password'" label="Password" outlined dense class="q-mb-sm">
            <template v-slot:prepend>
              <q-icon name="lock" />
            </template>
            <template v-slot:append>
              <q-icon
                :name="showPassword ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="togglePasswordVisibility"
              />
            </template>
          </q-input>

          <q-input v-model="confirmpassword" :type="showPassword ? 'text' : 'password'" label="Confirm Password" outlined dense class="q-mb-sm">
            <template v-slot:prepend>
              <q-icon name="lock" />
            </template>
            <template v-slot:append>
              <q-icon
                :name="showPassword ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="togglePasswordVisibility"
              />
            </template>
          </q-input>

          <q-btn label="Update" color="red" class="full-width q-mt-md" @click="signup" />
        </div>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useQuasar } from 'quasar';
import axios from 'axios';

export default{
  data(){
    return{
      showPassword: false,
      first_name: '',
      last_name: '',
      email: '',
      contact_number: '',
    }
  },
  methods:{
    async getAllUserInformation(){
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://localhost/raj-express/backend/controller/userController/profile.php', {
          headers: {
            'Authorization': token
          }
        });

        console.log(response.data);
        this.first_name = response.data.fn;
        this.last_name = response.data.ln;
        this.email = response.data.mail;
        this.contact_number = response.data.number;

      } catch (error) {
        console.log('Error in ' . error);
      }
    },
    togglePasswordVisibility(){
      showPassword.value = !showPassword.value;
    },
    goToMenu(){
      this.$router.push("/menu"); 
    },
  },
  created(){
    this.getAllUserInformation();
  }
}


// const profilePicture = ref("");
// const first_name = ref("");
// const last_name = ref("");
// const contact_number = ref("");
// const email = ref("");
// const password = ref("");
// const confirmpassword = ref("");
// const showPassword = ref(false);
// const router = useRouter();
// const $q = useQuasar();

// // Check for authentication token
// const checkAuth = () => {
//   const token = localStorage.getItem('token');
//   if (!token) {
//     $q.notify({
//       type: 'negative',
//       message: 'Please log in to access this page'
//     });
//     router.push('/signin');
//   }
//   return token;
// };

// onMounted(() => {
//   const token = checkAuth();
//   if (token) {
//     fetchUserData(token);
//   }
// });

// const fetchUserData = async (token) => {
//   try {
//     const response = await fetch('http://localhost/raj-express/backend/controller/profile.php', {
//       headers: {
//         'Authorization': `Bearer ${token}`
//       }
//     });
//     if (!response.ok) {
//       throw new Error('Failed to fetch user data');
//     }
//     const userData = await response.json();
//     first_name.value = userData.first_name;
//     last_name.value = userData.last_name;
//     email.value = userData.email;
//     contact_number.value = userData.contact_number;
//     profilePicture.value = userData.profile_picture || "";
//   } catch (error) {
//     console.error('Error fetching user data:', error);
//     $q.notify({
//       type: 'negative',
//       message: 'Failed to load user data. Please try again.'
//     });
//   }
// };

// const togglePasswordVisibility = () => {
//   showPassword.value = !showPassword.value;
// };

// const signup = async () => {
//   if (password.value !== confirmpassword.value) {
//     $q.notify({
//       type: 'negative',
//       message: 'Passwords do not match!'
//     });
//     return;
//   }

//   const token = checkAuth();
//   if (!token) return;

//   const userData = {
//     first_name: first_name.value,
//     last_name: last_name.value,
//     contact_number: contact_number.value,
//     email: email.value,
//     password: password.value,
//     profile_picture: profilePicture.value,
//   };

//   try {
//     const response = await axios.get('http://localhost/raj-express/backend/controller/userController/update_profile.php', {
//       headers: {
//         'Content-Type': 'application/json',
//         'Authorization': token
//       },
//       body: JSON.stringify(userData)
//     });

//     if (!response.ok) {
//       throw new Error('Failed to update profile');
//     }

//     const result = await response.json();
//     $q.notify({
//       type: 'positive',
//       message: 'Profile updated successfully'
//     });
//   } catch (error) {
//     console.error('Error updating profile:', error);
//     $q.notify({
//       type: 'negative',
//       message: 'Failed to update profile. Please try again.'
//     });
//   }
// };

// const uploadImage = () => {
//   // Logic for uploading and updating the profile picture
//   // This can be a file input dialog
//   document.getElementById("file-input").click();
// };

// const handleFileUpload = async (event) => {
//   const file = event.target.files[0];
//   if (file) {
//     const formData = new FormData();
//     formData.append("profile_picture", file);

//     try {
//       const response = await fetch('http://localhost/raj-express/backend/uploads/', {
//         method: "POST",
//         body: formData
//       });
//       const result = await response.json();
//       if (response.ok) {
//         profilePicture.value = result.url; 
//       } else {
//         alert("Failed to upload profile picture.");
//       }
//     } catch (error) {
//       alert("An error occurred while uploading the profile picture.");
//     }
//   }
// };


// const goToMenu = () => {
//   router.push("/menu"); // Navigate to the previous page or profile info page
// };
</script>

<style scoped>
.profile-card {
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
}

.profile-picture-container {
  display: flex;
  justify-content: center;
}

.edit-icon {
  position: absolute;
  bottom: 0;
  right: 0;
}

.q-input {
  margin-bottom: 15px;
}
</style>

