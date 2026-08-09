<div class="modal fade"
    id="manageModal"
    tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-shield-lock me-2"></i>

                    Verifikasi

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body py-5">

                <form id="manageLoginForm" class="verify-box">

                    <div class="verify-icon">

                        <i class="bi bi-key-fill"></i>

                    </div>

                    <h4>

                        Masukkan Security Code

                    </h4>

                    <p>

                        Masukkan kode keamanan untuk masuk ke Mode Edit Portal.

                    </p>

                    <input
                        type="password"
                        class="form-control verify-input"
                        autocomplete="current-password"
                        placeholder="Security Code (Default: gass)">

                    <div id="manageLoginError" class="alert alert-danger py-2 d-none mt-3"></div>

                    <button
                        type="submit"
                        class="btn btn-primary verify-button">
                        Masuk Mode Edit
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>