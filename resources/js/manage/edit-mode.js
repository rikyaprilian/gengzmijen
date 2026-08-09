import state from "../homepage/state";
import { renderEditModeElements } from "../homepage/render";
import Sortable from "sortablejs";

// Simpan semua instance Sortable agar bisa dihancurkan saat mode edit dinonaktifkan
let sortableInstances = [];

export function setEditMode(enabled) {

    state.editMode = enabled;

    updateNavbar();

    renderEditModeElements();

    if (enabled) {
        initSortable();
    } else {
        destroySortable();
    }

    console.log("Edit Mode:", enabled ? "AKTIF" : "NONAKTIF");

}

function updateNavbar() {

    const btnIcon = document.getElementById("manageBtnIcon");
    const btnText = document.getElementById("manageBtnText");
    const manageBtn = document.getElementById("portalManageButton");

    if (!btnText) return;

    if (state.editMode) {
        if (btnIcon) btnIcon.className = "bi bi-check-circle-fill me-1";
        btnText.textContent = "Selesai";
        manageBtn.dataset.bsToggle = "";
        manageBtn.dataset.bsTarget = "";
        manageBtn.onclick = (e) => {
            e.preventDefault();
            setEditMode(false);
            logoutEditMode();
        };
    } else {
        if (btnIcon) btnIcon.className = "bi bi-gear-fill me-1";
        btnText.textContent = "Kelola Portal";
        manageBtn.dataset.bsToggle = "modal";
        manageBtn.dataset.bsTarget = "#manageModal";
        manageBtn.onclick = null;
    }

}

function initSortable() {

    destroySortable(); // Pastikan tidak ada duplikat

    // Sortable untuk urutan Cards (level atas)
    const cardsContainer = document.querySelector(".cards-sortable-container");
    if (cardsContainer) {
        const cardSortable = new Sortable(cardsContainer, {
            animation: 150,
            handle: ".card-drag-handle",
            draggable: "[data-card]",
            ghostClass: "sortable-ghost",
            chosenClass: "sortable-chosen",
            onEnd: (evt) => {
                const uuids = [...cardsContainer.querySelectorAll("[data-card][data-uuid]")]
                    .map(el => el.dataset.uuid)
                    .filter(Boolean);
                saveCardOrder(uuids);
            },
        });
        sortableInstances.push(cardSortable);
    }

    // Sortable untuk urutan Links di dalam setiap card group
    document.querySelectorAll(".links-sortable-container").forEach(container => {
        const linkSortable = new Sortable(container, {
            animation: 150,
            handle: ".link-drag-handle",
            draggable: "[data-link]",
            ghostClass: "sortable-ghost",
            chosenClass: "sortable-chosen",
            onEnd: (evt) => {
                const uuids = [...container.querySelectorAll("[data-link][data-uuid]")]
                    .map(el => el.dataset.uuid)
                    .filter(Boolean);
                saveLinkOrder(uuids);
            },
        });
        sortableInstances.push(linkSortable);
    });

}

function destroySortable() {
    sortableInstances.forEach(s => s.destroy());
    sortableInstances = [];
}

async function saveCardOrder(uuids) {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    try {
        await fetch("/manage/cards/reorder", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({ order: uuids }),
        });
    } catch (e) {
        console.error("Gagal menyimpan urutan kartu:", e);
    }
}

async function saveLinkOrder(uuids) {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    try {
        await fetch("/manage/links/reorder", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({ order: uuids }),
        });
    } catch (e) {
        console.error("Gagal menyimpan urutan tautan:", e);
    }
}

export async function logoutEditMode() {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    try {
        await fetch("/manage/logout", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
        });
    } catch (e) {
        console.error("Gagal logout:", e);
    }
}