<template>
  <div>
    <div class="login-container">
      <h2>Login</h2>
      <form @submit.prevent="loginUser">
        <div>
          <label for="username">Username:</label>
          <input v-model="username" type="text" id="username" required />
        </div>

        <div>
          <label for="password">Password:</label>
          <input v-model="password" type="password" id="password" required />
        </div>

        <button @click="login">Login</button>

      </form>
    </div>
    <p v-if="successMessage" class="success">{{ successMessage }}</p>
    <p v-else-if="errorMessage" class="error">{{ errorMessage }}</p>
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
        try {
            // Send POST request to /login for authentication
            const res = await fetch('http://localhost:8000/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    username: this.username,
                    password: this.password
                })
            });

            const data = await res.json();

            if (res.ok && data.token) {
                // Store JWT token in localStorage
                localStorage.setItem('jwt', data.token);

                // Fetch the user's role
                const roleRes = await fetch('http://localhost:8000/me/role', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${data.token}`
                    }
                });

                const roleData = await roleRes.json();

                if (roleRes.ok && roleData.role) {
                    // Store the role in localStorage
                    localStorage.setItem('role', roleData.role);
                } else {
                    console.error('Failed to fetch user role');
                }

                // Redirect to dashboard
                this.$router.push('/dashboard');
            } else {
                alert(data.error || 'Login failed');
            }
        } catch (err) {
            console.error('Login error:', err);
            alert('Login error. Please try again.');
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
</style>
