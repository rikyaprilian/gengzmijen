document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll("[data-accordion]").forEach(header => {

        header.addEventListener("click", (e) => {
            if (e.target.closest("button") || e.target.closest("a") || e.target.closest(".card-drag-handle") || e.target.closest(".link-drag-handle") || e.target.closest(".edit-mode-actions")) {
                return;
            }

            const group = header.closest(".portal-group");

            const body = group.querySelector("[data-body]");

            const arrow = group.querySelector("[data-arrow]");

            const isOpen = body.classList.contains("open");

            if (isOpen) {

                body.style.maxHeight = "0px";
                body.classList.remove("open");

            } else {

                body.classList.add("open");
                body.style.maxHeight = body.scrollHeight + "px";

            }

            arrow.classList.toggle("rotate");

        });

    });

});