<template>
  <div class="dashboard">
    <header>
      <h1>Welcome, {{ roleLabel }}</h1>
      <button @click="logout">Logout</button>
    </header>

    <nav>
      <ul>
        <li v-for="item in menusForRole" :key="item.name">
          <router-link :to="item.route">{{ item.name }}</router-link>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
export default {
  name: 'MyDashboard',
  data() {
    return {
      // Fetch the role from localStorage
      role: localStorage.getItem('role') || 'guest',
      menus: {
        lecturer: [
          { name: 'Manage Student', route: '/lecturer/manage-students' },
          { name: 'Continuous Assessment', route: '/lecturer/assessments' }
        ],
        student: [
          { name: 'Assessment', route: '/student/assessment' },
          //{ name: 'Compare Mark with Coursemates', route: '/student/compare-marks' },
          { name: 'Student Ranking', route: '/student/studentranking' },
          { name: 'Student Performance Expectation', route: '/student/performance-expectation' }
        ],
        advisor: [
          { name: 'Student-Advisor List', route: '/advisor/student-advisor-list' },
          { name: 'Meeting Records', route: '/advisor/meeting-records' }
        ],
        admin: [
          { name: 'Manage Users', route: '/admin/manage-users' },
          // { name: 'Assign Lecturers to Courses', route: '/admin/manage-courses' }
        ]
      }
    };
  },
  computed: {
    // Use the role from localStorage to display the correct menu
    menusForRole() {
      return this.menus[this.role] || [];  // Get the menu items for the current role
    },
    roleLabel() {
      return {
        lecturer: 'Lecturer',
        student: 'Student',
        advisor: 'Academic Advisor',
        admin: 'Admin'
      }[this.role] || 'User';  // Default to 'User' if role is not recognized
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('jwt');
      localStorage.removeItem('role');  // Remove the role when logging out
      this.$router.push('/');  // Redirect to the login page
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
