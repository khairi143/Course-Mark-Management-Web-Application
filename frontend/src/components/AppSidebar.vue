<template>
  <div
  class="bg-dark text-white p-3 d-none d-md-block"
  style="width: 250px; height: calc(100vh - 56px); position: sticky; top: 56px; overflow-y: auto;"
>

    <h4 class="mb-4">{{ panelTitle }}</h4>
    <ul class="nav flex-column">
      <template v-if="role_id === 4">
        <li class="nav-item mb-2">
          <router-link to="/admin/manage-users" class="nav-link text-white">Manage Users</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/admin/assign-lecturers" class="nav-link text-white">Assign Lecturers</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/admin/system-logs" class="nav-link text-white">View Logs</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/reset-password" class="nav-link text-white">Reset Password</router-link>
        </li>
      </template>

      <template v-else-if="role_id === 1">
        <li class="nav-item mb-2">
          <router-link to="/lecturer/manage-students" class="nav-link text-white">Manage Student Records</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/lecturer/manage-assessments" class="nav-link text-white">Manage Continuous Assessment Components</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/lecturer/enter-assessment-marks" class="nav-link text-white">Enter Assessment Marks</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/lecturer/analytics" class="nav-link text-white">View Student Progress</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/lecturer/view-remarks" class="nav-link text-white">View Remark Request</router-link>
        </li>
      </template>

      <template v-else-if="role_id === 2">
        <li class="nav-item mb-2">
          <router-link to="/student/view-marks" class="nav-link text-white">View Assessment Marks</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/student/analytics" class="nav-link text-white">View Progress</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/student/compare" class="nav-link text-white">Compare Marks</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/student/view-rank" class="nav-link text-white">View Rank</router-link>
        </li>
      </template>

      <template v-else-if="role_id === 3">
        <li class="nav-item mb-2">
          <router-link to="/advisor/view-students" class="nav-link text-white">View My Advisees</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/advisor/view-students-mark" class="nav-link text-white">Advisee Marks Breakdown</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/advisor/notes" class="nav-link text-white">Add Private Notes</router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link to="/advisor/export" class="nav-link text-white">Export Consultation Report</router-link>
        </li>
      </template>
    </ul>
  </div>
</template>

  
<script>

export default {
  name: 'AppSidebar',
  data() {
    return {
      role_id: null,
      panelTitle: 'Panel'
    }
  },
  mounted() {
    const userStr = localStorage.getItem('user')
    if (userStr) {
      try {
        const user = JSON.parse(userStr)
        this.role_id = user.role_id
        this.panelTitle = user.role_id === 4
  ? 'Admin Panel'
  : user.role_id === 1
    ? 'Lecturer Panel'
    : user.role_id === 2
      ? 'Student Panel'
      : user.role_id === 3
      ? 'Advisor Panel'
      : 'Panel';
      } catch (e) { /* ignore */ }
    }
  }
}
</script>
  