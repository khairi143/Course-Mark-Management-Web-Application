<template>
  <div class="register-container">
    <h2>Register</h2>
    <form @submit.prevent="register">
      <!-- Common Fields -->
      <div class="form-row">
        <label for="username">Username:</label>
        <input v-model="form.username" type="text" id="username" required />
      </div>

      <div class="form-row">
        <label for="password">Password:</label>
        <input v-model="form.password" type="password" id="password" required />
      </div>

      <div class="form-row">
        <label for="confirmPassword">Confirm Password:</label>
        <input v-model="form.confirmPassword" type="password" id="confirmPassword" required />
      </div>

      <div class="form-row">
        <label for="role">Role:</label>
        <select v-model="form.role" id="role" required>
          <option value="student">Student</option>
          <option value="lecturer">Lecturer</option>
          <option value="advisor">Advisor</option>
        </select>
      </div>

        <!-- Conditional Fields: Student -->
        <div v-if="form.role === 'student'">
            <div class="form-row">
                <label for="fullName">Full Name:</label>
                <input v-model="form.fullName" type="text" id="fullName" required />
            </div>

            <div class="form-row">
                <label for="matricNo">Matric No:</label>
                <input v-model="form.matricNo" type="text" id="matricNo" required />
            </div>

            <div class="form-row">
                <label for="email">Email:</label>
                <input v-model="form.email" type="email" id="email" required />
            </div>

            <!-- <div class="form-row">
                <label for="yearOfStudy">Year of Study:</label>
                <input v-model="form.yearOfStudy" type="text" id="yearOfStudy" required />
            </div>

            <div class="form-row">
                <label for="programme">Programme:</label>
                <input v-model="form.programme" type="text" id="programme" required />
            </div> -->
        </div>


      <!-- Conditional Fields: Lecturer -->
      <div v-if="form.role === 'lecturer'">
        <div class="form-row">
            <label for="lecturerName">Full Name:</label>
            <input v-model="form.lecturerName" type="text" id="lecturerName" required />
        </div>

        <div class="form-row">
            <label for="lecturerStaffId">Staff ID:</label>
            <input v-model="form.lecturerStaffId" type="text" id="lecturerStaffId" required />
        </div>

        <div class="form-row">
            <label for="lecturerEmail">Email:</label>
            <input v-model="form.lecturerEmail" type="email" id="lecturerEmail" required />
        </div>

        <div class="form-row">
            <label for="lecturerDepartment">Department:</label>
            <input v-model="form.lecturerDepartment" type="text" id="lecturerDepartment" required />
        </div>
      </div>

      <!-- Conditional Fields: Advisor -->
      <div v-if="form.role === 'advisor'">
        <div class="form-row">
            <label for="advisorName">Full Name:</label>
            <input v-model="form.advisorName" type="text" id="advisorName" required />
        </div>

        <div class="form-row">
            <label for="advisorStaffId">Staff ID:</label>
            <input v-model="form.advisorStaffId" type="text" id="advisorStaffId" required />
        </div>

        <div class="form-row">
            <label for="advisorEmail">Email:</label>
            <input v-model="form.advisorEmail" type="email" id="advisorEmail" required />
        </div>

        <div class="form-row">
            <label for="advisorDepartment">Department:</label>
            <input v-model="form.advisorDepartment" type="text" id="advisorDepartment" required />
        </div>

        <div class="form-row">
            <label for="adviseeQuota">Advisee Quota:</label>
            <input v-model="form.adviseeQuota" type="number" id="adviseeQuota" required />
        </div>
      </div>

      <button type="submit">Register</button>

      <p class="success" v-if="successMessage">{{ successMessage }}</p>
      <p class="error" v-if="errorMessage">{{ errorMessage }}</p>

      <p class="login-link">
        Already have an account?
        <router-link to="/login">Login here</router-link>
      </p>
    </form>

  </div>
</template>

<script>
export default {
  name: 'MyRegister',
  data() {
    return {

      successMessage: '',
      errorMessage: '',
      form: {
        username: '',
        password: '',
        confirmPassword: '',
        role: 'student',

        //Student fields
        name: '',
        matricNo: '',
        email: '',
        // yearOfStudy: '',
        // programme: '',

        //Lecturer fields
        lecturerName: '',
        lecturerStaffId: '',
        lecturerEmail: '',
        lecturerDepartment: '',

        // Advisor fields
        advisorName: '',
        advisorStaffId: '',
        advisorEmail: '',
        advisorDepartment: '',
        adviseeQuota:''
      }
    };
  },
  methods: {
    async register() {
      // Basic client-side validation
      if (this.form.password !== this.form.confirmPassword) {
        this.errorMessage = "Passwords do not match.";
        return;
      }

      const body = {
        username: this.form.username,
        password: this.form.password,
        role: this.form.role
      };

      // Add role-specific fields
      if (this.form.role === 'student') {
        body.name = this.form.fullName;
        body.matric_number = this.form.matricNo;
        body.email = this.form.email;
        // body.year_of_study = this.form.yearOfStudy;
        // body.programme = this.form.programme;
      } else if (this.form.role === 'lecturer') {
        body.full_name = this.form.lecturerName;
        body.email = this.form.lecturerEmail;
        body.staff_id = this.form.lecturerStaffId;
        body.department = this.form.lecturerDepartment;
      } else if (this.form.role === 'advisor') {
        body.full_name = this.form.advisorName;
        body.email = this.form.advisorEmail;
        body.staff_id = this.form.advisorStaffId;
        body.department = this.form.advisorDepartment;
        body.advisee_quota = this.form.adviseeQuota;
      }

      try {
        const res = await fetch('http://localhost:8000/api/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body)
        });

        const data = await res.json();

        if (res.ok) {
          this.successMessage = 'Registration successful!';
          this.errorMessage = '';
          this.form = {
            username: '',
            password: '',
            confirmPassword: '',
            role: 'student',
            
            //Student fields
            fullName: '',
            matricNo: '',
            email: '',
            yearOfStudy: '',
            programme: '',

            //Lecturer fields
            lecturerName: '',
            lecturerStaffId: '',
            lecturerEmail: '',
            lecturerDepartment: '',

            // Advisor fields
            advisorName: '',
            advisorStaffId: '',
            advisorEmail: '',
            advisorDepartment: '',
            adviseeQuota:''
          };
        } else {
          this.errorMessage = data.error || 'Registration failed.';
        }
      } catch (err) {
        console.error('Registration error:', err);
        this.errorMessage = 'Network error. Please try again.';

      }
    }
  }
};
</script>

<style scoped>

.register-container {
  max-width: 400px;
  margin: auto;
  padding: 20px;
}
.error {
  color: red;
  margin-top: 10px;
}
.success {
  color: green;
  margin-top: 10px;
}
.login-link {
  margin-top: 10px;
  text-align: center;
}
.login-link a {
  color: #007bff;
  text-decoration: none;
}
.login-link a:hover {
  text-decoration: underline;
}
form > div:not(:has(div)) {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

form label {
  width: 150px; /* Fixed label width */
  margin-right: 10px;
  font-weight: bold;
  text-align: right;
}

form input,
form select {
  flex: 1; /* Input takes up remaining space */
  padding: 6px 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.form-row {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.form-row label {
  width: 150px;
  margin-right: 10px;
  font-weight: bold;
  text-align: right;
}

.form-row input,
.form-row select {
  flex: 1;
  padding: 6px 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

</style>
