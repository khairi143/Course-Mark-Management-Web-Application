<template>
    <div>
      <!-- Full-width Header -->
      <AdminHeader @toggle-sidebar="toggleSidebar" />
  
      <!-- Sidebar and Main Content (stacked vertically) -->
      <div class="d-flex">
        <div class="d-flex w-100">
          <!-- Sidebar (left column) -->
          <AdminSidebar v-if="sidebarOpen" class="flex-shrink-0" style="width: 250px;" />
  
          <!-- Main content (right column) -->
          <div class="flex-grow-1 p-4" style="background-color: #f8f9fa; min-height: 100vh;">
            <router-view />
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import AdminSidebar from '@/components/AdminSidebar.vue'
  import AdminHeader from '@/components/AdminHeader.vue'
  
  export default {
    components: {
      AdminSidebar,
      AdminHeader
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
  