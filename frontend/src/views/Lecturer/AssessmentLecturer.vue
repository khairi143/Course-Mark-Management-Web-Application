<template>
  <div>
    <h2>Assessment Components</h2>

    <!-- Loop through each course and display a table for each -->
    <div v-for="course in courses" :key="course.course_code">
      <h3>{{ course.course_name }} ({{ course.course_code }})</h3>

      <!-- Table for each course -->
      <table>
        <thead>
          <tr>
            <th>No.</th>
            <th>Component ID</th>
            <th>Component Name</th>
            <th>Max Mark</th>
            <th>Student Count</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(component, index) in course.assessments" :key="component.component_id">
            <td>{{ index + 1 }}</td>
            <td>{{ component.component_id }}</td>
            <td>{{ component.component_name }}</td>
            <td>{{ component.max_mark }}</td>
            <td>
              {{ component.student_count }}
              <button @click="$router.push(`/lecturer/manage-component/${component.component_id}`)" style="margin-left: 8px;">
              Manage
              </button>
            </td>
            <td>
              <button @click="editComponent(component)">Edit</button>
              <button @click="deleteComponent(component.component_id)">Delete</button>
              <button @click="clearAllMarks(component)">Clear All Marks</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Form to add a new assessment component -->
      <div>
        <h4>Add Assessment Component</h4>
        <form @submit.prevent="createAssessmentComponent(course.course_code)">
          <input type="text" v-model="newComponent.component_name" placeholder="Component Name" required />
          <input type="number" v-model="newComponent.max_mark" placeholder="Max Mark" required />
          <button type="submit">Create</button>
        </form>
      </div>
    </div>
    <button @click="$router.push('/dashboard')" style="margin-bottom: 16px;">Back to Dashboard</button>
  </div>
</template>

<script>
export default {
  name: 'AssessmentLecturer',
  data() {
    return {
      courses: [], // store all courses with their assessment components
      newComponent: {
        component_name: '',
        max_mark: null,
      },
    };
  },
  created() {
    this.fetchCourses(); // fetch courses when the component is created
  },
  methods: {
    async fetchCourses() {
      const lecturerId = localStorage.getItem('username');  // Get the lecturer's username (which acts as lecturer_id)
      const jwt = localStorage.getItem('jwt');  

      const response = await fetch(`http://localhost:8000/lecturer/${lecturerId}/get-assessment-components`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${jwt}`,
          'Content-Type': 'application/json'
        }
      });
      const data = await response.json();

      // Map and group assessments by course_code
      const groupedCourses = Object.keys(data).map(courseCode => ({
        course_code: courseCode,
        course_name: data[courseCode][0].course_name,  // Use the first component's course_name as the course name
        assessments: data[courseCode],
      }));

      this.courses = groupedCourses;  // Assign grouped courses to the component
    },

    async createAssessmentComponent(course_code) {
      const lecturerId = localStorage.getItem('username');
      const jwt = localStorage.getItem('jwt');  

      const response = await fetch(`http://localhost:8000/lecturer/${lecturerId}/create-assessment-components`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${jwt}`
        },
        body: JSON.stringify({
          course_code: course_code,
          component_name: this.newComponent.component_name,
          max_mark: this.newComponent.max_mark,
        }),
      });

      const data = await response.json();
      if (data.message) {
        alert('Assessment component created successfully');
        this.fetchCourses(); // Refresh the courses data
        this.newComponent.component_name = '';
        this.newComponent.max_mark = null;``
      }
    },

    editComponent(component) {
      const component_id = component.component_id;
      this.$router.push({
      path: `/lecturer/edit-assessment/${component_id}`,
      params: { component_id: component_id },
      });
    },

    async deleteComponent(component_id) {
      // Find the component by component_id to check student_count
      let component = null;
      const jwt = localStorage.getItem('jwt');  

      for (const course of this.courses) {
      component = course.assessments.find(c => c.component_id === component_id);
      if (component) break;
      }
      if (!component) {
      alert('Component not found');
      return;
      }
      if (component.student_count > 0) {
      alert('Cannot delete: There are students with marks for this component.');
      return;
      }

      const lecturerId = localStorage.getItem('username');
      const response = await fetch(`http://localhost:8000/lecturer/${lecturerId}/delete-assessment-components/${component_id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${jwt}`
        }
      });
      const data = await response.json();
      if (data.message) {
        alert('Assessment component deleted successfully');
        this.fetchCourses(); // Refresh the courses data
      }
    },

    // Method for "Clear All" button
    async clearAllMarks(component) {
      const lecturerId = localStorage.getItem('username'); // get the lecturer's username (equals to lecturer_id)
      const jwt = localStorage.getItem('jwt');  

      if (!lecturerId) {
        console.error("No lecturer username found in localStorage");
        return;
      }

      const component_id = component.component_id;

      const response = await fetch(`http://localhost:8000/lecturer/${lecturerId}/assessment-components/${component_id}/clear-all`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${jwt}`
        }
      });

      const data = await response.json();

      if (data.message) {
        alert('All marks for this assessment component have been cleared');
        this.fetchCourses();  // Refresh the courses data
      } else {
        console.error('Error clearing marks:', data);
      }
    },

  },
};
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}

table th, table td {
  padding: 8px 12px;
  border: 1px solid #ddd;
  text-align: left;
}

button {
  padding: 5px 10px;
  cursor: pointer;
  background-color: #2d3748;
  color: white;
  border: none;
  border-radius: 4px;
}

button:hover {
  background-color: #4a5568;
}

form input {
  margin: 5px;
}

form button {
  background-color: #4CAF50;
  color: white;
}
</style>
