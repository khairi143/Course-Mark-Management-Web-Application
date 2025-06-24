<template>
    <div>
      <h3 class="mb-4">Compare Marks Anonymously</h3>
  
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
  
        <div v-if="selectedSectionId">
          <h5>
            {{ selectedCourse?.course_code }} - {{ selectedCourse?.course_name }} <br />
            <small>Section: {{ selectedSection?.section_name }}</small>
          </h5>
  
          <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm">
              <thead>
                <tr>
                  <th>Student</th>
                  <th v-for="a in assessments" :key="a">{{ a }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, idx) in anonymizedMarks" :key="idx">
                  <td>{{ row.student_id === studentId ? fullName : `Student ${idx + 1}` }}</td>
                  <td v-for="a in assessments" :key="a">{{ row[a] ?? 'N/A' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
  
          <canvas ref="barChart" height="200"></canvas>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import {
    getCoursesByStudentId,
    getStudentIdByUserId,
    getStudentsAssessmentMarksBySection
  } from '../services/student'
  
  import Chart from 'chart.js/auto'
  
  export default {
    name: 'StudentCompareMarks',
    data() {
      return {
        loading: true,
        courses: [],
        selectedCourseId: '',
        selectedSectionId: '',
        assessmentData: [],
        studentId: null,
        fullName: '',
        barChart: null
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
      },
      assessments() {
        return [...new Set(this.assessmentData.map(m => m.assessment_name))]
      },
      anonymizedMarks() {
        const grouped = {}
        this.assessmentData.forEach(mark => {
          if (!grouped[mark.student_id]) {
            grouped[mark.student_id] = { student_id: mark.student_id }
          }
          grouped[mark.student_id][mark.assessment_name] = mark.marks_obtained
        })
        return Object.values(grouped)
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
          this.fullName = user?.full_name || 'You'
          const studentObj = await getStudentIdByUserId(user?.id)
          this.studentId = studentObj?.id
          const courses = await getCoursesByStudentId(this.studentId)
          this.courses = Array.isArray(courses) ? courses : []
        } catch (err) {
          console.error('Failed to fetch courses', err)
        }
        this.loading = false
      },
  
      async fetchAssessmentData() {
        this.assessmentData = []
        if (!this.selectedSectionId) return
        try {
          const result = await getStudentsAssessmentMarksBySection(this.selectedSectionId)
          this.assessmentData = Array.isArray(result) ? result : []
          this.$nextTick(this.renderBarChart)
        } catch (err) {
          console.error('Failed to fetch assessment data', err)
          this.assessmentData = []
        }
      },
  
      renderBarChart() {
        if (this.barChart) this.barChart.destroy()
        const ctx = this.$refs.barChart
  
        const labels = this.assessments
        const datasets = this.anonymizedMarks.map((row, idx) => ({
          label: row.student_id === this.studentId ? this.fullName : `Student ${idx + 1}`,
          data: labels.map(label => row[label] ?? 0),
          backgroundColor: row.student_id === this.studentId ? 'rgba(255, 99, 132, 0.5)' : `rgba(${50 + idx * 30}, 100, 200, 0.4)`
        }))
  
        this.barChart = new Chart(ctx, {
          type: 'bar',
          data: { labels, datasets },
          options: {
            responsive: true,
            plugins: {
              title: { display: true, text: 'Assessment Comparison' },
              legend: { position: 'top' }
            },
            scales: {
              y: { beginAtZero: true }
            }
          }
        })
      },
  
      onCourseChange() {
        this.selectedSectionId = ''
      }
    }
  }
  </script>
  