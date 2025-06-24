<template>
    <div class="container mt-4">
      <h2>Export Student Consultation Report</h2>
  
      <div class="mb-3">
        <label class="form-label">Select Advisee</label>
        <select v-model="selectedStudentId" class="form-select">
          <option value="" disabled>Select a student</option>
          <option v-for="s in students" :key="s.id" :value="s.id">
            {{ s.name }} ({{ s.matric_number }})
          </option>
        </select>
      </div>
  
      <div v-if="selectedStudentId">
        <button class="btn btn-outline-success" @click="exportCsv">Export as CSV</button>
      </div>
    </div>
  </template>
  
  
  <script setup>
import { ref, onMounted } from 'vue'
import { getAdvisees, exportAdvisorReportCsv } from '@/services/advisor'

const students = ref([])
const selectedStudentId = ref('')

onMounted(async () => {
  const user = JSON.parse(localStorage.getItem('user'))
  if (!user?.id) return
  const data = await getAdvisees(user.id)
  students.value = data
})

async function exportCsv() {
  const user = JSON.parse(localStorage.getItem('user'))
  const userId = user?.id
  if (!userId || !selectedStudentId.value) return

  try {
    const response = await exportAdvisorReportCsv(userId, selectedStudentId.value)

    const blob = new Blob([response.data], { type: 'text/csv' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'consultation_report.csv')
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    alert('Failed to export report.')
    console.error(error)
  }
}
</script>
