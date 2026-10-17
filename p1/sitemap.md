# StudentHub Portal – Sitemap

## 1. Website Structure

```text
StudentHub Portal
|
├── Home (index.html)
|
├── About Us (about.html)
|
├── Authentication
│   ├── Login (login.html)
│   └── Registration (register.html)
|
├── Student Dashboard (dashboard.html)
│   ├── Profile (profile.html)
│   ├── Attendance (attendance.html)
│   ├── Assignments (assignment.html)
│   ├── Notes (notes.html)
│   ├── Results (result.html)
│   └── Announcements
|
├── Academic Resources
│   ├── Timetable (timetable.html)
│   ├── Events (events.html)
│   └── FAQ (faq.html)
|
├── Contact Us (contact.html)
|
└── Administration
    ├── Admin Panel (admin.html)
    ├── Student Management
    └── Event Management
```

## 2. Main Pages

| No. | Page Name | Example File | Purpose |
|---|---|---|---|
| 1 | Home | `index.html` | Introduction and main navigation |
| 2 | About Us | `about.html` | Information about StudentHub |
| 3 | Login | `login.html` | Student authentication |
| 4 | Registration | `register.html` | New student registration |
| 5 | Dashboard | `dashboard.html` | Overview of student information |
| 6 | Profile | `profile.html` | Student profile details |
| 7 | Attendance | `attendance.html` | Attendance information |
| 8 | Assignments | `assignment.html` | Assignment information |
| 9 | Notes | `notes.html` | Study material and notes |
| 10 | Results | `result.html` | Academic results |
| 11 | Timetable | `timetable.html` | Weekly class schedule |
| 12 | Events | `events.html` | College events and activities |
| 13 | Contact Us | `contact.html` | Contact information and form |
| 14 | FAQ | `faq.html` | Frequently asked questions |
| 15 | Admin | `admin.html` | Administrative interface |

## 3. Dashboard Modules

The Student Dashboard provides access to:

- Student profile
- Attendance information
- Assignments
- Study notes
- Academic results
- Timetable
- Announcements and events

## 4. Navigation Flow

```text
Home
 |
 ├── About Us
 ├── Login
 │    ├── Registration (for new users)
 │    └── Dashboard (after successful login)
 │          ├── Profile
 │          ├── Attendance
 │          ├── Assignments
 │          ├── Notes
 │          ├── Results
 │          └── Timetable
 │
 ├── Events
 ├── FAQ
 ├── Contact Us
 └── Admin (authorized administrator)
```

## 5. Practical-Wise Functionality

- Practical 1: Project planning, requirements, sitemap, wireframe and GitHub setup.
- Practical 2: Semantic HTML5 pages and accessible page structure.
- Practical 3: Responsive layouts using CSS Grid and Flexbox.
- Practical 4: JavaScript DOM manipulation and event handling.
- Practical 5: Registration form validation and error handling.
- Practical 6: JSON data rendering, search and filtering.
- Practical 7: PHP form processing and CSV/JSON storage.
- Practical 8: MySQL schema design and database connectivity.
- Practical 9: Secure student registration and password hashing.
- Practical 10: Login, logout, sessions and role-based access.
- Practical 11: Student management CRUD operations.
- Practical 12: Event management CRUD and poster uploads.

## 6. Page Count

**Total planned pages: 15**

The sitemap includes the main student portal pages and administration modules. Additional PHP pages and supporting files may be created as required by individual practicals.

## 7. Conclusion

This sitemap provides a structured overview of the StudentHub Portal. It defines the main pages, dashboard modules and navigation flow, helping organize the project for development, testing and GitHub submission.