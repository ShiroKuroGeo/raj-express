<template>
  <q-page class="flex flex-center">
    <div class="chat-container">
      <!-- Header -->
      <q-header class="bg-pink-1 text-black">
        <q-toolbar>
          <q-btn flat round dense icon="arrow_back" />
          <q-toolbar-title>
            RAJ Food Express
            <div class="text-caption">Active now</div>
          </q-toolbar-title>
          <q-btn flat round dense icon="videocam" />
          <q-btn flat round dense icon="call" />
        </q-toolbar>
      </q-header>

      <!-- Chat area -->
      <div class="chat-area">
        <!-- Chat messages would go here -->
      </div>

      <!-- Message input -->
      <div class="message-input q-pa-sm">
        <q-input
          v-model="message"
          outlined
          dense
          placeholder="Type a message"
          bg-color="white"
        >
          <template v-slot:before>
            <q-btn round flat icon="image" />
          </template>
          <template v-slot:after>
            <q-btn round flat icon="send" @click="sendMessage" />
          </template>
        </q-input>
      </div>
    </div>
  </q-page>
</template>

<script>
import { ref } from 'vue'

export default {
  setup () {
    const message = ref('')

    const sendMessage = () => {
      // Implement send message functionality
      console.log('Sending message:', message.value)
      message.value = ''
    }

    return {
      message,
      sendMessage
    }
  }
}
</script>

<style lang="scss" scoped>
.chat-container {
  width: 100%;
  max-width: 400px;
  height: 100vh;
  display: flex;
  flex-direction: column;
  border: 1px solid #ddd;
}

.chat-area {
  flex-grow: 1;
  overflow-y: auto;
  background-color: white;
}

.message-input {
  background-color: #f0f0f0;
}

.q-toolbar {
  min-height: 70px;
}

.q-toolbar-title {
  font-size: 1.2rem;
  font-weight: bold;
}
</style>
