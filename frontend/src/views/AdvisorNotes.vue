<template>
    <div class="container mt-4">
      <h2>Advisor Notes & Meeting Records</h2>
  
      <!-- Select Advisee -->
      <div class="mb-3">
        <label class="form-label">Select Advisee</label>
        <select v-model="selectedStudentId" @change="fetchNotes" class="form-select">
          <option value="" disabled>Select a student</option>
          <option v-for="s in students" :key="s.id" :value="s.id">
            {{ s.name }} ({{ s.matric_number }})
          </option>
        </select>
      </div>
  
      <!-- Add Note -->
      <div v-if="selectedStudentId" class="card mb-4">
        <div class="card-body">
          <h5 class="card-title">Add New Note</h5>
          <form @submit.prevent="submitNote">
            <div class="mb-2">
              <label class="form-label">Title</label>
              <input v-model="newNote.title" class="form-control" required />
            </div>
            <div class="mb-2">
              <label class="form-label">Content</label>
              <textarea v-model="newNote.content" rows="3" class="form-control" required></textarea>
            </div>
            <div class="mb-2">
              <label class="form-label">Date (optional)</label>
              <input v-model="newNote.date" type="date" class="form-control" />
            </div>
            <button class="btn btn-success mt-2">Save Note</button>
          </form>
        </div>
      </div>
  
      <!-- List of Notes -->
    <div v-if="notes.length">
    <h5 class="mb-3">Past Notes</h5>
    <div v-for="note in notes" :key="note.id" class="card mb-3">
        <div class="card-body">
        <div v-if="editNoteId === note.id">
            <input v-model="editNote.title" class="form-control mb-2" placeholder="Title" />
            <textarea v-model="editNote.content" rows="3" class="form-control mb-2" placeholder="Content"></textarea>
            <input v-model="editNote.date" type="date" class="form-control mb-2" />
            <button class="btn btn-sm btn-primary me-2" @click="saveEdit(note.id)">Save</button>
            <button class="btn btn-sm btn-secondary" @click="cancelEdit">Cancel</button>
        </div>
        <div v-else>
            <h6 class="card-title">{{ note.title }}</h6>
            <p class="card-text">{{ note.content }}</p>
            <small class="text-muted">Created At: {{ formatDate(note.created_at || note.date) }}</small>
            <div class="mt-2">
            <button class="btn btn-sm btn-outline-primary me-2" @click="startEdit(note)">Edit</button>
            <button class="btn btn-sm btn-outline-danger" @click="removeNote(note.id)">Delete</button>
            </div>
        </div>
        </div>
    </div>
    </div>

  
      <div v-else-if="selectedStudentId" class="alert alert-info">
        No notes found for this advisee.
      </div>
    </div>
  </template>
  
  <script setup>
import { ref, onMounted } from 'vue'
import {
  getAdvisees,
  getAdvisorNotes,
  addAdvisorNote,
  updateAdvisorNote,
  deleteAdvisorNote
} from '@/services/advisor'

const students = ref([])
const selectedStudentId = ref('')
const notes = ref([])

const newNote = ref({
  title: '',
  content: '',
  date: ''
})

const editNoteId = ref(null)
const editNote = ref({
  title: '',
  content: '',
  date: ''
})

onMounted(async () => {
  const user = JSON.parse(localStorage.getItem('user'))
  if (!user?.id) return
  const data = await getAdvisees(user.id)
  students.value = data
})

async function fetchNotes() {
  if (!selectedStudentId.value) return

  const user = JSON.parse(localStorage.getItem('user'))
  const userId = user?.id
  if (!userId) return

  const data = await getAdvisorNotes(userId, selectedStudentId.value)
  notes.value = Array.isArray(data) ? data : []
}

async function submitNote() {
  const user = JSON.parse(localStorage.getItem('user'))
  const userId = user?.id
  if (!userId) return

  const noteData = {
    student_id: selectedStudentId.value,
    ...newNote.value
  }

  const res = await addAdvisorNote(userId, noteData)

  if (res && (res.success === true || res.id || typeof res === 'object')) {
    await fetchNotes()
    newNote.value = { title: '', content: '', date: '' }
    } else {
    alert('Failed to save note.')
    }

}

function startEdit(note) {
  editNoteId.value = note.id
  editNote.value = { ...note }
}

function cancelEdit() {
  editNoteId.value = null
  editNote.value = { title: '', content: '', date: '' }
}

async function saveEdit(noteId) {
  const user = JSON.parse(localStorage.getItem('user'))
  const userId = user?.id
  if (!userId) return

  const res = await updateAdvisorNote(userId, noteId, editNote.value)
  if (res?.success || res?.id) {
    await fetchNotes()
    cancelEdit()
  } else {
    alert('Failed to update note.')
  }
}

async function removeNote(noteId) {
  if (!confirm('Are you sure you want to delete this note?')) return

  const user = JSON.parse(localStorage.getItem('user'))
  const userId = user?.id
  if (!userId) return

  const res = await deleteAdvisorNote(userId, noteId)
  if (res?.success) {
    await fetchNotes()
  } else {
    alert('Failed to delete note.')
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleString()
}
</script>
