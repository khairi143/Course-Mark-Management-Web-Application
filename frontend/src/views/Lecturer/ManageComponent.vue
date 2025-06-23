<template>
  <div>
    <h2>Manage Component</h2>

    <!-- Display the assessment component details -->
    <div>
      <h3>Component: {{ component.component_name }}</h3>
      <p>Course Code: {{ component.course_code }}</p>
      <p>Max Mark: {{ component.max_mark }}</p>
    </div>

    <!-- Display a table with enrolled students and their marks -->
    <h3>Enrolled Students:</h3>
    <table>
      <thead>
        <tr>
          <th>Enrollment ID</th>
          <th>Student Matric No</th>
          <th>Student Name</th>
          <th>Mark Obtained</th>
          <th>Mark ID</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="student in students" :key="student.enrollment_id">
          <td>{{ student.enrollment_id }}</td>
          <td>{{ student.student_matric_no }}</td>
          <td>{{ student.student_name }}</td>
          <td>
            <div v-if="student.editing">
              <input 
                v-model="student.mark_obtained" 
                type="number" 
                :max="component.max_mark"
                min="0"
                step="0.01"
              />
              <button @click="student.editing = false" class="cancel-button">Cancel</button>
            </div>
            <div v-else>
              {{ student.mark_obtained }}
            </div>
          </td>
          <td>{{ student.mark_id }}</td>
          <td>
            <button v-if="!student.editing" @click="student.editing = true">Edit</button>
            <button v-else @click="saveMark(student)">Save</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Create Enrollment form -->
    <div>
      <h3>Create New Enrollment</h3>
      <form @submit.prevent="createEnrollment">
        <label for="matric_no">Student Matric No:</label>
        <input 
          type="text" 
          v-model="newEnrollment.matric_no" 
          required
          @input="fetchStudentName"
        />
        
        <label for="student_name">Student Name:</label>
        <input 
          type="text" 
          v-model="newEnrollment.student_name" 
          readonly
        />
        
        <button type="submit">Enroll Student</button>
      </form>
    </div>

    <button @click="$router.push('/lecturer/assessments')" class="back-button">Back to Assessments</button>
  </div>
</template>

<script>
export default {
  name: 'ManageComponent',
  data() {
    return {
      component: {},
      students: [],
      newEnrollment: {
        matric_no: '',
        student_name: ''
      }
    };
  },
  created() {
    this.fetchComponentData();
  },
  methods: {
    async fetchComponentData() {
      const componentId = this.$route.params.component_id;
      const lecturerId = localStorage.getItem('username');
      const jwt = localStorage.getItem('jwt');

      const response = await fetch(`http://localhost:8000/lecturer/${lecturerId}/assessment-components/${componentId}`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${jwt}`,
          'Content-Type': 'application/json',
        }
      });
      const data = await response.json();

      if (data && data.component) {
        this.component = data.component;
        this.students = data.students;
      }
    },

    async saveMark(student) {
        const componentId = this.$route.params.component_id;
        const jwt = localStorage.getItem('jwt');

        // Check if the mark already exists for the student in the assessment_marks table
        const responseCheck = await fetch(`http://localhost:8000/lecturer/${localStorage.getItem('username')}/assessment-marks/${componentId}/check-mark/${student.enrollment_id}`, {
            method: 'GET',
            headers: {
            'Authorization': `Bearer ${jwt}`,
            'Content-Type': 'application/json',
            }
        });

        const checkData = await responseCheck.json();
        
        // If the mark doesn't exist, create a new entry
        if (!checkData.exists) {
            const responseCreate = await fetch(`http://localhost:8000/lecturer/${localStorage.getItem('username')}/assessment-marks/${componentId}/create-mark/${student.enrollment_id}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${jwt}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                mark_obtained: student.mark_obtained,
            }),
            });

            const createData = await responseCreate.json();
            if (createData.message) {
            alert('Assessment mark created successfully');
            } else {
            alert('Failed to create assessment mark');
            }
        } else {
            // If the mark exists, update the existing entry
            const responseUpdate = await fetch(`http://localhost:8000/lecturer/${localStorage.getItem('username')}/assessment-marks/${componentId}/update-mark/${student.enrollment_id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${jwt}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mark_obtained: student.mark_obtained,
                }),
            });

            const updateData = await responseUpdate.json();
            if (updateData.message) {
            alert('Student mark updated successfully');
            } else {
            alert('Failed to update student mark');
            }
        }

        // Refresh the student data to show updated marks
        this.fetchComponentData();
    },

    async createEnrollment() {
        const componentId = this.$route.params.component_id;
        const lecturerId = localStorage.getItem('username');  // Get lecturer_id from localStorage
        const jwt = localStorage.getItem('jwt');

        const response = await fetch(`http://localhost:8000/lecturer/${lecturerId}/assessment-components/${componentId}/enroll`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${jwt}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                matric_no: this.newEnrollment.matric_no,
                student_name: this.newEnrollment.student_name,
                course_code: this.component.course_code,  // Send course_code
                lecturer_id: lecturerId                  // Send lecturer_id
            }),
        });

        const data = await response.json();

        if (data.message) {
            alert('Student enrolled successfully');
            this.fetchComponentData();
        } else {
            alert('Failed to enroll student');
        }
    },

    async fetchStudentName() {
      const matric_no = this.newEnrollment.matric_no;
      if (matric_no) {
        const response = await fetch(`http://localhost:8000/student-name/${matric_no}`);
        const data = await response.json();
        if (data && data.name) {
          this.newEnrollment.student_name = data.name;
        }
      }
    }
  }
};
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

table, th, td {
  border: 1px solid #ddd;
}

th, td {
  padding: 8px;
  text-align: center;
}

button {
  padding: 5px 10px;
  margin: 5px;
  cursor: pointer;
}

.cancel-button {
  background-color: #e53e3e;
  color: white;
}

.back-button {
  background-color: #3182ce;
  color: white;
}

form {
  margin-top: 20px;
}

input {
  padding: 5px;
  margin: 5px;
}

h3 {
  margin-top: 20px;
}
</style>
