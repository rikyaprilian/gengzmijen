import state from "../homepage/state";
import { setEditMode } from "./edit-mode";

document.addEventListener("DOMContentLoaded", () => {

    const button = document.querySelector(".verify-button");

    if (!button) return;

    button.addEventListener("click", login);

});

async function login() {

    const input = document.querySelector(".verify-input");

    const code = input.value.trim();

    if (code === "") {

        alert("Kode Edit harus diisi.");

        input.focus();

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
                    .content

            },

            body: JSON.stringify({

                security_code: code

            })

        });

        const result = await response.json();

        if (!result.success) {

            alert(result.message ?? "Kode Edit salah.");

            buttonLoading(false);

            return;

        }

        setEditMode(true);

        // sementara
        // nanti kita ganti menjadi renderEditMode();

        bootstrap.Modal
            .getInstance(document.getElementById("manageModal"))
            .hide();

    } catch (error) {

        console.error(error);

        alert("Terjadi kesalahan.");

    }

    buttonLoading(false);

}

function buttonLoading(status) {

    const button = document.querySelector(".verify-button");

    if (!button) return;

    button.disabled = status;

    button.innerHTML = status
        ? "Memproses..."
        : "Masuk";

}