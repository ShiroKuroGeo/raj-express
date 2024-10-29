<template>
    <q-layout>
      <q-header elevated>
        <q-toolbar>
          <q-img src="/icons/logo.png" alt="Logo" class="circle-img"></q-img>
  
          <div class="icon-container">
            <i class="fa-solid fa-bell q-mr-sm"></i>
            <i class="fa-solid fa-cart-shopping q-mr-sm"></i>
            <i class="fa-solid fa-bag-shopping q-mr-sm"></i>
            <i class="fa-solid fa-bars"></i>
          </div>
        </q-toolbar>
      </q-header>
    </q-layout>
</template>

<style scoped>
.icon-container {
  display: flex;
  gap: 15px;
  font-size: 20px;
  margin-left: auto;
}
.circle-img {
  width: 4%;
  height: auto;
  object-fit: cover;
  border-radius: 50%;
}
@media (max-width: 768px) {
  .circle-img {
    width: 40px;
  }
}
</style>
