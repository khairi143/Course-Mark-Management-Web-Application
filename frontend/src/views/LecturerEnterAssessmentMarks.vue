<template>
  <div>
    <h3 class="mb-4">Enter Continuous Assessment Marks</h3>
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

        <div v-if="studentsInSection.length > 0" class="mt-4">
          <h6>Select Student and Enter Assessment Marks</h6>
          
          <!-- Student Selection -->
          <div class="card p-3 mb-3">
            <div class="row g-3 align-items-end">
              <div class="col-md-6">
                <label class="form-label">Select Student</label>
                <select v-model="selectedStudentId" class="form-select" @change="onStudentChange">
                  <option value="" disabled>Choose a student</option>
                  <option v-for="student in studentsInSection" :key="student.id" :value="student.id">
                    {{ student.matric_number }} - {{ student.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Assessment Marks Entry -->
          <div v-if="selectedStudent && assessmentsInSection.length > 0" class="card p-3">
            <h6>Enter Marks for {{ selectedStudent.name }}</h6>
            <div class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead class="table-light">
                  <tr>
                    <th>Assessment</th>
                    <th>Max Marks</th>
                    <th>Weight (%)</th>
                    <th>Marks Obtained</th>
                    <th>Percentage</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="assessment in assessmentsInSection" :key="assessment.id">
                    <td>{{ assessment.assessment_name }}</td>
                    <td>{{ assessment.max_marks }}</td>
                    <td>{{ assessment.weight_percentage }}%</td>
                    <td>
                      <input 
                        v-model.number="assessment.marks_obtained" 
                        type="number" 
                        min="0" 
                        :max="assessment.max_marks" 
                        class="form-control form-control-sm" 
                        style="width: 80px;"
                        @input="calculateAssessmentPercentage(assessment)"
                      />
                    </td>
                    <td>{{ assessment.percentage || 0 }}%</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mt-2">
              <strong>Total Weighted Percentage: {{ totalWeightedPercentage.toFixed(2) }}%</strong>
            </div>
            <div class="mt-3">
              <button class="btn btn-success" @click="saveStudentMarks">
                Save Marks for {{ selectedStudent.name }}
              </button>
              <button class="btn btn-secondary ms-2" @click="resetStudentMarks">
                Reset Marks
              </button>
            </div>
          </div>
          
          <div v-else-if="selectedStudent && assessmentsInSection.length === 0" class="alert alert-info">
            No assessments created for this section yet. Please create assessments first.
          </div>
        </div>
        
        <div v-else-if="selectedSectionId" class="alert alert-info">
          No students enrolled in this section.
        </div>
      </div>
      
      <div v-else-if="selectedCourseId">
        <div class="alert alert-info">Please select a section to enter assessment marks.</div>
      </div>
    </div>
  </div>
</template>

<script>
import { getCoursesWithSections, getLecturerIdByUserId, getStudentsBySectionId, getAssessmentsBySectionId, saveStudentAssessmentMarks, getStudentAssessmentMarks, saveOrUpdateEnrollmentTotalPercentage } from '../services/lecturer.js'

export default {
  name: 'LecturerEnterAssessmentMarks',
  data() {
    return {
      loading: true,
      courses: [],
      selectedCourseId: '',
      selectedSectionId: '',
      selectedStudentId: '',
      studentsInSection: [],
      assessmentsInSection: [],
    }
  },
  computed: {
    filteredSections() {
      const course = this.courses.find(c => c.course_id === this.selectedCourseId)
      return course ? course.sections : []
    },
    selectedSection() {
      return this.filteredSections.find(s => s.section_id === this.selectedSectionId)
    },
    selectedStudent() {
      return this.studentsInSection.find(s => s.id === this.selectedStudentId)
    },
    totalWeightedPercentage() {
      // Sum the weighted percentages for all assessments with marks
      return this.assessmentsInSection.reduce((sum, a) => {
        if (a.marks_obtained !== null && a.max_marks > 0 && a.weight_percentage) {
          return sum + ((a.marks_obtained / a.max_marks) * a.weight_percentage)
        }
        return sum
      }, 0)
    }
  },
  watch: {
    selectedSectionId: {
      immediate: true,
      handler: 'fetchSectionData'
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
    async fetchSectionData() {
      this.studentsInSection = []
      this.assessmentsInSection = []
      this.selectedStudentId = ''
      
      if (this.selectedSectionId) {
        try {
          const [students, assessments] = await Promise.all([
            getStudentsBySectionId(this.selectedSectionId),
            getAssessmentsBySectionId(this.selectedSectionId)
          ])
          
          this.studentsInSection = students
          this.assessmentsInSection = assessments.map(assessment => ({
            ...assessment,
            marks_obtained: null,
            percentage: 0
          }))
        } catch (err) {
          console.error('Failed to fetch section data:', err)
          this.studentsInSection = []
          this.assessmentsInSection = []
        }
      }
    },
    onCourseChange() {
      this.selectedSectionId = ''
      this.selectedStudentId = ''
      this.studentsInSection = []
      this.assessmentsInSection = []
    },
    async onStudentChange() {
      // Reset assessment marks when student changes
      this.assessmentsInSection.forEach(assessment => {
        assessment.marks_obtained = null
        assessment.percentage = 0
      })
      if (this.selectedStudentId && this.selectedSectionId) {
        try {
          const marks = await getStudentAssessmentMarks(this.selectedSectionId, this.selectedStudentId)
          // Map marks to assessments
          this.assessmentsInSection.forEach(assessment => {
            const found = marks.find(m => m.assessment_id == assessment.id)
            if (found) {
              assessment.marks_obtained = Number(found.marks_obtained)
              assessment.percentage = assessment.max_marks > 0
                ? ((assessment.marks_obtained / assessment.max_marks) * assessment.weight_percentage)
                : 0;
            }
          })
        } catch (err) {
          // If error, just leave as null
        }
      }
    },
    calculateAssessmentPercentage(assessment) {
      if (assessment.marks_obtained !== null && assessment.max_marks > 0) {
        assessment.percentage = Math.round((assessment.marks_obtained / assessment.max_marks) * 100)
      } else {
        assessment.percentage = 0
      }
    },
    async saveStudentMarks() {
      if (!this.selectedStudentId) {
        alert('Please select a student first.')
        return
      }
      const assessmentsWithMarks = this.assessmentsInSection.filter(a => a.marks_obtained !== null)
      if (assessmentsWithMarks.length === 0) {
        alert('Please enter marks for at least one assessment.')
        return
      }
      if (!confirm(`Save marks for ${this.selectedStudent.name}?`)) {
        return
      }
      try {
        // Prepare payload: [{ assessment_id, marks_obtained }]
        const marksArray = assessmentsWithMarks.map(a => ({ assessment_id: a.id, marks_obtained: a.marks_obtained }))
        const result = await saveStudentAssessmentMarks(this.selectedSectionId, this.selectedStudentId, marksArray)
        
        // Show feedback about what was saved
        const summary = result.summary
        let message = `Marks saved successfully!\n\n`
        message += `Total marks processed: ${summary.total_marks_processed}\n`
        message += `Marks changed: ${summary.marks_changed}\n`
        message += `Marks unchanged: ${summary.marks_unchanged}`
        
        if (summary.marks_changed > 0) {
          message += `\n\n${summary.marks_changed} notification(s) sent to the student.`
        } else {
          message += `\n\nNo notifications sent (no marks were changed).`
        }
        
        // Calculate total weighted percentage
        let totalWeighted = 0
        for (const a of assessmentsWithMarks) {
          if (a.max_marks && a.weight_percentage) {
            const percent = (a.marks_obtained / a.max_marks) * a.weight_percentage
            totalWeighted += percent
          }
        }
        await saveOrUpdateEnrollmentTotalPercentage(this.selectedSectionId, this.selectedStudentId, totalWeighted)
        
        alert(message)
      } catch (err) {
        alert('Failed to save marks: ' + (err.response?.data?.error || err.message))
      }
    },
    resetStudentMarks() {
      if (confirm('Reset all marks for this student?')) {
        this.assessmentsInSection.forEach(assessment => {
          assessment.marks_obtained = null
          assessment.percentage = 0
        })
      }
    }
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
.table-responsive {
  max-height: 400px;
  overflow-y: auto;
}
</style> 