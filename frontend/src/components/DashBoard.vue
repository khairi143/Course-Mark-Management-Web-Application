<template>
  <div class="dashboard-container">
    <h1>Dashboard</h1>
    <p>Welcome to your student dashboard. Here you can view detailed information about your progress and performance.</p>

    <!-- Containers for various sections -->
    <div class="dashboard-sections">
      <!-- Full Mark Breakdown Section -->
      <div class="card">
        <h2>View Full Mark Breakdown</h2>
        <p>Detailed view of your marks across different assessments and components.</p>
        <!-- Add your actual content here -->
        <button>View Breakdown</button>
      </div>

      <!-- Performance Analytics & Averages Section -->
      <div class="card">
        <h2>Performance Analytics & Averages</h2>
        <p>Check your overall performance and compare it against class averages.</p>
        <!-- Add actual graphs or charts here -->
        <button>View Analytics</button>
      </div>

      <!-- Compare with Coursemates Section -->
      <div class="card">
        <h2>Compare with Coursemates</h2>
        <p>Compare your marks anonymously with your classmates.</p>
        <!-- Add comparison charts here -->
        <button>Compare</button>
      </div>

      <!-- View Ranking/Position Section -->
      <div class="card">
        <h2>View Ranking/Position</h2>
        <p>View your position relative to your classmates based on overall performance.</p>
        <!-- Add ranking system here -->
        <button>View Ranking</button>
      </div>

      <!-- Performance Trend Graphs Section -->
      <div class="card">
        <h2>Performance Trend Graphs</h2>
        <p>Analyze your performance trends over time in various assessments.</p>
        <!-- Add trend graphs here -->
        <button>View Trends</button>
      </div>

      <!-- Request Rechecking or Appeal Section -->
      <div class="card">
        <h2>Request Rechecking or Appeal</h2>
        <p>If you believe there is a mistake in your grades, request a rechecking or appeal here.</p>
        <!-- Add form or request button here -->
        <button>Request Rechecking</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      // Data for dashboard sections can be added here
    };
  },
};
</script>

<style scoped>
.dashboard-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  text-align: center;
  font-size: 2.5em;
  color: #333;
}

p {
  text-align: center;
  font-size: 1.2em;
  color: #555;
  margin-bottom: 30px;
}

.dashboard-sections {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 40px;
}

.card {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.card h2 {
  margin-bottom: 20px;
  font-size: 1.5em;
  color: #333;
}

.card button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
}

.card button:hover {
  background-color: #45a049;
}

.card p {
  margin-bottom: 20px;
  color: #777;
}
</style>
