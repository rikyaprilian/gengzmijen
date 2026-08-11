<div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-gear-fill me-2"></i>Pengaturan Portal & Keamanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="settingsForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="settingPortalName" class="form-label fw-bold">Nama Portal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="settingPortalName" name="portal_name" value="{{ $settings['portal_name'] }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="settingHomepageMessage" class="form-label fw-bold">Pesan / Sub-judul Beranda</label>
                        <textarea class="form-control" id="settingHomepageMessage" name="homepage_message" rows="3">{{ $settings['homepage_message'] }}</textarea>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="settingSecurityCode" class="form-label fw-bold text-danger">
                            <i class="bi bi-shield-lock-fill me-1"></i>Security Code / Password Edit Mode <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="settingSecurityCode" name="security_code" value="{{ $settings['security_code'] }}" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnToggleSecCode">
                                <i class="bi bi-eye-slash" id="secCodeEyeIcon"></i>
                            </button>
                        </div>
                        <small class="text-muted">Kode keamanan ini digunakan untuk masuk ke Mode Edit saat menekan tombol "Kelola Portal".></small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveSettings">
                        <i class="bi bi-check-circle me-1"></i>Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
