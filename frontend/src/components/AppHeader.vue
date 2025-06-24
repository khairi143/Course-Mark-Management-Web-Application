<template>
    <nav class="navbar navbar-light bg-light shadow-sm px-3"
     style="position: fixed; top: 0; left: 0; right: 0; z-index: 1030; height: 56px;">

      <button class="btn btn-outline-secondary me-2" @click="$emit('toggle-sidebar')">
        ☰
      </button>
      <span class="navbar-brand mb-0 h1">Course Mark Management</span>
      <div class="ms-auto d-flex align-items-center">
        <NotificationBell class="me-3" />
        <span class="me-3">{{ full_name }}</span>
        <button class="btn btn-outline-danger btn-sm" @click="logout">Logout</button>
      </div>
    </nav>
  </template>
  
<script>
import NotificationBell from './NotificationBell.vue'

export default {
  name: 'AppHeader',
  components: {
    NotificationBell
  },
  data() {
    return {
      full_name: 'User'
    }
  },
  mounted() {
    const userStr = localStorage.getItem('user')
    if (userStr) {
      try {
        const user = JSON.parse(userStr)
        this.full_name = user.full_name || 'User'
      } catch (e) {
        this.full_name = 'User'
      }
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
  }
}
</script>
  