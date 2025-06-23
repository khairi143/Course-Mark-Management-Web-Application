<template>
  <div class="student-dashboard">
    <header>
      <h1>Student Dashboard</h1>
      <p>Welcome, {{ user.name }}! Here you can view your marks, progress, and upcoming assignments.</p>
    </header>

    <section class="overview">
      <div class="card">
        <h2>Course Progress</h2>
        <div class="progress-bar">
          <div class="progress" :style="{ width: courseProgress + '%' }"></div>
        </div>
        <p>{{ courseProgress }}% Completed</p>
      </div>
      
      <div class="card">
        <h2>Upcoming Deadlines</h2>
        <ul>
          <li v-for="(assignment, index) in upcomingAssignments" :key="index">
            <p>{{ assignment.name }} - Due: {{ assignment.dueDate }}</p>
          </li>
        </ul>
      </div>
    </section>

    <!-- New Card to View Full Marks -->
    <section>
      <div class="card">
        <h2>View Full Marks</h2>
        <p>Fetch preview mark from database</p>
        <button class="button" @click="viewFullMarks">View Mark</button>
      </div>
    </section>

    <section class="recent-activities">
      <div class="card">
        <h2>Recent Activities</h2>
        <ul>
          <li v-for="(activity, index) in recentActivities" :key="index">
            <p>{{ activity.name }} - {{ activity.date }}</p>
          </li>
        </ul>
      </div>
    </section>

    <footer>
      <button @click="logout">Logout</button>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'StudentDashboard',
  data() {
    return {
      user: {
        name: 'John Doe',  // Placeholder, replace with actual data
        role: 'Student',   // Placeholder, replace with actual data
      },
      courseProgress: 75,  // Example progress value
      upcomingAssignments: [
        { name: 'Assignment 1', dueDate: '2023-10-15' },
        { name: 'Quiz 2', dueDate: '2023-10-20' },
        { name: 'Lab Report', dueDate: '2023-10-25' },
      ],
      recentActivities: [
        { name: 'Completed Quiz 1', date: '2023-10-10' },
        { name: 'Submitted Assignment 1', date: '2023-10-05' },
        { name: 'Joined Discussion Forum', date: '2023-10-01' },
      ],
    };
  },
  methods: {
    logout() {
      alert('Logging out...');
      // Add actual logout functionality here
    },
    viewFullMarks() {
      // Navigate to the full marks page
      this.$router.push({ name: 'view-mark' });
    },
  },
};
</script>

<style scoped>
.student-dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

header {
  text-align: center;
  margin-bottom: 30px;
}

header h1 {
  font-size: 2em;
  color: #333;
}

header p {
  font-size: 1.2em;
  color: #555;
}

.overview {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 40px;
}

.card {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  flex-basis: 48%;
  cursor: pointer;
}

.card h2 {
  margin-bottom: 10px;
  font-size: 1.5em;
}

.progress-bar {
  width: 100%;
  height: 20px;
  background-color: #f3f3f3;
  border-radius: 10px;
  margin-top: 10px;
  overflow: hidden;
}

.progress {
  height: 100%;
  background-color: #4CAF50;
  transition: width 0.3s ease;
}

.recent-activities .card {
  margin-top: 20px;
}

ul {
  list-style: none;
  padding: 0;
}

ul li {
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

footer {
  text-align: center;
  margin-top: 40px;
}

footer button {
  padding: 10px 20px;
  background-color: #e53e3e;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

footer button:hover {
  background-color: #c53030;
}

.button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.button:hover {
  background-color: #45a049; /* Slightly darker green on hover */
}

</style>
