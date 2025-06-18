<template>
  <div>
    <h3 class="mb-4">Assign Lecturers to Courses</h3>

    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Course Code</th>
          <th>Course Name</th>
          <th>Lecturer</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(course, index) in courses" :key="course.id">
          <td>{{ index + 1 }}</td>
          <td>{{ course.course_code }}</td>
          <td>{{ course.course_name }}</td>
          <td>{{ getLecturerName(course.lecturer_id) }}</td>
          <td>
            <button class="btn btn-sm btn-warning" @click="openAssignModal(course)">Assign</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" ref="assignModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <form @submit.prevent="submitAssignment">
            <div class="modal-header">
              <h5 class="modal-title">Assign Lecturer</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Course</label>
                <input v-model="form.course_name" class="form-control" readonly />
              </div>
              <div class="mb-3">
                <label class="form-label">Lecturer</label>
                <select v-model="form.lecturer_id" class="form-select" required>
                  <option v-for="lecturer in lecturers" :key="lecturer.id" :value="lecturer.id">
                    {{ lecturer.full_name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-success" type="submit">Save</button>
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import axios from 'axios'
import { Modal } from 'bootstrap'

export default {
  data() {
    return {
      courses: [],
      lecturers: [],
      assignModal: null,
      form: {
        id: null,
        course_name: '',
        lecturer_id: ''
      }
    }
  },
  mounted() {
    this.assignModal = new Modal(this.$refs.assignModal)
    this.fetchData()
  },
  methods: {
    async fetchData() {
      try {
        const [coursesRes, lecturersRes] = await Promise.all([
          axios.get('http://localhost:5000/api/courses'),
          axios.get('http://localhost:5000/api/lecturers') // assumes lecturers already join user table
        ])
        this.courses = coursesRes.data
        this.lecturers = lecturersRes.data
      } catch (err) {
        console.error('Fetch error:', err)
      }
    },
    getLecturerName(lecturerId) {
      const lecturer = this.lecturers.find(l => l.id === lecturerId)
      return lecturer ? lecturer.full_name : 'N/A'
    },
    openAssignModal(course) {
      this.form = {
        id: course.id,
        course_name: course.course_name,
        lecturer_id: course.lecturer_id
      }
      this.assignModal.show()
    },
    async submitAssignment() {
      try {
        await axios.put(`http://localhost:5000/api/courses/${this.form.id}`, {
          lecturer_id: this.form.lecturer_id
        })
        this.assignModal.hide()
        this.fetchData()
      } catch (err) {
        console.error('Assignment update failed:', err)
      }
    }
  }
}
</script>
