<?php
/**
 * StudentHub Master Practical Launcher (Practicals 1 to 15)
 * CHARUSAT - Faculty of Technology & Engineering (FTE)
 * Subject: Web Development Frameworks (ITUE203)
 */

$mysqlConnected = false;
try {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = new mysqli("localhost", "root", "", "studenthub");
    if (!$conn->connect_error) {
        $mysqlConnected = true;
    }
} catch (Exception $e) {
    $mysqlConnected = false;
}

$practicals = [
    [
        'id' => 1,
        'title' => 'Project Initiation & GitHub Setup',
        'desc' => 'Problem scope, user roles, sitemap, low-fidelity wireframe, folder structure, and GitHub repository.',
        'link' => 'p1/README.md',
        'tag' => 'Requirements & Wireframes',
        'badge' => 'P1'
    ],
    [
        'id' => 2,
        'title' => 'Semantic HTML5 Pages',
        'desc' => '10+ static StudentHub HTML5 semantic skeletons with accessibility readiness (Home, About, Register, etc.).',
        'link' => 'p2/index.html',
        'tag' => 'HTML5 Semantics',
        'badge' => 'P2'
    ],
    [
        'id' => 3,
        'title' => 'Responsive UI Design',
        'desc' => 'Responsive multi-device layouts using CSS Grid and Flexbox with mobile-first design principles.',
        'link' => 'p3/index.html',
        'tag' => 'CSS Grid & Flexbox',
        'badge' => 'P3'
    ],
    [
        'id' => 4,
        'title' => 'DOM Manipulation & Interactivity',
        'desc' => 'Interactive UI components: Collapsible FAQs, modal popups, hamburger menu, and localStorage theme switcher.',
        'link' => 'p4/index.html',
        'tag' => 'JavaScript DOM',
        'badge' => 'P4'
    ],
    [
        'id' => 5,
        'title' => 'Form Validation & UX Handling',
        'desc' => 'Registration form with RegEx validations, password strength checker, and user-friendly inline error alerts.',
        'link' => 'p5/index.html',
        'tag' => 'Frontend Validation',
        'badge' => 'P5'
    ],
    [
        'id' => 6,
        'title' => 'Rendering External JSON via Fetch API',
        'desc' => 'Consuming external JSON datasets (students, events, faqs) with search, filter, and pagination.',
        'link' => 'p6/index.html',
        'tag' => 'JSON & Fetch API',
        'badge' => 'P6'
    ],
    [
        'id' => 7,
        'title' => 'PHP Form Processing & CSV Storage',
        'desc' => 'Server-side POST processing, sanitization, validation, and safe data persistence in CSV format.',
        'link' => 'p7/index.php',
        'tag' => 'PHP & CSV Storage',
        'badge' => 'P7'
    ],
    [
        'id' => 8,
        'title' => 'MySQL Schema Design & Connectivity',
        'desc' => 'Relational database normalization, ER modeling, foreign key constraints, and MySQLi database connectivity.',
        'link' => 'p8/index.php',
        'tag' => 'MySQL & DB Connection',
        'badge' => 'P8'
    ],
    [
        'id' => 9,
        'title' => 'Secure User Registration',
        'desc' => 'Backend validation, duplicate email/username check, password hashing using password_hash(), and MySQL insert.',
        'link' => 'p9/register.php',
        'tag' => 'password_hash() & Security',
        'badge' => 'P9'
    ],
    [
        'id' => 10,
        'title' => 'Secure Login, RBAC & Remember-Me',
        'desc' => 'Session auth, password_verify(), 5m session timeout, Remember-Me token, and role redirection (Admin/Student).',
        'link' => 'p10/index.php',
        'tag' => 'PHP Sessions & Tokens',
        'badge' => 'P10'
    ],
    [
        'id' => 11,
        'title' => 'Student Management CRUD Module',
        'desc' => 'Full CRUD with MySQLi prepared statements, real-time search, filter, pagination, CSV export, and soft delete.',
        'link' => 'p11/index.php',
        'tag' => 'MySQLi CRUD & CSV Export',
        'badge' => 'P11'
    ],
    [
        'id' => 12,
        'title' => 'Event Management CRUD with Poster Upload',
        'desc' => 'Admin event scheduling, poster file upload with MIME & size validation, category filtering, and audit log.',
        'link' => 'p12/index.php',
        'tag' => 'File Upload & Validation',
        'badge' => 'P12'
    ],
    [
        'id' => 13,
        'title' => 'PHP JSON API & Fetch CRUD (No Reload)',
        'desc' => 'Decoupled RESTful JSON API endpoints, Fetch API, debounced search (350ms), and CRUD without full page reload.',
        'link' => 'p13/index.php',
        'tag' => 'Async Fetch & JSON API',
        'badge' => 'P13'
    ],
    [
        'id' => 14,
        'title' => 'Admin Dashboard, RBAC & Analytics',
        'desc' => 'Dynamic metrics via SQL aggregations, dynamic role menus, user active/inactive toggle, monthly analytics chart, and audit logs.',
        'link' => 'p14/login.php',
        'tag' => 'RBAC & Monthly Analytics',
        'badge' => 'P14'
    ],
    [
        'id' => 15,
        'title' => 'StudentHub Final Integrated Master Portal',
        'desc' => 'Master deployable web portal unifying all 15 practicals, AJAX registrations, automated DB setup, and Apache Virtual Host guide.',
        'link' => 'p15/index.php',
        'tag' => 'Final Unified Portal',
        'badge' => 'P15'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub &bull; Master Practical Suite (1 to 15)</title>
    <style>
        :root {
            --purple: #6558d3;
            --purple-hover: #5345bd;
            --purple-dark: #352e68;
            --purple-light: #f5f3ff;
            --purple-tint: #ede9fe;
            --ink: #292544;
            --muted: #6f6b80;
            --border: #e2dcfa;
            --white: #ffffff;
            --green: #10b981;
            --green-light: #ecfdf5;
            --shadow: 0 8px 24px rgba(64, 53, 143, 0.08);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--purple-light);
            color: var(--ink);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container { width: 92%; max-width: 1200px; margin: 0 auto; }
        
        /* Header */
        header {
            background: linear-gradient(135deg, #6558d3, #40358f);
            color: var(--white);
            padding: 30px 0;
            box-shadow: 0 4px 15px rgba(53, 46, 104, 0.25);
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .brand h1 { font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
        .brand p { color: #e0dbff; font-size: 14px; margin-top: 4px; }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.18);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            color: #fff;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: <?= $mysqlConnected ? '#10b981' : '#f59e0b' ?>;
            box-shadow: 0 0 8px <?= $mysqlConnected ? '#10b981' : '#f59e0b' ?>;
        }

        /* Banner */
        .info-strip {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .info-strip .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 13.5px;
        }
        .cred-box {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .cred-box code {
            background: var(--purple-tint);
            padding: 3px 8px;
            border-radius: 4px;
            color: var(--purple-dark);
            font-weight: 600;
        }

        /* Spotlight P15 Card */
        .spotlight-card {
            background: linear-gradient(135deg, #7162e3, #4f3ea8);
            color: #fff;
            border-radius: 14px;
            padding: 28px 32px;
            margin: 25px 0 28px;
            box-shadow: 0 10px 28px rgba(101, 88, 211, 0.25);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .spotlight-text h2 { font-size: 24px; font-weight: 800; margin-bottom: 6px; }
        .spotlight-text p { color: #ede9fe; font-size: 14.5px; max-width: 680px; }
        .spotlight-btn {
            background: #fff;
            color: var(--purple-dark);
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform 0.2s, background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .spotlight-btn:hover { transform: translateY(-2px); background: #faf9ff; }

        /* Grid */
        .grid-header {
            margin-bottom: 16px;
        }
        .grid-header h3 { font-size: 20px; color: var(--purple-dark); }
        .grid-header p { font-size: 13.5px; color: var(--muted); }

        .practical-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 22px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(64, 53, 143, 0.12);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .badge {
            font-size: 11.5px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--purple-tint);
            color: var(--purple);
            letter-spacing: 0.5px;
        }
        .tag {
            font-size: 11.5px;
            color: var(--muted);
            font-weight: 600;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
            line-height: 1.3;
        }
        .card-desc {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 18px;
            flex: 1;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 9px 14px;
            border-radius: 7px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            background: var(--purple);
            color: #fff;
            transition: background 0.2s;
        }
        .btn:hover { background: var(--purple-hover); }

        footer {
            background: var(--purple-dark);
            color: #ded7f0;
            text-align: center;
            padding: 22px 0;
            font-size: 13px;
            margin-top: auto;
        }
    </style>
</head>
<body>

<header>
    <div class="container header-content">
        <div class="brand">
            <h1>StudentHub Practical Suite</h1>
            <p>CHARUSAT &bull; FTE &bull; Web Development Frameworks (ITUE203)</p>
        </div>
        <div>
            <span class="status-pill">
                <span class="status-dot"></span>
                <span>Apache: <strong>Port 8080</strong> &bull; MySQL: <strong><?= $mysqlConnected ? 'Connected' : 'Check XAMPP' ?></strong></span>
            </span>
        </div>
    </div>
</header>

<div class="info-strip">
    <div class="container">
        <div>
            <span>Default Test Credentials:</span>
        </div>
        <div class="cred-box">
            <span>Admin: <code>admin</code> / <code>admin123</code></span>
            <span>Student: <code>student</code> / <code>student123</code></span>
        </div>
    </div>
</div>

<main class="container">

    <!-- Spotlight P15 Card -->
    <section class="spotlight-card">
        <div class="spotlight-text">
            <h2>🚀 Practical 15: Unified Master Portal</h2>
            <p>Integrates the semester project end-to-end: Role-based dashboards, student and event management, AJAX registration, and automated database verification.</p>
        </div>
        <div>
            <a href="p15/index.php" class="spotlight-btn" target="_blank">
                Launch Final Portal &rarr;
            </a>
        </div>
    </section>

    <!-- All Practicals Grid -->
    <div class="grid-header">
        <h3>📋 All Practical Modules (1 to 15)</h3>
        <p>Click "Run Practical" to launch any module directly in a new browser tab.</p>
    </div>

    <div class="practical-grid">
        <?php foreach ($practicals as $p): ?>
        <div class="card">
            <div>
                <div class="card-top">
                    <span class="badge"><?=htmlspecialchars($p['badge'])?></span>
                    <span class="tag"><?=htmlspecialchars($p['tag'])?></span>
                </div>
                <h4 class="card-title"><?=htmlspecialchars($p['title'])?></h4>
                <p class="card-desc"><?=htmlspecialchars($p['desc'])?></p>
            </div>
            <div>
                <a href="<?=htmlspecialchars($p['link'])?>" class="btn" target="_blank">
                    Run Practical <?=htmlspecialchars($p['badge'])?> &rarr;
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</main>

<footer>
    &copy; 2026 StudentHub &bull; Charotar University of Science and Technology (CHARUSAT)
</footer>

</body>
</html>
