<template>
    <nav class="navbar navbar-light bg-light shadow-sm px-3">
      <button class="btn btn-outline-secondary me-2" @click="$emit('toggle-sidebar')">
        ☰
      </button>
      <span class="navbar-brand mb-0 h1">Course Mark Management</span>
      <div class="ms-auto">
        <span class="me-3">Admin</span>
        <button class="btn btn-outline-danger btn-sm">Logout</button>
      </div>
    </nav>
  </template>
  