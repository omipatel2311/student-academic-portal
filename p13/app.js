/**
 * Practical 13 - Asynchronous AJAX / Fetch API CRUD
 * Features:
 *  - RESTful JSON API Communication via Fetch API
 *  - Asynchronous CRUD without full page reload
 *  - Intermediate: Real-time search with debounce (350ms)
 *  - Advanced: HTTP status code inspection and user-friendly error banners
 *  - Interactive modal dialogs & toast alerts
 */

const API_BASE = 'api.php';

// DOM References
const searchInput = document.getElementById('searchInput');
const courseFilter = document.getElementById('courseFilter');
const statusFilter = document.getElementById('statusFilter');
const studentsTableBody = document.getElementById('studentsTableBody');
const totalCountSpan = document.getElementById('totalCount');
const activeCountSpan = document.getElementById('activeCount');
const studentModal = document.getElementById('studentModal');
const modalTitle = document.getElementById('modalTitle');
const studentForm = document.getElementById('studentForm');
const btnAddNew = document.getElementById('btnAddNew');
const btnCancelModal = document.getElementById('btnCancelModal');
const modalCloseX = document.getElementById('modalCloseX');
const toastContainer = document.getElementById('toast-container');

// State
let debounceTimer = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    fetchStudents();
    bindEvents();
});

function bindEvents() {
    // Intermediate Extension: Search with Debounce
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchStudents();
        }, 350);
    });

    courseFilter.addEventListener('change', () => fetchStudents());
    statusFilter.addEventListener('change', () => fetchStudents());

    btnAddNew.addEventListener('click', () => openModal());
    btnCancelModal.addEventListener('click', () => closeModal());
    modalCloseX.addEventListener('click', () => closeModal());

    studentModal.addEventListener('click', (e) => {
        if (e.target === studentModal) closeModal();
    });

    studentForm.addEventListener('submit', handleFormSubmit);
}

/**
 * Fetch students asynchronously from API
 */
async function fetchStudents() {
    renderLoading();

    const query = new URLSearchParams({
        action: 'list',
        search: searchInput.value.trim(),
        course: courseFilter.value,
        status: statusFilter.value
    });

    try {
        const response = await fetch(`${API_BASE}?${query.toString()}`);
        const result = await response.json();

        if (response.ok && result.success) {
            renderStudents(result.data.students);
            if (result.data.stats) {
                totalCountSpan.textContent = result.data.stats.total;
                activeCountSpan.textContent = result.data.stats.active;
            }
        } else {
            showToast(result.message || 'Failed to fetch student data.', 'error');
            renderError('Unable to load records.');
        }
    } catch (err) {
        console.error('Fetch error:', err);
        showToast('Network error: Could not reach the server.', 'error');
        renderError('Network connection failed. Verify XAMPP server.');
    }
}

/**
 * Render table rows
 */
function renderStudents(students) {
    if (!students || students.length === 0) {
        studentsTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-row">
                    No matching student records found. Try modifying your search or filter.
                </td>
            </tr>
        `;
        return;
    }

    studentsTableBody.innerHTML = students.map(s => `
        <tr id="row-student-${s.student_id}">
            <td><strong>#${s.student_id}</strong></td>
            <td><strong>${escapeHtml(s.name)}</strong></td>
            <td>${escapeHtml(s.email)}</td>
            <td>${escapeHtml(s.course)}</td>
            <td>Sem ${s.semester}</td>
            <td>
                <span class="badge ${s.status === 'Active' ? 'badge-active' : 'badge-inactive'}">
                    ${s.status}
                </span>
            </td>
            <td>
                <button class="btn btn-secondary btn-sm" onclick="editStudent(${s.student_id})" title="Edit">
                    Edit
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteStudent(${s.student_id}, '${escapeHtml(s.name)}')" title="Delete">
                    Delete
                </button>
            </td>
        </tr>
    `).join('');
}

function renderLoading() {
    studentsTableBody.innerHTML = `
        <tr>
            <td colspan="7" class="loading-row">
                <div class="spinner"></div>
                <div>Fetching records asynchronously...</div>
            </td>
        </tr>
    `;
}

function renderError(msg) {
    studentsTableBody.innerHTML = `
        <tr>
            <td colspan="7" class="loading-row" style="color: var(--red);">
                ${escapeHtml(msg)}
            </td>
        </tr>
    `;
}

/**
 * Open Modal Dialog for Add or Edit
 */
function openModal(student = null) {
    studentForm.reset();
    document.getElementById('studentId').value = '';

    if (student) {
        modalTitle.textContent = 'Edit Student Details';
        document.getElementById('studentId').value = student.student_id;
        document.getElementById('studentName').value = student.name;
        document.getElementById('studentEmail').value = student.email;
        document.getElementById('studentCourse').value = student.course;
        document.getElementById('studentSemester').value = student.semester;
        document.getElementById('studentPhone').value = student.phone;
        document.getElementById('studentStatus').value = student.status;
    } else {
        modalTitle.textContent = 'Add New Student';
        document.getElementById('studentSemester').value = '1';
        document.getElementById('studentStatus').value = 'Active';
    }

    studentModal.classList.add('active');
}

function closeModal() {
    studentModal.classList.remove('active');
}

/**
 * Edit Student: Fetch details via GET API and populate modal
 */
async function editStudent(id) {
    try {
        const response = await fetch(`${API_BASE}?action=get&id=${id}`);
        const result = await response.json();

        if (response.ok && result.success) {
            openModal(result.data);
        } else {
            showToast(result.message || 'Failed to retrieve student record.', 'error');
        }
    } catch (err) {
        showToast('Error communicating with API.', 'error');
    }
}

/**
 * Handle Add/Update Form Submission
 */
async function handleFormSubmit(e) {
    e.preventDefault();

    const id = document.getElementById('studentId').value;
    const isEdit = Boolean(id);

    const payload = {
        student_id: id ? parseInt(id, 10) : undefined,
        name: document.getElementById('studentName').value.trim(),
        email: document.getElementById('studentEmail').value.trim(),
        course: document.getElementById('studentCourse').value,
        semester: parseInt(document.getElementById('studentSemester').value, 10),
        phone: document.getElementById('studentPhone').value.trim(),
        status: document.getElementById('studentStatus').value
    };

    const action = isEdit ? 'update' : 'create';
    const submitBtn = document.getElementById('btnSaveStudent');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';

    try {
        const response = await fetch(`${API_BASE}?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            showToast(result.message, 'success');
            closeModal();
            fetchStudents(); // Refresh data asynchronously
        } else {
            let errorMsg = result.message || 'Operation failed.';
            if (result.errors) {
                const firstErr = Object.values(result.errors)[0];
                if (firstErr) errorMsg = firstErr;
            }
            showToast(errorMsg, 'error');
        }
    } catch (err) {
        console.error('Submit error:', err);
        showToast('Network error while saving record.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

/**
 * Delete Student with Confirmation & Async API call
 */
async function deleteStudent(id, name) {
    if (!confirm(`Are you sure you want to delete student "${name}" (#${id})?`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}?action=delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: id })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            showToast(result.message, 'success');
            // Animate removal of table row without page reload
            const row = document.getElementById(`row-student-${id}`);
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    row.remove();
                    // Update stats
                    fetchStudents();
                }, 300);
            } else {
                fetchStudents();
            }
        } else {
            showToast(result.message || 'Failed to delete record.', 'error');
        }
    } catch (err) {
        showToast('Network error during deletion.', 'error');
    }
}

/**
 * Toast Notifications
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <span>${type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ'}</span>
        <span>${escapeHtml(message)}</span>
    `;

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
