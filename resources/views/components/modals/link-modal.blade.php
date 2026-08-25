<div class="modal fade" id="linkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="linkModalTitle">
                    <i class="bi bi-link-45deg me-2"></i>Tambah Tautan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="linkForm">
                <input type="hidden" id="linkUuid" name="uuid">
                <input type="hidden" id="linkCardUuid" name="card_uuid">
                <input type="hidden" id="linkIcon" name="icon" value="link-45deg">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="linkTitle" class="form-label fw-bold">Judul Tautan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="linkTitle" name="title" required placeholder="Contoh: SIPGN SIPHR Absensi">
                    </div>

                    <div class="mb-3">
                        <label for="linkSubtitle" class="form-label fw-bold">Sub-judul / Keterangan Singkat</label>
                        <input type="text" class="form-control" id="linkSubtitle" name="subtitle" placeholder="Contoh: Absensi Harian Pegawai">
                    </div>

                    <div class="mb-3">
                        <label for="linkUrl" class="form-label fw-bold">URL Tautan / Email / Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="linkUrl" name="url" required placeholder="https://... atau mailto:admin@domain.com atau tel:+62812...">
                    </div>

                    {{-- ICON PICKER --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Ikon atau URL Gambar Flaticon / PNG / SVG</label>
                        <div class="icon-picker-wrapper border rounded p-3">
                            {{-- Input Kustom Flaticon / Custom URL --}}
                            <div class="mb-3">
                                <label for="customIconUrl" class="form-label small text-muted">Link Gambar Kustom (URL Flaticon / PNG / SVG) atau Kode Ikon FontAwesome (fa-solid fa-star)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="bi bi-image"></i></span>
                                    <input type="text" id="customIconUrl" class="form-control" placeholder="https://cdn-icons-png.flaticon.com/... atau fa-solid fa-shield">
                                    <button class="btn btn-outline-secondary" type="button" id="btnApplyCustomIcon">Gunakan</button>
                                </div>
                            </div>

                            {{-- Icon Library Filter Tabs --}}
                            <div class="icon-filter-tabs d-flex flex-wrap gap-1 mb-3">
                                <button type="button" class="btn btn-sm btn-primary icon-tab-btn active" data-lib="all">🌟 Populer</button>
                                <button type="button" class="btn btn-sm btn-outline-primary icon-tab-btn" data-lib="remix">🔮 Remix Icon (Lengkap)</button>
                                <button type="button" class="btn btn-sm btn-outline-primary icon-tab-btn" data-lib="bootstrap">🎨 Bootstrap Icons</button>
                                <button type="button" class="btn btn-sm btn-outline-primary icon-tab-btn" data-lib="fontawesome">⚡ Font Awesome</button>
                            </div>

                            {{-- Preview & Search --}}
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="icon-preview-box d-flex align-items-center justify-content-center rounded overflow-hidden"
                                     style="width:52px;height:52px;background:#f1f5f9;font-size:1.6rem;flex-shrink:0;">
                                    <div id="iconPickerPreviewContainer">
                                        <i id="iconPickerPreview" class="bi bi-link-45deg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" id="iconSearch" class="form-control form-control-sm"
                                           placeholder="🔍 Cari dari ribuan ikon… (contoh: user, document, drive, chart, lock)">
                                    <small class="text-muted">Klik ikon di bawah atau masukkan URL Flaticon kustom di atas</small>
                                </div>
                            </div>
                            {{-- Icon Grid --}}
                            <div id="iconGrid" class="icon-grid">
                                {{-- Diisi via JS --}}
                            </div>
                        </div>
                    </div>

                    {{-- COLOR SELECTOR --}}
                    <div class="mb-3">
                        <label for="linkColor" class="form-label fw-bold">Warna Aksen Tautan / Badge Ikon</label>
                        <select class="form-select" id="linkColor" name="color">
                            <option value="">Default (Sesuai Ikon)</option>
                            <option value="blue">🔵 Blue Accent</option>
                            <option value="emerald">🟢 Emerald Green</option>
                            <option value="purple">🟣 Deep Purple</option>
                            <option value="amber">🟡 Amber Gold</option>
                            <option value="rose">🔴 Crimson Rose</option>
                            <option value="cyan">🔵 Teal Cyan</option>
                            <option value="orange">🟠 Sunset Orange</option>
                            <option value="indigo">🔮 Indigo</option>
                            <option value="gradient-blue">🌊 Ocean Gradient</option>
                            <option value="gradient-purple">✨ Cosmic Gradient</option>
                            <option value="gradient-sunset">🌅 Sunset Gradient</option>
                            <option value="gradient-emerald">🌲 Forest Gradient</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="linkExpiredAt" class="form-label fw-bold">Tanggal Kadaluarsa (Opsional)</label>
                        <input type="date" class="form-control" id="linkExpiredAt" name="expired_at">
                        <small class="text-muted">Kosongkan jika berlaku selamanya. Minimal besok.</small>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveLink">
                        <i class="bi bi-check-circle me-1"></i>Simpan Tautan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
