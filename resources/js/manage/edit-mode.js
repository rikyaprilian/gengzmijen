import state from "../homepage/state";

export function setEditMode(enabled) {

    state.editMode = enabled;

    updateNavbar();

    console.log("Edit Mode:", enabled);

}

function updateNavbar() {

    const text = document.getElementById("portalManageButton");

    if (!text) return;

    text.textContent = state.editMode
        ? "Selesai"
        : "Kelola Portal";

}