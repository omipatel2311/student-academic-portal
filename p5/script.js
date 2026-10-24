
const form = document.getElementById("registrationForm");

const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const mobileInput = document.getElementById("mobile");
const passwordInput = document.getElementById("password");
const confirmPasswordInput = document.getElementById("confirmPassword");
const courseInput = document.getElementById("course");
const yearInput = document.getElementById("year");
const termsInput = document.getElementById("terms");

function showError(id, message) {
    document.getElementById(id).textContent = message;
}

function clearErrors() {
    document.querySelectorAll(".error").forEach(function (error) {
        error.textContent = "";
    });

    document.getElementById("successMessage").textContent = "";
}

function checkPasswordStrength(password) {
    let strength = 0;

    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    const bar = document.getElementById("strengthBar");
    const text = document.getElementById("strengthText");

    bar.style.width = (strength * 25) + "%";

    if (password.length === 0) {
        text.textContent = "Password strength: Not entered";
    } else if (strength <= 1) {
        text.textContent = "Password strength: Weak";
    } else if (strength <= 3) {
        text.textContent = "Password strength: Medium";
    } else {
        text.textContent = "Password strength: Strong";
    }
}

passwordInput.addEventListener("input", function () {
    checkPasswordStrength(passwordInput.value);
});

form.addEventListener("submit", function (event) {
    event.preventDefault();
    clearErrors();

    let valid = true;

    if (!/^[A-Za-z ]{2,}$/.test(nameInput.value.trim())) {
        showError("nameError", "Enter a valid name (letters only).");
        valid = false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
        showError("emailError", "Enter a valid email address.");
        valid = false;
    }

    if (!/^[0-9]{10}$/.test(mobileInput.value.trim())) {
        showError("mobileError", "Enter a 10-digit mobile number.");
        valid = false;
    }

    if (passwordInput.value.length < 8) {
        showError("passwordError", "Password must be at least 8 characters.");
        valid = false;
    }

    if (confirmPasswordInput.value !== passwordInput.value ||
        confirmPasswordInput.value === "") {
        showError("confirmPasswordError", "Passwords do not match.");
        valid = false;
    }

    if (courseInput.value === "") {
        showError("courseError", "Please select your course.");
        valid = false;
    }

    if (yearInput.value === "") {
        showError("yearError", "Please select your academic year.");
        valid = false;
    }

    if (!document.querySelector('input[name="gender"]:checked')) {
        showError("genderError", "Please select your gender.");
        valid = false;
    }

    if (!termsInput.checked) {
        showError("termsError", "Please accept the terms and conditions.");
        valid = false;
    }

    if (valid) {
        document.getElementById("successMessage").textContent =
            "Registration form validated successfully!";

        form.reset();
        checkPasswordStrength("");
    }
});
