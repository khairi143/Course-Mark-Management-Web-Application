<template>
  <div class="register-container">
    <h2>Register</h2>
    <form @submit.prevent="handleRegister">
      <div>
        <label for="username">Username</label>
        <input type="text" id="username" v-model="username" required />
      </div>
      <div>
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" v-model="full_name" required />
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
      <button type="submit">Register</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      username: '',
      full_name: '',
      password: '',
      role: 'student',  // Default role
    };
  },
  methods: {
    async handleRegister() {
      // Prepare the data to send to the backend
      const userData = {
        username: this.username,
        full_name: this.full_name,  // Added full_name field
        password: this.password,
        role_id: this.role === 'lecturer' ? 1 : this.role === 'student' ? 2 : 3, // Example role_id mapping
      };

      try {
        // Send POST request to the backend to register the user
        const response = await axios.post('http://localhost:8000/backend/src/register.php', userData);

        if (response.data.success) {
          alert('Registration successful!');
          this.$router.push('/login');  // Redirect to login page
        } else {
          alert(response.data.message);  // Show error message from backend
        }
      } catch (error) {
        console.error('Error during registration:', error);
        alert('An error occurred. Please try again.');
      }
    }
  }
};
</script>

<style scoped>
/* Add styles for the register page */
.register-container {
  max-width: 400px;
  margin: auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h2 {
  text-align: center;
}

form {
  display: flex;
  flex-direction: column;
}

form div {
  margin-bottom: 15px;
}

label {
  font-size: 1.1em;
  margin-bottom: 5px;
}

input, select {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1em;
}

button {
  padding: 10px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1.1em;
}

button:hover {
  background-color: #45a049;
}
</style>