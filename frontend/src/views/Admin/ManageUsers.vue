<template>
  <div class="manage-users">
    <h2>Manage Users</h2>

    <div class="filters">
      <label for="roleFilter">Filter by Role:</label>
      <select v-model="roleFilter" id="roleFilter">
        <option value="">All</option>
        <option value="admin">Admin</option>
        <option value="personbmi">Person BMI</option>
        <option value="nutritionist">Nutritionist</option>
        <option value="fitness">Fitness Instructor</option>
      </select>
    </div>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th style="text-align: center;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in filteredUsers" :key="user.email">
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ formatRole(user.role) }}</td>
          <td class="actions">
            <button @click="viewUser(user)">View</button>
            <button @click="editUser(user)">Edit</button>
            <button class="danger" @click="deleteUser(user)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'ManageUsers',
  data() {
    return {
      roleFilter: '',
      //for mock only, impl using fetch backend later
      users: [
        { name: 'Azman Musa', email: 'azman@bmiapp.com', role: 'personbmi' },
        { name: 'Dr. Nisa', email: 'nisa@bmiapp.com', role: 'nutritionist' },
        { name: 'Coach Rizal', email: 'rizal@bmiapp.com', role: 'fitness' },
        { name: 'Admin Lisa', email: 'admin@bmiapp.com', role: 'admin' }
      ]
    };
  },
  computed: {
    filteredUsers() {
      return this.roleFilter
        ? this.users.filter(u => u.role === this.roleFilter)
        : this.users;
    }
  },
  methods: {
    formatRole(role) {
      return role === 'personbmi' ? 'Person BMI' : role.charAt(0).toUpperCase() + role.slice(1);
    },
    viewUser(user) {
      alert(`Viewing:\n${user.name}\n${user.email}`);
    },
    editUser(user) {
      alert(`Editing: ${user.name}`);
    },
    deleteUser(user) {
      if (confirm(`Delete ${user.name}?`)) {
        this.users = this.users.filter(u => u.email !== user.email);
      }
    }
  }
};
</script>

<style scoped>
.manage-users {
  padding: 24px;
}

h2 {
  font-size: 22px;
  margin-bottom: 16px;
}

.filters {
  margin-bottom: 20px;
  display: flex;
  gap: 10px;
  align-items: center;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

th,
td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

th {
  background-color: #f7fafc;
  color: #2d3748;
  font-weight: 600;
}

.actions {
  text-align: center;
  display: flex;
  gap: 8px;
}

button {
  padding: 6px 12px;
  font-size: 13px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  background: #edf2f7;
  color: #2d3748;
  transition: background 0.2s ease;
}

button:hover {
  background: #e2e8f0;
}

button.danger {
  background: #feb2b2;
  color: #742a2a;
}

button.danger:hover {
  background: #fc8181;
}
</style>
