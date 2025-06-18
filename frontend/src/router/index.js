import { createRouter, createWebHistory } from 'vue-router'
import AdminDashboard from '../views/AdminDashboard.vue'
import AdminManageUsers from '../views/AdminManageUsers.vue'
import AdminAssignLecturers from '../views/AdminAssignLecturer.vue'
import AdminSystemLogs from '../views/AdminSystemLogs.vue'
import AdminResetPassword from '../views/AdminResetPassword.vue'
// import HomeView from '../views/HomeView.vue'

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
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
