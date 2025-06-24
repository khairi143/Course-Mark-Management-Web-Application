<template>
  <div>
    <h3 class="mb-4">Manage Assessments</h3>
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
        <div class="card p-3 mb-3">
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Assessment Name</label>
              <input v-model="assessmentName" type="text" class="form-control" placeholder="e.g. Quiz 1, Assignment 2, Project" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Max Marks</label>
              <input v-model.number="assessmentMaxMarks" type="number" min="1" class="form-control" placeholder="e.g. 10" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Weight (%)</label>
              <input v-model.number="assessmentWeight" type="number" min="1" max="100" class="form-control" placeholder="e.g. 20" />
            </div>
            <div class="col-md-2">
              <button class="btn btn-success w-100" @click="createAssessment">Create</button>
            </div>
          </div>
        </div>
        <div v-if="createdAssessment" class="alert alert-success">Assessment created: {{ createdAssessment.type }} (Max: {{ createdAssessment.maxMark }}, Weight: {{ createdAssessment.weight }}%)</div>
        <div class="mt-4">
          <h6>Existing Assessments</h6>
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th>#</th>
                <th>Assessment Name</th>
                <th>Max Marks</th>
                <th>Weight (%)</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(assessment, idx) in assessmentsInSection" :key="assessment.id || idx">
                <td>{{ idx + 1 }}</td>
                <td>{{ assessment.assessment_name || assessment.type }}</td>
                <td>{{ assessment.max_marks || assessment.maxMark }}</td>
                <td>{{ assessment.weight_percentage || assessment.weight }}%</td>
                <td>
                  <button class="btn btn-primary btn-sm me-1" @click="startEditAssessment(assessment)">Edit</button>
                  <button class="btn btn-danger btn-sm" @click="removeAssessment(idx)">Remove</button>
                </td>
              </tr>
              <tr v-if="assessmentsInSection.length === 0">
                <td colspan="5" class="text-center">No assessments yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div v-else-if="selectedCourseId">
        <div class="alert alert-info">Please select a section to manage assessments.</div>
      </div>
    </div>
    <div v-if="editingAssessment" class="modal fade show" tabindex="-1" style="display:block; background:rgba(0,0,0,0.3);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Assessment</h5>
            <button type="button" class="btn-close" @click="cancelEditAssessment"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Assessment Name</label>
              <input v-model="editAssessmentName" class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label">Max Marks</label>
              <input v-model.number="editAssessmentMaxMarks" type="number" min="1" class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label">Weight (%)</label>
              <input v-model.number="editAssessmentWeight" type="number" min="1" max="100" class="form-control" />
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="cancelEditAssessment">Cancel</button>
            <button class="btn btn-success" @click="saveEditAssessment">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getCoursesWithSections, getLecturerIdByUserId, createAssessment, getAssessmentsBySectionId, deleteAssessment, updateAssessment } from '../services/lecturer.js'
export default {
  name: 'LecturerManageAssessment',
  data() {
    return {
      loading: true,
      courses: [],
      selectedCourseId: '',
      selectedSectionId: '',
      assessmentName: '',
      assessmentMaxMarks: '',
      assessmentWeight: '',
      createdAssessment: null,
      assessmentTypes: ['Quiz', 'Assignment', 'Exercises', 'Labs', 'Test', 'Project'],
      assessmentsInSection: [],
      editingAssessment: null,
      editAssessmentName: '',
      editAssessmentMaxMarks: '',
      editAssessmentWeight: '',
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
      handler: 'fetchAssessmentsForSection'
    }
  },
  mounted() {
    this.fetchSections()
  },
  methods: {
    async fetchSections() {
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
      } catch (err) {
        this.courses = []
      }
      this.loading = false
    },
    onCourseChange() {
      this.selectedSectionId = ''
      this.assessmentName = ''
      this.assessmentMaxMarks = ''
      this.assessmentWeight = ''
      this.createdAssessment = null
      this.assessmentsInSection = []
    },
    async createAssessment() {
      if (!this.assessmentName || !this.assessmentMaxMarks || !this.assessmentWeight) {
        alert('Please enter assessment name, max marks, and weight.')
        return
      }
      if (!this.selectedSectionId) {
        alert('Please select a section.')
        return
      }
      try {
        const payload = {
          section_id: this.selectedSectionId,
          assessment_name: this.assessmentName,
          max_marks: this.assessmentMaxMarks,
          weight_percentage: this.assessmentWeight
        }
        const created = await createAssessment(payload)
        this.createdAssessment = {
          type: created.assessment_name || this.assessmentName,
          maxMark: created.max_marks || this.assessmentMaxMarks,
          weight: created.weight_percentage || this.assessmentWeight
        }
        // Optionally, refresh the list of assessments from backend
        if (this.selectedSectionId) {
          this.assessmentsInSection = await getAssessmentsBySectionId(this.selectedSectionId)
        }
        this.assessmentName = ''
        this.assessmentMaxMarks = ''
        this.assessmentWeight = ''
      } catch (err) {
        alert('Failed to create assessment: ' + (err.response?.data?.error || err.message))
      }
    },
    async removeAssessment(idx) {
      if (!confirm('Remove this assessment?')) {
        return
      }
      const assessment = this.assessmentsInSection[idx]
      if (!assessment.id) {
        // If no ID, just remove from local array (fallback)
        this.assessmentsInSection.splice(idx, 1)
        return
      }
      try {
        await deleteAssessment(assessment.id)
        this.assessmentsInSection.splice(idx, 1)
        alert('Assessment removed successfully!')
      } catch (err) {
        alert('Failed to remove assessment: ' + (err.response?.data?.error || err.message))
      }
    },
    async fetchAssessmentsForSection() {
      this.assessmentsInSection = []
      if (this.selectedSectionId) {
        try {
          this.assessmentsInSection = await getAssessmentsBySectionId(this.selectedSectionId)
        } catch (err) {
          console.error('Failed to fetch assessments:', err)
          this.assessmentsInSection = []
        }
      }
    },
    startEditAssessment(assessment) {
      this.editingAssessment = assessment
      this.editAssessmentName = assessment.assessment_name
      this.editAssessmentMaxMarks = assessment.max_marks
      this.editAssessmentWeight = assessment.weight_percentage
    },
    cancelEditAssessment() {
      this.editingAssessment = null
      this.editAssessmentName = ''
      this.editAssessmentMaxMarks = ''
      this.editAssessmentWeight = ''
    },
    async saveEditAssessment() {
      if (!this.editingAssessment) return
      try {
        await updateAssessment(this.editingAssessment.id, {
          assessment_name: this.editAssessmentName,
          max_marks: this.editAssessmentMaxMarks,
          weight_percentage: this.editAssessmentWeight
        })
        if (this.selectedSectionId) {
          this.assessmentsInSection = await getAssessmentsBySectionId(this.selectedSectionId)
        }
        this.cancelEditAssessment()
        alert('Assessment updated successfully!')
      } catch (err) {
        alert('Failed to update assessment: ' + (err.response?.data?.error || err.message))
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