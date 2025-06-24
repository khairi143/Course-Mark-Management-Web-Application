<template>
  <div>
    <h3 class="mb-4">Visual Analytics: Student Progress Overview</h3>
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
        <div class="mb-3">
          <button 
            @click="exportToCSV" 
            class="btn btn-success"
            :disabled="!selectedSectionId || studentsInSection.length === 0"
          >
            <i class="fas fa-download me-2"></i>Export Full Results as CSV
          </button>
        </div>
        <div class="card p-3">
          <h6>Student Progress Overview</h6>
          <div class="mb-3">
            <label class="form-label">Select Student</label>
            <select v-model="selectedStudentId" class="form-select">
              <option value="" disabled>Select a student</option>
              <option v-for="student in studentsInSection" :key="student.id" :value="student.id">
                {{ student.matric_number }} - {{ student.name }}
              </option>
            </select>
          </div>
          <canvas ref="studentChart" height="100"></canvas>
        </div>
      </div>
      <div v-else-if="selectedCourseId">
        <div class="alert alert-info">Please select a section to view analytics.</div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref, watch, nextTick, computed } from 'vue'
import { getCoursesWithSections, getLecturerIdByUserId, getStudentsBySectionId, getAssessmentMarksJoin, getTotalPercentageByStudentIdAndSectionId } from '../services/lecturer.js'
import Chart from 'chart.js/auto'

