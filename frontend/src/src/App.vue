<template>
  <div id="app">
    <!-- You could add a navigation bar here -->
    <nav>
      <button @click="logout">Logout</button>
    </nav>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App',
  methods: {
    logout() {
      localStorage.removeItem('role');  // Remove the user's role from localStorage
      this.$router.push('/');           // Redirect back to the login page
    }
  }
};
</script>

<style>
/* Add some styles for the logout button and navigation */
nav {
  background-color: #333;
  color: #fff;
  padding: 10px;
  text-align: center;
}

button {
  background-color: #f44336;
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #d32f2f;
}
</style>
