<template>
    <div>
      <h3 class="mb-4">View Your Rank and Percentile</h3>
  
      <div v-if="loading" class="text-center my-5">
        <span>Loading...</span>
      </div>
  
      <div v-else>
        <div class="d-flex justify-content-center mb-4 flex-wrap gap-3">
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
  
        <div v-if="rankInfo">
          <h5>{{ selectedCourse?.course_code }} - {{ selectedCourse?.course_name }}</h5>
          <p>Section: {{ selectedSection?.section_name }}</p>
  
          <div class="card mt-3 p-3">
            <p><strong>Total Students:</strong> {{ rankInfo.total }}</p>
            <p><strong>Your Rank:</strong> {{ rankInfo.rank }}</p>
            <p><strong>Your Percentile:</strong> {{ rankInfo.percentile.toFixed(2) }}%</p>
          </div>
  
          <!-- What-If Simulation -->
          <div class="mt-4">
            <h5>What-If Simulation</h5>
            <p>Try changing your total percentage to see how your rank and percentile would be affected.</p>
  
            <div class="d-flex flex-column align-items-center">
                <div class="mb-3" style="max-width: 300px; width: 100%;">
                    <label class="form-label text-center w-100">Hypothetical Total Percentage</label>
                    <input type="number" class="form-control text-center" v-model.number="whatIfPercentage" min="0" max="100" />
                </div>

                <button class="btn btn-primary" @click="simulateWhatIf">Simulate</button>
            </div>

  
            <div v-if="simulatedResult" class="mt-3 alert alert-info">
              <p><strong>Simulated Rank:</strong> {{ simulatedResult.rank }} out of {{ simulatedResult.total }}</p>
              <p><strong>Simulated Percentile:</strong> {{ simulatedResult.percentile.toFixed(2) }}%</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import {
    getCoursesByStudentId,
    getStudentIdByUserId,
    getTotalPercentageBySectionId
  } from '../services/student'
  
  export default {
    name: 'StudentViewRank',
    data() {
      return {
        loading: true,
        studentId: null,
        courses: [],
        selectedCourseId: '',
        selectedSectionId: '',
        rankInfo: null,
        percentageData: [],
        whatIfPercentage: null,
        simulatedResult: null
      }
    },
    computed: {
      filteredSections() {
        return this.selectedCourse?.sections || []
      },
      selectedCourse() {
        return this.courses.find(c => c.course_id === this.selectedCourseId)
      },
      selectedSection() {
        return this.filteredSections.find(s => s.section_id === this.selectedSectionId)
      }
    },
    watch: {
      selectedSectionId: 'fetchAssessmentData'
    },
    mounted() {
      this.fetchCourses()
    },
    methods: {
      async fetchCourses() {
        try {
          const user = JSON.parse(localStorage.getItem('user'))
          const student = await getStudentIdByUserId(user?.id)
          this.studentId = student.id
          const courses = await getCoursesByStudentId(this.studentId)
          this.courses = Array.isArray(courses) ? courses : []
        } catch (err) {
          console.error('Failed to fetch courses', err)
        }
        this.loading = false
      },
  
      async fetchAssessmentData() {
        this.rankInfo = null
        this.simulatedResult = null
        if (!this.selectedSectionId) return
  
        try {
          const data = await getTotalPercentageBySectionId(this.selectedSectionId)
          this.percentageData = Array.isArray(data)
            ? data.map(item => ({
                student_id: Number(item.student_id),
                percentage: Number(item.total_percentage)
              }))
            : []
  
          // Sort descending
          const sorted = [...this.percentageData].sort((a, b) => b.percentage - a.percentage)
          const myIndex = sorted.findIndex(item => item.student_id === Number(this.studentId))
          const rank = myIndex + 1
          const total = sorted.length
          const percentile = ((total - rank) / (total - 1)) * 100
  
          this.rankInfo = {
            rank,
            total,
            percentile: isNaN(percentile) ? 100 : percentile
          }
  
          // Default what-if value to current percentage
          const myData = this.percentageData.find(p => p.student_id === this.studentId)
          this.whatIfPercentage = myData?.percentage || 0
        } catch (err) {
          console.error('Failed to fetch percentage data:', err)
        }
      },
  
      simulateWhatIf() {
        if (this.whatIfPercentage == null || isNaN(this.whatIfPercentage)) return
  
        const simulated = [...this.percentageData]
        const alreadyExists = simulated.find(p => p.student_id === this.studentId)
  
        if (alreadyExists) {
          alreadyExists.percentage = this.whatIfPercentage
        } else {
          simulated.push({
            student_id: this.studentId,
            percentage: this.whatIfPercentage
          })
        }
  
        const sorted = simulated.sort((a, b) => b.percentage - a.percentage)
        const myIndex = sorted.findIndex(item => item.student_id === this.studentId)
        const rank = myIndex + 1
        const total = sorted.length
        const percentile = ((total - rank) / (total - 1)) * 100
  
        this.simulatedResult = {
          rank,
          total,
          percentile: isNaN(percentile) ? 100 : percentile
        }
      },
  
      onCourseChange() {
        this.selectedSectionId = ''
        this.rankInfo = null
        this.simulatedResult = null
      }
    }
  }
  </script>
  