<template>
    <div>
      <h3 class="mb-4">System Logs</h3>
  
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Timestamp</th>
            <th>User</th>
            <th>Action</th>
            <th>Status</th>
            <th>Controls</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(log, index) in logs" :key="log.id">
            <td>{{ index + 1 }}</td>
            <td>{{ formatTimestamp(log.timestamp) }}</td>
            <td>{{ log.username || 'System' }}</td>
            <td>{{ log.action }}</td>
            <td>
              <span :class="['badge', log.reviewed ? 'bg-success' : 'bg-secondary']">
                {{ log.reviewed ? 'Reviewed' : 'Unreviewed' }}
              </span>
            </td>
            <td>
              <button v-if="!log.reviewed"
                      class="btn btn-sm btn-outline-primary"
                      @click="markReviewed(log.id)">
                Mark as Reviewed
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  export default {
    data() {
      return {
        logs: []
      }
    },
    mounted() {
      this.fetchLogs()
    },
    methods: {
      async fetchLogs() {
        try {
          const response = await axios.get('http://localhost:5000/api/logs')
          this.logs = response.data
        } catch (error) {
          console.error('Failed to fetch logs:', error)
        }
      },
      async markReviewed(logId) {
        try {
          await axios.put(`http://localhost:5000/api/logs/${logId}`, {
            reviewed: true
          })
          this.fetchLogs() // refresh
        } catch (error) {
          console.error('Failed to mark as reviewed:', error)
        }
      },
      formatTimestamp(ts) {
        return new Date(ts).toLocaleString()
      }
    }
  }
  </script>
  