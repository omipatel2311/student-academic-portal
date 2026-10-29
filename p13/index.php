<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 13 - PHP JSON API & Fetch CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="container header-row">
        <div>
            <h1>StudentHub Portal</h1>
            <p>Practical 13: Asynchronous PHP JSON API & Fetch-Based CRUD</p>
        </div>
        <div>
            <a href="../p10/login.php" class="btn btn-secondary btn-sm" style="background:rgba(255,255,255,0.2); color:#fff;">Login Portal</a>
        </div>
    </div>
</header>
<nav>
    <div class="container nav-links">
        <a href="index.php" class="active">API CRUD (No Reload)</a>
        <a href="../p10/index.php">Practical 10 (Auth)</a>
        <a href="../p11/index.php">Practical 11 (CRUD)</a>
        <a href="../p12/index.php">Practical 12 (Events)</a>
        <a href="../p14/index.php">Practical 14 (Admin)</a>
        <a href="../p15/index.php">Practical 15 (Portal)</a>
    </div>
</nav>
<main class="container">
    <section class="stats-grid">
        <div class="stat-card">
            <div>
                <div class="label">Total Students</div>
                <div class="value" id="totalCount">...</div>
            </div>
            <div style="font-size:32px; color:var(--purple);">👥</div>
        </div>
        <div class="stat-card">
            <div>
                <div class="label">Active Enrolled</div>
                <div class="value" id="activeCount" style="color:var(--green);">...</div>
            </div>
            <div style="font-size:32px; color:var(--green);">✓</div>
        </div>
        <div class="stat-card">
            <div>
                <div class="label">Architecture</div>
                <div class="value" style="font-size:18px; font-weight:600; color:#4a4473;">Fetch & JSON API</div>
            </div>
            <div style="font-size:32px; color:#6257bd;">⚡</div>
        </div>
    </section>
    <section class="content-card">
        <div class="controls-bar">
            <div class="search-filter-group">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search by name, email, or phone (Debounced)..." autocomplete="off">
                </div>
                <select id="courseFilter">
                    <option value="">All Courses</option>
                    <option value="Computer Engineering">Computer Engineering</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Mechanical Engineering">Mechanical Engineering</option>
                    <option value="Civil Engineering">Civil Engineering</option>
                    <option value="Electronics & Comm">Electronics & Comm</option>
                </select>
                <select id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button class="btn btn-primary" id="btnAddNew">
                + Add Student
            </button>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Student Name</th>
                        <th>Email Address</th>
                        <th>Department</th>
                        <th style="width: 100px;">Semester</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    <tr>
                        <td colspan="7" class="loading-row">
                            <div class="spinner"></div>
                            <div>Loading records via Fetch API...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

</main>
<div class="modal-overlay" id="studentModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Student</h3>
            <button type="button" class="modal-close" id="modalCloseX">&times;</button>
        </div>
        <form id="studentForm">
            <input type="hidden" id="studentId" name="student_id">
            <div class="form-group">
                <label for="studentName">Full Name *</label>
                <input type="text" id="studentName" placeholder="e.g. Aarav Patel" required>
            </div>
            <div class="form-group">
                <label for="studentEmail">Email Address *</label>
                <input type="email" id="studentEmail" placeholder="student@charusat.edu.in" required>
            </div>
            <div class="form-group">
                <label for="studentCourse">Department / Course *</label>
                <select id="studentCourse" required>
                    <option value="Computer Engineering">Computer Engineering</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Mechanical Engineering">Mechanical Engineering</option>
                    <option value="Civil Engineering">Civil Engineering</option>
                    <option value="Electronics & Comm">Electronics & Comm</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px;">
                <div class="form-group" style="flex: 1;">
                    <label for="studentSemester">Semester *</label>
                    <select id="studentSemester" required>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                        <option value="3" selected>Semester 3</option>
                        <option value="4">Semester 4</option>
                        <option value="5">Semester 5</option>
                        <option value="6">Semester 6</option>
                        <option value="7">Semester 7</option>
                        <option value="8">Semester 8</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="studentStatus">Status</label>
                    <select id="studentStatus">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="studentPhone">Mobile Phone (10 digits) *</label>
                <input type="text" id="studentPhone" pattern="[0-9]{10}" placeholder="9825011223" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnCancelModal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSaveStudent">Save Record</button>
            </div>
        </form>
    </div>
</div>
<div id="toast-container"></div>
<footer>
    &copy; 2026 StudentHub &bull; ITUE203 Web Development Frameworks &bull; Practical 13
</footer>
<script src="app.js"></script>
</body>
</html>
