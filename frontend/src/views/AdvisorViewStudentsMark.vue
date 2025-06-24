<template>
    <div class="container mt-4">
      <h2>Advisee Marks Breakdown</h2>
  
      <!-- Student Dropdown -->
      <div class="mb-3">
        <label class="form-label">Select Student</label>
        <select v-model="selectedStudentId" @change="onStudentChange" class="form-select">
          <option value="" disabled>Select a student</option>
          <option v-for="s in students" :key="s.id" :value="s.id">
            {{ s.name }} ({{ s.matric_number }})
          </option>
        </select>
      </div>
  
      <!-- Course Dropdown -->
      <div class="mb-3" v-if="availableCourses.length">
        <label class="form-label">Select Course</label>
        <select v-model="selectedCourseName" @change="onCourseChange" class="form-select">
          <option value="" disabled>Select a course</option>
          <option v-for="c in availableCourses" :key="c">{{ c }}</option>
        </select>
      </div>
  
      <!-- Section Dropdown -->
      <div class="mb-3" v-if="availableSections.length">
        <label class="form-label">Select Section</label>
        <select v-model="selectedSectionName" @change="computeRank" class="form-select">
          <option value="" disabled>Select a section</option>
          <option v-for="s in availableSections" :key="s">{{ s }}</option>
        </select>
      </div>
  
      <!-- Summary Card -->
    <div v-if="selectedStudentId && selectedCourseName && selectedSectionName">
      <div v-if="rankData && filteredAssessments.length" class="alert mt-3" :class="isAtRisk ? 'alert-danger' : 'alert-success'">
        <strong>Rank:</strong> {{ rankData.rank }} /
        {{ rankData.total }} |
        <strong>Percentile:</strong> {{ rankData.percentile.toFixed(2) }}%
        <span v-if="isAtRisk" class="badge bg-danger ms-3">At-Risk</span>
      </div>

      <div v-else class="alert alert-info mt-3">
        This student does not have any marks yet.
      </div>
    </div>

  
      <!-- Assessment Table -->
      <div v-if="filteredAssessments.length">
        <table class="table table-bordered table-sm mt-3">
          <thead class="table-light">
            <tr>
              <th>Assessment</th>
              <th>Max Marks</th>
              <th>Weight %</th>
              <th>Obtained</th>
              <th>Submitted At</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(a, i) in filteredAssessments" :key="i">
              <td>{{ a.assessment_name }}</td>
              <td>{{ a.max_marks }}</td>
              <td>{{ a.weight_percentage }}</td>
              <td>{{ a.marks_obtained ?? '-' }}</td>
              <td>{{ formatDate(a.submission_date) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <div v-else-if="selectedSectionName" class="alert alert-warning mt-3">
        No assessments found for this student in this course/section.
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, computed } from 'vue'
  import { getAdviseesMarks } from '@/services/advisor.js'
  
  const rawMarks = ref([])
  const students = ref([])
  const selectedStudentId = ref('')
  const selectedCourseName = ref('')
  const selectedSectionName = ref('')
  
  const availableCourses = ref([])
  const availableSections = ref([])
  
  const rankData = ref(null)
  const isAtRisk = ref(false)
  
  const filteredAssessments = computed(() => {
    return rawMarks.value.filter(r =>
      r.student_id === selectedStudentId.value &&
      r.course_name === selectedCourseName.value &&
      r.section_name === selectedSectionName.value &&
      r.assessment_name
    ).map(a => ({
      assessment_name: a.assessment_name,
      max_marks: a.max_marks,
      weight_percentage: a.weight_percentage,
      marks_obtained: a.marks_obtained,
      submission_date: a.submission_date
    }))
  })
  
  onMounted(async () => {
    const user = JSON.parse(localStorage.getItem('user'))
    if (!user?.id) return
    const data = await getAdviseesMarks(user.id)
    rawMarks.value = data
  
    const uniqueStudents = new Map()
    for (const row of data) {
      if (!uniqueStudents.has(row.student_id)) {
        uniqueStudents.set(row.student_id, {
          id: row.student_id,
          name: row.student_name,
          matric_number: row.matric_number,
          email: row.email
        })
      }
    }
    students.value = Array.from(uniqueStudents.values())
  })
  
  function onStudentChange() {
    selectedCourseName.value = ''
    selectedSectionName.value = ''
    rankData.value = null
    isAtRisk.value = false
  
    const courses = rawMarks.value
      .filter(r => r.student_id === selectedStudentId.value)
      .map(r => r.course_name)
    availableCourses.value = [...new Set(courses)]
    availableSections.value = []
  }
  
  function onCourseChange() {
    selectedSectionName.value = ''
    rankData.value = null
    isAtRisk.value = false
  
    const sections = rawMarks.value
      .filter(r =>
        r.student_id === selectedStudentId.value &&
        r.course_name === selectedCourseName.value
      )
      .map(r => r.section_name)
    availableSections.value = [...new Set(sections)]
  }
  
  function computeRank() {
    if (!selectedStudentId.value || !selectedCourseName.value || !selectedSectionName.value) {
      rankData.value = null
      return
    }

    const sectionMarks = rawMarks.value.filter(r =>
      r.course_name === selectedCourseName.value &&
      r.section_name === selectedSectionName.value &&
      r.total_percentage !== null
    )

    // 💡 Check if the selected student has any marks in this section
    const studentHasMark = sectionMarks.some(r => r.student_id === selectedStudentId.value)

    if (!studentHasMark) {
      rankData.value = null
      isAtRisk.value = false
      return
    }

    const scoreList = sectionMarks.reduce((acc, cur) => {
      if (!acc.some(s => s.student_id === cur.student_id)) {
        acc.push({ student_id: cur.student_id, percentage: parseFloat(cur.total_percentage) || 0 })
      }
      return acc
    }, [])

    scoreList.sort((a, b) => b.percentage - a.percentage)

    const index = scoreList.findIndex(s => s.student_id === selectedStudentId.value)
    const rank = index + 1
    const total = scoreList.length
    const percentile = total > 1 ? ((total - rank) / (total - 1)) * 100 : 100

    rankData.value = { rank, total, percentile }
    isAtRisk.value = percentile < 20
  }

  
  function formatDate(dateStr) {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleString()
  }
  </script>
  
  <style scoped>
  .badge {
    font-size: 0.9em;
  }
  </style>
  