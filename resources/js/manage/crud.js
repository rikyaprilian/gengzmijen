// ======================================================
// Manage CRUD Module
// Menangani semua operasi AJAX CRUD dari Edit Mode:
// - Card (tambah, edit, hapus)
// - Link (tambah, edit, hapus)
// - Category (tambah, edit, hapus)
// - Settings (update)
// ======================================================

import Swal from "sweetalert2";

const csrf = () =>
    document.querySelector('meta[name="csrf-token"]').content;

// ==========================================
// Inisialisasi semua event listener CRUD
// ==========================================
document.addEventListener("DOMContentLoaded", () => {

    initCardEvents();
    initUnifiedModal();
    initLinkEvents();
    initCategoryEvents();
    initSettingsEvents();
    initIconPicker();
    initSecurityCodeToggle();

});

// ==========================================
// HELPER: AJAX request
// ==========================================
async function apiRequest(url, method, body = null) {
    const options = {
        method,
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrf(),
        },
    };
    if (body) options.body = JSON.stringify(body);

    const response = await fetch(url, options);
    return response.json();
}

// ==========================================
// HELPER: Reload halaman setelah aksi sukses
// ==========================================
function reloadAfterSuccess(message) {
    sessionStorage.setItem("portal_keep_edit_mode", "1");
    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: message,
        timer: 1200,
        showConfirmButton: false,
    }).then(() => window.location.reload());
}

// ==========================================
// CARD EVENTS
// ==========================================
function initCardEvents() {

    // Buka modal Tambah Card
    document.addEventListener("click", (e) => {
        const btn = e.target.closest("#btnAddCardModal");
        if (!btn) return;
        openCardModal();
    });

    // Buka modal Edit Card
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-edit-card");
        if (!btn) return;
        openCardModal({
            uuid:        btn.dataset.uuid,
            title:       btn.dataset.title,
            description: btn.dataset.description,
            badge:       btn.dataset.badge,
            color:       btn.dataset.color,
            expired_at:  btn.dataset.expiredAt,
            categories:  JSON.parse(btn.dataset.categories || "[]"),
        });
    });

    // Hapus Card
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-delete-card");
        if (!btn) return;
        const { uuid, title } = btn.dataset;

        const confirm = await Swal.fire({
            icon: "warning",
            title: "Hapus Kartu?",
            html: `Apakah Anda yakin ingin menghapus kartu <strong>"${title}"</strong>?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>`,
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal",
        });

        if (!confirm.isConfirmed) return;

        try {
            const result = await apiRequest(`/manage/cards/${uuid}`, "DELETE");
            if (result.success) {
                reloadAfterSuccess("Kartu berhasil dihapus.");
            } else {
                Swal.fire("Gagal", result.message ?? "Terjadi kesalahan.", "error");
            }
        } catch (err) {
            Swal.fire("Error", "Terjadi kesalahan koneksi.", "error");
        }
    });

    // Tambah Tautan ke Card (dari card group header)
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-add-link-to-card");
        if (!btn) return;
        openLinkModal({ card_uuid: btn.dataset.cardUuid });
    });

    // Submit form Card
    const cardForm = document.getElementById("cardForm");
    if (cardForm) {
        cardForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            await saveCard();
        });
    }

}

function openCardModal(data = null) {
    const modal = new bootstrap.Modal(document.getElementById("cardModal"));
    const modalTitle = document.getElementById("cardModalTitle");
    const unifiedForm = document.getElementById("unifiedLinkForm");
    const editForm = document.getElementById("cardForm");

    if (data) {
        // ── MODE EDIT: tampilkan form lama, sembunyikan unified form ──
        unifiedForm?.classList.add("d-none");
        editForm?.classList.remove("d-none");

        modalTitle.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Kartu Portal';

        document.getElementById("cardUuid").value          = data.uuid ?? "";
        document.getElementById("cardTitle").value         = data.title ?? "";
        document.getElementById("cardDescription").value   = data.description ?? "";
        document.getElementById("cardBadgeEdit").value     = data.badge ?? "";
        document.getElementById("cardExpiredAtEdit").value = data.expired_at ?? "";
        const colorEdit = document.getElementById("cardColorEdit");
        if (colorEdit) colorEdit.value = data.color ?? "";

        // Set checkboxes kategori (edit form)
        document.querySelectorAll('input[id^="cat_check_edit_"]').forEach(cb => {
            cb.checked = data.categories?.includes(parseInt(cb.value)) || false;
        });
    } else {
        // ── MODE TAMBAH: tampilkan unified form, sembunyikan edit form ──
        editForm?.classList.add("d-none");
        unifiedForm?.classList.remove("d-none");

        modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tambah Link / Group Link';

        // Reset unified form
        resetUnifiedForm();
    }

    modal.show();
}

// ==========================================
// UNIFIED MODAL LOGIC
// ==========================================
let linkRowCount = 0;

function initUnifiedModal() {
    // Tombol tambah link row
    document.addEventListener("click", (e) => {
        if (!e.target.closest("#btnAddLinkRow")) return;
        addLinkRow();
        updateGroupSection();
    });

    // Hapus link row (delegasi)
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-remove-link-row");
        if (!btn) return;
        const row = btn.closest(".link-row-item");
        if (row) row.remove();
        updateGroupSection();
        renumberLinkRows();
    });

    // Submit unified form
    const form = document.getElementById("unifiedLinkForm");
    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            await saveUnifiedLinks();
        });
    }
}

function resetUnifiedForm() {
    linkRowCount = 0;
    const container = document.getElementById("linkRowsContainer");
    if (container) container.innerHTML = "";

    // Reset pengaturan lanjutan
    ["cardBadge", "cardExpiredAt"].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = "";
    });
    const colorEl = document.getElementById("cardColor");
    if (colorEl) colorEl.value = "";

    document.querySelectorAll('#cardCategoriesContainer input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });

    // Reset group section
    document.getElementById("groupTitle").value = "";
    document.getElementById("groupDescription").value = "";
    document.getElementById("groupSection")?.classList.add("d-none");

    // Tutup advanced collapse
    const advanced = document.getElementById("advancedCardSettings");
    if (advanced) advanced.classList.remove("show");

    // Tambah 1 link row awal
    addLinkRow();
}

