
document.addEventListener("DOMContentLoaded", function () {

    const menuToggle =
        document.querySelector(".menu-toggle");

    const navLinks =
        document.querySelector(".nav-links");


    if (menuToggle && navLinks) {

        menuToggle.addEventListener("click", function () {

            navLinks.classList.toggle("open");

            const isOpen =
                navLinks.classList.contains("open");


            menuToggle.setAttribute(
                "aria-expanded",
                isOpen
            );


            menuToggle.textContent =
                isOpen ? "✕" : "☰";

        });

        navLinks.querySelectorAll("a").forEach(function (link) {

            link.addEventListener("click", function () {

                navLinks.classList.remove("open");

                menuToggle.setAttribute(
                    "aria-expanded",
                    "false"
                );

                menuToggle.textContent = "☰";

            });

        });

    }

    const themeToggle =
        document.querySelector(".theme-toggle");


    const savedTheme =
        localStorage.getItem("portfolio-theme");


    if (savedTheme === "dark") {

        document.body.classList.add("dark");

    }


    function updateThemeIcon() {

        if (!themeToggle) {
            return;
        }


        if (document.body.classList.contains("dark")) {

            themeToggle.textContent = "☀";

        } else {

            themeToggle.textContent = "☾";

        }

    }


    updateThemeIcon();


    if (themeToggle) {

        themeToggle.addEventListener("click", function () {

            document.body.classList.toggle("dark");


            const darkMode =
                document.body.classList.contains("dark");


            localStorage.setItem(
                "portfolio-theme",
                darkMode ? "dark" : "light"
            );


            updateThemeIcon();

        });

    }

    const typingText =
        document.querySelector("#typing-text");


    if (typingText) {

        const words = [
            "Student",
            "Web Developer",
            "Designer",
            "Problem Solver"
        ];


        let wordIndex = 0;

        let charIndex = 0;

        let deleting = false;


        function typeText() {

            const currentWord =
                words[wordIndex];


            if (!deleting) {

                typingText.textContent =
                    currentWord.substring(
                        0,
                        charIndex + 1
                    );

                charIndex++;


                if (charIndex === currentWord.length) {

                    deleting = true;

                    setTimeout(
                        typeText,
                        1300
                    );

                    return;
                }

            } else {

                typingText.textContent =
                    currentWord.substring(
                        0,
                        charIndex - 1
                    );

                charIndex--;


                if (charIndex === 0) {

                    deleting = false;

                    wordIndex =
                        (wordIndex + 1)
                        % words.length;

                }

            }


            setTimeout(
                typeText,
                deleting ? 60 : 100
            );

        }


        typeText();

    }

    const filterButtons =
        document.querySelectorAll(".filter-btn");


    const projectCards =
        document.querySelectorAll(".project-card");


    filterButtons.forEach(function (button) {

        button.addEventListener("click", function () {


            filterButtons.forEach(function (btn) {

                btn.classList.remove("active");

            });


            button.classList.add("active");


            const filter =
                button.dataset.filter;


            projectCards.forEach(function (card) {


                if (
                    filter === "all" ||
                    card.dataset.category === filter
                ) {

                    card.style.display = "block";

                } else {

                    card.style.display = "none";

                }

            });

        });

    });

    const modal =
        document.querySelector("#image-modal");


    const modalImage =
        document.querySelector("#modal-image");


    const modalTitle =
        document.querySelector("#modal-title");


    const modalClose =
        document.querySelector(".modal-close");


    const modalButtons =
        document.querySelectorAll(".modal-trigger");


    modalButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            modalImage.src =
                button.dataset.image;


            modalImage.alt =
                button.dataset.title;


            modalTitle.textContent =
                button.dataset.title;


            modal.classList.add("show");

        });

    });


    function closeModal() {

        if (modal) {

            modal.classList.remove("show");

        }

    }


    if (modalClose) {

        modalClose.addEventListener(
            "click",
            closeModal
        );

    }


    if (modal) {

        modal.addEventListener(
            "click",
            function (event) {

                if (event.target === modal) {

                    closeModal();

                }

            }
        );

    }

    const scrollTop =
        document.querySelector(".scroll-top");


    window.addEventListener(
        "scroll",
        function () {

            if (!scrollTop) {
                return;
            }


            if (window.scrollY > 400) {

                scrollTop.classList.add("show");

            } else {

                scrollTop.classList.remove("show");

            }

        }
    );


    if (scrollTop) {

        scrollTop.addEventListener(
            "click",
            function () {

                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });

            }
        );

    }

    const form =
        document.querySelector("#contact-form");


    if (form) {

        form.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();


                const name =
                    document.querySelector("#name");


                const email =
                    document.querySelector("#email");


                const subject =
                    document.querySelector("#subject");


                const message =
                    document.querySelector("#message");


                const success =
                    document.querySelector("#form-success");


                document.querySelectorAll(".error")
                    .forEach(function (error) {

                        error.textContent = "";

                    });


                success.textContent = "";


                let valid = true;


                const emailPattern =
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (name.value.trim().length < 2) {

                    document.querySelector(
                        "#name-error"
                    ).textContent =
                        "Please enter your name.";

                    valid = false;

                }

                if (
                    !emailPattern.test(
                        email.value.trim()
                    )
                ) {

                    document.querySelector(
                        "#email-error"
                    ).textContent =
                        "Please enter a valid email.";

                    valid = false;

                }

                if (
                    subject.value.trim().length < 3
                ) {

                    document.querySelector(
                        "#subject-error"
                    ).textContent =
                        "Please enter a subject.";

                    valid = false;

                }

                if (
                    message.value.trim().length < 10
                ) {

                    document.querySelector(
                        "#message-error"
                    ).textContent =
                        "Message must be at least 10 characters.";

                    valid = false;

                }

                if (valid) {

                    success.textContent =
                        "Message validated successfully!";

                    form.reset();

                }

            }
        );

    }

});