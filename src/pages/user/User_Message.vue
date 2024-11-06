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
        </q-toolbar>
      </q-header>

      <div class="chat-area">
        <div v-for="message in messages" :key="message.msg_id" :class="message.sender_id == currentId ? 'd-flex padding-top-bottom message-right' : 'd-flex padding-top-bottom message-left'">
          <div class="message">
            <q-card flat bordered class="message-content">
              <q-card-section>{{ message.content }}</q-card-section>
            </q-card>
          </div>
        </div>
      </div>

      <div class="message-input q-pa-sm">
        <q-input v-model="message" outlined dense placeholder="Type a message" bg-color="white" >
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
import axios from 'axios'

export default {
  data(){
    return{
      message: '',
      currentId: 0,
      messages: [],
      messageResult: '',
    }
  },
  methods:{
    getId(){
      const token = localStorage.getItem('token');
      this.currentId = token;
    },
    async readMessages(){
      const token = localStorage.getItem('token');
      
      const response = await axios.get("http://localhost/raj-express/backend/controller/messageController/readController.php", {
        headers: {
          "Authorization": token,
        }
      });
      this.messages = response.data.messages;
    },
    async sendMessage (){
      const token = localStorage.getItem('token');
      
      const messageData = {
        sender_id: token,
        receiver_id: 1, // 1 is the id of admin nothing more
        content: this.message
      };

      const response = await fetch("http://localhost/raj-express/backend/controller/messageController/sendController.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(messageData)
      });

      if (!response.ok) {
        const errorMessage = await response.text();
        throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
      }

      const result = await response.json();

      if (result && result.success) {
        this.readMessages();
        this.message = '';
      } else {
        throw new Error(result.error || "Message not sent!");
      }
    }
  },
  created(){
    this.readMessages();
    this.getId();
  }
}
</script>

<style scoped>
.chat-container {
  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  border: 1px solid #ddd;
}

.d-flex{
  display: flex;
}

.message-left {
  justify-content: flex-start;
}

.message-right {
  justify-content: flex-end;
}

.message-content {
  width: 100%;
}

.chat-area {
  flex-grow: 1;
  overflow-y: auto;
  background-color: white;
}

.message-input {
  background-color: #f0f0f0;
}

.padding-top-bottom{
  padding-top: 5px;
  padding-bottom: 5px;
}

.q-toolbar {
  min-height: 70px;
}

.q-toolbar-title {
  font-size: 1.2rem;
  font-weight: bold;
}
</style>
