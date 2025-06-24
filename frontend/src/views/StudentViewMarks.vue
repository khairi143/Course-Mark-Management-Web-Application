<template>
  <div>
    <h3 class="mb-4">View My Marks</h3>

    <div v-if="loading" class="text-center my-5">
      <span>Loading...</span>
    </div>

    <div v-else>
      <!-- Course and Section Selection -->
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

      <!-- Assessment Table -->
      <div v-if="selectedSectionId">
        <h5 class="mt-3">
          <strong>{{ selectedCourse?.course_code }} - {{ selectedCourse?.course_name }}</strong><br />
          <small>Section: {{ selectedSection?.section_name }}</small>
        </h5>

        <table class="table table-bordered table-sm mt-3">
          <thead>
            <tr>
              <th>#</th>
              <th>Assessment</th>
              <th>Max Marks</th>
              <th>Weight %</th>
              <th>Marks Obtained</th>
              <th>Percentage Obtained</th>
              <th>Remark</th> <!-- ✅ New column -->
            </tr>
          </thead>
          <tbody>
            <tr v-for="(mark, idx) in assessmentMarks" :key="mark.assessment_id">
              <td>{{ idx + 1 }}</td>
              <td>{{ mark.assessment_name }}</td>
              <td>{{ mark.max_marks }}</td>
              <td>{{ mark.weight_percentage }}</td>
              <td>{{ mark.marks_obtained }}</td>
              <td>
                {{
                  (mark.marks_obtained && mark.max_marks)
                    ? ((mark.marks_obtained / mark.max_marks) * mark.weight_percentage).toFixed(2)
                    : '0.00'
                }}%
              </td>
              <td>
                <button class="btn btn-sm btn-outline-primary" @click="openRemarkModal(mark)">
                  Request Remark
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="alert alert-info">
          <strong>Total Percentage:</strong> {{ totalPercentage }}%
        </div>
      </div>
      <div v-else-if="selectedCourseId">
        <div class="alert alert-info">Please select a section to view marks.</div>
      </div>
    </div>

    <!-- ✅ Remark Modal -->
    <div class="modal fade show" v-if="showModal" tabindex="-1" style="display: block;" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Submit Remark Request</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <p>
              <strong>Assessment:</strong> {{ selectedAssessment?.assessment_name }}
            </p>
            <textarea
              class="form-control"
              v-model="justification"
              placeholder="Explain why you are requesting a remark"
              rows="4"
            ></textarea>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button class="btn btn-primary" :disabled="justification.length < 10" @click="submitRemark">
              Submit Request
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal backdrop -->
    <div class="modal-backdrop fade show" v-if="showModal"></div>
  </div>
</template>

<script>
import {
  getCoursesByStudentId,
  getStudentAssessmentMarks,
  getTotalPercentageByStudentIdAndSectionId,
  getStudentIdByUserId,
  submitRemarkRequest // ✅ new API method
} from '../services/student'

export default {
  name: 'StudentViewMarks',
  data() {
    return {
      loading: true,
      courses: [],
      selectedCourseId: '',
      selectedSectionId: '',
      assessmentMarks: [],
      totalPercentage: 0,
      studentId: null,
      showModal: false,
      justification: '',
      selectedAssessment: null
    }
  },
  computed: {
    selectedCourse() {
      return this.courses.find(c => c.course_id === this.selectedCourseId)
    },
    filteredSections() {
      return this.selectedCourse?.sections || []
    },
    selectedSection() {
      return this.filteredSections.find(s => s.section_id === this.selectedSectionId)
    }
  },
  watch: {
    selectedSectionId: {
      immediate: true,
      handler: 'fetchAssessmentMarks'
    }
  },
  mounted() {
    this.fetchCourses()
  },
  methods: {
    async fetchCourses() {
      this.loading = true
      try {
        const user = JSON.parse(localStorage.getItem('user'))
        const studentObj = await getStudentIdByUserId(user?.id)
        this.studentId = studentObj?.id
        const courses = await getCoursesByStudentId(this.studentId)
        this.courses = Array.isArray(courses) ? courses : []
      } catch (err) {
        console.error('Failed to load courses:', err)
        this.courses = []
      }
      this.loading = false
    },

    async fetchAssessmentMarks() {
      this.assessmentMarks = []
      this.totalPercentage = 0

      if (!this.selectedSectionId || !this.studentId) return

      try {
        this.assessmentMarks = await getStudentAssessmentMarks(this.studentId, this.selectedSectionId)
        this.totalPercentage = await getTotalPercentageByStudentIdAndSectionId(this.studentId, this.selectedSectionId)
      } catch (err) {
        console.error('Error fetching assessment marks:', err)
        this.assessmentMarks = []
        this.totalPercentage = 0
      }
    },

    onCourseChange() {
      this.selectedSectionId = ''
    },

    openRemarkModal(assessment) {
      this.selectedAssessment = assessment
      this.justification = ''
      this.showModal = true
    },

    closeModal() {
      this.showModal = false
      this.selectedAssessment = null
    },

    async submitRemark() {
      try {
        await submitRemarkRequest({
          student_id: this.studentId,
          assessment_id: this.selectedAssessment.assessment_id,
          justification: this.justification
        })
        alert('Remark request submitted successfully.')
        this.closeModal()
      } catch (err) {
        console.error('Failed to submit remark request:', err)
        alert('Failed to submit request. Please try again.')
      }
    }
  }
}
</script>

<style scoped>
.modal {
  background-color: rgba(0, 0, 0, 0.5);
}
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>
