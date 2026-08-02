document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".parent-toggle").forEach(parent => {

        parent.addEventListener("click", () => {

            const wrapper = parent.nextElementSibling;

            const arrow = parent.querySelector(".parent-arrow");

            wrapper.classList.toggle("d-none");

            arrow.classList.toggle("rotate");

        });

    });

});