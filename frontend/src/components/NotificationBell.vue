<template>
  <div v-if="isStudent" class="notification-bell position-relative">
    <!-- Notification Bell Icon -->
    <button 
      @click="toggleNotifications" 
      class="btn btn-link position-relative text-dark"
      type="button"
    >
      <i class="fas fa-bell"></i>
      <!-- Unread Badge -->
      <span 
        v-if="unreadCount > 0" 
        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
        style="font-size: 0.6rem;"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Notifications Dropdown -->
    <div 
      v-if="showNotifications" 
      class="notification-dropdown position-absolute top-100 end-0 mt-2 bg-white border rounded shadow-lg"
      style="width: 350px; max-height: 400px; overflow-y: auto; z-index: 1000;"
    >
      <div class="p-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Notifications</h6>
          <button 
            v-if="notifications.length > 0" 
            @click="markAllAsRead" 
            class="btn btn-sm btn-outline-primary"
          >
            Mark all read
          </button>
        </div>
      </div>
      
      <div v-if="loading" class="p-3 text-center">
        <span>Loading...</span>
      </div>
      
      <div v-else-if="notifications.length === 0" class="p-3 text-center text-muted">
        <i class="fas fa-bell-slash mb-2"></i>
        <p class="mb-0">No notifications</p>
      </div>
      
      <div v-else>
        <div 
          v-for="notification in notifications" 
          :key="notification.id"
          class="notification-item p-3 border-bottom"
          :class="{ 'bg-light': !notification.is_read }"
          @click="markAsRead(notification.id)"
          style="cursor: pointer;"
        >
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <h6 class="mb-1">{{ notification.title }}</h6>
              <p class="mb-1 text-muted small">{{ notification.message }}</p>
              <small class="text-muted">
                {{ formatDate(notification.created_at) }}
              </small>
            </div>
            <div v-if="!notification.is_read" class="ms-2">
              <span class="badge bg-primary">New</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import {
  getStudentNotifications,
  markNotificationAsRead,
  markAllNotificationsAsRead,
  getUnreadNotificationCount
} from '../services/student.js'

export default {
  name: 'NotificationBell',
  setup() {
    const showNotifications = ref(false)
    const notifications = ref([])
    const unreadCount = ref(0)
    const loading = ref(false)
    const user = JSON.parse(localStorage.getItem('user'))
    const userId = user?.id
    const isStudent = user?.role_id === 2

    const fetchNotifications = async () => {
      if (!userId) return
      try {
        loading.value = true
        notifications.value = await getStudentNotifications(userId)
      } catch (error) {
        console.error('Error fetching notifications:', error)
      } finally {
        loading.value = false
      }
    }

    const fetchUnreadCount = async () => {
      if (!userId) return
      try {
        unreadCount.value = await getUnreadNotificationCount(userId)
      } catch (error) {
        console.error('Error fetching unread count:', error)
      }
    }

    const toggleNotifications = () => {
      showNotifications.value = !showNotifications.value
      if (showNotifications.value) {
        fetchNotifications()
      }
    }

    const markAsRead = async (notificationId) => {
      try {
        await markNotificationAsRead(userId, notificationId)
        const notification = notifications.value.find(n => n.id === notificationId)
        if (notification) {
          notification.is_read = true
        }
        await fetchUnreadCount()
      } catch (error) {
        console.error('Error marking notification as read:', error)
      }
    }

    const markAllAsRead = async () => {
      try {
        await markAllNotificationsAsRead(userId)
        notifications.value.forEach(n => n.is_read = true)
        await fetchUnreadCount()
      } catch (error) {
        console.error('Error marking all as read:', error)
      }
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      const now = new Date()
      const diffInHours = (now - date) / (1000 * 60 * 60)
      if (diffInHours < 1) return 'Just now'
      if (diffInHours < 24) return `${Math.floor(diffInHours)}h ago`
      return date.toLocaleDateString()
    }

    const handleClickOutside = (event) => {
      if (!event.target.closest('.notification-bell')) {
        showNotifications.value = false
      }
    }

    onMounted(() => {
      if (!isStudent || !userId) return
      fetchUnreadCount()
      document.addEventListener('click', handleClickOutside)

      const interval = setInterval(fetchUnreadCount, 30000)
      onUnmounted(() => {
        clearInterval(interval)
        document.removeEventListener('click', handleClickOutside)
      })
    })

    return {
      isStudent,
      showNotifications,
      notifications,
      unreadCount,
      loading,
      toggleNotifications,
      markAsRead,
      markAllAsRead,
      formatDate
    }
  }
}
</script>

<style scoped>
.notification-bell {
  display: inline-block;
}

.notification-item:hover {
  background-color: #f8f9fa !important;
}

.notification-dropdown {
  min-width: 300px;
}
</style>
