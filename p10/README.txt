P10 FINAL - STUDENTHUB SECURE LOGIN

1. Copy this folder to C:\xampp\htdocs\p10_final
2. Keep a copy in Desktop\WDF Practical\p10_final for submission.
3. Start Apache and MySQL in XAMPP.
4. Ensure database studenthub and table users exist with columns:
   id, username, email, password, role, status.
5. In phpMyAdmin > studenthub > SQL, run only if columns are missing:
   ALTER TABLE users ADD COLUMN remember_token VARCHAR(64) NULL, ADD COLUMN remember_expiry DATETIME NULL;
   If duplicate-column error appears, they already exist; do not run again.
6. Open http://localhost:8080/p10_final/setup_users.php once.
7. Demo credentials: admin/admin123 and student/student123.
8. Delete setup_users.php from BOTH folders after success.
9. Open http://localhost:8080/p10_final/login.php

Features: password_hash/password_verify, prepared statements, session ID regeneration,
separate role dashboards, role checks, 5-minute inactivity timeout, 30-day Remember Me,
logout clearing session and token.

Remember Me test: tick Remember Me, login, close browser WITHOUT clicking Logout,
then reopen login.php. Logout intentionally removes Remember Me.
This code package is statically checked only in this environment; run it in your local
XAMPP to verify it against your actual MySQL table and PHP configuration.
