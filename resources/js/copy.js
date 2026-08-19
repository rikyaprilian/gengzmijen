// ======================================================
// Copy Button & Simple Flash Message Module
// Menangani fungsi salin URL link ke clipboard
// serta menampilkan flash message simpel di bawah tengah
// ======================================================

let toastTimeout = null;

document.addEventListener("DOMContentLoaded", () => {
    initCopyEvents();
});

export function initCopyEvents() {
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".copy-btn");
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const url = btn.dataset.url;
        if (!url) {
            console.warn("Copy button missing data-url attribute.");
            return;
        }

        const success = await copyToClipboard(url);

        if (success) {
            animateCopyButton(btn);
            showCopyFlashMessage(url);
        }
    });
}

/**
 * Salin teks ke clipboard menggunakan Clipboard API dengan fallback execCommand
 */
async function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.warn("navigator.clipboard.writeText gagal, mengalihkan ke fallback execCommand:", err);
        }
    }

    try {
        const textarea = document.createElement("textarea");
        textarea.value = text;
        textarea.style.position = "fixed";
        textarea.style.left = "-999999px";
        textarea.style.top = "-999999px";
        textarea.setAttribute("readonly", "");
        document.body.appendChild(textarea);
        textarea.select();
        textarea.setSelectionRange(0, 99999);
        const successful = document.execCommand("copy");
        document.body.removeChild(textarea);
        return successful;
    } catch (err) {
        console.error("Gagal menyalin tautan:", err);
        return false;
    }
}

/**
 * Visual feedback pada tombol copy
 */
function animateCopyButton(btn) {
    const icon = btn.querySelector("i");
    btn.classList.add("copied");

    if (icon) {
        const originalClass = icon.className;
        icon.className = "bi bi-check2 fs-6";

        setTimeout(() => {
            icon.className = originalClass;
            btn.classList.remove("copied");
        }, 1500);
    }
}

/**
 * Menampilkan Flash Message simpel 1 baris berisi link yang dicopy di bagian bawah tengah tanpa animasi
 */
function showCopyFlashMessage(url) {
    let toast = document.getElementById("copy-flash-toast");

    if (!toast) {
        toast = document.createElement("div");
        toast.id = "copy-flash-toast";
        toast.className = "copy-flash-toast";
        document.body.appendChild(toast);
    }

    toast.textContent = url;
    toast.style.display = "block";

    if (toastTimeout) {
        clearTimeout(toastTimeout);
    }

    toastTimeout = setTimeout(() => {
        toast.style.display = "none";
    }, 2000);
}
