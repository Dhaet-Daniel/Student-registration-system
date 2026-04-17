document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('registrationForm');
  const submitBtn = document.getElementById('submitBtn');
  const cancelEditBtn = document.getElementById('cancelEditBtn');
  const toast = document.getElementById('toast');
  const themeToggle = document.getElementById('themeToggle');
  const tableBody = document.getElementById('studentTableBody');
  const formTitle = document.getElementById('formTitle');
  const searchInput = document.getElementById('searchInput');
  const refreshBtn = document.getElementById('refreshBtn');
  const programBreakdown = document.getElementById('programBreakdown');

  const stats = {
    totalStudents: document.getElementById('totalStudents'),
    totalPrograms: document.getElementById('totalPrograms'),
    recentRegistrations: document.getElementById('recentRegistrations'),
    latestRegistration: document.getElementById('latestRegistration')
  };

  const state = {
    students: [],
    filteredStudents: [],
    editingId: null
  };

  function showToast(message, type) {
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(function () {
      toast.className = 'toast';
    }, 3500);
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function clearValidation() {
    document.querySelectorAll('.error').forEach(function (item) {
      item.style.display = 'none';
    });
  }

  function validate() {
    clearValidation();
    const studentName = document.getElementById('studentName').value.trim();
    const studentID = document.getElementById('studentID').value.trim();
    const email = document.getElementById('email').value.trim();
    const program = document.getElementById('program').value.trim();
    let isValid = true;

    if (!studentName) { document.getElementById('nameError').style.display = 'block'; isValid = false; }
    if (!studentID) { document.getElementById('idError').style.display = 'block'; isValid = false; }
    if (!email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) { document.getElementById('emailError').style.display = 'block'; isValid = false; }
    if (!program) { document.getElementById('programError').style.display = 'block'; isValid = false; }

    return isValid;
  }

  function resetForm() {
    form.reset();
    document.getElementById('recordId').value = '';
    state.editingId = null;
    formTitle.textContent = 'Edit Student';
    submitBtn.textContent = 'Save Changes';
    cancelEditBtn.classList.add('hidden');
    clearValidation();
  }

  function fillForm(student) {
    document.getElementById('recordId').value = student.id;
    document.getElementById('studentName').value = student.full_name;
    document.getElementById('studentID').value = student.student_id;
    document.getElementById('email').value = student.email;
    document.getElementById('program').value = student.program;
    state.editingId = student.id;
    cancelEditBtn.classList.remove('hidden');
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function formatDate(dateString) {
    if (!dateString) { return 'No entries yet'; }
    const date = new Date(dateString.replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) { return dateString; }

    return new Intl.DateTimeFormat('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit'
    }).format(date);
  }

  function renderProgramBreakdown(items) {
    if (!items.length) {
      programBreakdown.className = 'program-list empty-state';
      programBreakdown.textContent = 'No student records yet.';
      return;
    }

    programBreakdown.className = 'program-list';
    programBreakdown.innerHTML = items.map(function (item) {
      return '<div class="program-item"><div><strong>' + escapeHtml(item.program) + '</strong><span>' + escapeHtml(String(item.total)) + ' students</span></div><div class="program-bar"><span style="width:' + escapeHtml(String(Math.max(12, item.total * 12))) + 'px"></span></div></div>';
    }).join('');
  }

  function renderStats(summary) {
    stats.totalStudents.textContent = summary.totalStudents;
    stats.totalPrograms.textContent = summary.totalPrograms;
    stats.recentRegistrations.textContent = summary.recentRegistrations;
    stats.latestRegistration.textContent = formatDate(summary.latestRegistration);
    renderProgramBreakdown(summary.programBreakdown || []);
  }

  function renderTable() {
    if (!state.filteredStudents.length) {
      tableBody.innerHTML = '<tr><td colspan="5" class="table-empty">No student records match your search.</td></tr>';
      return;
    }

    tableBody.innerHTML = state.filteredStudents.map(function (student) {
      return '<tr><td><div class="student-cell"><strong>' + escapeHtml(student.full_name) + '</strong><span>' + escapeHtml(student.email) + '</span></div></td><td><span class="chip">' + escapeHtml(student.student_id) + '</span></td><td>' + escapeHtml(student.program) + '</td><td>' + escapeHtml(formatDate(student.registration_date)) + '</td><td><div class="table-actions"><button class="table-btn edit-btn" type="button" data-id="' + escapeHtml(String(student.id)) + '">Edit</button><button class="table-btn delete-btn" type="button" data-id="' + escapeHtml(String(student.id)) + '">Delete</button></div></td></tr>';
    }).join('');
  }

  function applyFilter() {
    const searchTerm = searchInput.value.trim().toLowerCase();
    state.filteredStudents = state.students.filter(function (student) {
      return [student.full_name, student.student_id, student.email, student.program].join(' ').toLowerCase().includes(searchTerm);
    });
    renderTable();
  }

  async function requestData(url, options) {
    const response = await fetch(url, options);
    const contentType = response.headers.get('content-type') || '';
    const payload = contentType.includes('application/json') ? await response.json() : { success: false, message: await response.text() };

    if (!response.ok || !payload.success) {
      throw new Error(payload.message || 'Something went wrong.');
    }

    return payload;
  }

  function hydrateDashboard(payload) {
    state.students = payload.data || [];
    renderStats(payload.stats || { totalStudents: 0, totalPrograms: 0, recentRegistrations: 0, latestRegistration: null, programBreakdown: [] });
    applyFilter();
  }

  async function loadStudents() {
    tableBody.innerHTML = '<tr><td colspan="5" class="table-empty">Loading student records...</td></tr>';
    try {
      const payload = await requestData(form.action, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      hydrateDashboard(payload);
    } catch (error) {
      tableBody.innerHTML = '<tr><td colspan="5" class="table-empty">Unable to load records.</td></tr>';
      showToast(error.message || 'Unable to load records.', 'error');
    }
  }

  form.addEventListener('submit', async function (event) {
    event.preventDefault();

    if (!validate() || !state.editingId) {
      if (!state.editingId) { showToast('Select a student from the table before editing.', 'error'); }
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';

    try {
      const payload = await requestData(form.action, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
          id: document.getElementById('recordId').value,
          studentName: document.getElementById('studentName').value.trim(),
          studentID: document.getElementById('studentID').value.trim(),
          email: document.getElementById('email').value.trim(),
          program: document.getElementById('program').value.trim()
        })
      });
      hydrateDashboard(payload);
      resetForm();
      showToast(payload.message, 'success');
    } catch (error) {
      showToast(error.message || 'Unable to update student.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Save Changes';
    }
  });

  tableBody.addEventListener('click', async function (event) {
    const target = event.target;
    const id = Number(target.getAttribute('data-id'));
    if (!id) { return; }

    const selectedStudent = state.students.find(function (student) {
      return Number(student.id) === id;
    });

    if (target.classList.contains('edit-btn') && selectedStudent) {
      fillForm(selectedStudent);
      return;
    }

    if (target.classList.contains('delete-btn')) {
      if (!window.confirm('Delete this student record? This action cannot be undone.')) { return; }

      try {
        const payload = await requestData(form.action, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify({ id: id })
        });

        if (state.editingId === id) { resetForm(); }

        hydrateDashboard(payload);
        showToast(payload.message, 'success');
      } catch (error) {
        showToast(error.message || 'Unable to delete student.', 'error');
      }
    }
  });

  cancelEditBtn.addEventListener('click', resetForm);
  refreshBtn.addEventListener('click', loadStudents);
  searchInput.addEventListener('input', applyFilter);

  ['studentName', 'studentID', 'email', 'program'].forEach(function (fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) { return; }
    field.addEventListener('input', clearValidation);
    field.addEventListener('change', clearValidation);
  });

  themeToggle.addEventListener('click', function () {
    document.body.classList.toggle('dark');
    themeToggle.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
  });

  resetForm();
  loadStudents();
});
