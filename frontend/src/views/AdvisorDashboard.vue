<template>
    <div>
      <!-- Full-width Header -->
      <Header @toggle-sidebar="toggleSidebar" />
  
      <!-- Sidebar and Main Content (stacked vertically) -->
      <div class="d-flex">
        <div class="d-flex w-100">
          <!-- Sidebar (left column) -->
          <transition name="slide">
            <Sidebar v-if="sidebarOpen" class="flex-shrink-0" style="width: 250px;" />
          </transition>
          
  
          <!-- Main content (right column) -->
          <div class="flex-grow-1 p-4" style="margin-top: 56px; background-color: #f8f9fa; min-height: 100vh;">
            <router-view />
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import Sidebar from '@/components/AppSidebar.vue'
  import Header from '@/components/AppHeader.vue'
  
  export default {
    components: {
      Sidebar,
      Header
    },
    data() {
      return {
        sidebarOpen: true
      }
    },
    methods: {
      toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen
      }
    }
  }
  </script> 

<style>
/* Slide animation for sidebar */
.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from, .slide-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}
</style>