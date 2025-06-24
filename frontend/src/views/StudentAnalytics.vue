<template>
    <div>
      <h3 class="mb-4">My Progress Overview</h3>
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
          <h5 class="mt-4">
            {{ selectedCourse?.course_code }} - {{ selectedCourse?.course_name }}<br/>
            <small>Section: {{ selectedSection.section_name }}</small>
          </h5>
  
          <div class="my-4">
            <label class="form-label">Total Weighted Percentage</label>
            <div class="progress" style="height: 30px;">
              <div class="progress-bar" :style="{ width: totalPercentage + '%' }">
                {{ totalPercentage }}%
              </div>
            </div>
          </div>
  
          <div class="card p-3 mt-4">
            <h6>Assessment Breakdown</h6>
            <canvas ref="studentChart" height="100"></canvas>
          </div>
        </div>
  
        <div v-else-if="selectedCourseId">
          <div class="alert alert-info">Please select a section to view progress.</div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import Chart from 'chart.js/auto'
  import {
    getCoursesByStudentId,
    getStudentIdByUserId,
    getStudentAssessmentMarks,
    getTotalPercentageByStudentIdAndSectionId
  } from '../services/student'
  
  export default {
    name: 'StudentAnalytics',
    data() {
      return {
        loading: true,
        courses: [],
        selectedCourseId: '',
        selectedSectionId: '',
        assessmentMarks: [],
        totalPercentage: 0,
        studentId: null,
        chartInstance: null
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
        handler: 'fetchStudentProgress'
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
        }
        this.loading = false
      },
  
      async fetchStudentProgress() {
        if (!this.selectedSectionId || !this.studentId) return
  
        try {
          this.assessmentMarks = await getStudentAssessmentMarks(this.studentId, this.selectedSectionId)
          this.totalPercentage = await getTotalPercentageByStudentIdAndSectionId(this.studentId, this.selectedSectionId)
          this.renderChart()
        } catch (err) {
          console.error('Error fetching progress:', err)
        }
      },
  
      renderChart() {
        if (this.chartInstance) {
          this.chartInstance.destroy()
        }
  
        const ctx = this.$refs.studentChart
        const labels = this.assessmentMarks.map(m => m.assessment_name)
        const data = this.assessmentMarks.map(m => m.marks_obtained)
  
        this.chartInstance = new Chart(ctx, {
          type: 'bar',
          data: {
            labels,
            datasets: [{
              label: 'Marks Obtained',
              data,
              backgroundColor: 'rgba(255, 159, 64, 0.6)'
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              title: { display: false }
            },
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Marks'
                }
              }
            }
          }
        })
      },
  
      onCourseChange() {
        this.selectedSectionId = ''
        if (this.chartInstance) this.chartInstance.destroy()
      }
    }
  }
  </script>
  
  <style scoped>
  .progress-bar {
    background-color: #007bff;
    line-height: 30px;
    font-weight: bold;
  }
  </style>
  