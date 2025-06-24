# course-mark-management

## Project setup
```
npm install
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
```

### Lints and fixes files
```
npm run lint
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).



1. Overview
The project aims to develop a Course Mark Management Web Application with multiple user roles, specifically designed for educational institutions to streamline the management and monitoring of student marks. The application includes features for Lecturers, Students, Academic Advisors, and Admins. It is expected to be developed using the Vue.js frontend framework, PHP Slim Framework for the backend, and MySQL for data storage.

2. Technology Stack

    Frontend:

        Vue.js (via Vue CLI or CDN): A modern JavaScript framework for building user interfaces.

    Backend:

        PHP Slim Framework: A PHP micro-framework that is lightweight, ideal for small to medium web applications. It supports routing, middleware, and controller setup.

    Database:

        MySQL: A relational database management system used for persistent data storage.

    Communication:

        RESTful API with JSON: Used for communication between frontend and backend. Axios or fetch() will be used for making API calls.

    Charts:

        Chart.js or ApexCharts: Used for visual analytics and displaying data in graphical formats.

3. Project Objective
The core objective of this project is to develop a feature-rich web application that facilitates the following functionalities:

    Lecturers: Efficient management of student records and grades.

    Students: Transparent access to marks and performance tracking.

    Academic Advisors: Monitoring student progress and offering support when needed.

4. User Roles and Functional Modules

    Lecturer:

        Secure Login: Ensures that only authorized lecturers can access their account.

        Manage Student Records: Ability to add, edit, or delete student information.

        Create/Edit Continuous Assessment Components: Manage assessments like quizzes, assignments, and labs, each with a set weight and maximum mark.

        Final Exam Marks Entry: Entry for the final exam (30% weight).

        Auto-calculation of Total: The system automatically calculates total marks by combining the continuous assessment (70%) and final exam (30%).

        Visual Analytics: Display of student progress and performance overview through charts and graphs.

        Export Results: Ability to export the full results as CSV for reporting purposes.

        Notification: Sending notifications to students about mark updates.

    Student Dashboard:

        Login: Students log in with a matric number and secure PIN.

        View Component-wise Marks: Students can view individual marks for each assessment and their overall score.

        Progress and Assessment Breakdown: Visualization of progress with a progress bar and breakdown of marks.

        Compare Marks: Students can anonymously compare their marks with their peers using tables or bar charts.

        Class Rank and Percentile: Display of the student's rank within the class and their percentile.

        What-if Tool: A simulation tool that lets students adjust future grades to predict their final result.

        Remark Requests: Students can request a remark with a justification for their scores.

        Academic Advisor Workspace:

        Secure Login: Advisors can log in securely.

        View Advisee Records: Advisors can view all records for students they are supervising.

        Highlight At-Risk Students: Advisors can highlight students at risk, such as those with a GPA below 2.0 or in the bottom 20% of the class.

        Add Meeting Notes: Advisors can add private notes or records for meetings with their advisees.

        Export Reports: Advisors can export consultation reports for students.

Admin Panel:

    Manage User Accounts: Admin can manage and assign users (lecturers, students, advisors).

    System Logs: View logs of system activities, such as mark updates.

    Reset User Passwords: Admin can reset passwords for any user in the system.

5. Technical Requirements

    Frontend:

        Use of Vue.js for building dynamic and reactive user interfaces. Frontend can be built either via CDN for simplicity or Vue CLI for modular projects.

    Backend:

        The backend will use PHP Slim Framework to handle routing, controllers, and middleware efficiently.

        RESTful API using JSON will allow smooth communication between the frontend and backend.

    Database:

        MySQL will be used to store user data and course marks securely.

    Charts:

        The application will use Chart.js or ApexCharts to display data in visual formats like bar charts, pie charts, and line graphs.

    Responsive UI:

        The application will be responsive, ensuring it works on both mobile and desktop platforms.