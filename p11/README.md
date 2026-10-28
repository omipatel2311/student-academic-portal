# Practical 11 — Student Management CRUD with Search and Filter

## Requirements covered
- Add, view, edit, and permanently delete student records
- Search by student name, email, or course
- Filter by course and Active/Inactive status
- Confirmation before permanent delete
- Soft-delete style status toggle (Deactivate/Activate)
- Export filtered results to CSV
- MySQLi prepared statements and server-side validation
- 22 seeded student records
- Pagination (10 records per page)
- Success/error feedback messages

## Setup on Windows with XAMPP
1. Copy the entire `p11` folder to `C:\xampp\htdocs\p11`.
2. Start **Apache** and **MySQL** from XAMPP Control Panel.
3. Open `http://localhost:8080/phpmyadmin` (use `http://localhost/phpmyadmin` if Apache uses port 80).
4. Create a database named `studenthub` with collation `utf8mb4_general_ci`.
5. Select the `studenthub` database, choose **Import**, select `schema.sql`, and click **Import/Go**.
6. Open `http://localhost:8080/p11/index.php` (or `http://localhost/p11/index.php` if Apache uses port 80).

## Database settings
`db.php` uses the default XAMPP settings: host `localhost`, username `root`, blank password, database `studenthub`. If your MySQL password differs, update `$password` in `db.php`.

## Test checklist
1. Confirm the list shows 22 sample records.
2. Search for `Aarav`, an email, or `Computer Engineering`.
3. Filter by course and status, then test Reset.
4. Add a student using a unique email; verify success message.
5. Edit a record; verify updated values.
6. Click Deactivate/Activate and verify status changes.
7. Click Delete and confirm the browser prompt; verify the record is removed.
8. Export to CSV and verify a `.csv` file downloads.
9. With 22 records, test Next/Previous pagination.

## Notes
- Importing `schema.sql` again will upsert the seeded sample rows by their IDs/emails; do not import repeatedly after making edits if you want to preserve all changes.
- The project is a local practical demo, not a production-ready public deployment.
