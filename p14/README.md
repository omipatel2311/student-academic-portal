# Practical 14: Admin Dashboard with Role-Based Access Control, Dynamic Menu, and Audit Log

## Overview
Practical 14 implements a secure Administrative Dashboard featuring:
1. **Dynamic KPI Metrics (SQL Aggregations)**:
   - Real-time counts for User Accounts, Active Students, Campus Events, and Total Registrations.
2. **Role-Based Access Control (RBAC)**:
   - Dedicated authentication check: Only users with the `admin` role can access the dashboard.
   - Non-admin users (such as `student`) are redirected to the Student Portal view.
3. **Dynamic Menu Loading**:
   - The navigation bar automatically adapts based on whether the active session is an Admin, a Student, or a Guest.
4. **Intermediate Extension - Active/Inactive User Status Toggle**:
   - Allows an administrator to toggle account statuses between Active and Inactive with CSRF token verification.
   - Every toggle action is recorded in the `audit_logs` table.
5. **Advanced Extension - Monthly Analytics Chart**:
   - Dynamic bar chart showing registrations per month, aggregated using SQL `GROUP BY DATE_FORMAT(registration_date, '%Y-%m')`.
6. **Comprehensive Audit Logging (`audit_logs`)**:
   - Tracks logins, logouts, session timeouts, and status modifications with IP addresses and timestamps.

## Default Credentials
- **Admin**: `admin` / `admin123` (Accesses Admin Dashboard, User Toggle, Audit Logs)
- **Student**: `student` / `student123` (Accesses Student Portal with student-specific menu)

## How to Test
1. Make sure Apache and MySQL are running in XAMPP.
2. Visit `http://localhost/WDF%20Practical/p14/login.php` in your browser.
3. Log in as `admin` to view the KPI cards, monthly analytics chart, user management status toggle, and audit logs.
4. Log out and log in as `student` to observe role-based redirection and dynamic student menu.
