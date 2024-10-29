<template>
   <div class="row q-pa-sm bg-orange-1">
      <div class="text-h6">
        📩 Messages <q-badge color="orange" text-color="white"></q-badge>
      </div>
    </div>
    <div class="row full-height"></div>
   <q-page padding >
     <div class="row full-height q-pa-md">
       <!-- Left sidebar -->
       <div class="col-4 q-pa-sm">
         <q-card flat class="bg-white">
           <q-card-section class="q-pa-sm">
             <q-item>
               <q-item-section avatar>
                 <q-avatar>
                   <img src="https://cdn.quasar.dev/img/avatar.png">
                 </q-avatar>
               </q-item-section>
               <q-item-section>
                 <q-item-label class="text-weight-bold">admin</q-item-label>
                 <q-item-label caption>+8**********</q-item-label>
               </q-item-section>
             </q-item>
           </q-card-section>
         </q-card>

         <q-input outlined dense v-model="search" placeholder="Search customers..." class="q-mt-md">
           <template v-slot:prepend>
             <q-icon name="search" />
           </template>
         </q-input>

         <q-scroll-area style="height: calc(100vh - 250px); margin-top: 20px;">
           <q-list separator>
             <q-item v-for="user in users" :key="user.id" clickable v-ripple @click="selectUser(user)" class="q-py-md">
               <q-item-section avatar>
                 <q-avatar>
                   <img :src="user.avatar || 'https://cdn.quasar.dev/img/avatar.png'">
                 </q-avatar>
               </q-item-section>
               <q-item-section>
                 <q-item-label class="text-weight-medium">{{ user.name }}</q-item-label>
                 <q-item-label caption>{{ maskPhone(user.phone) }}</q-item-label>
               </q-item-section>
             </q-item>
           </q-list>
         </q-scroll-area>
       </div>

       <!-- Right chat area -->
       <div class="col-8 flex column" style="border-left: 1px solid black">
         <q-card v-if="selectedUser" flat class="bg-white q-mb-md">
           <q-card-section class="q-pa-sm">
             <q-item>
               <q-item-section avatar>
                 <q-avatar>
                   <img :src="selectedUser.avatar || 'https://cdn.quasar.dev/img/avatar.png'">
                 </q-avatar>
               </q-item-section>
               <q-item-section>
                 <q-item-label class="text-weight-bold">{{ selectedUser.name }}</q-item-label>
                 <q-item-label caption>{{ selectedUser.phone }}</q-item-label>
               </q-item-section>
             </q-item>
           </q-card-section>
         </q-card>

         <q-card flat class="bg-white q-mb-md flex-grow-1" style="overflow: hidden;" >
           <q-scroll-area style="height: calc(100vh - 300px);">
             <q-chat-message
               v-for="message in messages"
               :key="message.id"
               :name="message.sender"
               :text="[message.content]"
               :sent="message.sender === 'admin'"
               :stamp="message.timestamp"
               text-color="white"
               :bg-color="message.sender === 'admin' ? 'primary' : 'grey-4'"
             />
           </q-scroll-area>
         </q-card>

          <q-card flat class="bg-white" style="margin-top: 0px;">
                <q-card-section horizontal class="q-pa-sm" style="padding-top: 30px;">
                  <q-input v-model="newMessage" outlined dense class="full-width" placeholder="Type a message...">
                    <template v-slot:after>
                      <q-btn flat round color="grey" icon="attach_file" @click="uploadFile" />
                      <q-btn flat round color="red" icon="send" @click="sendMessage" />
                    </template>
                  </q-input>
                </q-card-section>
            </q-card>
       </div>
     </div>
   </q-page>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
 setup() {
   const search = ref('')
   const users = ref([])
   const selectedUser = ref(null)
   const messages = ref([])
   const newMessage = ref('')

   const fetchUsers = async () => {
     try {
       const response = await axios.get('/api/users.php')
       users.value = response.data
     } catch (error) {
       console.error('Failed to fetch users:', error)
     }
   }

   const fetchMessages = async () => {
     if (!selectedUser.value) return
     try {
       const response = await axios.get(`/api/messages.php?user_id=${selectedUser.value.id}`)
       messages.value = response.data
     } catch (error) {
       console.error('Failed to fetch messages:', error)
     }
   }

   const selectUser = (user) => {
     selectedUser.value = user
     fetchMessages()
   }

   const sendMessage = async () => {
     if (!newMessage.value.trim() || !selectedUser.value) return
     try {
       await axios.post('/api/messages.php', {
         sender: 'admin',
         receiver: selectedUser.value.id,
         content: newMessage.value
       })
       newMessage.value = ''
       fetchMessages()
     } catch (error) {
       console.error('Failed to send message:', error)
     }
   }

   const uploadFile = () => {
     console.log('File upload not implemented')
   }

   const maskPhone = (phone) => {
     return phone.slice(0, 2) + '*'.repeat(phone.length - 2)
   }

   onMounted(fetchUsers)

   return {
     search,
     users,
     selectedUser,
     messages,
     newMessage,
     selectUser,
     sendMessage,
     uploadFile,
     maskPhone
   }
 }
}
</script>

<style lang="scss" scoped>
.q-page {
 background-color: #f5f5f5;
}

.q-card {
 box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}

.q-item {
 min-height: 60px;
}

.q-scroll-area {
 border-radius: 4px;
 background-color: white;
}

.q-chat-message {
 margin: 8px 0;
}
</style>
