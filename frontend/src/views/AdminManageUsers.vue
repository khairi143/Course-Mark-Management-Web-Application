<template>
  <div>
    <h3 class="mb-4">Manage Users</h3>

    <button class="btn btn-primary mb-3" @click="openAddModal">
      + Add New User
    </button>

    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Matric/Username</th>
          <th>Full Name</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(user, index) in users" :key="user.id">
          <td>{{ index + 1 }}</td>
          <td>{{ user.username || '-' }}</td>
          <td>{{ user.full_name || '-' }}</td>
          <td>{{ getRoleName(user.role_id) || '-' }}</td>
          <td>
            <button class="btn btn-sm btn-warning me-2" @click="editUser(user)">Edit</button>
            <button class="btn btn-sm btn-danger" @click="promptDeleteUser(user.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Add/Edit User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" ref="userModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <form @submit.prevent="submitUser">
            <div class="modal-header">
              <h5 class="modal-title">{{ isEditMode ? 'Edit User' : 'Add New User' }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">
                  {{ isStudent ? 'Matric No' : 'Username' }}
                </label>
                <input v-model="form.username" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input v-model="form.fullName" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Role</label>
                <select v-model="form.role_id" class="form-select" required>
                  <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.role_name }}
                  </option>
                </select>
              </div>

              <!-- Password / Pin Field for Add Mode -->
              <div class="mb-3" v-if="!isEditMode">
                <label class="form-label">
                  {{ isStudent ? 'Pin' : 'Password' }}
                </label>
                <input type="password" v-model="form.password" class="form-control" required />
              </div>

              <!-- Student Extra Fields -->
              <div v-if="isStudent">
                <div class="mb-3">
                  <label class="form-label">Phone</label>
                  <input v-model="form.phone" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input v-model="form.email" type="email" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Address</label>
                  <textarea v-model="form.address" class="form-control" rows="2" required></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Save</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true" ref="confirmDeleteModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this user?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" @click="confirmDelete">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Modal } from 'bootstrap'

export default {
  data() {
    return {
      users: [],
      roles: [],
      isEditMode: false,
      form: {
        id: null,
        username: '',
        fullName: '',
        role_id: '',
        password: '',
        phone: '',
        email: '',
        address: ''
      },
      userModal: null,
      confirmDeleteModal: null,
      userToDelete: null
    }
  },
  computed: {
    isStudent() {
      const selectedRole = this.roles.find(r => r.id === this.form.role_id)
      return selectedRole && selectedRole.role_name === 'student'
    }
  },
  mounted() {
    this.userModal = new Modal(this.$refs.userModal)
    this.confirmDeleteModal = new Modal(this.$refs.confirmDeleteModal)
    this.fetchRoles()
    this.fetchUsers()
  },
  methods: {
    async fetchUsers() {
      try {
        const res = await axios.get('http://localhost:5000/api/users')
        this.users = res.data
      } catch (err) {
        console.error('Failed to fetch users:', err)
      }
    },
    async fetchRoles() {
      try {
        const res = await axios.get('http://localhost:5000/api/roles')
        this.roles = res.data
      } catch (err) {
        console.error('Failed to fetch roles:', err)
      }
    },
    getRoleName(role_id) {
      const role = this.roles.find(r => r.id === role_id)
      return role ? role.role_name : null
    },
    openAddModal() {
      this.isEditMode = false
      this.form = { id: null, username: '', fullName: '', role_id: '', password: '' }
      this.userModal.show()
    },
    editUser(user) {
      this.isEditMode = true
      this.form = {
        id: user.id,
        username: user.username,
        fullName: user.full_name,
        role_id: user.role_id,
        // Include these if user is a student
        phone: user.phone || '',
        email: user.email || '',
        address: user.address || '',
        password: '' // optional, in case you want to allow changing password
      }
      this.userModal.show()
    },
    async submitUser() {
      try {
        const payload = {
          username: this.form.username,
          fullName: this.form.fullName,
          role_id: this.form.role_id
        }
        if (this.form.password) payload.password = this.form.password

        if (this.isEditMode) {
          await axios.put(`http://localhost:5000/api/users/${this.form.id}`, payload)
        } else {
          await axios.post('http://localhost:5000/api/users', this.form)
        }

        this.userModal.hide()
        this.fetchUsers()
      } catch (err) {
        console.error('Failed to submit user:', err)
      }
    },
    promptDeleteUser(id) {
      this.userToDelete = id
      this.confirmDeleteModal.show()
    },
    async confirmDelete() {
      try {
        await axios.delete(`http://localhost:5000/api/users/${this.userToDelete}`)
        this.confirmDeleteModal.hide()
        this.fetchUsers()
      } catch (err) {
        console.error('Failed to delete user:', err)
      }
    }
  }
}
</script>
