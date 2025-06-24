<template>
  <div class="login-wrapper">
    <h1 class="system-title">Course Mark Management System</h1>

    <div class="login-container">
      <div class="login-tabs">
        <button :class="{ active: activeTab === 'student' }" @click="activeTab = 'student'">Student Login</button>
        <button :class="{ active: activeTab === 'staff' }" @click="activeTab = 'staff'">Staff Login</button>
      </div>

      <div class="login-form">
        <div v-if="error" class="error-message">{{ error }}</div>

        <!-- Student Form -->
        <form v-if="activeTab === 'student'" @submit.prevent="handleStudentLogin">
          <h2>Student Login</h2>
          <div class="form-row">
            <label for="matricNo">Matric No</label>
            <input id="matricNo" v-model="student.matricNo" required />
          </div>
          <div class="form-row">
            <label for="studentPin">PIN</label>
            <input id="studentPin" type="password" v-model="student.pin" required />
          </div>

          <button type="submit" :disabled="loading">{{ loading ? 'Logging in...' : 'Login' }}</button>
        </form>

        <!-- Staff Form -->
        <form v-else @submit.prevent="handleStaffLogin">
          <h2>Staff Login</h2>
          <div class="form-row">
            <label for="staffUsername">Username</label>
            <input id="staffUsername" v-model="staff.username" required />
          </div>
          <div class="form-row">
            <label for="staffPassword">Password</label>
            <input id="staffPassword" type="password" v-model="staff.password" required />
          </div>
          <button type="submit" :disabled="loading">{{ loading ? 'Logging in...' : 'Login' }}</button>
        </form>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { loginStaff, loginStudent } from '../services/auth.js'

const activeTab = ref('student')
const student = ref({ matricNo: '', pin: '' })
const staff = ref({ username: '', password: '' })
const error = ref('')
const loading = ref(false)

async function handleStudentLogin() {
  error.value = ''
  loading.value = true
  try {
    const res = await loginStudent(student.value.matricNo, student.value.pin)
    if (res.token) {
      localStorage.setItem('token', res.token)
      if (res.user) {
        localStorage.setItem('user', JSON.stringify(res.user))
      }
      if (res.user && res.user.role_id === 2) {
        window.location.href = '/student/view-marks'
      } else {
        window.location.reload()
      }
    } else {
      error.value = res.error || 'Login failed.'
    }
  } catch (e) {
    error.value = 'Network error.'
  } finally {
    loading.value = false
  }
}

async function handleStaffLogin() {
  error.value = ''
  loading.value = true
  try {
    const res = await loginStaff(staff.value.username, staff.value.password)
    if (res.token) {
      localStorage.setItem('token', res.token)
      if (res.user) {
        localStorage.setItem('user', JSON.stringify(res.user))
      }
      if (res.user && res.user.role_id === 1) {
        window.location.href = '/lecturer/manage-students'
      } else if (res.user && res.user.role_id === 4) {
        window.location.href = '/admin/manage-users'
      } else if (res.user && res.user.role_id === 3) {
        window.location.href = '/advisor/view-students'
      } else {
        window.location.reload()
      }
    } else {
      error.value = res.error || 'Login failed.'
    }
  } catch (e) {
    error.value = 'Network error.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-wrapper {
  background: linear-gradient(to right, #eef2f3, #8e9eab);
  min-height: 100vh;
  padding-top: 60px;
  text-align: center;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.system-title {
  font-size: 2rem;
  color: #333;
  font-weight: 700;
  margin-bottom: 20px;
}

.login-container {
  max-width: 400px;
  margin: 0 auto;
  padding: 2rem;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
}

.login-tabs {
  display: flex;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid #e0e0e0;
}

.login-tabs button {
  flex: 1;
  padding: 0.75rem;
  border: none;
  background: none;
  font-weight: bold;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  transition: border-bottom 0.3s, color 0.3s;
}

.login-tabs button.active {
  color: #42b983;
  border-bottom: 3px solid #42b983;
}

.login-form form {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.login-form label {
  margin-bottom: 0.25rem;
  font-weight: 600;
  color: #444;
}

.login-form input {
  margin-bottom: 1rem;
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

.login-form button[type="submit"] {
  background: #42b983;
  color: #fff;
  border: none;
  padding: 0.75rem;
  font-size: 1rem;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.login-form button[type="submit"]:hover {
  background: #369870;
}

.error-message {
  background: #ffe5e5;
  color: #b71c1c;
  padding: 0.75rem;
  border-radius: 6px;
  margin-bottom: 1rem;
  font-weight: 500;
}

.login-form .form-row {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.login-form .form-row label {
  width: 100px;
  margin-right: 1rem;
  font-weight: 600;
  color: #444;
}

.login-form .form-row input {
  flex: 1;
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

</style>
