document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const modeToggle = document.querySelector(".mode-toggle");
    const sidebar = document.querySelector("nav");
    const sidebarToggle = document.querySelector(".sidebar-toggle");
    const overlay = document.querySelector(".sidebar-overlay");

    /* ===========================
       🌙 DARK MODE
    ============================ */
    if (modeToggle) {
        // load state from localStorage
        if (localStorage.getItem("theme") === "dark") {
            body.classList.add("dark");
        }

        modeToggle.addEventListener("click", (e) => {
            e.preventDefault();
            body.classList.toggle("dark");
            localStorage.setItem(
                "theme",
                body.classList.contains("dark") ? "dark" : "light"
            );
        });
    }

    /* ===========================
       📌 SIDEBAR TOGGLE
    ============================ */
    if (sidebar && sidebarToggle) {
        const handleDesktopState = () => {
            if (window.innerWidth > 768) {
                // load sidebar state desktop
                if (localStorage.getItem("sidebar") === "close") {
                    sidebar.classList.add("close");
                } else {
                    sidebar.classList.remove("close");
                }
            }
        };

        // set initial state
        handleDesktopState();

        sidebarToggle.addEventListener("click", () => {
            if (window.innerWidth <= 768) {
                // Mode Mobile
                sidebar.classList.toggle("active");
                overlay?.classList.toggle("active");
            } else {
                // Mode Desktop
                sidebar.classList.toggle("close");
                localStorage.setItem(
                    "sidebar",
                    sidebar.classList.contains("close") ? "close" : "open"
                );
            }
        });

        // Overlay click to close sidebar (mobile only)
        if (overlay) {
            overlay.addEventListener("click", () => {
                sidebar.classList.remove("active");
                overlay.classList.remove("active");
            });
        }

        // Re-run when screen resized  
        window.addEventListener("resize", handleDesktopState);
    }
});
