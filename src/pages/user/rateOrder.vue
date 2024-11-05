<template>
    <div class="q-pa-md">
        <h2>Rate This Product</h2>

        <q-form @submit.prevent="submitFeedback">
            <div class="q-gutter-md">
                <div>
                    <span class="rating-title">Feedback:</span>
                    <div class="star-rating">
                        <q-btn v-for="star in 5" :key="star" @click="setRating(star)"
                            :class="['q-ma-xs', { 'active': rating >= star }]" flat color="yellow">
                            <q-icon :name="rating >= star ? 'star' : 'star_border'" size="40px" />
                        </q-btn>
                    </div>
                </div>

                <div>
                    <q-input v-model="fb_description" label="Your Comments" type="textarea" rows="4" filled required />
                </div>

                <q-btn label="Submit" color="primary" type="submit" />
            </div>
        </q-form>
    </div>
</template>
<script>
export default {
  data() {
    return {
      rating: 0,
      fb_description: '',
      submitted: false,
    };
  },
  methods: {
    setRating(star) {
      this.rating = star;
    },
    async submitFeedback() {
        try {
            const rateData = {
                feedback: this.rating,
                fb_description: this.fb_description,
                user_id: localStorage.getItem('token'),
                pid: this.$route.params.id,
            };

            const response = await fetch("http://localhost/raj-express/backend/controller/ratingController/addRating.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(rateData)
            });

            if (!response.ok) {
                const errorMessage = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
            }

            const result = await response.json();

            if (result && result.success) {
                alert('Rate Created!');
                this.submitted = true;
                this.resetForm();
            } else {
                throw new Error(result.error || "Failed to submit a rating!");
            }

        } catch (error) {
            console.log('Error in ' + error);
        }
    
    },
    resetForm() {
      this.rating = 0;
      this.fb_description = '';
    },
  },
};
</script>

<style scoped>
.star-rating {
  display: flex;
  align-items: center;
}

.star-rating .active {
  color: gold;
}
</style>