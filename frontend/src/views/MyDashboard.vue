<template>
  <div class="dashboard">
    <header>
      <h1>Welcome, {{ roleLabel }}</h1>
      <button @click="logout">Logout</button>
    </header>

    <nav>
      <ul>
        <li v-for="item in menu" :key="item.name">
          <router-link :to="item.route">{{ item.name }}</router-link>
        </li>
      </ul>
    </nav>

    <main>
      <router-view /> <!-- Loads sub-pages based on route -->
    </main>
  </div>
</template>

<script>
export default {
  name: 'MyDashboard',

  data() {
    return {
      menuItems: {
        lecturer: [
          { name: 'Manage Students', route: '/lecturer/manage-students' },
          { name: 'Assessment Entry', route: '/lecturer/assessments' },
          { name: 'Final Exam Entry', route: '/lecturer/final-exam' },
          { name: 'Analytics', route: '/lecturer/analytics' }
        ],
        student: [
          { name: 'Progress', route: '/student/progress' },
          { name: 'Compare Marks', route: '/student/comparison' },
          { name: 'Ranking', route: '/student/ranking' }
        ],
        advisor: [
          { name: 'Advisee List', route: '/advisor/advisees' },
          { name: 'At Risk Students', route: '/advisor/risk' }
        ],
        admin: [
          { name: 'User Management', route: '/admin/users' },
          { name: 'Student Enrollment', route: '/admin/enrollments' },
          { name: 'System Logs', route: '/admin/logs' },
          { name: 'Reset Passwords', route: '/admin/reset-passwords' },
          { name: 'Course List (Optional)', route: '/admin/courses' }
        ]
      }
    };
  },

  computed: {
    token() {
      return localStorage.getItem('jwt');
    },
    role() {
      return localStorage.getItem('role') || 'guest';
    },
    menu() {
      return this.menuItems[this.role] || [];
    },
    roleLabel() {
      return {
        lecturer: 'Lecturer',
        student: 'Student',
        advisor: 'Academic Advisor',
        admin: 'Admin'
      }[this.role] || 'User';
    }
  },

  mounted() {
    if (!this.token || this.role === 'guest') {
      console.warn('Authentication token or user info missing. Redirecting to login.');
      this.$router.push('/login');
    }
  },

  methods: {
    logout() {
      localStorage.removeItem('jwt');
      localStorage.removeItem('role');
      this.$router.push('/');
    }
  }
};
</script>

<style scoped>
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
nav ul {
  list-style: none;
  padding: 0;
}
nav li {
  margin-bottom: 8px;
}
</style>
