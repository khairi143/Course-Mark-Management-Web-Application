<template>
  <div>
    <h2>Edit Student Record</h2>
    <form @submit.prevent="updateStudent">
    <div>
      <label>Student Name:</label>
      <span>{{ student.student_name }}</span>
    </div>
      <div>
        <label for="final_exam_mark">Final Exam Mark:</label>
        <input type="number" id="final_exam_mark" v-model="student.final_exam_mark" required />
      </div>
      <div>
        <label for="final_total">Final Total:</label>
        <input type="number" id="final_total" v-model="student.final_total" required />
      </div>
      <button type="submit">Update</button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'EditEnrollment',
  props: ['enrollment_id'],
  data() {
    return {
      student: {
        student_name: '',
        final_exam_mark: '',
        final_total: '',
      }
    };
  },
  created() {
    this.fetchStudent();
  },
  methods: {
    async fetchStudent() {
      const response = await fetch(`http://localhost:8000/students/${this.enrollment_id}`);
      const data = await response.json();
      this.student = data;  // Populate the form with the existing student data
    },
    async updateStudent() {
      const response = await fetch(`http://localhost:8000/students/${this.enrollment_id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(this.student)
      });
      const data = await response.json();
      alert('Student record updated successfully');
      data.$router.push('/manage-students');  // Navigate back to the students page
    }
  }
};
</script>

<style scoped>
form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

label {
  font-weight: bold;
}

input {
  padding: 8px;
  font-size: 16px;
  border: 1px solid #ccc;
}

button {
  padding: 10px;
  background-color: #2f855a;
  color: white;
  border: none;
  border-radius: 4px;
}

button:hover {
  background-color: #4a9f7f;
}
</style>
