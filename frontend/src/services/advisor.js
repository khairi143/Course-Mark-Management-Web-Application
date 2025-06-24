import axios from 'axios'
const API_BASE = 'http://localhost:5000/api/advisor'

export async function getAdvisees(advisorId) {
  const response = await axios.get(`${API_BASE}/${advisorId}/students`)
  return response.data
}

export async function getAdviseesMarks(userId) {
    const res = await axios.get(`${API_BASE}/${userId}/advisees-marks`)
    return res.data
  }

  export async function getAdvisorNotes(userId, studentId) {
    const res = await axios.get(`${API_BASE}/notes/${userId}/${studentId}`)
    return res.data
  }
  
  
  export async function addAdvisorNote(userId, noteData) {
    const res = await axios.post(`${API_BASE}/notes/${userId}`, noteData)
    return res.data
  }

  // Update an advisor note
export async function updateAdvisorNote(userId, noteId, noteData) {
    const res = await axios.put(`${API_BASE}/notes/${userId}/${noteId}`, noteData)
    return res.data
  }
  
  // Delete an advisor note
  export async function deleteAdvisorNote(userId, noteId) {
    const res = await axios.delete(`${API_BASE}/notes/${userId}/${noteId}`)
    return res.data
  }

  export async function exportAdvisorReportCsv(userId, studentId) {
    const res = await axios.get(
      `${API_BASE}/${userId}/students/${studentId}/export`,
      { responseType: 'blob' }
    )
    return res
  }
  
  
