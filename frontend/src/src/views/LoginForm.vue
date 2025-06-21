<template>
  <div class="login-container">
    <h2>Login</h2>
    <form @submit.prevent="handleLogin">
      <div>
        <label for="username">Username</label>
        <input type="text" id="username" v-model="username" required />
      </div>
      <div>
        <label for="password">Password</label>
        <input type="password" id="password" v-model="password" required />
      </div>
      <div>
        <label for="role">Role</label>
        <select id="role" v-model="role" required>
          <option value="lecturer">Lecturer</option>
          <option value="student">Student</option>
          <option value="advisor">Advisor</option>
        </select>
      </div>
      <button type="submit">Login</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      password: '',
      role: 'lecturer',  // Default role
    };
  },
  methods: {
    handleLogin() {
      localStorage.setItem('role', this.role);

      if (this.role === 'lecturer') {
        this.$router.push('/lecturer');
      } else if (this.role === 'student') {
        this.$router.push('/student');
      } else if (this.role === 'advisor') {
        this.$router.push('/advisor');
      }
    }
  }
};
</script>

<style scoped>
/* Add styles for login page here */
</style>
