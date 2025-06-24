-- Insert Roles
INSERT INTO roles (role_name) VALUES
('lecturer'),
('student'),
('advisor'),
('admin');

-- Insert Users (Lecturers, Students, Advisors)
-- Insert Admin User
INSERT INTO users (username, password, role) VALUES
('admin', 'admin_password', 'admin');

-- Insert Lecturer User
INSERT INTO users (username, password, role) VALUES
('lecturer1', 'lecturer_password', 'lecturer');

-- Insert Advisor User
INSERT INTO users (username, password, role) VALUES
('advisor1', 'advisor_password', 'advisor');

-- Insert Student Users
INSERT INTO users (username, password, role) VALUES
('student1', 'student_password', 'student'),
('student2', 'student_password', 'student'),
('student3', 'student_password', 'student');

-- Insert Lecturer Info
INSERT INTO lecturers (user_id, department) VALUES
(2, 'Computer Science');

-- Insert Advisor Info
INSERT INTO students (user_id, name, matric_number, email) VALUES
(4, 'John Doe', 'M123456', 'johndoe@example.com'),
(5, 'Jane Smith', 'M654321', 'janesmith@example.com'),
(6, 'Alex Johnson', 'M135792', 'alexjohnson@example.com');

-- Insert Course Info
INSERT INTO courses (course_code, course_name, lecturer_id) VALUES
('CS101', 'Introduction to Computer Science', 1),
('CS102', 'Data Structures and Algorithms', 1);

-- Insert Enrollments
INSERT INTO enrollments (student_id, course_id) VALUES
(1, 1),  -- John Doe enrolled in CS101
(2, 1),  -- Jane Smith enrolled in CS101
(3, 2);  -- Alex Johnson enrolled in CS102

-- Insert Continuous Assessments
INSERT INTO assessments (course_id, assessment_name, max_marks, weight_percentage) VALUES
(1, 'Quiz 1', 20, 10),
(1, 'Assignment 1', 30, 20),
(2, 'Midterm', 50, 30);

-- Insert Final Exam
INSERT INTO final_exam (course_id, exam_date, max_marks) VALUES
(1, '2025-06-15', 100),
(2, '2025-06-20', 100);

-- Insert Marks for Students
INSERT INTO marks (student_id, assessment_id, marks_obtained) VALUES
(1, 1, 18),  -- John Doe's marks for Quiz 1
(1, 2, 25),  -- John Doe's marks for Assignment 1
(2, 1, 15),  -- Jane Smith's marks for Quiz 1
(3, 2, 45);  -- Alex Johnson's marks for Midterm

-- Insert Notifications
INSERT INTO notifications (user_id, message, status) VALUES
(1, 'Your marks have been updated for Quiz 1.', 'unread'),
(2, 'Your marks have been updated for Assignment 1.', 'unread'),
(3, 'Your marks for Midterm have been updated.', 'read');

-- Insert Advisor Notes
INSERT INTO advisor_notes (advisor_id, student_id, note) VALUES
(4, 1, 'Student is doing well, but needs improvement in quizzes.'),
(4, 2, 'Student has shown improvement after additional sessions.');
