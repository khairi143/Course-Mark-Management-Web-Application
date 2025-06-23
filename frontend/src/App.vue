// App.vue
<template>
  <div id="app">
    <header>
      <h1>Course Work Management</h1>
      <div class="header-right">
        <select v-model="selectedRole" @change="changeRole">
          
        </select>
        <ProfileSidebar :user="currentUser" /> 
      </div>
    </header>

    <NavBar :menus="menus[selectedRole]" />

    <DashBoard></DashBoard>
    <router-view />
  </div>
</template>

<script>
import NavBar from './components/NavBar.vue'
import ProfileSidebar from './components/ProfileSidebar.vue'
// import DashBoard from './components/DashBoard.vue'

export default {
  components: { NavBar, ProfileSidebar},
  data() {
    return {
      selectedRole: 'personbmi',
      profiles: {
        admin: { name: "Admin Lisa", email: "admin@bmiapp.com" },
        personbmi: { name: "Azman Musa", email: "azman@bmiapp.com" },
        nutritionist: { name: "Dr. Nisa", email: "nisa@bmiapp.com" },
        fitness: { name: "Coach Rizal", email: "rizal@bmiapp.com" }
      },
      menus: {
        lecturer: [
          { name: 'Manage Student', route: '/manage-student' },
          { name: 'Continuous Assessment', route: '/continuous-assessment' }
        ],
        student: [
          { name: 'Assessment', route: '/assessment' },
          { name: 'Compare Mark with Coursemates', route: '/compare-marks' },
          { name: 'Personal Class Rank', route: '/class-rank' },
          { name: 'Student Performance Expectation', route: '/performance' }
        ],
        advisor: [
          { name: 'Student-Advisor List', route: '/student-advisor-list' },
          { name: 'Meeting Records', route: '/meeting-records' }
        ],
        admin: [
          { name: 'Manage Users', route: '/manage-users' },
          { name: 'Assign Lecturers to Courses', route: '/assign-lecturers' }
        ]
      }
    }
  },
  computed: {
    currentUser() {
      return { ...this.profiles[this.selectedRole], role: this.selectedRole }
    }
  },
  methods: {
    changeRole() {
      // Will re-render menus and profile
    }
  }
}
</script>

    <style>
        :root {
            --primary: #2f855a;
            --gray: #e2e8f0;
            --light-gray: #f7fafc;
            --text: #2d3748;
            --bg: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--light-gray);
            color: var(--text);
        }

        header {
            background: var(--bg);
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--gray);
        }

        header h1 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        select {
            padding: 6px;
            font-size: 14px;
        }

        .profile {
            cursor: pointer;
        }

        .profile img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid #ccc;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.3);
            display: none;
            z-index: 998;
        }

        .sidebar {
            position: fixed;
            background: var(--bg);
            width: 100%;
            max-width: 360px;
            height: 100vh;
            right: 0;
            top: 0;
            padding: 24px;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.08);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 999;
            display: flex;
            flex-direction: column;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar .close-btn {
            align-self: flex-end;
            font-size: 20px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .sidebar .avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .sidebar .name {
            font-weight: 600;
            font-size: 16px;
        }

        .sidebar .email {
            font-size: 13px;
            color: #718096;
            margin-bottom: 4px;
        }

        .sidebar .role {
            font-size: 13px;
            font-style: italic;
            color: #4a5568;
            margin-bottom: 24px;
        }

        .sidebar button {
            background: none;
            border: none;
            padding: 12px 0;
            text-align: left;
            font-size: 14px;
            cursor: pointer;
            color: var(--text);
            border-top: 1px solid #eee;
            transition: background 0.2s;
        }

        .sidebar button:hover {
            background-color: var(--light-gray);
        }

        .menu {
            padding: 30px;
        }

        .menu h2 {
            font-size: 16px;
            font-weight: 600;
        }

        .menu ul {
            list-style: none;
            padding: 0;
        }

        .menu li {
            background: var(--bg);
            padding: 10px 15px;
            border: 1px solid var(--gray);
            border-radius: 6px;
            margin-bottom: 8px;
        }

        nav ul li a:hover {
            background-color: #edf2f7;
        }


        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                max-width: none;
                height: 50vh;
                bottom: 0;
                top: auto;
                right: 0;
                left: 0;
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
                transform: translateY(100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateY(0);
            }
        }
    </style>