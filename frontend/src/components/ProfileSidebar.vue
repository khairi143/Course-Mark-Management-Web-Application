<template>
  <div>
    <!-- Avatar Trigger -->
    <img :src="img" class="avatar" alt="User" @click="toggleSidebar" />

    <!-- Sidebar Panel -->
    <div class="sidebar" :class="{ open: isOpen }">
      <div class="header">
        <span class="close" @click="toggleSidebar">✕</span>
      </div>
      <div class="content">
        <img :src="img" class="avatar-large" />
        <div class="name">{{ user.name }}</div>
        <div class="email">{{ user.email }}</div>
        <div class="role">({{ formattedRole }})</div>

        <div class="actions">
          <button @click="viewProfile">
            👁️ View Profile
          </button>
          <button @click="logout" class="logout">
            🔓 Logout
          </button>
        </div>
      </div>
    </div>

    <!-- Dimmed Background (does nothing on click) -->
    <div v-if="isOpen" class="overlay-disabled"></div>
  </div>
</template>

<script>
export default {
  props: ['user'],
  data() {
    return {
      isOpen: false,
      img: "https://i.pravatar.cc/150?img=12"
    }
  },
  computed: {
    formattedRole() {
      return this.user.role === 'personbmi'
        ? 'Person BMI'
        : this.user.role.charAt(0).toUpperCase() + this.user.role.slice(1);
    }
  },
  methods: {
    toggleSidebar() {
      this.isOpen = !this.isOpen;
    },
    viewProfile() {
      alert(`Name: ${this.user.name}\nEmail: ${this.user.email}`);
    },
    logout() {
      alert('Logging out...');
      
    }
  }
}
</script>

<style scoped>
.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid #cbd5e0;
  cursor: pointer;
}

.sidebar {
  position: fixed;
  top: 0;
  right: 0;
  height: 100vh;
  width: 300px;
  background: #fff;
  box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1);
  transform: translateX(100%);
  transition: transform 0.3s ease;
  z-index: 2000;
}

.sidebar.open {
  transform: translateX(0);
}

.header {
  display: flex;
  justify-content: flex-end;
  padding: 12px;
  border-bottom: 1px solid #e2e8f0;
}

.close {
  font-size: 22px;
  color: #718096;
  cursor: pointer;
}

.content {
  padding: 24px;
  text-align: center;
}

.avatar-large {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  margin-bottom: 12px;
}

.name {
  font-weight: 600;
  font-size: 18px;
  margin-bottom: 4px;
}

.email {
  font-size: 14px;
  color: #4a5568;
}

.role {
  font-size: 13px;
  font-style: italic;
  color: #718096;
  margin-bottom: 24px;
}

.actions {
  border-top: 1px solid #e2e8f0;
  padding-top: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

button {
  background: none;
  border: none;
  font-size: 14px;
  color: #2d3748;
  text-align: left;
  cursor: pointer;
}

.logout {
  color: #e53e3e;
}

.overlay-disabled {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.15);
  z-index: 1999;
  pointer-events: none;
}
</style>