function addLinkRow() {
    linkRowCount++;
    const idx = linkRowCount;
    const container = document.getElementById("linkRowsContainer");
    if (!container) return;

    const row = document.createElement("div");
    row.className = "link-row-item border rounded p-3 mb-3 bg-light";
    row.dataset.rowIdx = idx;
    row.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-muted small">Link <span class="link-row-num">${idx}</span></span>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-link-row" title="Hapus link ini">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="mb-2">
            <label class="form-label small fw-semibold">Judul Link <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm link-title-input"
                   placeholder="Contoh: SIPGN, Absensi Harian" required>
        </div>
        <div class="mb-2">
            <label class="form-label small fw-semibold">URL <span class="text-danger">*</span></label>
            <input type="url" class="form-control form-control-sm link-url-input"
                   placeholder="https://" required>
        </div>
        <div class="mb-2">
            <label class="form-label small fw-semibold">Sub-judul / Keterangan (Opsional)</label>
            <input type="text" class="form-control form-control-sm link-subtitle-input"
                   placeholder="Contoh: Sistem Informasi Kepegawaian">
        </div>
        <div class="row g-2">
            <div class="col">
                <label class="form-label small fw-semibold">Ikon</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text link-icon-preview-wrap" style="min-width:36px;">
                        <i class="bi bi-link-45deg link-icon-preview"></i>
                    </span>
                    <input type="text" class="form-control link-icon-input"
                           placeholder="ri-file-line atau URL gambar" value="link-45deg">
                    <button type="button" class="btn btn-outline-secondary btn-sm btn-pick-icon"
                            title="Pilih dari icon picker">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </button>
                </div>
            </div>
            <div class="col-auto">
                <label class="form-label small fw-semibold">Warna</label>
                <select class="form-select form-select-sm link-color-input">
                    <option value="">Default</option>
                    <option value="blue">🔵 Blue</option>
                    <option value="emerald">🟢 Emerald</option>
                    <option value="purple">🟣 Purple</option>
                    <option value="amber">🟡 Amber</option>
                    <option value="rose">🔴 Rose</option>
                    <option value="cyan">🔵 Cyan</option>
                    <option value="orange">🟠 Orange</option>
                    <option value="indigo">🔮 Indigo</option>
                </select>
            </div>
        </div>
    `;

    container.appendChild(row);

    // Live update icon preview di row ini
    const iconInput = row.querySelector(".link-icon-input");
    const iconPreview = row.querySelector(".link-icon-preview");
    const iconWrap = row.querySelector(".link-icon-preview-wrap");
    if (iconInput && iconPreview) {
        iconInput.addEventListener("input", () => {
            updateRowIconPreview(iconInput.value.trim(), iconPreview, iconWrap);
        });
    }

    // Live update saat judul diketik (untuk auto-fill group title jika hanya 1 link)
    const titleInput = row.querySelector(".link-title-input");
    if (titleInput) {
        titleInput.addEventListener("input", () => {
            const rows = document.querySelectorAll(".link-row-item");
            if (rows.length === 1) {
                // Tidak ada group title saat 1 link, tapi tidak perlu sync apapun
            }
        });
    }
}

function updateRowIconPreview(iconName, previewEl, wrapEl) {
    if (!previewEl) return;
    const isUrl = iconName.startsWith("http") || iconName.includes("/");
    const isFa = iconName.startsWith("fa-") || iconName.startsWith("ri-");

    if (isUrl) {
        if (wrapEl) wrapEl.innerHTML = `<img src="${iconName}" style="width:20px;height:20px;object-fit:contain;">`;
    } else if (isFa) {
        if (wrapEl) wrapEl.innerHTML = `<i class="${iconName}"></i>`;
    } else {
        if (wrapEl) wrapEl.innerHTML = `<i class="bi bi-${iconName}"></i>`;
    }
}

function updateGroupSection() {
    const rows = document.querySelectorAll(".link-row-item");
    const groupSection = document.getElementById("groupSection");
    const groupTitleInput = document.getElementById("groupTitle");

    if (rows.length >= 2) {
        groupSection?.classList.remove("d-none");
        if (groupTitleInput) groupTitleInput.required = true;
    } else {
        groupSection?.classList.add("d-none");
        if (groupTitleInput) groupTitleInput.required = false;
    }
}

function renumberLinkRows() {
    document.querySelectorAll(".link-row-item").forEach((row, i) => {
        const numEl = row.querySelector(".link-row-num");
        if (numEl) numEl.textContent = i + 1;
    });
}

async function saveUnifiedLinks() {
    const rows = document.querySelectorAll(".link-row-item");
    const isGroup = rows.length >= 2;

    // Kumpulkan data links
    const links = [];
    let valid = true;
    rows.forEach(row => {
        const title    = row.querySelector(".link-title-input")?.value.trim();
        const url      = row.querySelector(".link-url-input")?.value.trim();
        const subtitle = row.querySelector(".link-subtitle-input")?.value.trim();
        const icon     = row.querySelector(".link-icon-input")?.value.trim() || "link-45deg";
        const color    = row.querySelector(".link-color-input")?.value || "";

        if (!title || !url) { valid = false; return; }
        links.push({ title, url, subtitle, icon, color });
    });

    if (!valid || links.length === 0) {
        Swal.fire("Validasi", "Judul dan URL wajib diisi di semua link.", "warning");
        return;
    }

    // Tentukan judul card
    let cardTitle = "";
    let cardDescription = "";
    if (isGroup) {
        cardTitle = document.getElementById("groupTitle")?.value.trim();
        cardDescription = document.getElementById("groupDescription")?.value.trim();
        if (!cardTitle) {
            Swal.fire("Validasi", "Nama Group / Card wajib diisi karena ada lebih dari 1 link.", "warning");
            return;
        }
    } else {
        // Single link: judul card = judul link
        cardTitle = links[0].title;
    }

    // Pengaturan lanjutan
    const badge      = document.getElementById("cardBadge")?.value.trim() ?? "";
    const color      = document.getElementById("cardColor")?.value ?? "";
    const expired_at = document.getElementById("cardExpiredAt")?.value ?? "";
    const category_ids = [...document.querySelectorAll('#cardCategoriesContainer input[type="checkbox"]:checked')]
        .map(cb => parseInt(cb.value));

    const payload = {
        title: cardTitle,
        description: cardDescription,
        badge,
        color,
        expired_at,
        category_ids,
        links,
    };

    try {
        const result = await apiRequest("/manage/cards/with-links", "POST", payload);
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById("cardModal"))?.hide();
            reloadAfterSuccess(result.message);
        } else {
            Swal.fire("Gagal", result.message ?? "Terjadi kesalahan.", "error");
        }
    } catch (err) {
        Swal.fire("Error", "Terjadi kesalahan koneksi.", "error");
    }
}

async function saveCard() {
    const uuid = document.getElementById("cardUuid").value;
    const title = document.getElementById("cardTitle").value.trim();
    const description = document.getElementById("cardDescription").value.trim();
    const badge = document.getElementById("cardBadgeEdit")?.value.trim() ?? "";
    const color = document.getElementById("cardColorEdit")?.value ?? "";
    const expired_at = document.getElementById("cardExpiredAtEdit")?.value ?? "";
    const category_ids = [...document.querySelectorAll('input[id^="cat_check_edit_"]:checked')]
        .map(cb => parseInt(cb.value));


    if (!title) {
        Swal.fire("Validasi", "Judul kartu wajib diisi.", "warning");
        return;
    }

    const payload = { title, description, badge, color, expired_at, category_ids };

    try {
        let result;
        if (uuid) {
            result = await apiRequest(`/manage/cards/${uuid}`, "PUT", payload);
        } else {
            result = await apiRequest("/manage/cards", "POST", payload);
        }

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById("cardModal"))?.hide();
            reloadAfterSuccess(result.message);
        } else {
            Swal.fire("Gagal", result.message ?? "Terjadi kesalahan.", "error");
        }
    } catch (err) {
        Swal.fire("Error", "Terjadi kesalahan koneksi.", "error");
    }
}

// ==========================================
// LINK EVENTS
// ==========================================
function initLinkEvents() {

    // Buka modal Edit Link
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-edit-link");
        if (!btn) return;
        openLinkModal({
            uuid:       btn.dataset.uuid,
            title:      btn.dataset.title,
            subtitle:   btn.dataset.subtitle,
            url:        btn.dataset.url,
            icon:       btn.dataset.icon,
            color:      btn.dataset.color,
            expired_at: btn.dataset.expiredAt,
        });
    });

    // Hapus Link
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-delete-link");
        if (!btn) return;
        const { uuid, title } = btn.dataset;

        const confirm = await Swal.fire({
            icon: "warning",
            title: "Hapus Tautan?",
            html: `Apakah Anda yakin ingin menghapus tautan <strong>"${title}"</strong>?`,
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal",
        });

        if (!confirm.isConfirmed) return;

        try {
            const result = await apiRequest(`/manage/links/${uuid}`, "DELETE");
            if (result.success) {
                reloadAfterSuccess("Tautan berhasil dihapus.");
            } else {
                Swal.fire("Gagal", result.message ?? "Terjadi kesalahan.", "error");
            }
        } catch (err) {
            Swal.fire("Error", "Terjadi kesalahan koneksi.", "error");
        }
    });

    // Submit form Link
    const linkForm = document.getElementById("linkForm");
    if (linkForm) {
        linkForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            await saveLink();
        });
    }

}

function openLinkModal(data = null) {
    const modal = new bootstrap.Modal(document.getElementById("linkModal"));
    const title = document.getElementById("linkModalTitle");
    const uuidField = document.getElementById("linkUuid");
    const cardUuidField = document.getElementById("linkCardUuid");
    const titleField = document.getElementById("linkTitle");
    const subtitleField = document.getElementById("linkSubtitle");
    const urlField = document.getElementById("linkUrl");
    const iconField = document.getElementById("linkIcon");
    const colorField = document.getElementById("linkColor");
    const expiredField = document.getElementById("linkExpiredAt");

    title.innerHTML = data?.uuid
        ? '<i class="bi bi-pencil me-2"></i>Edit Tautan'
        : '<i class="bi bi-plus-circle me-2"></i>Tambah Tautan Baru';

    uuidField.value     = data?.uuid ?? "";
    cardUuidField.value = data?.card_uuid ?? "";
    titleField.value    = data?.title ?? "";
    subtitleField.value = data?.subtitle ?? "";
    urlField.value      = data?.url ?? "";
    if (colorField) colorField.value = data?.color ?? "";
    expiredField.value  = data?.expired_at ?? "";

    if (iconField && data?.icon) {
        iconField.value = data.icon;
        updateIconPreview(data.icon);
    }

    modal.show();
}

async function saveLink() {
    const uuid      = document.getElementById("linkUuid").value;
    const card_uuid = document.getElementById("linkCardUuid").value;
    const title     = document.getElementById("linkTitle").value.trim();
    const subtitle  = document.getElementById("linkSubtitle").value.trim();
    const url       = document.getElementById("linkUrl").value.trim();
    const icon      = document.getElementById("linkIcon").value;
    const color     = document.getElementById("linkColor")?.value ?? "";
    const expired_at = document.getElementById("linkExpiredAt").value;

    if (!title || !url) {
        Swal.fire("Validasi", "Judul dan URL wajib diisi.", "warning");
        return;
    }

    const payload = { title, subtitle, url, icon, color, expired_at };

    try {
        let result;
        if (uuid) {
            result = await apiRequest(`/manage/links/${uuid}`, "PUT", payload);
        } else {
            result = await apiRequest("/manage/links", "POST", { ...payload, card_uuid });
        }

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById("linkModal"))?.hide();
            reloadAfterSuccess(result.message);
        } else {
            Swal.fire("Gagal", result.message ?? "Terjadi kesalahan.", "error");
        }
    } catch (err) {
        Swal.fire("Error", "Terjadi kesalahan koneksi.", "error");
    }
}

// ==========================================
// CATEGORY EVENTS
// ==========================================
function initCategoryEvents() {

    // Buka modal Kategori dari toolbar
    document.addEventListener("click", (e) => {
        const btn = e.target.closest("#btnManageCategoriesModal");
        if (!btn) return;
        const modal = new bootstrap.Modal(document.getElementById("categoryModal"));
        modal.show();
    });

    // Edit kategori: isi form dengan data existing
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-edit-cat");
        if (!btn) return;
        const formTitle = document.getElementById("categoryFormTitle");
        const uuidField = document.getElementById("catUuid");
        const nameField = document.getElementById("catName");
        const iconField = document.getElementById("catIcon");
        const colorField = document.getElementById("catColor");
        const saveBtn   = document.getElementById("btnSaveCategory");

        formTitle.textContent = "Edit Kategori";
        uuidField.value = btn.dataset.uuid;
        nameField.value = btn.dataset.name;
        if (iconField) iconField.value = btn.dataset.icon;
        if (colorField) colorField.value = btn.dataset.color;
        saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Update';
        nameField.focus();
    });

    // Hapus kategori
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-delete-cat");
        if (!btn) return;
        const { uuid, name } = btn.dataset;

        const confirm = await Swal.fire({
            icon: "warning",
            title: "Hapus Kategori?",
            html: `Apakah Anda yakin ingin menghapus kategori <strong>"${name}"</strong>?`,
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal",
        });

        if (!confirm.isConfirmed) return;

        try {
            const result = await apiRequest(`/manage/categories/${uuid}`, "DELETE");
            if (result.success) {
                reloadAfterSuccess("Kategori berhasil dihapus.");
            } else {
                Swal.fire("Gagal", result.message ?? "Terjadi kesalahan.", "error");
            }
        } catch (err) {
            Swal.fire("Error", "Terjadi kesalahan koneksi.", "error");
        }
    });

    // Submit form kategori
    const categoryForm = document.getElementById("categoryForm");
    if (categoryForm) {
        categoryForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            await saveCategory();
        });
    }

}

async function saveCategory() {
    const uuid  = document.getElementById("catUuid").value;
    const name  = document.getElementById("catName").value.trim();
    const icon  = document.getElementById("catIcon")?.value;
    const color = document.getElementById("catColor")?.value;

    if (!name) {
        Swal.fire("Validasi", "Nama kategori wajib diisi.", "warning");
        return;
    }

    const payload = { name, icon, color };

    try {
        let result;
        if (uuid) {
            result = await apiRequest(`/manage/categories/${uuid}`, "PUT", payload);
        } else {
            result = await apiRequest("/manage/categories", "POST", payload);
        }

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById("categoryModal"))?.hide();
            reloadAfterSuccess(result.message);
        } else {
            Swal.fire("Gagal", result.message ?? "Terjadi kesalahan.", "error");
        }
    } catch (err) {
        Swal.fire("Error", "Terjadi kesalahan koneksi.", "error");
    }
}

// ==========================================
// SETTINGS EVENTS
// ==========================================
function initSettingsEvents() {

    // Buka modal Settings dari toolbar
    document.addEventListener("click", (e) => {
        const btn = e.target.closest("#btnPortalSettingsModal");
        if (!btn) return;
        const modal = new bootstrap.Modal(document.getElementById("settingsModal"));
        modal.show();
    });

    // Submit form settings
    const settingsForm = document.getElementById("settingsForm");
    if (settingsForm) {
        settingsForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            await saveSettings();
        });
    }

}

async function saveSettings() {
    const portal_name      = document.getElementById("settingPortalName").value.trim();
    const homepage_message = document.getElementById("settingHomepageMessage").value.trim();
    const security_code    = document.getElementById("settingSecurityCode").value.trim();

    if (!portal_name || !security_code) {
        Swal.fire("Validasi", "Nama Portal dan Security Code wajib diisi.", "warning");
        return;
    }

    try {
        const result = await apiRequest("/manage/settings", "POST", {
            portal_name,
            homepage_message,
            security_code,
        });

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById("settingsModal"))?.hide();
            reloadAfterSuccess(result.message);
        } else {
            Swal.fire("Gagal", result.message ?? "Terjadi kesalahan.", "error");
        }
    } catch (err) {
        Swal.fire("Error", "Terjadi kesalahan koneksi.", "error");
    }
}

// ==========================================
// ICON GRID PICKER — 200+ Ikon (Bootstrap, FontAwesome, Remix, Flaticon PNG)
// ==========================================
const ICON_LIST = [
    // Aplikasi & Brand Populer (FontAwesome & Remix)
    { name: "fa-brands fa-google",           label: "Google" },
    { name: "fa-brands fa-whatsapp",         label: "WhatsApp" },
    { name: "fa-brands fa-youtube",          label: "YouTube" },
    { name: "fa-brands fa-microsoft",        label: "Microsoft" },
    { name: "fa-brands fa-telegram",         label: "Telegram" },
    { name: "fa-brands fa-facebook",         label: "Facebook" },
    { name: "fa-brands fa-instagram",        label: "Instagram" },
    { name: "fa-brands fa-twitter",          label: "Twitter / X" },
    { name: "fa-brands fa-github",           label: "GitHub" },
    { name: "fa-brands fa-linkedin",         label: "LinkedIn" },
    { name: "fa-brands fa-chrome",           label: "Chrome" },
    { name: "fa-brands fa-android",          label: "Android" },
    { name: "fa-brands fa-apple",            label: "Apple" },
    { name: "ri-drive-line",                 label: "Google Drive" },
    { name: "ri-mail-unread-line",           label: "Gmail / Mail" },
    { name: "ri-shield-user-line",           label: "Keamanan User" },
    { name: "ri-file-chart-line",            label: "Laporan Chart" },
    { name: "ri-customer-service-2-line",    label: "Helpdesk CS" },

    // Umum & navigasi
    { name: "link-45deg",          label: "Link" },
    { name: "house",               label: "Beranda" },
    { name: "house-fill",          label: "Beranda Utama" },
    { name: "globe",               label: "Website Portal" },
    { name: "globe2",              label: "Dunia Net" },
    { name: "box-arrow-up-right",  label: "Link External" },
    { name: "arrow-right-circle",  label: "Panah Masuk" },
    { name: "chevron-right",       label: "Chevron" },
    { name: "bookmark",            label: "Bookmark" },
    { name: "bookmark-fill",       label: "Bookmark Simpan" },
    { name: "star",                label: "Bintang" },
    { name: "star-fill",           label: "Bintang Favorit" },
    { name: "heart",               label: "Hati" },
    { name: "pin-map",             label: "Lokasi Peta" },
    { name: "geo-alt",             label: "GPS Alamat" },

    // Dokumen & Administrasi
    { name: "file-earmark-text",   label: "Dokumen Teks" },
    { name: "file-earmark-pdf",    label: "File PDF" },
    { name: "file-earmark-word",   label: "File Word" },
    { name: "file-earmark-excel",  label: "File Excel" },
    { name: "file-earmark-zip",    label: "File ARSIP ZIP" },
    { name: "file-earmark-image",  label: "File Gambar" },
    { name: "file-earmark-code",   label: "File Script" },
    { name: "file-earmark-check",  label: "File Verifikasi" },
    { name: "folder",              label: "Folder Berkas" },
    { name: "folder-fill",         label: "Folder Warna" },
    { name: "folder2-open",        label: "Folder Terbuka" },
    { name: "journal-text",        label: "Jurnal Operasional" },
    { name: "clipboard",           label: "Clipboard Form" },
    { name: "clipboard-check",     label: "Checklist Tugas" },
    { name: "clipboard-data",      label: "Data Kinerja" },
    { name: "printer",             label: "Cetak Laporan" },

    // SDM, Pegawai & Organisasi
    { name: "person",              label: "Pegawai Single" },
    { name: "person-fill",         label: "Profil User" },
    { name: "people",              label: "Tim Kerja" },
    { name: "people-fill",         label: "Grup Organisasi" },
    { name: "person-badge",        label: "ID Card Pegawai" },
    { name: "person-check",        label: "Absensi Hadir" },
    { name: "person-vcard",        label: "VCard Kontak" },
    { name: "award",               label: "Penghargaan Kinerja" },
    { name: "mortarboard",         label: "Pendidikan Diklat" },

    // Sistem, Server & IT
    { name: "gear",                label: "Pengaturan Sistem" },
    { name: "gear-fill",           label: "Setting Utilitas" },
    { name: "sliders",             label: "Panel Kontrol" },
    { name: "database",            label: "Database Utama" },
    { name: "server",              label: "Server Cloud" },
    { name: "hdd",                 label: "Penyimpanan HDD" },
    { name: "shield-check",        label: "Sistem Keamanan" },
    { name: "lock",                label: "Kunci Otentikasi" },
    { name: "key",                 label: "Hak Akses Key" },
    { name: "terminal",            label: "Console Terminal" },
    { name: "code-slash",          label: "Pemrograman Kode" },
    { name: "bug",                 label: "Laporan Bug" },
    { name: "wifi",                label: "Jaringan WiFi" },
    { name: "cpu",                 label: "Prosessor CPU" },
    { name: "phone",               label: "Aplikasi Mobile" },
    { name: "laptop",              label: "Komputer Workstation" },
    { name: "pc-display",          label: "Monitor Dashboard" },

    // Statistik, Grafik & Analisis
    { name: "grid",                label: "Grid Modul" },
    { name: "grid-fill",           label: "Grid Menu" },
    { name: "layout-dashboard",    label: "Layout Dashboard" },
    { name: "bar-chart",           label: "Grafik Batang" },
    { name: "bar-chart-fill",      label: "Statistik Bar" },
    { name: "pie-chart",           label: "Diagram Pie" },
    { name: "graph-up",            label: "Grafik Naik" },
    { name: "graph-down",          label: "Grafik Turun" },
    { name: "activity",            label: "Monitoring Log" },

    // Komunikasi & Layanan
    { name: "envelope",            label: "Email Kedinasan" },
    { name: "envelope-fill",       label: "Surat Masuk" },
    { name: "chat",                label: "Layanan Chat" },
    { name: "chat-fill",           label: "Pesan Diskusi" },
    { name: "megaphone",           label: "Pengumuman Resmi" },
    { name: "bell",                label: "Notifikasi Sistem" },
    { name: "bell-fill",           label: "Pemberitahuan" },
    { name: "rss",                 label: "Feed Berita" },
    { name: "broadcast",           label: "Siaran Informasi" },

    // Keuangan & Operasional
    { name: "cash-coin",           label: "Keuangan Kas" },
    { name: "credit-card",         label: "Transaksi Kartu" },
    { name: "bank",                label: "Rekening Bank" },
    { name: "wallet",              label: "Dompet Anggaran" },
    { name: "receipt",             label: "Kwitansi Bukti" },
    { name: "currency-exchange",   label: "Konversi Kurs" },

    // Kesehatan, Logistik & Sarpras
    { name: "heart-pulse",         label: "Layanan Kesehatan" },
    { name: "hospital",            label: "Fasilitas RS" },
    { name: "capsule",             label: "Bantuan Obat" },
    { name: "calendar-event",      label: "Kalender Kegiatan" },
    { name: "calendar-check",      label: "Jadwal Rapat" },
    { name: "clock",               label: "Waktu Absen" },
    { name: "alarm",               label: "Pengingat Alarm" },
    { name: "truck",               label: "Armada Logistik" },
    { name: "building",            label: "Gedung Kantor" },
    { name: "map",                 label: "Peta Wilayah" },
    { name: "camera",              label: "Dokumentasi Foto" },
    { name: "image",               label: "Galeri Gambar" },
    { name: "play-circle",         label: "Video Panduan" },
    { name: "music-note",          label: "Audio Musik" },
    { name: "flag",                label: "Bendera Target" },
    { name: "lightning",           label: "Layanan Cepat" },
    { name: "cloud",               label: "Cloud Storage" },
    { name: "cloud-upload",        label: "Upload File" },
    { name: "cloud-download",      label: "Unduh File" },
    { name: "recycle",             label: "Daur Ulang" },
    { name: "box",                 label: "Paket Kiriman" },
    { name: "boxes",               label: "Inventori Stok" },
    { name: "tools",               label: "Peralatan Kerja" },
    { name: "wrench",              label: "Pemeliharaan Sarpras" },
    { name: "hammer",              label: "Konstruksi Pekerjaan" },
    { name: "search",              label: "Pencarian Data" },
    { name: "info-circle",         label: "Pusat Informasi" },
    { name: "question-circle",     label: "Bantuan FAQ" },
    { name: "check-circle",        label: "Status Selesai" },
    { name: "x-circle",            label: "Status Batal" },
    { name: "exclamation-triangle",label: "Peringatan Alert" },

    // Makanan & Minuman (Bootstrap Icons)
    { name: "cup-hot",             label: "Kopi / Minuman Panas" },
    { name: "cup-hot-fill",        label: "Kopi Fill" },
    { name: "cup-straw",           label: "Minuman Dingin" },
    { name: "cup",                 label: "Gelas Minum" },
    { name: "cup-fill",            label: "Gelas Fill" },
    { name: "egg",                 label: "Telur" },
    { name: "egg-fill",            label: "Telur Fill" },
    { name: "egg-fried",           label: "Telur Goreng" },
    { name: "basket",              label: "Keranjang Pasar" },
    { name: "basket-fill",         label: "Keranjang Fill" },
    { name: "basket2",             label: "Keranjang Belanja" },
    { name: "basket2-fill",        label: "Keranjang Belanja Fill" },
    { name: "bag",                 label: "Kantong Makanan" },
    { name: "bag-fill",            label: "Kantong Fill" },
    { name: "cart",                label: "Troli Belanja" },
    { name: "cart-fill",           label: "Troli Fill" },
];

// ==========================================
// REMIX ICON FULL LIST — 200+ Pilihan Remix Icon Populer & Lengkap
// ==========================================
const REMIX_ICON_LIST = [
    // User & Tim
    { name: "ri-user-line", label: "User Line" },
    { name: "ri-user-fill", label: "User Fill" },
    { name: "ri-user-smile-line", label: "User Smile" },
    { name: "ri-user-smile-fill", label: "User Smile Fill" },
    { name: "ri-user-star-line", label: "User Star" },
    { name: "ri-user-star-fill", label: "User Star Fill" },
    { name: "ri-user-settings-line", label: "User Setting" },
    { name: "ri-user-add-line", label: "Tambah User" },
    { name: "ri-user-follow-line", label: "Follow User" },
    { name: "ri-user-shared-line", label: "Share User" },
    { name: "ri-user-location-line", label: "Lokasi User" },
    { name: "ri-user-search-line", label: "Cari User" },
    { name: "ri-team-line", label: "Tim Kerja" },
    { name: "ri-team-fill", label: "Tim Fill" },
    { name: "ri-group-line", label: "Grup Line" },
    { name: "ri-group-fill", label: "Grup Fill" },
    { name: "ri-admin-line", label: "Admin Line" },
    { name: "ri-admin-fill", label: "Admin Fill" },
    { name: "ri-contacts-line", label: "Kontak Pegawai" },
    { name: "ri-account-circle-line", label: "Akun Circle" },
    { name: "ri-account-box-line", label: "Akun Box" },

    // Dokumen & Berkas
    { name: "ri-file-line", label: "File Line" },
    { name: "ri-file-fill", label: "File Fill" },
    { name: "ri-file-text-line", label: "File Teks" },
    { name: "ri-file-text-fill", label: "File Teks Fill" },
    { name: "ri-file-list-line", label: "Daftar File" },
    { name: "ri-file-list-2-line", label: "Daftar File 2" },
    { name: "ri-file-pdf-line", label: "File PDF" },
    { name: "ri-file-pdf-fill", label: "File PDF Fill" },
    { name: "ri-file-word-line", label: "File Word" },
    { name: "ri-file-word-fill", label: "File Word Fill" },
    { name: "ri-file-excel-line", label: "File Excel" },
    { name: "ri-file-excel-fill", label: "File Excel Fill" },
    { name: "ri-file-ppt-line", label: "File PPT" },
    { name: "ri-file-zip-line", label: "File ZIP ARSIP" },
    { name: "ri-file-chart-line", label: "Laporan Chart" },
    { name: "ri-file-code-line", label: "File Kode" },
    { name: "ri-file-copy-line", label: "Salin File" },
    { name: "ri-file-download-line", label: "Unduh File" },
    { name: "ri-file-upload-line", label: "Upload File" },
    { name: "ri-file-edit-line", label: "Edit File" },
    { name: "ri-file-paper-line", label: "Kertas Dokumen" },
    { name: "ri-folder-line", label: "Folder Berkas" },
    { name: "ri-folder-fill", label: "Folder Fill" },
    { name: "ri-folder-open-line", label: "Folder Buka" },
    { name: "ri-folder-add-line", label: "Tambah Folder" },
    { name: "ri-folder-user-line", label: "Folder User" },
    { name: "ri-folder-lock-line", label: "Folder Kunci" },
    { name: "ri-draft-line", label: "Draft Berkas" },
    { name: "ri-survey-line", label: "Survei Form" },
    { name: "ri-task-line", label: "Tugas Task" },
    { name: "ri-todo-line", label: "Todo Checklist" },

    // Sistem & IT
    { name: "ri-dashboard-line", label: "Dashboard Line" },
    { name: "ri-dashboard-fill", label: "Dashboard Fill" },
    { name: "ri-settings-line", label: "Pengaturan Line" },
    { name: "ri-settings-fill", label: "Pengaturan Fill" },
    { name: "ri-settings-3-line", label: "Setting Gear 3" },
    { name: "ri-settings-4-line", label: "Setting Gear 4" },
    { name: "ri-shield-line", label: "Keamanan Line" },
    { name: "ri-shield-fill", label: "Keamanan Fill" },
    { name: "ri-shield-check-line", label: "Keamanan Ok" },
    { name: "ri-shield-flash-line", label: "Keamanan Cepat" },
    { name: "ri-shield-keyhole-line", label: "Keamanan Key" },
    { name: "ri-shield-user-line", label: "Keamanan User" },
    { name: "ri-lock-line", label: "Kunci Lock" },
    { name: "ri-lock-fill", label: "Kunci Fill" },
    { name: "ri-lock-password-line", label: "Kunci Password" },
    { name: "ri-unlock-line", label: "Buka Kunci" },
    { name: "ri-key-line", label: "Kunci Akses" },
    { name: "ri-key-fill", label: "Kunci Fill" },
    { name: "ri-database-line", label: "Database Line" },
    { name: "ri-database-fill", label: "Database Fill" },
    { name: "ri-database-2-line", label: "Database 2" },
    { name: "ri-server-line", label: "Server Line" },
    { name: "ri-server-fill", label: "Server Fill" },
    { name: "ri-cpu-line", label: "CPU Hardware" },
    { name: "ri-hard-drive-line", label: "Hard Disk Storage" },
    { name: "ri-terminal-line", label: "Console Terminal" },
    { name: "ri-terminal-box-line", label: "Terminal Box" },
    { name: "ri-code-line", label: "Kode Line" },
    { name: "ri-code-s-slash-line", label: "Syntax Kode" },
    { name: "ri-bug-line", label: "Bug Report" },
    { name: "ri-device-line", label: "Perangkat Device" },
    { name: "ri-computer-line", label: "Komputer PC" },
    { name: "ri-laptop-line", label: "Laptop Work" },
    { name: "ri-smartphone-line", label: "Smartphone Mobile" },
    { name: "ri-tablet-line", label: "Tablet Display" },
    { name: "ri-wifi-line", label: "Jaringan WiFi" },
    { name: "ri-global-line", label: "Global Internet" },
    { name: "ri-cloud-line", label: "Cloud Line" },
    { name: "ri-cloud-fill", label: "Cloud Fill" },
    { name: "ri-cloud-windy-line", label: "Cloud Server" },

    // Keuangan & Bisnis
    { name: "ri-bank-card-line", label: "Kartu Bank" },
    { name: "ri-bank-card-fill", label: "Kartu Bank Fill" },
    { name: "ri-money-dollar-circle-line", label: "Uang Kas" },
    { name: "ri-money-euro-circle-line", label: "Mata Uang" },
    { name: "ri-wallet-line", label: "Dompet Line" },
    { name: "ri-wallet-fill", label: "Dompet Fill" },
    { name: "ri-safe-line", label: "Brankas Kas" },
    { name: "ri-funds-line", label: "Investasi Dana" },
    { name: "ri-exchange-box-line", label: "Transaksi Box" },
    { name: "ri-shopping-cart-line", label: "Keranjang Belanja" },
    { name: "ri-shopping-bag-line", label: "Kantung Belanja" },
    { name: "ri-store-line", label: "Toko Sarpras" },
    { name: "ri-briefcase-line", label: "Tas Kerja" },
    { name: "ri-briefcase-fill", label: "Tas Kerja Fill" },
    { name: "ri-calculator-line", label: "Kalkulator Anggaran" },
    { name: "ri-auction-line", label: "Lelang Pengadaan" },
    { name: "ri-scales-3-line", label: "Timbangan Hukum" },

    // Grafik & Analisis
    { name: "ri-bar-chart-line", label: "Grafik Batang" },
    { name: "ri-bar-chart-fill", label: "Grafik Batang Fill" },
    { name: "ri-bar-chart-2-line", label: "Bar Chart 2" },
    { name: "ri-bar-chart-box-line", label: "Bar Chart Box" },
    { name: "ri-pie-chart-line", label: "Diagram Pie" },
    { name: "ri-pie-chart-fill", label: "Diagram Pie Fill" },
    { name: "ri-pie-chart-2-line", label: "Pie Chart 2" },
    { name: "ri-line-chart-line", label: "Grafik Garis" },
    { name: "ri-line-chart-fill", label: "Grafik Garis Fill" },
    { name: "ri-presentation-line", label: "Presentasi Rapat" },
    { name: "ri-donut-chart-line", label: "Donut Chart" },

    // Komunikasi & Layanan
    { name: "ri-mail-line", label: "Email Line" },
    { name: "ri-mail-fill", label: "Email Fill" },
    { name: "ri-mail-unread-line", label: "Gmail Unread" },
    { name: "ri-mail-send-line", label: "Kirim Surat" },
    { name: "ri-message-line", label: "Pesan Line" },
    { name: "ri-message-fill", label: "Pesan Fill" },
    { name: "ri-message-3-line", label: "Chat Message" },
    { name: "ri-chat-1-line", label: "Layanan Chat" },
    { name: "ri-chat-3-line", label: "Diskusi Chat" },
    { name: "ri-chat-smile-line", label: "Chat Smile" },
    { name: "ri-phone-line", label: "Telepon Line" },
    { name: "ri-phone-fill", label: "Telepon Fill" },
    { name: "ri-customer-service-line", label: "Customer Service" },
    { name: "ri-customer-service-2-line", label: "Helpdesk CS 2" },
    { name: "ri-send-plane-line", label: "Kirim Berkas" },
    { name: "ri-notification-line", label: "Notifikasi Line" },
    { name: "ri-notification-fill", label: "Notifikasi Fill" },
    { name: "ri-bell-line", label: "Lonceng Bell" },
    { name: "ri-bell-fill", label: "Lonceng Fill" },

    // Media & Desain
    { name: "ri-image-line", label: "Gambar Line" },
    { name: "ri-image-fill", label: "Gambar Fill" },
    { name: "ri-image-edit-line", label: "Edit Gambar" },
    { name: "ri-camera-line", label: "Kamera Line" },
    { name: "ri-camera-fill", label: "Kamera Fill" },
    { name: "ri-video-line", label: "Video Line" },
    { name: "ri-video-fill", label: "Video Fill" },
    { name: "ri-movie-line", label: "Film Video" },
    { name: "ri-play-circle-line", label: "Play Video" },
    { name: "ri-music-line", label: "Musik Audio" },
    { name: "ri-mic-line", label: "Microphone Rekam" },
    { name: "ri-palette-line", label: "Palet Warna" },
    { name: "ri-magic-line", label: "Magic Wand" },
    { name: "ri-brush-line", label: "Kuas Desain" },

    // Peta, Gedung & Transportasi
    { name: "ri-map-pin-line", label: "Pin Peta" },
    { name: "ri-map-pin-fill", label: "Pin Peta Fill" },
    { name: "ri-map-2-line", label: "Peta Denah" },
    { name: "ri-building-line", label: "Gedung Kantor" },
    { name: "ri-building-fill", label: "Gedung Fill" },
    { name: "ri-building-4-line", label: "Kementerian BGN" },
    { name: "ri-community-line", label: "Komunitas Masy" },
    { name: "ri-government-line", label: "Pemerintahan Institusi" },
    { name: "ri-hospital-line", label: "Rumah Sakit" },
    { name: "ri-hotel-line", label: "Hotel Penginapan" },
    { name: "ri-store-2-line", label: "Kantin / Toko" },
    { name: "ri-truck-line", label: "Truk Pengiriman" },
    { name: "ri-car-line", label: "Mobil Dinas" },
    { name: "ri-flight-takeoff-line", label: "Penerbangan Tugas" },

    // Kesehatan & Obat
    { name: "ri-hospital-line", label: "Rumah Sakit" },
    { name: "ri-heart-pulse-line", label: "Detak Jantung" },
    { name: "ri-capsule-line", label: "Kapsul Obat" },
    { name: "ri-medicine-bottle-line", label: "Botol Obat" },
    { name: "ri-stethoscope-line", label: "Stetoskop Dokter" },
    { name: "ri-first-aid-kit-line", label: "P3K Darurat" },
    { name: "ri-mental-health-line", label: "Kesehatan Mental" },
    { name: "ri-pulse-line", label: "Denyut Nadi" },

    // Brands & Logins
    { name: "ri-drive-line", label: "Google Drive Line" },
    { name: "ri-drive-fill", label: "Google Drive Fill" },
    { name: "ri-google-fill", label: "Google App" },
    { name: "ri-whatsapp-line", label: "WhatsApp Chat" },
    { name: "ri-youtube-line", label: "YouTube Channel" },
    { name: "ri-facebook-box-line", label: "Facebook Page" },
    { name: "ri-instagram-line", label: "Instagram Official" },
    { name: "ri-twitter-x-line", label: "Twitter X" },
    { name: "ri-github-line", label: "GitHub Repo" },
    { name: "ri-linkedin-box-line", label: "LinkedIn Pro" },
    { name: "ri-android-line", label: "Android System" },
    { name: "ri-apple-line", label: "Apple System" },
    { name: "ri-windows-line", label: "Windows System" },
    { name: "ri-chrome-line", label: "Chrome Browser" },

    // Simbol, Tombol & Panah
    { name: "ri-arrow-right-line", label: "Panah Kanan" },
    { name: "ri-arrow-right-s-line", label: "Chevron Kanan" },
    { name: "ri-arrow-up-s-line", label: "Chevron Atas" },
    { name: "ri-arrow-down-s-line", label: "Chevron Bawah" },
    { name: "ri-external-link-line", label: "Link Luar" },
    { name: "ri-links-line", label: "Tautan Ganda" },
    { name: "ri-checkbox-circle-line", label: "Check Sukses" },
    { name: "ri-close-circle-line", label: "Batal Silang" },
    { name: "ri-add-circle-line", label: "Tambah Plus" },
    { name: "ri-information-line", label: "Info Lingkar" },
    { name: "ri-question-line", label: "Tanya FAQ" },
    { name: "ri-error-warning-line", label: "Peringatan Alert" },
    { name: "ri-star-line", label: "Bintang Star" },
    { name: "ri-star-fill", label: "Bintang Fill" },
    { name: "ri-heart-line", label: "Suka Heart" },
    { name: "ri-heart-fill", label: "Suka Fill" },
    { name: "ri-bookmark-line", label: "Bookmark Line" },
    { name: "ri-price-tag-3-line", label: "Tag Kategori" },

    // Makanan & Minuman (Remix Icons)
    { name: "ri-restaurant-line",          label: "Restoran" },
    { name: "ri-restaurant-fill",          label: "Restoran Fill" },
    { name: "ri-restaurant-2-line",        label: "Restoran 2" },
    { name: "ri-restaurant-2-fill",        label: "Restoran 2 Fill" },
    { name: "ri-knife-line",               label: "Pisau Dapur" },
    { name: "ri-knife-fill",               label: "Pisau Fill" },
    { name: "ri-cup-line",                 label: "Gelas Minum" },
    { name: "ri-cup-fill",                 label: "Gelas Fill" },
    { name: "ri-goblet-line",              label: "Gelas Anggur" },
    { name: "ri-goblet-fill",              label: "Gelas Anggur Fill" },
    { name: "ri-wine-line",                label: "Botol Minuman" },
    { name: "ri-wine-fill",                label: "Botol Fill" },
    { name: "ri-beer-line",                label: "Minuman Bersoda" },
    { name: "ri-beer-fill",                label: "Minuman Bersoda Fill" },
    { name: "ri-cake-line",                label: "Kue / Ulang Tahun" },
    { name: "ri-cake-fill",                label: "Kue Fill" },
    { name: "ri-cake-2-line",              label: "Kue Slice" },
    { name: "ri-cake-2-fill",              label: "Kue Slice Fill" },
    { name: "ri-cake-3-line",              label: "Kue Tart" },
    { name: "ri-cake-3-fill",              label: "Kue Tart Fill" },
    { name: "ri-bread-line",               label: "Roti" },
    { name: "ri-bread-fill",               label: "Roti Fill" },
    { name: "ri-plant-line",               label: "Sayuran / Tanaman" },
    { name: "ri-plant-fill",               label: "Sayuran Fill" },
    { name: "ri-leaf-line",                label: "Daun Herbal" },
    { name: "ri-leaf-fill",                label: "Daun Fill" },
    { name: "ri-seedling-line",            label: "Benih Pertanian" },
    { name: "ri-seedling-fill",            label: "Benih Fill" },
    { name: "ri-shopping-basket-line",     label: "Keranjang Pasar" },
    { name: "ri-shopping-basket-fill",     label: "Keranjang Pasar Fill" },
    { name: "ri-shopping-cart-line",       label: "Troli Belanja" },
    { name: "ri-store-line",               label: "Kantin / Warung" },
    { name: "ri-store-2-line",             label: "Warung / Toko" },
    { name: "ri-store-3-line",             label: "Pasar Swalayan" },
    { name: "ri-price-tag-line",           label: "Label Harga" },
    { name: "ri-price-tag-fill",           label: "Label Harga Fill" },
    { name: "ri-price-tag-2-line",         label: "Harga Produk" },
    { name: "ri-coupon-line",              label: "Kupon Diskon" },
    { name: "ri-coupon-fill",              label: "Kupon Fill" },
    { name: "ri-coupon-3-line",            label: "Voucher Belanja" },
    { name: "ri-copper-coin-line",         label: "Koin Kasir" },
    { name: "ri-hand-coin-line",           label: "Pembayaran Tunai" },
    { name: "ri-scales-3-line",            label: "Timbangan Bahan" },
    { name: "ri-fire-line",                label: "Api Masak" },
    { name: "ri-fire-fill",                label: "Api Fill" },
    { name: "ri-drop-line",                label: "Air Minum" },
    { name: "ri-drop-fill",                label: "Air Fill" },
    { name: "ri-water-flash-line",         label: "Air Bersih" },
    { name: "ri-water-percent-line",       label: "Kadar Air" },
    { name: "ri-sun-line",                 label: "Segar / Alami" },
    { name: "ri-recycle-line",             label: "Kemasan Ramah Lingk." },
    { name: "ri-gift-line",                label: "Paket Makanan" },
    { name: "ri-gift-fill",                label: "Paket Fill" },
    { name: "ri-box-line",                 label: "Kemasan Box" },
    { name: "ri-box-fill",                 label: "Kemasan Fill" },
    { name: "ri-truck-line",               label: "Antar Makanan" },
    { name: "ri-e-bike-line",              label: "Ojek Makanan" },
    { name: "ri-motorbike-line",           label: "Kurir Antar" },
    { name: "ri-map-pin-line",             label: "Lokasi Restoran" },
    { name: "ri-time-line",                label: "Jam Buka" },
    { name: "ri-calendar-event-line",      label: "Jadwal Katering" },
    { name: "ri-star-line",                label: "Rating Makanan" },
    { name: "ri-thumb-up-line",            label: "Rekomendasi" },
    { name: "ri-thumb-up-fill",            label: "Rekomendasi Fill" },
    { name: "ri-heart-line",               label: "Favorit Makanan" },
    { name: "ri-heart-fill",               label: "Favorit Fill" },
];

let activeIconLibrary = "all";

function getActiveIconSet() {
    if (activeIconLibrary === "remix") {
        return REMIX_ICON_LIST;
    } else if (activeIconLibrary === "bootstrap") {
        return ICON_LIST.filter(ic => !ic.name.startsWith("fa-") && !ic.name.startsWith("ri-"));
    } else if (activeIconLibrary === "fontawesome") {
        return ICON_LIST.filter(ic => ic.name.startsWith("fa-"));
    } else {
        // Combined list
        return [...ICON_LIST, ...REMIX_ICON_LIST];
    }
}

function initIconPicker() {
    const grid = document.getElementById("iconGrid");
    const searchInput = document.getElementById("iconSearch");
    const customIconInput = document.getElementById("customIconUrl");
    const btnApplyCustom = document.getElementById("btnApplyCustomIcon");
    if (!grid) return;

    renderIconGrid(getActiveIconSet());

    // Library Tab Filter
    document.querySelectorAll(".icon-tab-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.querySelectorAll(".icon-tab-btn").forEach(b => {
                b.classList.remove("active", "btn-primary");
                b.classList.add("btn-outline-primary");
            });
            btn.classList.remove("btn-outline-primary");
            btn.classList.add("active", "btn-primary");

            activeIconLibrary = btn.dataset.lib || "all";
            const q = searchInput?.value.toLowerCase().trim() || "";
            filterAndRenderIcons(q);
        });
    });

    if (btnApplyCustom && customIconInput) {
        btnApplyCustom.addEventListener("click", () => {
            const val = customIconInput.value.trim();
            if (val) selectIcon(val);
        });
        customIconInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                const val = customIconInput.value.trim();
                if (val) selectIcon(val);
            }
        });
    }

    // Search filter
    if (searchInput) {
        searchInput.addEventListener("input", () => {
            const q = searchInput.value.toLowerCase().trim();
            filterAndRenderIcons(q);
        });
    }
}

function filterAndRenderIcons(query) {
    const set = getActiveIconSet();
    const filtered = query
        ? set.filter(ic =>
            ic.name.includes(query) || ic.label.toLowerCase().includes(query))
        : set;
    
    // Jika user mengetikkan langsung kelas remix seperti 'ri-alarm-line' tapi tidak ada di preset list, tambahkan secara dinamis
    if (query.startsWith("ri-") && !filtered.some(ic => ic.name === query)) {
        filtered.unshift({ name: query, label: `Remix (${query})` });
    } else if (query.startsWith("fa-") && !filtered.some(ic => ic.name === query)) {
        filtered.unshift({ name: query, label: `FontAwesome (${query})` });
    }

    renderIconGrid(filtered);
    const current = document.getElementById("linkIcon")?.value;
    if (current) highlightIcon(current);
}

function renderIconGrid(icons) {
    const grid = document.getElementById("iconGrid");
    if (!grid) return;

    grid.innerHTML = icons.map(ic => {
        const isUrl = ic.name.startsWith("http://") || ic.name.startsWith("https://") || ic.name.startsWith("data:") || ic.name.includes("/");
        const isFa = ic.name.startsWith("fa-") || ic.name.startsWith("fa ") || ic.name.startsWith("fas ") || ic.name.startsWith("ri-");
        
        let iconHtml = `<i class="bi bi-${ic.name}"></i>`;
        if (isUrl) {
            iconHtml = `<img src="${ic.name}" alt="${ic.label}" style="width:20px;height:20px;object-fit:contain;">`;
        } else if (isFa) {
            iconHtml = `<i class="${ic.name}"></i>`;
        }

        return `
        <button type="button"
                class="icon-grid-item"
                data-icon="${ic.name}"
                title="${ic.label} (${ic.name})">
            ${iconHtml}
            <span>${ic.label}</span>
        </button>
    `}).join("");

    // Event click per item
    grid.querySelectorAll(".icon-grid-item").forEach(btn => {
        btn.addEventListener("click", () => {
            selectIcon(btn.dataset.icon);
        });
    });
}

function selectIcon(iconName) {
    const hiddenInput = document.getElementById("linkIcon");
    const container = document.getElementById("iconPickerPreviewContainer");
    const customIconInput = document.getElementById("customIconUrl");

    if (hiddenInput) hiddenInput.value = iconName;
    if (customIconInput && (iconName.startsWith("http") || iconName.includes("/"))) {
        customIconInput.value = iconName;
    }

    if (container) {
        const isUrl = iconName.startsWith("http://") || iconName.startsWith("https://") || iconName.startsWith("data:") || iconName.includes("/");
        const isFa = iconName.startsWith("fa-") || iconName.startsWith("fa ") || iconName.startsWith("fas ") || iconName.startsWith("ri-");

        if (isUrl) {
            container.innerHTML = `<img src="${iconName}" alt="Preview" style="width:36px;height:36px;object-fit:contain;">`;
        } else if (isFa) {
            container.innerHTML = `<i class="${iconName}"></i>`;
        } else {
            container.innerHTML = `<i id="iconPickerPreview" class="bi bi-${iconName}"></i>`;
        }
    }

    highlightIcon(iconName);
}

function highlightIcon(iconName) {
    document.querySelectorAll(".icon-grid-item").forEach(btn => {
        btn.classList.toggle("selected", btn.dataset.icon === iconName);
    });
}

function updateIconPreview(iconName) {
    selectIcon(iconName);
}

// ==========================================
// Toggle visibility Security Code
// ==========================================
function initSecurityCodeToggle() {
    const toggleBtn = document.getElementById("btnToggleSecCode");
    const secInput  = document.getElementById("settingSecurityCode");
    const eyeIcon   = document.getElementById("secCodeEyeIcon");

    if (!toggleBtn || !secInput) return;

    toggleBtn.addEventListener("click", () => {
        const isPassword = secInput.type === "password";
        secInput.type = isPassword ? "text" : "password";
        if (eyeIcon) {
            eyeIcon.className = isPassword ? "bi bi-eye" : "bi bi-eye-slash";
        }
    });
}

// ==========================================
// MINI ICON PICKER (untuk unified link rows)
// ==========================================
let miniPickerTargetRow = null;
let miniIconsRendered   = false;

function initMiniIconPicker() {
    const picker    = document.getElementById("miniIconPicker");
    const grid      = document.getElementById("miniIconGrid");
    const searchEl  = document.getElementById("miniIconSearch");
    const closeBtn  = document.getElementById("btnCloseMiniPicker");
    if (!picker) return;

    // Render ikon (gunakan data dari ICON_LIST + REMIX_ICON_LIST)
    function renderMiniGrid(query = "") {
        const all = [...ICON_LIST, ...REMIX_ICON_LIST];
        const filtered = query
            ? all.filter(ic => ic.name.includes(query) || ic.label.toLowerCase().includes(query))
            : all;
        grid.innerHTML = filtered.slice(0, 120).map(ic => {
            const isFa  = ic.name.startsWith("ri-") || ic.name.startsWith("fa");
            const isUrl = ic.name.startsWith("http") || ic.name.includes("/");
            let html = isFa ? `<i class="${ic.name}"></i>` : `<i class="bi bi-${ic.name}"></i>`;
            if (isUrl) html = `<img src="${ic.name}" style="width:16px;height:16px;object-fit:contain;">`;
            return `<button type="button" class="mini-icon-item btn btn-sm btn-light p-1" title="${ic.label}" data-icon="${ic.name}"
                            style="font-size:18px;line-height:1;display:flex;align-items:center;justify-content:center;height:36px;">
                ${html}
            </button>`;
        }).join("");

        grid.querySelectorAll(".mini-icon-item").forEach(btn => {
            btn.addEventListener("click", () => {
                selectMiniIcon(btn.dataset.icon);
                hideMiniPicker();
            });
        });
    }

    if (!miniIconsRendered) {
        renderMiniGrid();
        miniIconsRendered = true;
    }

    if (searchEl) {
        searchEl.addEventListener("input", () => renderMiniGrid(searchEl.value.trim().toLowerCase()));
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", hideMiniPicker);
    }

    document.addEventListener("click", (e) => {
        if (!picker.classList.contains("d-none")
            && !picker.contains(e.target)
            && !e.target.closest(".btn-pick-icon")) {
            hideMiniPicker();
        }
    });

    // Delegasi klik tombol picker
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-pick-icon");
        if (!btn) return;
        miniPickerTargetRow = btn.closest(".link-row-item");
        const rect = btn.getBoundingClientRect();
        picker.style.top  = (rect.bottom + 4) + "px";
        picker.style.left = Math.max(4, rect.left - 200) + "px";
        picker.classList.remove("d-none");
        picker.style.display = "flex";
        if (searchEl) { searchEl.value = ""; renderMiniGrid(); searchEl.focus(); }
    });
}

function selectMiniIcon(iconName) {
    if (!miniPickerTargetRow) return;
    const iconInput   = miniPickerTargetRow.querySelector(".link-icon-input");
    const iconWrap    = miniPickerTargetRow.querySelector(".link-icon-preview-wrap");
    const iconPreview = miniPickerTargetRow.querySelector(".link-icon-preview");
    if (iconInput) iconInput.value = iconName;
    updateRowIconPreview(iconName, iconPreview, iconWrap);
}

function hideMiniPicker() {
    const picker = document.getElementById("miniIconPicker");
    if (picker) { picker.classList.add("d-none"); picker.style.display = ""; }
    miniPickerTargetRow = null;
}

// ==========================================
// EXPIRED DATE VALIDATION (min = besok)
// ==========================================
function getTomorrow() {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return d.toISOString().split("T")[0];
}

function initExpiredDateValidation() {
    const tomorrow = getTomorrow();

    // Set min attribute pada semua date input expired_at
    ["cardExpiredAt", "cardExpiredAtEdit"].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.min = tomorrow;
    });

    // Validasi saat nilai berubah (card unified form)
    document.addEventListener("change", (e) => {
        if (e.target.id === "cardExpiredAt" || e.target.id === "cardExpiredAtEdit") {
            const errId = e.target.id === "cardExpiredAt" ? "cardExpiredAtError" : "cardExpiredAtEditError";
            const errEl = document.getElementById(errId);
            if (e.target.value && e.target.value < tomorrow) {
                e.target.value = "";
                errEl?.classList.remove("d-none");
                setTimeout(() => errEl?.classList.add("d-none"), 3000);
            } else {
                errEl?.classList.add("d-none");
            }
        }
        // Link expired_at di link rows
        if (e.target.classList.contains("link-expired-input")) {
            if (e.target.value && e.target.value < tomorrow) {
                e.target.value = "";
                const err = e.target.parentElement.querySelector(".link-expired-error");
                if (err) { err.classList.remove("d-none"); setTimeout(() => err.classList.add("d-none"), 3000); }
            }
        }
    });
}

// ==========================================
// ARCHIVE MODAL LOGIC
// ==========================================
function initArchiveModal() {
    const btnArchive = document.getElementById("btnArchiveModal");
    if (!btnArchive) return;

    btnArchive.addEventListener("click", () => {
        const modal = new bootstrap.Modal(document.getElementById("archiveModal"));
        modal.show();
        loadArchive();
    });

    // Delegasi: Restore Card
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-restore-card");
        if (!btn) return;
        await archiveAction(`/manage/cards/${btn.dataset.uuid}/restore`, "POST", "Kartu berhasil dipulihkan!");
        loadArchive();
    });

    // Delegasi: Force Delete Card
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-force-delete-card");
        if (!btn) return;
        const confirmed = await Swal.fire({
            title: "Hapus Permanen?",
            text: `"${btn.dataset.title}" akan dihapus selamanya dan tidak dapat dikembalikan.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Ya, Hapus Permanen",
            cancelButtonText: "Batal",
        });
        if (!confirmed.isConfirmed) return;
        await archiveAction(`/manage/cards/${btn.dataset.uuid}/force`, "DELETE", "Kartu berhasil dihapus permanen.");
        loadArchive();
    });

    // Delegasi: Restore Link
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-restore-link");
        if (!btn) return;
        await archiveAction(`/manage/links/${btn.dataset.uuid}/restore`, "POST", "Tautan berhasil dipulihkan!");
        loadArchive();
    });

    // Delegasi: Force Delete Link
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-force-delete-link");
        if (!btn) return;
        const confirmed = await Swal.fire({
            title: "Hapus Permanen?",
            text: `"${btn.dataset.title}" akan dihapus selamanya.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Ya, Hapus Permanen",
            cancelButtonText: "Batal",
        });
        if (!confirmed.isConfirmed) return;
        await archiveAction(`/manage/links/${btn.dataset.uuid}/force`, "DELETE", "Tautan berhasil dihapus permanen.");
        loadArchive();
    });
}

async function archiveAction(url, method, successMsg) {
    try {
        const res = await fetch(url, {
            method,
            headers: { "Content-Type": "application/json", "Accept": "application/json", "X-CSRF-TOKEN": csrf() },
        });
        const data = await res.json();
        if (data.success) {
            Swal.fire({ toast: true, position: "top-end", icon: "success", title: successMsg, timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire("Gagal", data.message ?? "Terjadi kesalahan.", "error");
        }
    } catch {
        Swal.fire("Error", "Koneksi gagal.", "error");
    }
}

async function loadArchive() {
    const loading = document.getElementById("archiveLoading");
    const content = document.getElementById("archiveContent");
    if (loading) { loading.classList.remove("d-none"); }
    if (content) { content.classList.add("d-none"); }

    try {
        const res  = await fetch("/manage/archived", { headers: { "Accept": "application/json", "X-CSRF-TOKEN": csrf() } });
        const data = await res.json();

        renderArchivedCards(data.archived_cards ?? []);
        renderArchivedLinks(data.archived_links ?? []);

        document.getElementById("archiveCardCount").textContent = (data.archived_cards ?? []).length;
        document.getElementById("archiveLinkCount").textContent = (data.archived_links ?? []).length;

        if (loading) loading.classList.add("d-none");
        if (content) content.classList.remove("d-none");
    } catch {
        if (loading) loading.innerHTML = `<p class="text-danger py-4">Gagal memuat arsip.</p>`;
    }
}

function renderArchivedCards(cards) {
    const el = document.getElementById("archiveCardsList");
    if (!el) return;
    if (!cards.length) {
        el.innerHTML = `<p class="text-muted text-center py-4">Tidak ada kartu yang dihapus.</p>`;
        return;
    }
    el.innerHTML = cards.map(card => `
        <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-2 bg-light">
            <div>
                <div class="fw-bold"><i class="bi bi-card-heading me-1 text-muted"></i>${card.title}</div>
                <small class="text-muted">Dihapus: ${card.deleted_at ? new Date(card.deleted_at).toLocaleDateString("id-ID") : "-"} &bull; ${card.links?.length ?? 0} tautan</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success btn-restore-card"
                        data-uuid="${card.uuid}" title="Pulihkan">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Pulihkan
                </button>
                <button type="button" class="btn btn-sm btn-danger btn-force-delete-card"
                        data-uuid="${card.uuid}" data-title="${card.title}" title="Hapus Permanen">
                    <i class="bi bi-trash3 me-1"></i>Hapus Permanen
                </button>
            </div>
        </div>
    `).join("");
}

function renderArchivedLinks(links) {
    const el = document.getElementById("archiveLinksList");
    if (!el) return;
    if (!links.length) {
        el.innerHTML = `<p class="text-muted text-center py-4">Tidak ada tautan kadaluarsa atau terhapus.</p>`;
        return;
    }
    el.innerHTML = links.map(link => {
        const reason = link.deleted_at ? "Dihapus" : `Kadaluarsa: ${link.expired_at}`;
        const cardTitle = link.card?.title ?? "-";
        return `
        <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-2 bg-light">
            <div>
                <div class="fw-bold"><i class="bi bi-link-45deg me-1 text-muted"></i>${link.title}</div>
                <small class="text-muted">${reason} &bull; Card: ${cardTitle}</small>
                <div><a href="${link.url}" target="_blank" class="small text-truncate text-muted" style="max-width:300px;display:block;">${link.url}</a></div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success btn-restore-link"
                        data-uuid="${link.uuid}" title="Pulihkan">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Pulihkan
                </button>
                <button type="button" class="btn btn-sm btn-danger btn-force-delete-link"
                        data-uuid="${link.uuid}" data-title="${link.title}" title="Hapus Permanen">
                    <i class="bi bi-trash3 me-1"></i>Hapus Permanen
                </button>
            </div>
        </div>
    `}).join("");
}

// Tambahkan initMiniIconPicker, initExpiredDateValidation, initArchiveModal ke DOMContentLoaded
document.addEventListener("DOMContentLoaded", () => {
    initMiniIconPicker();
    initExpiredDateValidation();
    initArchiveModal();
});
