<template>
    <div>
      <h3 class="mb-4">View Remark Requests</h3>
  
      <div v-if="loading" class="text-center my-5">
        <span>Loading...</span>
      </div>
  
      <div v-else>
        <!-- Course and Section Selector -->
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
  
        <!-- Remark Table -->
        <div v-if="selectedSectionId && remarks.length > 0">
          <h5 class="mt-3">
            <strong>{{ selectedCourse?.course_code }} - {{ selectedCourse?.course_name }}</strong><br />
            <small>Section: {{ selectedSection?.section_name }}</small>
          </h5>
  
          <table class="table table-bordered table-sm mt-3">
            <thead>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Assessment</th>
                <th>Justification</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(remark, idx) in remarks" :key="remark.id">
                <td>{{ idx + 1 }}</td>
                <td>{{ remark.student_full_name }} ({{ remark.student_matric_no }})</td>
                <td>{{ remark.assessment_name }}</td>
                <td>{{ remark.justification }}</td>
              </tr>
            </tbody>
          </table>
        </div>
  
        <div v-else-if="selectedSectionId && remarks.length === 0">
          <div class="alert alert-info">No remark requests for this section.</div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import {
    getCoursesWithSections,
    getLecturerIdByUserId,
    getRemarkRequestsBySectionId
  } from '../services/lecturer'
  
  export default {
    name: 'LecturerViewRemarks',
    data() {
      return {
        loading: true,
        courses: [], // Ensure it's an array
        selectedCourseId: '',
        selectedSectionId: '',
        remarks: []
      }
    },
    computed: {
      filteredSections() {
        const course = Array.isArray(this.courses)
          ? this.courses.find(c => c.course_id === this.selectedCourseId)
          : null
        return course?.sections || []
      },
      selectedCourse() {
        return Array.isArray(this.courses)
          ? this.courses.find(c => c.course_id === this.selectedCourseId)
          : null
      },
      selectedSection() {
        return this.filteredSections.find(s => s.section_id === this.selectedSectionId)
      }
    },
    watch: {
      selectedSectionId: {
        immediate: true,
        handler: 'fetchRemarks'
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
          const lecturerId = await getLecturerIdByUserId(user?.id)
          const result = await getCoursesWithSections(lecturerId)
          this.courses = Array.isArray(result) ? result : []
        } catch (err) {
          console.error('Error fetching courses:', err)
          this.courses = []
        }
        this.loading = false
      },
      async fetchRemarks() {
        this.remarks = []
        if (!this.selectedSectionId) return
        try {
          this.remarks = await getRemarkRequestsBySectionId(this.selectedSectionId)
        } catch (err) {
          console.error('Error fetching remarks:', err)
          this.remarks = []
        }
      },
      onCourseChange() {
        this.selectedSectionId = ''
      }
    }
  }
  </script>
  
  <style scoped>
  h5 {
    margin-bottom: 1rem;
  }
  </style>
  