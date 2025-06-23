// router/index.js
import { createRouter, createWebHashHistory } from 'vue-router';

// Universal Views
import MyDashboard from '../views/MyDashboard.vue';
import MyRegister from '../views/MyRegister.vue';
import MyLogin from '../views/MyLogin.vue';
// import DashboardLecturer from '../views/DashboardLecturer.vue';
// import DashboardStudent from '../views/DashboardStudent.vue';
// import DashboardAdvisor from '../views/DashboardAdvisor.vue';

// // Lecturer Views
import ManageStudents from '../views/Lecturer/ManageStudents.vue';
import AssessmentLecturerEntry from '../views/Lecturer/AssessmentLecturer.vue';
import EditEnrollment from '../views/Lecturer/EditEnrollment.vue';
import AssessmentEdit from '../views/Lecturer/EditComponent.vue';
import ManageComponent from '../views/Lecturer/ManageComponent.vue';
// import FinalExamEntry from '../views/FinalExamEntry.vue';
// import Analytics from '../views/Analytics.vue';
// import ExportCSV from '../views/ExportCSV.vue';
// import Notification from '../views/Notification.vue';

// // Student Views
import StudentAssessment from '../views/Student/StudentAssessment.vue';
import PerformanceExpectation from '../views/Student/PerformanceExpectation.vue'; // Alias for student ranking
// import ProgressViewer from '../views/ProgressViewer.vue';
// import MarkComparison from '../views/MarkComparison.vue';
import StudentRanking from '../views/Student/StudentRanking.vue';
// import WhatIfSimulator from '../views/WhatIfSimulator.vue';
// import RemarkRequest from '../views/RemarkRequest.vue';

// // Advisor Views
import StudentAdvisorList from '../views/Advisor/StudentAdvisorList.vue';
import MeetingRecord from '../views/Advisor/MeetingRecord.vue';
// import AdviseeDetails from '../views/AdviseeDetails.vue';
// import AtRiskHighlights from '../views/AtRiskHighlights.vue';
// import AdvisorNotes from '../views/AdvisorNotes.vue';
// import ExportConsultations from '../views/ExportConsultations.vue';

// // Admin 
import ManageUsers from '../views/Admin/ManageUsers.vue';
// import ManageCourses from '../views/Admin/ManageCourses.vue';
// import AdminPanel from '../views/AdminPanel.vue';

// import NotFound from '../views/NotFound.vue';

const routes = [
  { path: '/', component: MyLogin },

  { path: '/register', component: MyRegister },

  {
    path: '/login',
    name: 'Login',
    component: MyLogin,
  },


  // Dashboards
  { path: '/dashboard', component: MyDashboard },
  // { path: '/lecturer', component: DashboardLecturer },
  // { path: '/lecturer', component: DashboardLecturer },
  // { path: '/student', component: DashboardStudent },
  // { path: '/advisor', component: DashboardAdvisor },

  // // Lecturer Routes
  { path: '/lecturer/manage-students', component: ManageStudents },
  { path: '/lecturer/assessments', component: AssessmentLecturerEntry },
  {
    path: '/edit-enrollment/:enrollment_id', name: 'editEnrollment', component: EditEnrollment, props: true  // Pass the enrollment_id as a prop to the EditEnrollment component
  },
  { path: '/lecturer/edit-assessment/:component_id', component: AssessmentEdit, props: true },
  { path: '/lecturer/manage-component/:component_id', component: ManageComponent },
  // { path: '/lecturer/final-exam', component: FinalExamEntry },
  // { path: '/lecturer/analytics', component: Analytics },
  // { path: '/lecturer/export', component: ExportCSV },
  // { path: '/lecturer/notify', component: Notification },

  // // Student Routes
  { path: '/student/assessment', component: StudentAssessment },
  // { path: '/student/progress', component: ProgressViewer },
  { path: '/student/studentranking', component: StudentRanking },
  { path: '/student/performance-expectation', component: PerformanceExpectation }, // Alias for student ranking
  // { path: '/student/what-if', component: WhatIfSimulator },
  // { path: '/student/remark', component: RemarkRequest },

  // // Advisor Routes
  { path: '/advisor/student-advisor-list', component: StudentAdvisorList },
  { path: '/advisor/meeting-records', component: MeetingRecord },
  // { path: '/advisor/details/:id', component: AdviseeDetails, props: true },
  // { path: '/advisor/risk', component: AtRiskHighlights },
  // { path: '/advisor/notes/:id', component: AdvisorNotes, props: true },
  // { path: '/advisor/exports', component: ExportConsultations },

  // // Admin 
  { path: '/admin/manage-users', component: ManageUsers },
  // { path: '/admin/manage-courses', component: ManageCourses },
  // { path: '/admin', component: AdminPanel },

  // // 404
  // { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound }
];

const router = createRouter({
  history: createWebHashHistory(),
  routes
});

export default router;
