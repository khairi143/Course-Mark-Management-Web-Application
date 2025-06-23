<template>
  <div class="view-mark">
    <header>
      <h1>Your Marks</h1>
      <p>Here is the detailed breakdown of your marks.</p>
    </header>

    <section class="marks-table">
      <table>
        <thead>
          <tr>
            <th>Course</th>
            <th>Assessment</th>
            <th>Marks Obtained</th>
            <th>Total Marks</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(mark, index) in marks" :key="index">
            <td>{{ mark.course }}</td>
            <td>{{ mark.assessment }}</td>
            <td>{{ mark.marksObtained }}</td>
            <td>{{ mark.totalMarks }}</td>
          </tr>
        </tbody>
      </table>
    </section>

    <footer>
      <button @click="goBack">Go Back</button>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'ViewMark',
  data() {
    return {
      marks: [
        { course: 'Math', assessment: 'Midterm', marksObtained: 85, totalMarks: 100 },
        { course: 'Science', assessment: 'Lab Report', marksObtained: 90, totalMarks: 100 },
        { course: 'History', assessment: 'Final Exam', marksObtained: 75, totalMarks: 100 },
      ],
    };
  },
  methods: {
    goBack() {
      this.$router.push({ name: 'student-dashboard' });
    },
  },
};
</script>

<style scoped>
.view-mark {
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

.marks-table table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.marks-table th, .marks-table td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: center;
}

footer {
  text-align: center;
  margin-top: 40px;
}

footer button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

footer button:hover {
  background-color: #45a049;
}
</style>
