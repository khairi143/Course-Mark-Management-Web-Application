export async function loginStaff(username, password) {
  const res = await fetch('http://localhost:5000/api/auth/login-staff', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password })
  });
  return res.json();
}

export async function loginStudent(matricNo, pin) {
  const res = await fetch('http://localhost:5000/api/auth/login-student', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ matricNo, pin })
  });
  return res.json();
} 