<template>
  <div>
    <h3 class="mb-4">View My Students</h3>
    <div v-if="loading" class="text-center my-5">
      <span>Loading...</span>
    </div>
    <div v-else>
      <div class="d-flex justify-content-center mb-3 flex-wrap gap-3">
        <div style="min-width: 250px;">
          <label class="form-label">Select Course</label>
          <select v-model="selectedCourseId" class="form-select" @change="onCourseChange">
            <option value="" disabled>Select a course</option>
            <option v-for="course in courses" :key="course.course_id" :value="course.course_id">
              {{ course.course_code }} - {{ course.course_name }}
            </option>
          </select>
        </div>
        <div style="min-width: 250px;">
          <label class="form-label">Select Section</label>
          <select v-model="selectedSectionId" class="form-select" :disabled="!selectedCourseId">
            <option value="" disabled>Select a section</option>
            <option v-for="section in filteredSections" :key="section.section_id" :value="section.section_id">
              {{ section.section_name }}
            </option>
          </select>
        </div>
      </div>
      <div v-if="selectedSection">
        <h5>
          {{ selectedSection.course_code }} - {{ selectedSection.course_name }}<br/>
          <small>Section: {{ selectedSection.section_name }}</small>
        </h5>
        <button class="btn btn-success mb-3" @click="showAddStudentModal = true">+ Add Student</button>
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>#</th>
              <th>Matric No</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(student, sidx) in studentsInSection" :key="student.id">
              <td>{{ sidx + 1 }}</td>
              <td>{{ student.matric_number }}</td>
              <td>{{ student.full_name }}</td>
              <td>{{ student.email }}</td>
              <td>{{ student.phone }}</td>
              <td>
                <button class="btn btn-danger btn-sm" @click="removeStudent(student.id)">Remove</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else-if="selectedCourseId">
        <div class="alert alert-info">Please select a section to view students.</div>
      </div>
    </div>
    <!-- Add Student Modal -->
    <div class="modal fade" tabindex="-1" :class="{ show: showAddStudentModal }" style="display: block;" v-if="showAddStudentModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <form @submit.prevent="submitAddStudent">
            <div class="modal-header">
              <h5 class="modal-title">Add Student to Section</h5>
              <button type="button" class="btn-close" @click="showAddStudentModal = false"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Select Existing Student</label>
                <select v-model="selectedExistingStudentId" class="form-select mb-3">
                  <option value="" disabled>Select existing student</option>
                  <option v-for="student in allStudents" :key="student.id" :value="student.id">
                    {{ student.matric_number }} - {{ student.name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Add</button>
              <button type="button" class="btn btn-secondary" @click="showAddStudentModal = false">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getCoursesWithSections, getLecturerIdByUserId, getAllStudents, createEnrollment, getStudentsBySectionId, removeEnrollmentByStudentAndSection } from '../services/lecturer.js'
export default {
  name: 'LecturerManageStudent',
  data() {
    return {
      loading: true,
      courses: [], // [{ course_id, course_code, course_name, sections: [{section_id, section_name, students: []}] }]
      selectedCourseId: '',
      selectedSectionId: '',
      showAddStudentModal: false,
      addStudentForm: {
        matric_number: '',
        full_name: '',
        email: '',
        phone: ''
      },
      allStudents: [],
      selectedExistingStudentId: '',
      studentsInSection: [],
    }
  },
  computed: {
    filteredSections() {
      const course = this.courses.find(c => c.course_id === this.selectedCourseId)
      return course ? course.sections : []
    },
    selectedSection() {
      return this.filteredSections.find(s => s.section_id === this.selectedSectionId)
    }
  },
  watch: {
    selectedSectionId: {
      immediate: true,
      handler: 'fetchStudentsForSection'
    }
  },
  mounted() {
    this.fetchSectionsWithStudents()
  },
  methods: {
    async fetchSectionsWithStudents() {
      this.loading = true
      let userId = null
      try {
        const user = JSON.parse(localStorage.getItem('user'))
        userId = user && user.id
      } catch (e) {
        console.error('Error getting user from localStorage:', e)
      }
      if (!userId) {
        this.courses = []
        this.loading = false
        return
      }
      try {
        const lecturerId = await getLecturerIdByUserId(userId)
        if (!lecturerId) {
          this.courses = []
          this.loading = false
          return
        }
        const courses = await getCoursesWithSections(lecturerId)
        this.courses = courses
        this.allStudents = await getAllStudents()
      } catch (err) {
        this.courses = []
      }
      this.loading = false
    },
    async fetchStudentsForSection() {
      this.studentsInSection = []
      if (this.selectedSectionId) {
        try {
          this.studentsInSection = await getStudentsBySectionId(this.selectedSectionId)
        } catch (err) {
          this.studentsInSection = []
        }
      }
    },
    onCourseChange() {
      this.selectedSectionId = ''
    },
    async submitAddStudent() {
      if (this.selectedExistingStudentId && this.selectedSectionId) {
        try {
          await createEnrollment({ student_id: this.selectedExistingStudentId, section_id: this.selectedSectionId })
          alert('Student enrolled successfully!')
          this.fetchStudentsForSection()
        } catch (err) {
          alert('Failed to enroll student: ' + (err.response?.data?.error || err.message))
        }
      }
      this.showAddStudentModal = false
      this.selectedExistingStudentId = ''
    },
    async removeStudent(studentId) {
      if (!this.selectedSectionId) return
      if (!confirm('Are you sure you want to remove this student from the section?')) return
      try {
        await removeEnrollmentByStudentAndSection(studentId, this.selectedSectionId)
        alert('Student removed from section.')
        this.fetchStudentsForSection()
      } catch (err) {
        alert('Failed to remove student: ' + (err.response?.data?.error || err.message))
      }
    },
  }
}
</script>

<style scoped>
h5 {
  margin-bottom: 0.5rem;
}
.d-flex.gap-3 > * {
  margin-right: 1rem;
}
</style> 