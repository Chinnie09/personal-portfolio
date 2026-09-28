/* =========================
   MOBILE NAVIGATION
========================= */
const menuToggle = document.getElementById("menu-toggle");
const navLinks = document.getElementById("nav-links");

if (menuToggle && navLinks) {
    menuToggle.addEventListener("click", function () {
        navLinks.classList.toggle("show");
    });
}

/* =========================
   TYPING EFFECT
========================= */
const typingText = document.getElementById("typing-text");

if (typingText) {
    const words = [
        "IT Student",
        "Aspiring Data Analyst",
        "Part-Time Tutor",
        "MySQL Enthusiast"
    ];

    let wordIndex = 0;
    let letterIndex = 0;
    let deleting = false;

    function typeEffect() {
        const currentWord = words[wordIndex];

        if (deleting) {
            typingText.textContent = currentWord.substring(0, letterIndex--);
        } else {
            typingText.textContent = currentWord.substring(0, letterIndex++);
        }

        if (!deleting && letterIndex === currentWord.length + 1) {
            deleting = true;
            setTimeout(typeEffect, 1200);
            return;
        }

        if (deleting && letterIndex === 0) {
            deleting = false;
            wordIndex = (wordIndex + 1) % words.length;
        }

        setTimeout(typeEffect, deleting ? 60 : 120);
    }

    typeEffect();
}

/* =========================
   SCROLL TO TOP BUTTON
========================= */
const scrollButton = document.getElementById("scroll-top");

if (scrollButton) {
    window.addEventListener("scroll", function () {
        scrollButton.style.display = window.scrollY > 300 ? "flex" : "none";
    });

    scrollButton.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
}

/* =========================
   ACTIVE NAVIGATION
========================= */
const currentPage = window.location.pathname.split("/").pop() || "index.html";
const navigationLinks = document.querySelectorAll(".nav-links a");

navigationLinks.forEach(function (link) {
    const href = link.getAttribute("href");
    if (href === currentPage) {
        link.classList.add("active");
    }
});

/* =========================
   THEME TOGGLE + LOCALSTORAGE
   Default = dark (black & gold)
========================= */
const themeToggle = document.getElementById("theme-toggle");

function applyTheme(theme) {
    if (theme === "light") {
        document.body.classList.add("light-mode");
        if (themeToggle) themeToggle.textContent = "🌙";
    } else {
        document.body.classList.remove("light-mode");
        if (themeToggle) themeToggle.textContent = "☀";
    }
}

const savedTheme = localStorage.getItem("theme") || "dark";
applyTheme(savedTheme);

if (themeToggle) {
    themeToggle.addEventListener("click", function () {
        const newTheme = document.body.classList.contains("light-mode") ? "dark" : "light";
        localStorage.setItem("theme", newTheme);
        applyTheme(newTheme);
    });
}

/* =========================
   CONTACT FORM VALIDATION
========================= */
const contactForm = document.querySelector(".contact-form");

if (contactForm) {
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const messageInput = document.getElementById("message");

    const statusMsg = document.createElement("p");
    statusMsg.className = "form-status";
    contactForm.appendChild(statusMsg);

    function showError(input, message) {
        input.classList.add("input-error");
        statusMsg.textContent = message;
        statusMsg.style.color = "#e74c3c";
    }

    function showSuccess(message) {
        statusMsg.textContent = message;
        statusMsg.style.color = "#27ae60";
    }

    function clearError(input) {
        input.classList.remove("input-error");
    }

    [nameInput, emailInput, messageInput].forEach(function (input) {
        if (input) {
            input.addEventListener("input", function () {
                clearError(input);
                statusMsg.textContent = "";
            });
        }
    });

    contactForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const nameVal = nameInput.value.trim();
        const emailVal = emailInput.value.trim();
        const messageVal = messageInput.value.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (nameVal.length < 2) {
            showError(nameInput, "Please enter your name (at least 2 characters).");
            return;
        }

        if (!emailPattern.test(emailVal)) {
            showError(emailInput, "Please enter a valid email address.");
            return;
        }

        if (messageVal.length < 10) {
            showError(messageInput, "Message must be at least 10 characters long.");
            return;
        }

        showSuccess("✅ Thank you, " + nameVal + "! Your message has been validated successfully.");
        contactForm.reset();
    });
}

/* =========================
   IMAGE MODAL (Projects Page)
========================= */
const projectImages = document.querySelectorAll(".project-card img");

if (projectImages.length > 0) {
    const modal = document.createElement("div");
    modal.className = "image-modal";
    modal.innerHTML = '<span class="modal-close">&times;</span><img class="modal-img" src="" alt="Preview">';
    document.body.appendChild(modal);

    const modalImg = modal.querySelector(".modal-img");
    const modalClose = modal.querySelector(".modal-close");

    projectImages.forEach(function (img) {
        img.style.cursor = "zoom-in";
        img.addEventListener("click", function () {
            modal.style.display = "flex";
            modalImg.src = img.src;
            modalImg.alt = img.alt;
        });
    });

    modalClose.addEventListener("click", function () {
        modal.style.display = "none";
    });

    modal.addEventListener("click", function (e) {
        if (e.target === modal) modal.style.display = "none";
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") modal.style.display = "none";
    });
}

/* =========================
   SCROLL REVEAL ANIMATION
========================= */
const revealTargets = document.querySelectorAll(".skill-card, .project-card, .resume-item, .about-text, .resume-block, .contact-info, .contact-form");

if (revealTargets.length > 0) {
    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    revealTargets.forEach(function (card, i) {
        card.classList.add("fade-in");
        card.style.transitionDelay = (i % 4) * 0.08 + "s";
        observer.observe(card);
    });
}