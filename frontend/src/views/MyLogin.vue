<template>
  <div>
    <div class="login-container">
      <h2>Login</h2>

      <form @submit.prevent="login"> 

        <div>
          <label for="username">Username:</label>
          <input v-model="username" type="text" id="username" required />
        </div>

        <div>
          <label for="password">Password:</label>
          <input v-model="password" type="password" id="password" required />
        </div>


        <!-- The button should trigger the form's submit event -->
        <button type="submit">Login</button>


        <p class="register-link">
          Don't have an account?
          <router-link to="/register">Register here</router-link>
        </p>
      </form>
    </div>

    <!-- ✅ improved feedback display -->
    <p v-if="successMessage" class="success">{{ successMessage }}</p>
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
  </div>
</template>

<script>
export default {
  name: 'MyLogin',
  data() {
    return {
      username: '',
      password: '',
      errorMessage: '',
      successMessage: ''
    };
  },
  methods: {
    async login() {

      console.log("Login method triggered");
      try {
        const res = await fetch('http://localhost:8000/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            username: this.username,
            password: this.password
          })
        });

        const data = await res.json();
        console.log('Login response:', data);

        if (res.ok && data.token) {
          // Clear messages
          this.errorMessage = '';
          this.successMessage = 'Login successful! Redirecting...';

          // Store JWT token
          localStorage.setItem('jwt', data.token);

          // Fetch user role
          console.log('JWT token:', data.token);

          const roleRes = await fetch('http://localhost:8000/api/me/role', {
            headers: {
              'Authorization': `Bearer ${data.token}`
            }
          });

          console.log('Role response status:', roleRes.status);
          const roleData = await roleRes.json();
          console.log('Role data:', roleData);
          
          if (roleRes.ok && roleData.role) {
            localStorage.setItem('role', roleData.role);
          } else {
            console.error('Failed to fetch user role');
          }

          // Redirect
          console.log("Redirecting to dashboard...");
          this.$router.push('/dashboard');
          this.$router.push('/dashboard');
        } else {
          this.errorMessage = data.error || 'Login failed.';}


      } catch (err) {
        console.error('Login error:', err);
        this.errorMessage = 'Login error. Please try again.';
      }
    }
  }
};
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: auto;
  padding: 20px;
}
.error {
  color: red;
  margin-top: 10px;
}
.success {
  color: green;
  margin-top: 10px;
}

.register-link {
  margin-top: 10px;
  text-align: center;
}
.register-link a {
  color: #007bff;
  text-decoration: none;
}
.register-link a:hover {
  text-decoration: underline;
}

</style>
