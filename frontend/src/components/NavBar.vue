// components/NavBar.vue
<template>
  <nav>
    <ul>
      <li v-for="item in menus" :key="item">
        <router-link :to="`/${item.toLowerCase().replace(/\s+/g, '-')}`">{{ item }}</router-link>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  menus: ['Course Subjects', 'My Progress', 'Programs']
}
</script>

<style scoped>
nav ul {
  display: flex;
  gap: 16px;
  list-style: none;
  padding: 16px;
  background: white;
  border-bottom: 1px solid #e2e8f0;
}
nav ul li a {
  text-decoration: none;
  padding: 6px 12px;
  border-radius: 4px;
  transition: background 0.2s;
}
nav ul li a:hover {
  background: #f0f0f0;
}
</style>