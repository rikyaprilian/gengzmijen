// ======================================
// Homepage Render Engine
// Bertugas menentukan visibility card
// berdasarkan state.search & state.category
// ======================================

import state from "./state";

export default function renderCards() {

    const cards = document.querySelectorAll("[data-card]");

    cards.forEach(card => {

        const keyword = (state.search ?? "")
            .trim()
            .toLowerCase();

        const activeCategory = state.category ?? "all";

        const search = (card.dataset.search ?? "")
            .toLowerCase();

        const categories = (card.dataset.category ?? "")
            .split(",")
            .map(item => item.trim());

        const matchSearch =
            keyword === "" ||
            search.includes(keyword);

        const matchCategory =
            activeCategory === "all" ||
            categories.includes(activeCategory);

        card.style.display =
            matchSearch && matchCategory
                ? ""
                : "none";

    });
    

}
document.addEventListener("DOMContentLoaded", renderCards);

