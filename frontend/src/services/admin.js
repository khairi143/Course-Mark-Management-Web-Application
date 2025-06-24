import axios from 'axios'

export async function createCourse(course) {
  // course should have: course_code, course_name, department, sections (array of {section_name, max_capacity})
  const res = await axios.post('http://localhost:5000/api/courses', course)
  return res.data
}

export async function updateCourse(course) {
  // course should have: id, course_code, course_name, department, sections
  const res = await axios.put(`http://localhost:5000/api/courses/${course.id}`, course)
  return res.data
}

export async function deleteCourse(id) {
  try {
    const response = await axios.delete(`http://localhost:5000/api/courses/${id}`);
    return response.data;
  } catch (error) {
    console.error('Failed to delete course:', error);
    throw error;
  }
}

// Update lecturer for a section
export async function updateSectionLecturer(sectionId, lecturerId) {
  const res = await axios.put(`http://localhost:5000/api/sections/${sectionId}/lecturer`, { lecturer_id: lecturerId })
  return res.data
}

// Get all sections for a course
export async function getSectionsByCourse(courseId) {
  const res = await axios.get(`http://localhost:5000/api/sections/by-course/${courseId}`)
  return res.data
} 


export async function fetchAdvisors() {
  try {
    const res = await axios.get('http://localhost:5000/api/advisors')
    return res.data
  } catch (err) {
    console.error('Failed to fetch advisors:', err)
    return []
  }
}
