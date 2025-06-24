<template>
    <div class="container mt-4">
      <h2>My Advisees</h2>
  
      <div v-if="advisees.length === 0" class="alert alert-info mt-3 text-center">
        No advisees found.
      </div>
  
      <table v-else class="table table-bordered table-hover mt-3">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Matric Number</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(student, index) in advisees" :key="student.id">
            <td>{{ index + 1 }}</td>
            <td>{{ student.name }}</td>
            <td>{{ student.matric_number }}</td>
            <td>{{ student.email }}</td>
            <td>{{ student.phone ?? '-' }}</td>
            <td>{{ student.address ?? '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { getAdvisees } from '@/services/advisor.js'
  
  const advisees = ref([])
  
  onMounted(async () => {
    const user = JSON.parse(localStorage.getItem('user'))
    if (!user?.id) return
  
    try {
      advisees.value = await getAdvisees(user.id)
    } catch (err) {
      console.error('Failed to fetch advisees:', err)
    }
  })
  </script>
  