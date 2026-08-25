import state from "../homepage/state";
import { renderEditModeElements } from "../homepage/render";
import Sortable from "sortablejs";

// Simpan semua instance Sortable agar bisa dihancurkan saat mode edit dinonaktifkan
let sortableInstances = [];
let isProcessingDrag = false;

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
    isProcessingDrag = false;

    // 1. Sortable untuk Cards Container (Root)
    const cardsContainer = document.querySelector(".cards-sortable-container");
    if (cardsContainer) {
        const cardSortable = new Sortable(cardsContainer, {
            animation: 150,
            handle: ".card-drag-handle",
            draggable: "[data-card]",
            ghostClass: "sortable-ghost",
            chosenClass: "sortable-chosen",
            group: {
                name: "cards",
                put: ["links"], // Menerima link yang ditarik keluar dari grup menjadi kartu baru
            },
            onEnd: async (evt) => {
                // Reorder kartu biasa di level root
                if (evt.item.hasAttribute("data-card")) {
                    const uuids = [...cardsContainer.querySelectorAll(":scope > [data-card][data-uuid]")]
                        .map(el => el.dataset.uuid)
                        .filter(Boolean);
                    saveCardOrder(uuids);
                }
            },
        });
        sortableInstances.push(cardSortable);
    }

    // 2. Sortable untuk Links Container (di dalam Group Card & Single Card)
    document.querySelectorAll(".links-sortable-container").forEach(container => {
        const linkSortable = new Sortable(container, {
            animation: 150,
            handle: ".link-drag-handle",
            draggable: "[data-link]",
            ghostClass: "sortable-ghost",
            chosenClass: "sortable-chosen",
            group: {
                name: "links",
                pull: true,
                put: ["links"], // Menerima link dari kartu mana pun
            },
            onEnd: async (evt) => {
                const linkUuid     = evt.item.dataset.uuid;
                const fromCardUuid = evt.from.dataset.cardUuid;
                const toCardUuid   = evt.to.dataset.cardUuid;

                // Kasus A: Link dijatuhkan keluar ke container kartu utama (detach jadi Single Card baru)
                if (!toCardUuid || evt.to.classList.contains("cards-sortable-container")) {
                    const rootCardsContainer = document.querySelector(".cards-sortable-container");
                    const cardOrder = [...rootCardsContainer.children]
                        .map(el => {
                            if (el.hasAttribute("data-card")) return el.dataset.uuid;
                            if (el.hasAttribute("data-link")) return el.dataset.uuid;
                            return null;
                        })
                        .filter(Boolean);

                    await detachLinkToCard(linkUuid, evt.newIndex, cardOrder);
                    return;
                }

                // Kasus B: Link berpindah ke kartu lain (Single Card lain atau Group Card lain)
                if (fromCardUuid !== toCardUuid) {
                    const newOrder = [...evt.to.querySelectorAll("[data-link][data-uuid]")]
                        .map(el => el.dataset.uuid)
                        .filter(Boolean);
                    await moveLinkToCard(linkUuid, toCardUuid, newOrder);
                } else {
                    // Kasus C: Hanya reorder dalam kartu yang sama
                    const newOrder = [...container.querySelectorAll("[data-link][data-uuid]")]
                        .map(el => el.dataset.uuid)
                        .filter(Boolean);
                    await saveLinkOrder(newOrder);
                }
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

async function moveLinkToCard(linkUuid, cardUuid, newOrder) {
    if (isProcessingDrag) return;
    isProcessingDrag = true;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    try {
        const res = await fetch(`/manage/links/${linkUuid}/move`, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({ card_uuid: cardUuid, order: newOrder }),
        });
        const data = await res.json();
        if (data.success) {
            sessionStorage.setItem("portal_keep_edit_mode", "1");
            window.location.reload();
        } else {
            console.error("Gagal memindahkan tautan:", data.message);
            isProcessingDrag = false;
        }
    } catch (e) {
        console.error("Gagal memindahkan tautan:", e);
        isProcessingDrag = false;
    }
}

async function detachLinkToCard(linkUuid, newIndex, cardOrder) {
    if (isProcessingDrag) return;
    isProcessingDrag = true;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    try {
        const res = await fetch(`/manage/links/${linkUuid}/detach`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({ new_index: newIndex, card_order: cardOrder }),
        });
        const data = await res.json();
        if (data.success) {
            sessionStorage.setItem("portal_keep_edit_mode", "1");
            window.location.reload();
        } else {
            console.error("Gagal memisahkan tautan:", data.message);
            isProcessingDrag = false;
        }
    } catch (e) {
        console.error("Gagal memisahkan tautan:", e);
        isProcessingDrag = false;
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