<?php
require_once __DIR__ . '/auth.php';

if (!isAdminAuthenticated()) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page-shell">
        <section class="hero-panel">
            <div class="topbar">
                <div>
                    <p class="eyebrow">Admin Dashboard</p>
                    <h1>Manage every registered student from one secure workspace.</h1>
                </div>
                <div class="topbar-actions">
                    <a href="index.php" class="ghost-btn top-link">Registration Page</a>
                    <a href="logout.php" class="ghost-btn top-link">Log Out</a>
                    <button id="themeToggle" class="icon-toggle" aria-label="Toggle dark mode">🌙</button>
                </div>
            </div>
            <div class="stats-grid">
                <article class="stat-card"><span class="stat-label">Total Students</span><strong id="totalStudents">0</strong></article>
                <article class="stat-card"><span class="stat-label">Programs</span><strong id="totalPrograms">0</strong></article>
                <article class="stat-card"><span class="stat-label">Added This Week</span><strong id="recentRegistrations">0</strong></article>
                <article class="stat-card"><span class="stat-label">Latest Registration</span><strong id="latestRegistration">No entries yet</strong></article>
            </div>
        </section>

        <main class="dashboard-grid">
            <section class="panel form-panel">
                <div class="panel-header">
                    <div>
                        <p class="section-kicker">Record Editor</p>
                        <h2 id="formTitle">Edit Student</h2>
                    </div>
                    <button id="cancelEditBtn" class="ghost-btn hidden" type="button">Cancel Edit</button>
                </div>

                <form id="registrationForm" action="process.php" method="POST">
                    <input type="hidden" id="recordId" name="id">
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
                        <button type="submit" id="submitBtn" class="primary-btn">Save Changes</button>
                    </div>
                </form>
            </section>

            <section class="panel insights-panel">
                <div class="panel-header">
                    <div>
                        <p class="section-kicker">Dashboard Pulse</p>
                        <h2>Program Breakdown</h2>
                    </div>
                </div>
                <div id="programBreakdown" class="program-list empty-state">No student records yet.</div>
            </section>

            <section class="panel table-panel">
                <div class="panel-header table-header">
                    <div>
                        <p class="section-kicker">Registered Students</p>
                        <h2>Student Directory</h2>
                    </div>
                    <div class="toolbar">
                        <input type="search" id="searchInput" class="search-input" placeholder="Search by name, ID, email, or program">
                        <button id="refreshBtn" class="ghost-btn" type="button">Refresh</button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Student ID</th>
                                <th>Program</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            <tr><td colspan="5" class="table-empty">Loading student records...</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    <script src="dashboard.js"></script>
</body>
</html>
