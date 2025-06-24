import { createRouter, createWebHistory } from 'vue-router'
import AdminDashboard from '../views/AdminDashboard.vue'
import AdminManageUsers from '../views/AdminManageUsers.vue'
import AdminAssignLecturers from '../views/AdminAssignLecturer.vue'
import AdminSystemLogs from '../views/AdminSystemLogs.vue'
import AdminResetPassword from '../views/AdminResetPassword.vue'
import StudentStaffLogin from '../views/StudentStaffLogin.vue'
import LecturerDashboard from '../views/LecturerDashboard.vue'
import StudentDashboard from '../views/StudentDashboard.vue'
import AdvisorDashboard from '../views/AdvisorDashboard.vue'
// import HomeView from '../views/HomeView.vue'

const LecturerManageStudent = () => import('../views/LecturerManageStudent.vue')
const LecturerManageAssessment = () => import('../views/LecturerManageAssessment.vue')
const LecturerEnterAssessmentMarks = () => import('../views/LecturerEnterAssessmentMarks.vue')
const LecturerAnalytics = () => import('../views/LecturerAnalytics.vue')
const StudentViewMarks = () => import('../views/StudentViewMarks.vue')
const StudentAnalytics = () => import('../views/StudentAnalytics.vue')
const StudentCompareMarks = () => import('../views/StudentCompareMarks.vue')
const StudentViewRank = () => import('../views/StudentViewRank.vue')
const LecturerViewRemarks = () => import('../views/LecturerViewRemarks.vue')
const AdvisorViewStudents = () => import('../views/AdvisorViewStudents.vue')
const AdvisorViewStudentsMark = () => import('../views/AdvisorViewStudentsMark.vue')
const AdvisorNotes = () => import('../views/AdvisorNotes.vue')
const AdvisorExport = () => import('../views/AdvisorExport.vue')

const routes = [
  // {
  //   path: '/',
  //   name: 'home',
  //   component: HomeView
  // },
  // {
  //   path: '/about',
  //   name: 'about',
  //   // route level code-splitting
  //   // this generates a separate chunk (about.[hash].js) for this route
  //   // which is lazy-loaded when the route is visited.
  //   component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  // }

  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'StudentStaffLogin',
    component: StudentStaffLogin
  },
  {
    path: '/admin',
    component: AdminDashboard,
    children: [
      {
        path: 'manage-users',
        name: 'ManageUsers',
        component: AdminManageUsers
      },
      {
        path: 'assign-lecturers',
        name: 'AssignLecturers',
        component: AdminAssignLecturers
      },
      {
        path: 'system-logs',
        name: 'SystemLogs',
        component: AdminSystemLogs
      },
      {
        path: 'reset-password',
        name: 'ResetPassword',
        component: AdminResetPassword
      },
      // Add other admin subroutes here
    ]
  },
  {
    path: '/lecturer',
    name: 'LecturerDashboard',
    component: LecturerDashboard,
    children: [
      {
        path: 'manage-students',
        name: 'LecturerManageStudent',
        component: LecturerManageStudent
      },
      {
        path: 'manage-assessments',
        name: 'LecturerManageAssessment',
        component: LecturerManageAssessment
      },
      {
        path: 'enter-assessment-marks',
        name: 'LecturerEnterAssessmentMarks',
        component: LecturerEnterAssessmentMarks
      },
      {
        path: 'analytics',
        name: 'LecturerAnalytics',
        component: LecturerAnalytics
      },
      {
        path: 'view-remarks',
        name: 'LecturerViewRemarks',
        component: LecturerViewRemarks
      }
    ]
  },
  {
    path: '/student',
    name: 'StudentDashboard',
    component: StudentDashboard,
    children: [
      {
        path: 'view-marks',
        name: 'StudentViewMarks',
        component: StudentViewMarks
      },
      { 
        path: 'analytics', 
        component: StudentAnalytics 
      },
      { 
        path: 'compare', 
        component: StudentCompareMarks
      },
      { 
        path: 'view-rank', 
        component: StudentViewRank
      }
    ]
  },
  {
    path: '/advisor',
    name: 'AdvisorDashboard',
    component: AdvisorDashboard,
    children: [
      {
        path: 'view-students',
        name: 'AdvisorViewStudent',
        component: AdvisorViewStudents
      },
      {
        path: 'view-students-mark',
        name: 'AdvisorViewStudentMark',
        component: AdvisorViewStudentsMark
      },
      {
        path: 'notes',
        name: 'AdvisorNotes',
        component: AdvisorNotes
      },
      {
        path: 'export',
        name: 'AdvisorExport',
        component: AdvisorExport
      },
    ]
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