export default {
  name: 'LecturerAnalytics',
  setup() {
    const loading = ref(true)
    const courses = ref([])
    const selectedCourseId = ref('')
    const selectedSectionId = ref('')
    const studentsInSection = ref([])
    const progressChart = ref(null)
    const selectedStudentId = ref('')
    const studentAssessmentMarks = ref([])
    let chartInstance = null
    let studentChartInstance = null
    const studentChart = ref(null)

    const filteredSections = computed(() => {
      const course = courses.value.find(c => c.course_id === selectedCourseId.value)
      return course ? course.sections : []
    })
    const selectedSection = computed(() => {
      return filteredSections.value.find(s => s.section_id === selectedSectionId.value)
    })

    async function fetchSectionsAndStudents() {
      loading.value = true
      let userId = null
      try {
        const user = JSON.parse(localStorage.getItem('user'))
        userId = user && user.id
      } catch (e) { /* ignore */ }
      if (!userId) {
        courses.value = []
        studentsInSection.value = []
        loading.value = false
        return
      }
      try {
        const lecturerId = await getLecturerIdByUserId(userId)
        if (!lecturerId) {
          courses.value = []
          studentsInSection.value = []
          loading.value = false
          return
        }
        const courseList = await getCoursesWithSections(lecturerId)
        courses.value = courseList
      } catch (err) {
        courses.value = []
      }
      loading.value = false
    }

    async function fetchStudentsForSection() {
      studentsInSection.value = []
      if (selectedSectionId.value) {
        try {
          const students = await getStudentsBySectionId(selectedSectionId.value)
          studentsInSection.value = students
          await nextTick()
          renderChart()
        } catch (err) {
          studentsInSection.value = []
        }
      } else {
        if (chartInstance) chartInstance.destroy()
      }
    }

    async function fetchStudentAssessmentMarks() {
      studentAssessmentMarks.value = []
      if (selectedSectionId.value && selectedStudentId.value) {
        try {
          const marks = await getAssessmentMarksJoin({ studentId: selectedStudentId.value })
          studentAssessmentMarks.value = selectedSectionId.value
            ? marks.filter(m => m.section_id == selectedSectionId.value)
            : marks
          await nextTick()
          renderStudentChart()
        } catch (err) {
          studentAssessmentMarks.value = []
        }
      } else {
        if (studentChartInstance) studentChartInstance.destroy()
      }
    }

    function renderChart() {
      if (!progressChart.value) return
      if (chartInstance) chartInstance.destroy()
      const labels = studentsInSection.value.map(s => s.matric_number + ' - ' + s.name)
      const data = studentsInSection.value.map(s => s.total_percentage !== undefined ? s.total_percentage : 0)
      chartInstance = new Chart(progressChart.value, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Total Weighted %',
            data,
            backgroundColor: 'rgba(54, 162, 235, 0.6)'
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
            title: { display: false }
          },
          scales: {
            y: { beginAtZero: true, max: 100 }
          }
        }
      })
    }

    function renderStudentChart() {
      if (!studentChart.value) return
      if (studentChartInstance) studentChartInstance.destroy()
      const labels = studentAssessmentMarks.value.map(m => m.assessment_name || m.assessment_id)
      const data = studentAssessmentMarks.value.map(m => m.marks_obtained)
      studentChartInstance = new Chart(studentChart.value, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Marks Obtained',
            data,
            fill: false,
            borderColor: 'rgba(255, 99, 132, 0.8)',
            backgroundColor: 'rgba(255, 99, 132, 0.3)',
            tension: 0.3,
            pointRadius: 5,
            pointHoverRadius: 7
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
            title: { display: false },
            tooltip: {
              callbacks: {
                title: function(context) {
                  return labels[context[0].dataIndex];
                }
              }
            }
          },
          scales: {
            x: {
              title: {
                display: true,
                text: 'Assessment Name'
              },
              ticks: {
                autoSkip: false,
                maxRotation: 45,
                minRotation: 0
              }
            },
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Marks Obtained'
              }
            }
          }
        }
      })
    }

    function onCourseChange() {
      selectedSectionId.value = ''
      studentsInSection.value = []
      if (chartInstance) chartInstance.destroy()
    }

    async function exportToCSV() {
      if (!selectedSectionId.value || studentsInSection.value.length === 0) {
        alert('Please select a section with students first.')
        return
      }

      try {
        // Get all assessment marks for all students in this section
        const allMarks = await getAssessmentMarksJoin()
        const sectionMarks = allMarks.filter(m => m.section_id == selectedSectionId.value)
        
        // Get unique assessment names for headers
        const assessmentNames = [...new Set(sectionMarks.map(m => m.assessment_name))]
        
        // Create CSV headers
        const headers = ['Matric Number', 'Student Name', 'Email', 'Total Weighted %', ...assessmentNames]
        
        // Create CSV rows
        const csvRows = []
        csvRows.push(headers.join(','))
        
        // Add data for each student
        for (const student of studentsInSection.value) {
          // Fetch total percentage for each student/section
          let totalPercentage = await getTotalPercentageByStudentIdAndSectionId(student.id, selectedSectionId.value)
          totalPercentage = totalPercentage !== null && totalPercentage !== undefined ? Number(totalPercentage).toFixed(2) + '%' : 'N/A'

          const studentMarks = sectionMarks.filter(m => m.student_id == student.id)
          const row = [
            student.matric_number,
            student.name,
            student.email,
            totalPercentage
          ]
          
          // Add marks for each assessment
          for (const assessmentName of assessmentNames) {
            const mark = studentMarks.find(m => m.assessment_name === assessmentName)
            row.push(mark ? mark.marks_obtained : 'N/A')
          }
          
          csvRows.push(row.join(','))
        }
        
        // Create and download CSV file
        const csvContent = csvRows.join('\n')
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
        const link = document.createElement('a')
        const url = URL.createObjectURL(blob)
        link.setAttribute('href', url)
        link.setAttribute('download', `${selectedSection.value.course_code}_${selectedSection.value.section_name}_results.csv`)
        link.style.visibility = 'hidden'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        
      } catch (error) {
        console.error('Error exporting CSV:', error)
        alert('Error exporting CSV. Please try again.')
      }
    }

    watch(selectedCourseId, fetchSectionsAndStudents, { immediate: true })
    watch(selectedSectionId, fetchStudentsForSection)
    watch(selectedStudentId, fetchStudentAssessmentMarks)

    onMounted(fetchSectionsAndStudents)

    return {
      loading,
      courses,
      selectedCourseId,
      selectedSectionId,
      filteredSections,
      selectedSection,
      studentsInSection,
      progressChart,
      onCourseChange,
      selectedStudentId,
      studentAssessmentMarks,
      studentChart,
      exportToCSV
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