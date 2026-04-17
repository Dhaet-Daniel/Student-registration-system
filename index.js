document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('registrationForm');
  const submitBtn = document.getElementById('submitBtn');
  const toast = document.getElementById('toast');
  const themeToggle = document.getElementById('themeToggle');
  const apiBase = window.location.port === '5500' ? 'http://127.0.0.1:8085/' : '';

  function showToast(message, type) {
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(function () {
      toast.className = 'toast';
    }, 3500);
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

    if (!studentName) {
      document.getElementById('nameError').style.display = 'block';
      isValid = false;
    }
    if (!studentID) {
      document.getElementById('idError').style.display = 'block';
      isValid = false;
    }
    if (!email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      document.getElementById('emailError').style.display = 'block';
      isValid = false;
    }
    if (!program) {
      document.getElementById('programError').style.display = 'block';
      isValid = false;
    }

    return isValid;
  }

  async function requestData(url, options) {
    const response = await fetch(apiBase + url, options);
    const contentType = response.headers.get('content-type') || '';
    const payload = contentType.includes('application/json')
      ? await response.json()
      : { success: false, message: await response.text() };

    if (!response.ok || !payload.success) {
      throw new Error(payload.message || 'Something went wrong.');
    }

    return payload;
  }

  if (window.location.protocol === 'file:') {
    submitBtn.disabled = true;
    showToast('Use a PHP server for registration and admin features. Live Server can show the layout but cannot execute PHP.', 'error');
  }

  form.addEventListener('submit', async function (event) {
    event.preventDefault();

    if (!validate()) {
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Registering...';

    try {
      const payload = await requestData(form.action, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          studentName: document.getElementById('studentName').value.trim(),
          studentID: document.getElementById('studentID').value.trim(),
          email: document.getElementById('email').value.trim(),
          program: document.getElementById('program').value.trim()
        })
      });

      form.reset();
      clearValidation();
      showToast(payload.message || 'Student registered successfully.', 'success');
    } catch (error) {
      showToast(error.message || 'Unable to register student.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Register Student';
    }
  });

  ['studentName', 'studentID', 'email', 'program'].forEach(function (fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) {
      return;
    }

    field.addEventListener('input', clearValidation);
    field.addEventListener('change', clearValidation);
  });

  themeToggle.addEventListener('click', function () {
    document.body.classList.toggle('dark');
    themeToggle.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
  });
});
