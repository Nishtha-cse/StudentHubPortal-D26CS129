// script.js - consumes api.php using the Fetch API (no page reloads)
const API_URL = 'api.php';

const form        = document.getElementById('studentForm');
const idInput     = document.getElementById('studentId');
const nameInput   = document.getElementById('name');
const emailInput  = document.getElementById('email');
const courseInput = document.getElementById('course');
const submitBtn   = document.getElementById('submitBtn');
const cancelBtn   = document.getElementById('cancelBtn');
const formTitle   = document.getElementById('formTitle');
const tbody       = document.getElementById('studentBody');
const loader      = document.getElementById('loader');
const messageBox  = document.getElementById('message');

let students = [];

// ---------- helpers ----------
function showMessage(text, type = 'success') {
  messageBox.textContent = text;
  messageBox.className = type;
  setTimeout(() => { messageBox.className = ''; messageBox.textContent = ''; }, 3500);
}

function setLoading(isLoading) {
  loader.style.display = isLoading ? 'block' : 'none';
}

function escapeHTML(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

// Generic fetch wrapper: parses JSON and throws on non-OK responses
async function request(url, options = {}) {
  const res = await fetch(url, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok || data.success === false) {
    throw new Error(data.message || `Request failed (${res.status})`);
  }
  return data;
}

// ---------- LIST ----------
async function loadStudents() {
  setLoading(true);
  try {
    const result = await request(API_URL);   // GET
    students = result.data;
    renderTable();
  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="5" class="empty">Could not load students.</td></tr>`;
    showMessage(err.message, 'error');
  } finally {
    setLoading(false);
  }
}

function renderTable() {
  if (students.length === 0) {
    tbody.innerHTML = `<tr><td colspan="5" class="empty">No students found.</td></tr>`;
    return;
  }
  tbody.innerHTML = students.map(s => `
    <tr>
      <td>${s.id}</td>
      <td>${escapeHTML(s.name)}</td>
      <td>${escapeHTML(s.email)}</td>
      <td>${escapeHTML(s.course)}</td>
      <td class="actions">
        <button class="btn-edit" onclick="editStudent(${s.id})">Edit</button>
        <button class="btn-delete" onclick="deleteStudent(${s.id})">Delete</button>
      </td>
    </tr>
  `).join('');
}

// ---------- ADD / UPDATE ----------
form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const payload = {
    name:   nameInput.value.trim(),
    email:  emailInput.value.trim(),
    course: courseInput.value.trim(),
  };
  const id = idInput.value;

  submitBtn.disabled = true;
  try {
    if (id) {
      payload.id = Number(id);
      await request(API_URL, { method: 'PUT', body: JSON.stringify(payload) });
      showMessage('Student updated successfully');
    } else {
      await request(API_URL, { method: 'POST', body: JSON.stringify(payload) });
      showMessage('Student added successfully');
    }
    resetForm();
    await loadStudents();
  } catch (err) {
    showMessage(err.message, 'error');
  } finally {
    submitBtn.disabled = false;
  }
});

// ---------- EDIT (fill form) ----------
function editStudent(id) {
  const s = students.find(st => Number(st.id) === Number(id));
  if (!s) return;
  idInput.value     = s.id;
  nameInput.value   = s.name;
  emailInput.value  = s.email;
  courseInput.value = s.course;
  formTitle.textContent = 'Edit Student';
  submitBtn.textContent = 'Update';
  cancelBtn.style.display = 'inline-block';
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
  form.reset();
  idInput.value = '';
  formTitle.textContent = 'Add Student';
  submitBtn.textContent = 'Add';
  cancelBtn.style.display = 'none';
}
cancelBtn.addEventListener('click', resetForm);

// ---------- DELETE ----------
async function deleteStudent(id) {
  if (!confirm('Are you sure you want to delete this student?')) return;
  try {
    await request(API_URL, { method: 'DELETE', body: JSON.stringify({ id }) });
    showMessage('Student deleted successfully');
    await loadStudents();
  } catch (err) {
    showMessage(err.message, 'error');
  }
}

// Initial load
loadStudents();