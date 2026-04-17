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
    <div class="page-shell narrow-public-shell">
        <section class="panel solo-form-panel">
            <div class="topbar compact-topbar">
                <p class="eyebrow">Smart Campus Desk</p>
                <div class="topbar-actions">
                    <a href="admin.php" class="ghost-btn top-link">Dashboard</a>
                    <button id="themeToggle" class="icon-toggle" aria-label="Toggle dark mode">🌙</button>
                </div>
            </div>

            <div class="form-intro">
                <h1>Student Registration Form</h1>
                <p class="hero-text">Fill in the student details below to complete registration.</p>
            </div>

            <form id="registrationForm" action="process.php" method="POST">
                <div class="form-group">
                    <label for="studentName">Student Name</label>
                    <input type="text" id="studentName" name="studentName" placeholder="e.g. Martha Chola">
                    <div class="error" id="nameError">Name is required.</div>
                </div>

                <div class="split-grid">
                    <div class="form-group">
                        <label for="studentID">Student ID</label>
                        <input type="text" id="studentID" name="studentID" placeholder="e.g. CS2026-014">
                        <div class="error" id="idError">Student ID is required.</div>
                    </div>
                    <div class="form-group">
                        <label for="program">Program</label>
                        <select id="program" name="program">
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
                    <input type="email" id="email" name="email" placeholder="name@example.com">
                    <div class="error" id="emailError">Valid email is required.</div>
                </div>

                <div class="form-actions">
                    <button type="submit" id="submitBtn" class="primary-btn">Register Student</button>
                </div>
            </form>
        </section>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    <script src="index.js"></script>
</body>
</html>
