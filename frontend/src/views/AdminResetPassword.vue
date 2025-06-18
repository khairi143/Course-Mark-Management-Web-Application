<template>
    <div class="container mt-5">
      <h2 class="mb-4">Reset User Password</h2>
      <form @submit.prevent="resetPassword">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input v-model="username" type="text" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="newPassword" class="form-label">New Password</label>
          <input v-model="newPassword" type="password" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
        <div class="mt-3 text-success" v-if="success">{{ success }}</div>
        <div class="mt-3 text-danger" v-if="error">{{ error }}</div>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'ResetPassword',
    data() {
      return {
        username: '',
        newPassword: '',
        success: '',
        error: ''
      };
    },
    methods: {
      async resetPassword() {
        try {
          const response = await axios.put('http://localhost:5000/api/users/reset-password', {
            username: this.username,
            password: this.newPassword
          });
          this.success = response.data.message;
          this.error = '';
          this.username = '';
          this.newPassword = '';
        } catch (err) {
          this.error = err.response?.data?.error || 'An error occurred';
          this.success = '';
        }
      }
    }
  };
  </script>
  