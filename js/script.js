document.addEventListener("DOMContentLoaded", function () {

    const menuToggle = document.querySelector(".menu-toggle");
    const navLinks = document.querySelector(".nav-links");

    if (menuToggle && navLinks) {
        menuToggle.addEventListener("click", function () {
            navLinks.classList.toggle("open");
            const isOpen = navLinks.classList.contains("open");

            menuToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
            menuToggle.textContent = isOpen ? "✕" : "☰";
        });

        navLinks.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", function () {
                navLinks.classList.remove("open");
                menuToggle.setAttribute("aria-expanded", "false");
                menuToggle.textContent = "☰";
            });
        });
    }

    const themeToggle = document.querySelector(".theme-toggle");
    const savedTheme = localStorage.getItem("portfolio-theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark");
    }

    function updateThemeIcon() {
        if (!themeToggle) {
            return;
        }

        themeToggle.textContent = document.body.classList.contains("dark") ? "☀" : "☾";
    }

    updateThemeIcon();

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            document.body.classList.toggle("dark");
            const darkMode = document.body.classList.contains("dark");
            localStorage.setItem("portfolio-theme", darkMode ? "dark" : "light");
            updateThemeIcon();
        });
    }

    const typingText = document.querySelector("#typing-text");

    if (typingText) {
        const words = ["Student", "Web Developer", "Designer", "Problem Solver"];
        let wordIndex = 0;
        let charIndex = 0;
        let deleting = false;

        function typeText() {
            const currentWord = words[wordIndex];

            if (!deleting) {
                typingText.textContent = currentWord.substring(0, charIndex + 1);
                charIndex++;

                if (charIndex === currentWord.length) {
                    deleting = true;
                    setTimeout(typeText, 1300);
                    return;
                }
            } else {
                typingText.textContent = currentWord.substring(0, charIndex - 1);
                charIndex--;

                if (charIndex === 0) {
                    deleting = false;
                    wordIndex = (wordIndex + 1) % words.length;
                }
            }

            setTimeout(typeText, deleting ? 60 : 100);
        }

        typeText();
    }

    const filterButtons = document.querySelectorAll(".filter-btn");
    const projectCards = document.querySelectorAll(".project-card");

    filterButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            filterButtons.forEach(function (btn) {
                btn.classList.remove("active");
            });

            button.classList.add("active");
            const filter = button.dataset.filter;

            projectCards.forEach(function (card) {
                card.style.display = filter === "all" || card.dataset.category === filter ? "block" : "none";
            });
        });
    });

    const modal = document.querySelector("#image-modal");
    const modalImage = document.querySelector("#modal-image");
    const modalTitle = document.querySelector("#modal-title");
    const modalClose = document.querySelector(".modal-close");
    const modalButtons = document.querySelectorAll(".modal-trigger");

    if (modal && modalImage && modalTitle) {
        modalButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                modalImage.src = button.dataset.image || "";
                modalImage.alt = button.dataset.title || "Project preview";
                modalTitle.textContent = button.dataset.title || "";
                modal.classList.add("show");
            });
        });

        function closeModal() {
            modal.classList.remove("show");
        }

        if (modalClose) {
            modalClose.addEventListener("click", closeModal);
        }

        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && modal.classList.contains("show")) {
                closeModal();
            }
        });
    }

    const scrollTop = document.querySelector(".scroll-top");

    if (scrollTop) {
        window.addEventListener("scroll", function () {
            scrollTop.classList.toggle("show", window.scrollY > 400);
        });

        scrollTop.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }

    const form = document.querySelector("#contact-form");

    if (form) {
        form.addEventListener("submit", function (event) {
            event.preventDefault();

            const name = form.querySelector("#name");
            const email = form.querySelector("#email");
            const subject = form.querySelector("#subject");
            const message = form.querySelector("#message");
            const success = form.querySelector("#form-success");

            if (!name || !email || !subject || !message) {
                return;
            }

            form.querySelectorAll(".error").forEach(function (error) {
                error.textContent = "";
            });

            if (success) {
                success.textContent = "";
            }

            let valid = true;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (name.value.trim().length < 2) {
                const error = form.querySelector("#name-error");
                if (error) error.textContent = "Please enter your name.";
                valid = false;
            }

            if (!emailPattern.test(email.value.trim())) {
                const error = form.querySelector("#email-error");
                if (error) error.textContent = "Please enter a valid email.";
                valid = false;
            }

            if (subject.value.trim().length < 3) {
                const error = form.querySelector("#subject-error");
                if (error) error.textContent = "Please enter a subject.";
                valid = false;
            }

            if (message.value.trim().length < 10) {
                const error = form.querySelector("#message-error");
                if (error) error.textContent = "Message must be at least 10 characters.";
                valid = false;
            }

            if (valid && success) {
                success.textContent = "Message validated successfully!";
                form.reset();
            }
        });
    }
});
