<template>
  <q-page padding class="settings-page">
    <h2 class="settings-title">Settings</h2>

    <div class="profile-image-container">
      <div class="profile-image-wrapper">
        <q-img
          v-if="profile.image"
          :src="profile.image"
          class="profile-image"
        />
        <q-btn
          round
          color="primary"
          icon="edit"
          size="sm"
          class="edit-image-button"
          @click="$refs.fileInput.click()"
        />
      </div>
      <input
        type="file"
        ref="fileInput"
        @change="handleImageUpload"
        accept="image/*"
        style="display: none;"
      />
    </div>

    <q-form @submit.prevent="saveProfile" class="settings-form">
      <!-- Basic Information Section -->
      <q-card class="settings-card">
        <q-card-section>
          <div class="section-title">Basic information</div>
          <div class="input-row">
            <label>Name</label>
            <q-input v-model="profile.name" filled />
          </div>
          <div class="input-row">
            <label>Email</label>
            <q-input v-model="profile.email" type="email" filled />
          </div>
          <div class="input-row">
            <label>Phone</label>
            <q-input v-model="profile.phone" type="tel" filled />
          </div>
          <div class="input-row">
            <label>Address</label>
            <q-input v-model="profile.address" filled />
          </div>
        </q-card-section>
      </q-card>

      <!-- Change Password Section -->
      <q-card class="settings-card">
        <q-card-section>
          <div class="section-title">Change password</div>
          <div class="input-row">
            <label>Current Password</label>
            <q-input v-model="password.currentPassword" type="password" filled>
              <template v-slot:append>
                <q-icon
                  :name="showCurrentPassword ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="showCurrentPassword = !showCurrentPassword"
                />
              </template>
            </q-input>
          </div>
          <div class="input-row">
            <label>New Password</label>
            <q-input v-model="password.newPassword" :type="showNewPassword ? 'text' : 'password'" filled>
              <template v-slot:append>
                <q-icon
                  :name="showNewPassword ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="showNewPassword = !showNewPassword"
                />
              </template>
            </q-input>
          </div>
          <div class="input-row">
            <label>Confirm Password</label>
            <q-input v-model="password.confirmPassword" :type="showConfirmPassword ? 'text' : 'password'" filled>
              <template v-slot:append>
                <q-icon
                  :name="showConfirmPassword ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="showConfirmPassword = !showConfirmPassword"
                />
              </template>
            </q-input>
          </div>
        </q-card-section>
      </q-card>

      <q-btn type="submit" label="Save Changes" color="orange" class="save-button" />
    </q-form>
  </q-page>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      profile: {
        name: '',
        email: '',
        phone: '',
        image: '',
        address: ''
      },
      password: {
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      showCurrentPassword: false,
      showNewPassword: false,
      showConfirmPassword: false,
      profileImage: null
    };
  },
  methods: {
    async fetchProfile() {
      try {
        const response = await axios.get('http://localhost/AMBOT-KAPOYA-NA/raj-express/backend/controller/profile.php');
        console.log('Profile response:', response.data);
        if (response.data && !response.data.error) {
          this.profile = response.data;
        } else {
          throw new Error(response.data.error || 'Failed to fetch profile');
        }
      } catch (error) {
        console.error('Error fetching profile:', error);
        this.$q.notify({
          color: 'negative',
          message: 'Failed to fetch profile. Please try again later.',
          icon: 'report_problem'
        });
        // Initialize profile with empty values
        this.profile = {
          name: '',
          email: '',
          phone: '',
          image: ''
        };
      }
    },
    async saveProfile() {
      try {
        const formData = new FormData();
        formData.append('name', this.profile.name);
        formData.append('email', this.profile.email);
        formData.append('phone', this.profile.phone);
        formData.append('address', this.profile.address);
        formData.append('currentPassword', this.password.currentPassword);
        formData.append('newPassword', this.password.newPassword);
        formData.append('confirmPassword', this.password.confirmPassword);
        if (this.profileImage) {
          formData.append('image', this.profileImage);
        }

        const response = await axios.post('http://localhost/AMBOT-KAPOYA-NA/raj-express/backend/controller/update-profile.php', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        console.log('Profile update response:', response.data);

        this.$q.notify({
          type: 'positive',
          message: 'Profile updated successfully!'
        });

        // Fetch updated profile data
        await this.fetchProfile();
      } catch (error) {
        console.error('Error saving profile:', error);
        this.$q.notify({
          color: 'negative',
          message: 'Failed to save profile. Please try again later.',
          icon: 'report_problem'
        });
      }
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        // Handle the file upload here
        // You might want to preview the image and prepare it for submission
        this.profileImage = file;
        // Create a temporary URL for preview
        this.profile.image = URL.createObjectURL(file);
      }
    }
  },
  created() {
    this.fetchProfile();
  }
};
</script>

<style scoped>
.settings-page {
  max-width: 800px;
  margin: 0 auto;
}

.settings-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 1rem;
}

.profile-image-container {
  display: flex;
  justify-content: center;
  margin-bottom: 2rem;
}

.profile-image-wrapper {
  position: relative;
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid #f0f0f0;
}

.profile-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.edit-image-button {
  position: absolute;
  bottom: 5px;
  right: 5px;
  background-color: white;
  color: #1976D2;
}

.settings-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.settings-card {
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.section-title {
  font-size: 1.25rem;
  font-weight: 500;
  margin-bottom: 1rem;
}

.input-row {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.input-row label {
  flex: 0 0 150px;
  font-weight: 500;
}

.input-row .q-input {
  flex: 1;
}

.save-button {
  align-self: flex-end;
  padding: 0.5rem 2rem;
  font-weight: bold;
}
</style>
