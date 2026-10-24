
const eventList = document.getElementById("eventList");
const searchInput = document.getElementById("searchInput");
const categoryFilter = document.getElementById("categoryFilter");
const sortOrder = document.getElementById("sortOrder");
const message = document.getElementById("message");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
const pageInfo = document.getElementById("pageInfo");

let events = [];
let currentPage = 1;
const itemsPerPage = 6;

// Fetch events from JSON file
async function loadEvents() {
    try {
        const response = await fetch("events.json");

        if (!response.ok) {
            throw new Error("Unable to load events");
        }

        events = await response.json();
        displayEvents();
    } catch (error) {
        message.textContent = "Error loading events. Please check events.json.";
        console.error(error);
    }
}

// Search, filter, sort and display events
function displayEvents() {
    const searchText = searchInput.value.toLowerCase();
    const category = categoryFilter.value;

    let filteredEvents = events.filter(function(event) {
        return (
            event.title.toLowerCase().includes(searchText) ||
            event.description.toLowerCase().includes(searchText)
        ) && (category === "all" || event.category === category);
    });

    filteredEvents.sort(function(a, b) {
        if (sortOrder.value === "asc") {
            return a.title.localeCompare(b.title);
        } else {
            return b.title.localeCompare(a.title);
        }
    });

    const totalPages = Math.max(
        1,
        Math.ceil(filteredEvents.length / itemsPerPage)
    );

    if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    const start = (currentPage - 1) * itemsPerPage;
    const pageEvents = filteredEvents.slice(start, start + itemsPerPage);

    eventList.innerHTML = "";

    if (pageEvents.length === 0) {
        message.textContent = "No events found.";
    } else {
        message.textContent = "Total events found: " + filteredEvents.length;

        pageEvents.forEach(function(event) {
            const card = document.createElement("div");
            card.className = "event-card";

            const title = document.createElement("h3");
            title.textContent = event.title;

            const categoryText = document.createElement("p");
            categoryText.textContent = "Category: " + event.category;

            const dateText = document.createElement("p");
            dateText.textContent = "Date: " + event.date;

            const description = document.createElement("p");
            description.textContent = event.description;

            card.append(title, categoryText, dateText, description);
            eventList.appendChild(card);
        });
    }

    pageInfo.textContent = "Page " + currentPage + " of " + totalPages;
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
}

searchInput.addEventListener("input", function() {
    currentPage = 1;
    displayEvents();
});

categoryFilter.addEventListener("change", function() {
    currentPage = 1;
    displayEvents();
});

sortOrder.addEventListener("change", function() {
    displayEvents();
});

prevBtn.addEventListener("click", function() {
    if (currentPage > 1) {
        currentPage--;
        displayEvents();
    }
});

nextBtn.addEventListener("click", function() {
    currentPage++;
    displayEvents();
});

// Load data when page opens
loadEvents();
// Fetch and display students
fetch("students.json")
    .then(response => response.json())
    .then(students => {
        const studentList = document.getElementById("studentList");

        students.forEach(student => {
            const div = document.createElement("div");
            div.textContent =
                student.name + " - " +
                student.course + " - Semester " +
                student.semester;

            studentList.appendChild(div);
        });
    })
    .catch(error => console.log("Error loading students:", error));


// Fetch and display FAQs
fetch("faqs.json")
    .then(response => response.json())
    .then(faqs => {
        const faqList = document.getElementById("faqList");

        faqs.forEach(faq => {
            const div = document.createElement("div");
            div.innerHTML =
                "<h3></h3><p></p>";

            div.querySelector("h3").textContent = faq.question;
            div.querySelector("p").textContent = faq.answer;

            faqList.appendChild(div);
        });
    })
    .catch(error => console.log("Error loading FAQs:", error));