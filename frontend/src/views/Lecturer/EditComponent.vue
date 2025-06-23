<template>
  <div>
    <h2>Edit Assessment Component</h2>

    <!-- Display Form for Editing Assessment Component -->
    <form @submit.prevent="updateComponent">
      <div>
        <label for="component_id">Component ID:</label>
        <input type="text" v-model="component.component_id" disabled />
      </div>
      
      <div>
        <label for="course_code">Course Code:</label>
        <input type="text" v-model="component.course_code" disabled />
      </div>

      <div>
        <label for="course_name">Course Name:</label>
        <input type="text" v-model="component.course_name" disabled />
      </div>

      <!-- <div>
        <label for="lecturer_id">Lecturer ID:</label>
        <input type="text" v-model="component.lecturer_id" disabled />
      </div> -->

      <div>
        <label for="student_count">Student Count:</label>
        <input type="number" v-model="component.student_count" disabled />
      </div>

      <div>
        <label for="component_name">Component Name:</label>
        <input type="text" v-model="component.component_name" required />
      </div>

      <div>
        <label for="max_mark">Max Mark:</label>
        <input type="number" v-model="component.max_mark" required />
      </div>

      <div>
        <button type="submit">Save</button>
      </div>

      <div>
        <button @click="$router.push('/lecturer/assessments')">Back to Assessments</button>
      </div>  
    </form>
  </div>
</template>

<script>
export default {
  name: 'EditComponent',
  data() {
    return {
      component: {
        component_id: '',
        course_code: '',
        course_name: '',
        //lecturer_id: '',
        student_count: '',
        component_name: '',
        max_mark: ''
      }
    };
  },
  created() {
    this.fetchComponent();  // Fetch component details when the page loads
  },
  methods: {
    async fetchComponent() {
      const component_id = this.$route.params.component_id;  // Get component_id from URL params
      const lecturerId = localStorage.getItem('username'); // Get lecturer_id from localStorage
      const jwt = localStorage.getItem('jwt');  

      const response = await fetch(`http://localhost:8000/lecturer/${lecturerId}/get-assessment-component/${component_id}`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${jwt}`
        }
      });
      const data = await response.json();

      if (data && data.component) {
        // Populate form with the current data of the component
        this.component = { ...data.component };
      }
    },

    async updateComponent() {
      const lecturerId = localStorage.getItem('username');  // Get lecturer_id from localStorage
      const component_id = this.$route.params.component_id;  // Get component_id from URL params
      const jwt = localStorage.getItem('jwt');  

      const response = await fetch(`http://localhost:8000/lecturer/${lecturerId}/assessment-components/${component_id}/update`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${jwt}`
        },
        body: JSON.stringify({
          component_name: this.component.component_name,
          max_mark: this.component.max_mark,
        }),
      });

      const data = await response.json();

      if (data.message) {
        alert('Assessment component updated successfully');
        this.$router.push('/lecturer/assessments');  // Redirect back to the Assessment Lecturer page
      } else {
        alert('Failed to update assessment component');
      }
    },
  },
};
</script>
