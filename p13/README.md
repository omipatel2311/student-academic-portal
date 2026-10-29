# Practical 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload

## Overview
Practical 13 implements a decoupled architecture where backend PHP scripts act as a REST-like JSON API and the frontend communicates asynchronously using the modern JavaScript `fetch()` API and DOM manipulation. Full page reloads are completely eliminated for all CRUD operations (Create, Read, Update, Delete).

## Key Features & Practical Requirements
1. **PHP JSON API Endpoints (`api.php`)**:
   - `GET api.php?action=list`: Returns student JSON array with filtering and search.
   - `GET api.php?action=get&id=X`: Returns single student JSON record.
   - `POST api.php?action=create`: Validates inputs and inserts record, returns HTTP 201 Created.
   - `POST api.php?action=update`: Validates inputs and updates record, returns HTTP 200 OK.
   - `POST api.php?action=delete`: Deletes student using prepared statement, returns HTTP 200 OK.
2. **Intermediate Extension - Debounced AJAX Search**:
   - Real-time search with a 350ms debounce timer prevents excessive network requests while typing.
3. **Advanced Extension - HTTP Status Codes & Error Handling**:
   - Proper REST status codes (200, 201, 400, 404, 409, 422, 500) and structured JSON error responses.
   - Toast notification feedback and loading spinners.
4. **Theme Consistency**:
   - Matches the StudentHub purple-and-white theme.

## How to Test
1. Start Apache and MySQL in XAMPP.
2. Visit `http://localhost/WDF%20Practical/p13/` in your browser.
3. Test search, add student modal, edit modal, and delete without any page reload.
