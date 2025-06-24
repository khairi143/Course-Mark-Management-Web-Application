import axios from 'axios'

export async function getCoursesWithSections(lecturerId) {
  const res = await axios.get(`http://localhost:5000/api/lecturers/${lecturerId}/courses-with-sections`)
  return res.data
}

export async function getLecturerIdByUserId(userId) {
  const res = await axios.get(`http://localhost:5000/api/lecturers/by-user/${userId}`)
  return res.data.lecturer_id
}

export async function getAllStudents() {
  const res = await axios.get('http://localhost:5000/api/lecturers/students')
  return res.data
}

export async function getStudentById(id) {
  const res = await axios.get(`http://localhost:5000/api/lecturers/students/${id}`)
  return res.data
}

export async function createStudent(student) {
  const res = await axios.post('http://localhost:5000/api/lecturers/students', student)
  return res.data
}

export async function updateStudent(id, student) {
  const res = await axios.put(`http://localhost:5000/api/lecturers/students/${id}`, student)
  return res.data
}

export async function deleteStudent(id) {
  const res = await axios.delete(`http://localhost:5000/api/lecturers/students/${id}`)
  return res.data
}

export async function getAllEnrollments() {
  const res = await axios.get('http://localhost:5000/api/lecturers/enrollments')
  return res.data
}

export async function getEnrollmentById(id) {
  const res = await axios.get(`http://localhost:5000/api/lecturers/enrollments/${id}`)
  return res.data
}

export async function createEnrollment(enrollment) {
  // enrollment: { student_id, section_id }
  const res = await axios.post('http://localhost:5000/api/lecturers/enrollments', enrollment)
  return res.data
}

export async function updateEnrollment(id, enrollment) {
  const res = await axios.put(`http://localhost:5000/api/lecturers/enrollments/${id}`, enrollment)
  return res.data
}

export async function deleteEnrollment(id) {
  const res = await axios.delete(`http://localhost:5000/api/lecturers/enrollments/${id}`)
  return res.data
}

export async function getStudentsBySectionId(sectionId) {
  const res = await axios.get(`http://localhost:5000/api/lecturers/sections/${sectionId}/students`)
  return res.data
}

export async function removeEnrollmentByStudentAndSection(studentId, sectionId) {
  const res = await axios.delete('http://localhost:5000/api/lecturers/enrollments/by-student-section', {
    data: { student_id: studentId, section_id: sectionId }
  })
  return res.data
}

// Assessment management
export async function getAllAssessments() {
  const res = await axios.get('http://localhost:5000/api/lecturers/assessments')
  return res.data
}

export async function getAssessmentById(id) {
  const res = await axios.get(`http://localhost:5000/api/lecturers/assessments/${id}`)
  return res.data
}

export async function getAssessmentsBySectionId(sectionId) {
  const res = await axios.get(`http://localhost:5000/api/lecturers/sections/${sectionId}/assessments`)
  return res.data
}

export async function createAssessment(assessment) {
  // assessment: { section_id, assessment_name, max_marks, weight_percentage }
  const res = await axios.post('http://localhost:5000/api/lecturers/assessments', assessment)
  return res.data
}

export async function updateAssessment(id, assessment) {
  const res = await axios.put(`http://localhost:5000/api/lecturers/assessments/${id}`, assessment)
  return res.data
}

export async function deleteAssessment(id) {
  const res = await axios.delete(`http://localhost:5000/api/lecturers/assessments/${id}`)
  return res.data
}

// Save or update assessment marks for a student in a section
export async function saveStudentAssessmentMarks(sectionId, studentId, marksArray) {
  // marksArray: [{ assessment_id, marks_obtained }]
  // This will insert or update each mark as needed
  const res = await axios.post(`http://localhost:5000/api/lecturers/sections/${sectionId}/students/${studentId}/assessment-marks`, marksArray)
  return res.data
}

// Get assessment marks for a student in a section
export async function getStudentAssessmentMarks(sectionId, studentId) {
  const res = await axios.get(`http://localhost:5000/api/lecturers/sections/${sectionId}/students/${studentId}/assessment-marks`)
  return res.data
}

// Save or update total_percentage for a student in a section
export async function saveOrUpdateEnrollmentTotalPercentage(sectionId, studentId, totalPercentage) {
  const res = await axios.post(`http://localhost:5000/api/lecturers/sections/${sectionId}/students/${studentId}/enrollment-total-percentage`, { total_percentage: totalPercentage })
  return res.data
}

// Get joined assessment and student assessment marks (optionally filter by studentId, assessmentId)
export async function getAssessmentMarksJoin({ studentId = null, assessmentId = null } = {}) {
  let url = 'http://localhost:5000/api/lecturers/assessment-marks-join';
  const params = [];
  if (studentId !== null) params.push(`student_id=${studentId}`);
  if (assessmentId !== null) params.push(`assessment_id=${assessmentId}`);
  if (params.length) url += '?' + params.join('&');
  const res = await axios.get(url);
  return res.data;
}

// Get total percentage for a student in a section
export async function getTotalPercentageByStudentIdAndSectionId(studentId, sectionId) {
  const res = await axios.get('http://localhost:5000/api/lecturers/total-percentage', {
    params: { student_id: studentId, section_id: sectionId }
  })
  return res.data.total_percentage
}

export async function getRemarkRequestsBySectionId(sectionId) {
  const response = await axios.get(`http://localhost:5000/api/lecturers/remarks/${sectionId}`)
  return response.data
}
