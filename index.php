<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page-shell registration-layout">
        <aside class="welcome-panel" aria-label="Student registration information">
            <div class="welcome-topline"><span class="brand-mark" aria-hidden="true">S</span><span>Smart Campus Desk</span></div>
            <div class="welcome-copy">
                <p class="eyebrow">Your campus journey starts here</p>
                <h1>One small step.<br><span>A place to belong.</span></h1>
                <p>Register your details to create your student record and get started with campus life.</p>
            </div>
            <div class="welcome-orbit" aria-hidden="true"><span></span><i></i><b></b></div>
            <div class="welcome-note"><span class="note-check" aria-hidden="true"><svg viewBox="0 0 20 20"><path d="m4 10 4 4 8-9"/></svg></span><span><strong>Quick and secure</strong><small>Your information is handled with care.</small></span></div>
            <div class="welcome-footer"><span>STUDENT SERVICES</span><span>01 / 01</span></div>
        </aside>

        <section class="panel solo-form-panel registration-panel">
            <div class="topbar compact-topbar">
                <div class="mobile-brand"><span class="brand-mark" aria-hidden="true">S</span><span>Smart Campus Desk</span></div>
                <div class="topbar-actions">
                    <a href="admin.php" class="ghost-btn top-link">Admin portal</a>
                    <button id="themeToggle" class="icon-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false">
                        <svg class="theme-icon icon-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.2 15.3A8.4 8.4 0 0 1 8.7 3.8 8.5 8.5 0 1 0 20.2 15.3Z"/></svg>
                        <svg class="theme-icon icon-sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                    </button>
                </div>
            </div>

            <div class="form-intro">
                <p class="section-kicker">New student registration</p>
                <h2>Create your student record</h2>
                <p class="hero-text">Enter your details below. All fields are required.</p>
            </div>

            <form id="registrationForm" action="process.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="studentName">Student Name</label>
                    <input type="text" id="studentName" name="studentName" placeholder="e.g. Martha Chola" autocomplete="name" required>
                    <div class="error" id="nameError">Name is required.</div>
                </div>

                <div class="split-grid">
                    <div class="form-group">
                        <label for="studentID">Student ID</label>
                        <input type="text" id="studentID" name="studentID" placeholder="e.g. CS2026-014" autocomplete="off" required>
                        <div class="error" id="idError">Student ID is required.</div>
                    </div>
                    <div class="form-group">
                        <label for="program">Program</label>
                        <select id="program" name="program" required>
                            <option value="">Select a Program</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Business Administration">Business Administration</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Accounting">Accounting</option>
                        </select>
                        <div class="error" id="programError">Please select a program.</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="name@example.com" autocomplete="email" required>
                    <div class="error" id="emailError">Valid email is required.</div>
                </div>

                <div class="form-actions">
                    <button type="submit" id="submitBtn" class="primary-btn"><span class="button-label">Register student</span><span class="button-arrow" aria-hidden="true">→</span></button>
                </div>
                <p class="form-footnote"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 10 4 4 8-9"/></svg> Your details are only used for student registration.</p>
            </form>
        </section>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    <script src="index.js"></script>
</body>
</html>
