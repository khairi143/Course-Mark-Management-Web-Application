<template>
    <div class="bg-dark text-white vh-100 p-3 d-none d-md-block" style="width: 250px;">
      <h4 class="mb-4">Admin Panel</h4>
      <ul class="nav flex-column">
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
      </ul>
    </div>
  </template>
  