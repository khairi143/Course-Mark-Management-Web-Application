<template>
  <div>
    <h3 class="mb-4">Assign Lecturers to Courses</h3>

    <button class="btn btn-primary mb-3" @click="openAddModal">
      + Add New Course
    </button>

    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Course Code</th>
          <th>Course Name</th>
          <th>Department</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(course, index) in courses" :key="course.id">
          <tr @click="toggleExpand(course.id)" style="cursor:pointer;">
            <td>{{ index + 1 }}</td>
            <td>{{ course.course_code }}</td>
            <td>{{ course.course_name }}</td>
            <td>{{ course.department }}</td>
            <td>
              <button class="btn btn-sm btn-primary" @click.stop="openEditCourseModal(course)">Edit</button>
              <button class="btn btn-sm btn-danger ms-2" @click.stop="confirmDelete(course.id)">Delete</button>
            </td>
          </tr>
          <tr v-if="expandedCourses.includes(course.id)">
            <td colspan="4" style="background:#f9f9f9;">
              <strong>Sections:</strong>
              <table class="table table-sm mt-2">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Section Name</th>
                    <th>Max Capacity</th>
                    <th>Lecturer</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(section, sidx) in sectionsByCourse[course.id] || []" :key="section.id">
                    <td>{{ sidx + 1 }}</td>
                    <td>{{ section.section_name }}</td>
                    <td>{{ section.max_capacity }}</td>
                    <td>{{ getLecturerName(section.lecturer_id) }}</td>
                    <td>
                      <button class="btn btn-sm btn-warning" @click.stop="openAssignSectionModal(section, course)">Assign</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </template>
      </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" ref="assignModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <form @submit.prevent="submitAssignment">
            <div class="modal-header">
              <h5 class="modal-title">Assign Lecturer</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Course</label>
                <input v-model="form.course_name" class="form-control" readonly />
              </div>
              <div class="mb-3">
                <label class="form-label">Lecturer</label>
                <select v-model="form.lecturer_id" class="form-select" required>
                  <option v-for="lecturer in filteredLecturers" :key="lecturer.id" :value="lecturer.id">
                    {{ lecturer.full_name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-success" type="submit">Save</button>
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true" ref="courseModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <form @submit.prevent="submitCourse">
            <div class="modal-header">
              <h5 class="modal-title">Add New Course</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Course Code</label>
                <input v-model="form.course_code" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Course Name</label>
                <input v-model="form.course_name" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Department</label>
                <input v-model="form.department" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Sections</label>
                <div v-for="(section, idx) in form.sections" :key="idx" class="input-group mb-2">
                  <input v-model="section.section_name" class="form-control" placeholder="Section Name" required />
                  <input v-model.number="section.max_capacity" type="number" min="1" class="form-control" placeholder="Max Capacity" required style="max-width: 140px;" />
                  <button class="btn btn-danger" type="button" @click="removeSection(idx)">Remove</button>
                </div>
                <button class="btn btn-secondary btn-sm" type="button" @click="addSection">+ Add Section</button>
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

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true" ref="editCourseModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <form @submit.prevent="submitEditCourse">
            <div class="modal-header">
              <h5 class="modal-title">Edit Course</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Course Code</label>
                <input v-model="editCourseForm.course_code" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Course Name</label>
                <input v-model="editCourseForm.course_name" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Department</label>
                <input v-model="editCourseForm.department" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Sections</label>
                <div v-for="(section, idx) in editCourseForm.sections" :key="section.id || idx" class="input-group mb-2">
                  <input v-model="section.section_name" class="form-control" placeholder="Section Name" required />
                  <input v-model.number="section.max_capacity" type="number" min="1" class="form-control" placeholder="Max Capacity" required style="max-width: 140px;" />
                  <button class="btn btn-danger" type="button" @click="removeEditSection(idx)">Remove</button>
                </div>
                <button class="btn btn-secondary btn-sm" type="button" @click="addEditSection">+ Add Section</button>
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
  </div>
</template>


<script>
import axios from 'axios'
import { Modal } from 'bootstrap'
import { deleteCourse, createCourse, getSectionsByCourse, updateSectionLecturer, updateCourse } from '../services/admin.js'


export default {
  data() {
    return {
      courses: [],
      lecturers: [],
      expandedCourses: [],
      sectionsByCourse: {},
      assignModal: null,
      form: {
        id: null,
        course_name: '',
        lecturer_id: '',
        course_code: '',
        department: '',
        sections: []
      },
      editCourseForm: {
        id: null,
        course_code: '',
        course_name: '',
        department: '',
        sections: []
      },
      editCourseModal: null,
      courseModal: null
    }
  },
  computed: {
    filteredLecturers() {
      console.log('Filtering for department:', this.form.department, this.lecturers);
      return this.lecturers.filter(l => l.department === this.form.department)
    }
  },
  mounted() {
    this.assignModal = new Modal(this.$refs.assignModal)
    this.courseModal = new Modal(this.$refs.courseModal)
    this.editCourseModal = new Modal(this.$refs.editCourseModal)
    this.fetchData()
  },
  methods: {
    async confirmDelete(courseId) {
  if (confirm('Are you sure you want to delete this course?')) {
    try {
      const result = await deleteCourse(courseId)
      if (result.success) {
        alert('Course deleted successfully.')
        this.fetchData()
      } else {
        alert('Failed to delete course.')
      }
    } catch (err) {
      alert('Delete failed: ' + (err.response?.data?.error || err.message))
    }
  }
}
,
    async fetchData() {
      try {
        const [coursesRes, lecturersRes] = await Promise.all([
          axios.get('http://localhost:5000/api/courses'),
          axios.get('http://localhost:5000/api/lecturers')
        ])
        const coursesMap = {}
        for (const row of coursesRes.data) {
          if (!coursesMap[row.id]) {
            coursesMap[row.id] = {
              id: row.id,
              course_code: row.course_code,
              course_name: row.course_name,
              department: row.department,
              sections: []
            }
          }
          if (row.section_id) {
            coursesMap[row.id].sections.push({
              id: row.section_id,
              section_name: row.section_name,
              max_capacity: row.max_capacity,
              lecturer_id: row.section_lecturer_id
            })
          }
        }
        this.courses = Object.values(coursesMap)
        this.lecturers = lecturersRes.data
      } catch (err) {
        console.error('Fetch error:', err)
      }
    },
    getLecturerName(lecturerId) {
      const lecturer = this.lecturers.find(l => l.id === lecturerId)
      return lecturer ? lecturer.full_name : 'N/A'
    },
    toggleExpand(courseId) {
      const idx = this.expandedCourses.indexOf(courseId)
      if (idx === -1) {
        this.expandedCourses.push(courseId)
        if (!this.sectionsByCourse[courseId]) {
          getSectionsByCourse(courseId).then(sections => {
            this.sectionsByCourse[courseId] = sections
          })
        }
      } else {
        this.expandedCourses.splice(idx, 1)
      }
    },
    openAssignSectionModal(section, course) {
    this.form = {
      id: section.id,
      course_id: course.id, // ✅ Add this
      course_name: course.course_name,
      lecturer_id: section.lecturer_id || '',
      department: course.department
    }
    this.assignModal.show()
  }
  ,
  async submitAssignment() {
  try {
    await updateSectionLecturer(this.form.id, this.form.lecturer_id)
    this.assignModal.hide()
    await this.fetchData()
    this.sectionsByCourse[this.form.course_id] = await getSectionsByCourse(this.form.course_id) // ✅ Reload updated section
  } catch (err) {
    console.error('Assignment update failed:', err)
  }
},


    openAddModal() {
      this.form = { course_code: '', course_name: '', department: '', sections: [] }
      this.courseModal.show()
    },
    addSection() {
      this.form.sections.push({ section_name: '', max_capacity: 1 })
    },
    removeSection(idx) {
      this.form.sections.splice(idx, 1)
    },
    async submitCourse() {
      try {
        const result = await createCourse(this.form)
        if (result && result.message) {
          this.courseModal.hide()
          this.fetchData()
        } else {
          alert(result.error || 'Failed to create course')
        }
      } catch (err) {
        alert('Failed to create course: ' + (err.response?.data?.error || err.message))
      }
    },
    openEditCourseModal(course) {
      this.editCourseForm = {
        id: course.id,
        course_code: course.course_code,
        course_name: course.course_name,
        department: course.department,
        sections: (this.sectionsByCourse[course.id] || []).map(s => ({
          id: s.id,
          section_name: s.section_name,
          max_capacity: s.max_capacity
        }))
      }
      this.editCourseModal.show()
    },
    addEditSection() {
      this.editCourseForm.sections.push({ section_name: '', max_capacity: 1 })
    },
    removeEditSection(idx) {
      this.editCourseForm.sections.splice(idx, 1)
    },
    async submitEditCourse() {
      try {
        const courseId = this.editCourseForm.id
        await updateCourse(this.editCourseForm)
        this.editCourseModal.hide()

        await this.fetchData()

        // Expand the updated course row again
        this.expandedCourses = [courseId]
        this.sectionsByCourse[courseId] = await getSectionsByCourse(courseId)
      } catch (err) {
        alert('Failed to update course: ' + (err.response?.data?.error || err.message))
      }
    }

  }
}
</script>
