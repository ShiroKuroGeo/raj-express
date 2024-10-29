<template>
  <q-page padding>
    <div class="row">
      <!-- Contacts List -->
      <div class="col-3">
        <q-card>
          <q-card-section>
            <div class="text-h6">Messages</div>
            <q-input
              outlined
              v-model="searchQuery"
              placeholder="Search"
              dense
              prepend-inner-icon="search"
            />
          </q-card-section>
          <q-list>
            <q-item
              v-for="contact in filteredContacts"
              :key="contact.id"
              clickable
              @click="selectContact(contact)"
            >
              <q-item-section avatar>
                <q-avatar>
                  <q-icon name="account_circle" />
                </q-avatar>
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ contact.name }}</q-item-label>
                <q-item-label caption>{{ contact.phone }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card>
      </div>

      <!-- Chat Section -->
      <div class="col-9">
        <q-card v-if="selectedContact">
          <q-card-section class="bg-primary text-white">
            <div class="text-h6">{{ selectedContact.name }}</div>
            <div>{{ selectedContact.phone }}</div>
          </q-card-section>
          <q-card-section style="height: 300px; overflow-y: auto">
            <div v-for="message in messages" :key="message.id">
              <q-chip
                :color="message.sender === 'me' ? 'orange' : 'grey-5'"
                :class="{
                  'float-right': message.sender === 'me',
                  'float-left': message.sender !== 'me',
                }"
                text-color="white"
              >
                {{ message.text }}
              </q-chip>
            </div>
          </q-card-section>
          <q-card-section>
            <q-input
              v-model="newMessage"
              placeholder="Type a message..."
              dense
              outlined
              @keyup.enter="sendMessage"
              append="send"
            >
              <template v-slot:append>
                <q-btn
                  flat
                  round
                  icon="send"
                  @click="sendMessage"
                  color="primary"
                />
              </template>
            </q-input>
          </q-card-section>
        </q-card>
        <q-card v-else>
          <q-card-section class="text-center">
            <q-icon name="message" size="lg" />
            <div>Select a contact to start chatting.</div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      contacts: [],
      messages: [],
      selectedContact: null,
      newMessage: "",
      searchQuery: "",
    };
  },
  computed: {
    filteredContacts() {
      return this.contacts.filter((contact) =>
        contact.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },
  mounted() {
    this.fetchContacts();
  },
  methods: {
    fetchContacts() {
      axios
        .get("http://localhost/api/contacts.php")
        .then((response) => {
          this.contacts = response.data;
        })
        .catch((error) => {
          console.error(error);
        });
    },
    selectContact(contact) {
      this.selectedContact = contact;
      this.fetchMessages(contact.id);
    },
    fetchMessages(contactId) {
      axios
        .get(`http://localhost/api/messages.php?contact_id=${contactId}`)
        .then((response) => {
          this.messages = response.data;
        })
        .catch((error) => {
          console.error(error);
        });
    },
    sendMessage() {
      if (this.newMessage.trim() !== "") {
        const messageData = {
          contact_id: this.selectedContact.id,
          text: this.newMessage,
          sender: "me",
        };
        axios
          .post("http://localhost/api/send_message.php", messageData)
          .then(() => {
            this.messages.push({ ...messageData, id: Date.now() });
            this.newMessage = "";
          })
          .catch((error) => {
            console.error(error);
          });
      }
    },
  },
};
</script>

<style scoped>
.q-chip {
  margin-bottom: 8px;
}
</style>
