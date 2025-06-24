import axios from 'axios'

const API_BASE = 'http://localhost:5000/api/students'

/**
 * Get all students (Admin usage)
 */
export async function getAllStudents() {
  const res = await axios.get(`${API_BASE}`)
  return res.data
}

/**
 * Get a student by ID
 */
export async function getStudentById(id) {
  const res = await axios.get(`${API_BASE}/${id}`)
  return res.data
}

/**
 * Create a new student (Admin usage)
 */
export async function createStudent(student) {
  const res = await axios.post(`${API_BASE}`, student)
  return res.data
}

/**
 * Update an existing student
 */
export async function updateStudent(id, student) {
  const res = await axios.put(`${API_BASE}/${id}`, student)
  return res.data
}

/**
 * Delete a student
 */
export async function deleteStudent(id) {
  const res = await axios.delete(`${API_BASE}/${id}`)
  return res.data
}

/**
 * Get all courses (with sections) that a student is enrolled in
 */
export async function getCoursesByStudentId(studentId) {
  const res = await axios.get(`${API_BASE}/${studentId}/courses`)
  return res.data
}


export async function getStudentIdByUserId(userId) {
  const res = await axios.get(`${API_BASE}/by-user/${userId}`)
  return res.data
}

export async function getStudentAssessmentMarks(studentId, sectionId) {
    const res = await axios.get(`${API_BASE}/${studentId}/sections/${sectionId}/marks`)
    return res.data
  }

  export async function getTotalPercentageByStudentIdAndSectionId(studentId, sectionId) {
    try {
      const res = await axios.get(`${API_BASE}/${studentId}/total-percentage/section/${sectionId}`)
      return res.data.total_percentage
    } catch (err) {
      console.error('Error fetching total percentage:', err)
      return 0
    }
  }

  export async function getStudentsAssessmentMarksBySection(sectionId) {
    const res = await axios.get(`${API_BASE}/assessment-marks/section/${sectionId}`)
    return res.data
  }

  export async function getTotalPercentageBySectionId(section_id) {
    const res = await axios.get(`${API_BASE}/section/${section_id}/percentages`)
    return res.data
  }

  export async function getStudentNotifications(userId, unreadOnly = false, limit = 50) {
    const url = `${API_BASE}/${userId}/notifications?unread_only=${unreadOnly}&limit=${limit}`
    const response = await axios.get(url)
    return response.data
  }
  
  export async function markNotificationAsRead(userId, notificationId) {
    const url = `${API_BASE}/${userId}/notifications/${notificationId}/read`
    const response = await axios.put(url)
    return response.data
  }
  
  export async function markAllNotificationsAsRead(userId) {
    const url = `${API_BASE}/${userId}/notifications/mark-all`
    const response = await axios.put(url)
    return response.data
  }
  
  export async function getUnreadNotificationCount(userId) {
    const url = `${API_BASE}/${userId}/notifications/unread-count`
    const response = await axios.get(url)
    return response.data.count
  }
  
  export async function deleteNotification(userId, notificationId) {
    const url = `${API_BASE}/${userId}/notifications/${notificationId}`
    const response = await axios.delete(url)
    return response.data
  }

export async function submitRemarkRequest(payload) {
  const res = await axios.post(`${API_BASE}/remarks`, payload)
  return res.data
}

