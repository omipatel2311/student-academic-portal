
const menuBtn = document.getElementById("menuBtn");
const navbar = document.getElementById("navbar");
const themeBtn = document.getElementById("themeBtn");
const noticeBtn = document.getElementById("noticeBtn");
const notification = document.getElementById("notification");
const closeNotice = document.getElementById("closeNotice");
const openModal = document.getElementById("openModal");
const closeModal = document.getElementById("closeModal");
const modal = document.getElementById("modal");


// Hamburger menu
menuBtn.addEventListener("click", function () {
    navbar.classList.toggle("show");
});


// Dark and light theme
if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
    themeBtn.textContent = "☀️ Light Mode";
}

themeBtn.addEventListener("click", function () {
    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
        themeBtn.textContent = "☀️ Light Mode";
    } else {
        localStorage.setItem("theme", "light");
        themeBtn.textContent = "🌙 Dark Mode";
    }
});


// Show notification
noticeBtn.addEventListener("click", function () {
    notification.style.display = "block";
});

closeNotice.addEventListener("click", function () {
    notification.style.display = "none";
});


// Modal popup
openModal.addEventListener("click", function () {
    modal.style.display = "flex";
});

closeModal.addEventListener("click", function () {
    modal.style.display = "none";
});

modal.addEventListener("click", function (event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});


// FAQ open and close
const questions = document.querySelectorAll(".faq-question");

questions.forEach(function (question) {
    question.addEventListener("click", function () {
        const answer = question.nextElementSibling;

        if (answer.style.display === "block") {
            answer.style.display = "none";
        } else {
            answer.style.display = "block";
        }
    });
});


// Content slider
const slides = [
    {
        title: "Manage Your Time",
        text: "Create a daily study timetable."
    },
    {
        title: "Stay Organized",
        text: "Keep your notes and assignments organized."
    },
    {
        title: "Join College Events",
        text: "Take part in workshops and campus activities."
    }
];

let currentSlide = 0;

function showSlide() {
    document.getElementById("slideTitle").textContent =
        slides[currentSlide].title;

    document.getElementById("slideText").textContent =
        slides[currentSlide].text;
}

document.getElementById("nextBtn").addEventListener("click", function () {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide();
});

document.getElementById("prevBtn").addEventListener("click", function () {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide();
});
