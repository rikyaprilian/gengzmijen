// ======================================
// Homepage Render Engine
// Bertugas menentukan visibility card
// berdasarkan state.search & state.category
// Serta toggle edit-mode UI elements
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

export function renderEditModeElements() {

    // Gunakan class 'edit-mode-hidden' (bukan inline style) karena Bootstrap d-flex
    // memakai display: flex !important yang mengalahkan inline style.
    // Class .edit-mode-hidden di manage.css memakai display: none !important
    // dan diimport SETELAH bootstrap, sehingga selalu menang.
    const editOnlyEls = document.querySelectorAll(".edit-mode-only");
    editOnlyEls.forEach(el => {
        el.classList.toggle("edit-mode-hidden", !state.editMode);
    });

    // Toggle class pada body (untuk styling tambahan via CSS)
    document.body.classList.toggle("is-edit-mode", state.editMode);

}

document.addEventListener("DOMContentLoaded", () => {

    // Inisialisasi: sembunyikan SEMUA elemen edit-mode-only saat halaman pertama dibuka.
    // Karena state.editMode = false, ini akan set style.display = "none" secara inline,
    // mengalahkan Bootstrap's d-flex !important.
    renderEditModeElements();

    renderCards();

});
