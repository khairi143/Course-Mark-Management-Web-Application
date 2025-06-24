<template>
  <div class="container mt-5">
    <h2 class="mb-4">Reset User Password</h2>
    <form @submit.prevent="resetPassword">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input v-model="username" type="text" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-primary">Reset to Factory Password</button>

      <div class="mt-3 text-success" v-if="success">
        {{ success }}<br />
        <strong>New Password: </strong>{{ newPassword }}
      </div>
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
      success: '',
      error: '',
      newPassword: ''
    };
  },
  methods: {
    async resetPassword() {
      try {
        const response = await axios.put('http://localhost:5000/api/users/reset-password', {
          username: this.username
        });
        this.success = response.data.message;
        this.newPassword = response.data.new_password;
        this.error = '';
        this.username = '';
      } catch (err) {
        this.error = err.response?.data?.error || 'An error occurred';
        this.success = '';
        this.newPassword = '';
      }
    }
  }
};
</script>
