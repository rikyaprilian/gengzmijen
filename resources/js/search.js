import state from "./homepage/state";
import renderCards from "./homepage/render";

document.addEventListener("DOMContentLoaded", () => {

    const input = document.getElementById("portal-search");

    if (!input) return;

    input.addEventListener("input", () => {

        state.search = input.value
            .trim()
            .toLowerCase();

        renderCards();

    });

    // Render pertama
    renderCards();

});