import { createRouter, createWebHistory } from 'vue-router';

// Import views (pages)
import LecturerDashboard from '../views/LecturerDashboard.vue';
import StudentDashboard from '../views/Student/StudentDashboard.vue';
import ViewMark from '../views/Student/ViewMark.vue';
import AdvisorDashboard from '../views/AdvisorDashboard.vue';
import LoginForm from '../views/LoginForm.vue';
import RegisterForm from '../views/RegisterForm.vue';

const routes = [
  {
    path: '/',
    name: 'login',
    component: LoginForm
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterForm
  },
  {
    path: '/lecturer',
    name: 'lecturer-dashboard',
    component: LecturerDashboard,
    meta: { requiresAuth: true, role: 'lecturer' }
  },
  {
    path: '/student',
    name: 'student-dashboard',
    component: StudentDashboard,
    meta: { requiresAuth: true, role: 'student' }
  },
  {
    path: '/student/viewmark',
    name: 'view-mark',
    component: ViewMark,
    meta: { requiresAuth: true, role: 'student' }
  },
  {
    path: '/advisor',
    name: 'advisor-dashboard',
    component: AdvisorDashboard,
    meta: { requiresAuth: true, role: 'advisor' }
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

// Navigation Guard to handle RBAC and redirect based on user role
router.beforeEach((to, from, next) => {
  const loggedInRole = localStorage.getItem('role');  // Assuming role is stored in localStorage after login

  if (to.meta.requiresAuth) {
    // Check if user is logged in and the role matches
    if (!loggedInRole) {
      next({ name: 'login' });  // Redirect to login if not logged in
    } else if (to.meta.role !== loggedInRole) {
      // If the user tries to access a route that does not match their role
      if (loggedInRole === 'lecturer') {
        next({ name: 'lecturer-dashboard' });
      } else if (loggedInRole === 'student') {
        next({ name: 'student-dashboard' });
      } else if (loggedInRole === 'advisor') {
        next({ name: 'advisor-dashboard' });
      }
    } else {
      next();  // Proceed if role matches
    }
  } else {
    next();  // Proceed if the route doesn't require authentication
  }
});

export default router;
