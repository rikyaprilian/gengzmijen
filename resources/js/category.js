import state from "./homepage/state";
import renderCards from "./homepage/render";

document.addEventListener("DOMContentLoaded", () => {

    const buttons = document.querySelectorAll(".category-chip");

    buttons.forEach(button => {

        button.addEventListener("click", () => {

            state.category = button.dataset.category;

            buttons.forEach(btn => btn.classList.remove("active"));

            button.classList.add("active");

            renderCards();

        });

    });

});