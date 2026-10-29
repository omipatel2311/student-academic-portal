PRACTICAL 12 - EVENT MANAGEMENT CRUD WITH POSTER UPLOAD

Included: admin login, CRUD, MySQL prepared statements, poster validation, search, category filter, Open/Closed filter, success/error messages, access restriction, random image names, upload protection, audit log, SQL schema and sample data.

RUN IN XAMPP
1. Extract P12_Event_Management_Complete into C:\xampp\htdocs\
2. Start Apache and MySQL.
3. Open http://localhost:8080/P12_Event_Management_Complete/setup.php
   (If Apache port is 80, use http://localhost/P12_Event_Management_Complete/setup.php)
4. Login at http://localhost:8080/P12_Event_Management_Complete/login.php
   Username: admin
   Password: admin123
5. Test add, edit, delete, search, filters, and valid/invalid poster uploads.
6. After setup succeeds, delete setup.php.
7. database.sql is included as the SQL dump. The setup script creates the database tables and sample events.

Poster rules: JPG, PNG, WEBP only, maximum 2 MB. Uploads are renamed randomly.
Database defaults: MySQL root with empty password, standard local XAMPP settings.
This is a college practical demo; change the demo password before any public deployment.
