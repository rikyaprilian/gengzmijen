import { setEditMode, logoutEditMode } from "./edit-mode";

document.addEventListener("DOMContentLoaded", () => {

    const keepEditMode = sessionStorage.getItem("portal_keep_edit_mode");
    if (keepEditMode === "1") {
        sessionStorage.removeItem("portal_keep_edit_mode");
        setEditMode(true);
    } else {
        setEditMode(false);
        logoutEditMode();
    }

    // Form submit (Enter key atau klik tombol)
    const form = document.getElementById("manageLoginForm");
    if (form) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            login();
        });
    }

});

async function login() {

    const input = document.querySelector(".verify-input");
    const code = input ? input.value.trim() : "";

    if (code === "") {
        showError("Kode Edit harus diisi.");
        if (input) input.focus();
        return;
    }

    buttonLoading(true);

    try {

        const response = await fetch("/manage/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .content,
            },
            body: JSON.stringify({ security_code: code }),
        });

        const result = await response.json();

        if (!result.success) {
            showError(result.message ?? "Security Code salah. Periksa kembali.");
            buttonLoading(false);
            return;
        }

        // Sukses: tutup modal dan aktifkan Edit Mode
        const modal = bootstrap.Modal.getInstance(
            document.getElementById("manageModal")
        );
        if (modal) modal.hide();

        // Bersihkan input
        if (input) input.value = "";

        setEditMode(true);

    } catch (error) {

        console.error(error);
        showError("Terjadi kesalahan koneksi. Silakan coba lagi.");

    }

    buttonLoading(false);

}

function buttonLoading(status) {

    const button = document.querySelector(".verify-button");

    if (!button) return;

    button.disabled = status;

    button.innerHTML = status
        ? '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...'
        : "Masuk Mode Edit";

}

function showError(message) {

    const errorEl = document.getElementById("manageLoginError");
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.remove("d-none");
    } else {
        // Fallback
        alert(message);
    }

}