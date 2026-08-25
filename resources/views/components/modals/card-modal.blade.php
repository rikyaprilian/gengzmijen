{{-- ============================================================
     UNIFIED MODAL: Tambah Link / Group Link (New Card + Links)
     ============================================================ --}}
<div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="cardModalTitle">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Link / Group Link
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- ===================== FORM TAMBAH BARU (Unified) ===================== --}}
            <form id="unifiedLinkForm">
                <div class="modal-body p-4" id="unifiedFormBody">

                    {{-- SECTION: Nama Group (diatas, muncul saat >= 2 link) --}}
                    <div id="groupSection" class="d-none mb-4">
                        <div class="alert alert-info py-2 px-3 mb-3 small">
                            <i class="bi bi-info-circle me-1"></i>
                            Karena ada lebih dari 1 link, beri nama <strong>Grup / Card</strong> ini.
                        </div>
                        <div class="mb-3">
                            <label for="groupTitle" class="form-label fw-bold">
                                Nama Grup / Card <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="groupTitle" name="group_title"
                                   placeholder="Contoh: SIPGN, Aplikasi SDM, Portal Internal">
                        </div>
                        <div class="mb-3">
                            <label for="groupDescription" class="form-label fw-bold">Deskripsi Grup (Opsional)</label>
                            <textarea class="form-control" id="groupDescription" name="group_description"
                                      rows="2" placeholder="Penjelasan singkat mengenai grup ini"></textarea>
                        </div>
                        <hr class="my-3">
                    </div>

                    {{-- SECTION: Link Rows --}}
                    <div id="linkRowsContainer"></div>

                    {{-- Tombol tambah link --}}
                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnAddLinkRow">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Link Lagi
                        </button>
                    </div>

                    {{-- SECTION: Pengaturan Lanjutan (collapsed) --}}
                    <hr class="my-3">
                    <div>
                        <button type="button" class="btn btn-link btn-sm text-muted px-0 text-decoration-none"
                                data-bs-toggle="collapse" data-bs-target="#advancedCardSettings" aria-expanded="false">
                            <i class="bi bi-sliders me-1"></i>Pengaturan Lanjutan (Warna, Badge, Kadaluarsa, Kategori)
                        </button>
                        <div class="collapse mt-3" id="advancedCardSettings">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cardBadge" class="form-label fw-bold">Badge Teks (Opsional)</label>
                                    <input type="text" class="form-control" id="cardBadge" name="badge"
                                           placeholder="Contoh: POPULER / BARU">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cardColor" class="form-label fw-bold">Warna Aksen Card</label>
                                    <select class="form-select" id="cardColor" name="color">
                                        <option value="">Default (Biru)</option>
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
                            </div>
                            <div class="mb-3">
                                <label for="cardExpiredAt" class="form-label fw-bold">Tanggal Kadaluarsa Card (Opsional)</label>
                                <input type="date" class="form-control" id="cardExpiredAt" name="expired_at">
                                <small class="text-muted">Kosongkan jika berlaku selamanya. Minimal besok. Berlaku untuk semua link di card ini.</small>
                                <div id="cardExpiredAtError" class="text-danger small d-none mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Tanggal minimal adalah besok.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategori Card</label>
                                <div id="cardCategoriesContainer" class="d-flex flex-wrap gap-2 pt-1">
                                    @foreach($categories as $cat)
                                        <div class="form-check form-check-inline category-checkbox-item px-3 py-2 border rounded">
                                            <input class="form-check-input" type="checkbox" name="category_ids[]"
                                                   value="{{ $cat->id }}" id="cat_check_{{ $cat->id }}">
                                            <label class="form-check-label ms-1" for="cat_check_{{ $cat->id }}">
                                                <i class="bi bi-{{ $cat->icon }} me-1 text-{{ $cat->color }}"></i>{{ $cat->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveUnified">
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                </div>
            </form>

            {{-- ===================== FORM EDIT CARD LAMA (tersembunyi) ===================== --}}
            <form id="cardForm" class="d-none">
                <input type="hidden" id="cardUuid" name="uuid">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="cardTitle" class="form-label fw-bold">Judul Kartu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cardTitle" name="title" required
                               placeholder="Contoh: SIPGN / Tauwas Care">
                    </div>
                    <div class="mb-3">
                        <label for="cardDescription" class="form-label fw-bold">Deskripsi / Sub-judul</label>
                        <textarea class="form-control" id="cardDescription" name="description" rows="2"
                                  placeholder="Penjelasan singkat mengenai grup atau tautan ini"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cardBadgeEdit" class="form-label fw-bold">Badge Teks (Opsional)</label>
                            <input type="text" class="form-control" id="cardBadgeEdit" name="badge"
                                   placeholder="Contoh: POPULER / BARU">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cardColorEdit" class="form-label fw-bold">Warna Aksen Kartu</label>
                            <select class="form-select" id="cardColorEdit" name="color">
                                <option value="">Default (Biru)</option>
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
                    </div>
                    <div class="mb-3">
                        <label for="cardExpiredAtEdit" class="form-label fw-bold">Tanggal Kadaluarsa (Opsional)</label>
                        <input type="date" class="form-control" id="cardExpiredAtEdit" name="expired_at">
                        <small class="text-muted">Kosongkan jika berlaku selamanya. Minimal besok.</small>
                        <div id="cardExpiredAtEditError" class="text-danger small d-none mt-1">
                            <i class="bi bi-exclamation-triangle me-1"></i>Tanggal minimal adalah besok.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Kartu</label>
                        <div id="cardCategoriesContainerEdit" class="d-flex flex-wrap gap-2 pt-1">
                            @foreach($categories as $cat)
                                <div class="form-check form-check-inline category-checkbox-item px-3 py-2 border rounded">
                                    <input class="form-check-input" type="checkbox" name="category_ids[]"
                                           value="{{ $cat->id }}" id="cat_check_edit_{{ $cat->id }}">
                                    <label class="form-check-label ms-1" for="cat_check_edit_{{ $cat->id }}">
                                        <i class="bi bi-{{ $cat->icon }} me-1 text-{{ $cat->color }}"></i>{{ $cat->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveCard">
                        <i class="bi bi-check-circle me-1"></i>Simpan Kartu
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Mini Icon Picker (fixed, outside modal overflow) --}}
<div id="miniIconPicker" class="d-none" style="position:fixed;z-index:9999;background:#fff;border:1px solid #dee2e6;border-radius:0.5rem;box-shadow:0 8px 24px rgba(0,0,0,.15);width:320px;max-height:320px;overflow:hidden;display:flex;flex-direction:column;">
    <div class="p-2 border-bottom d-flex gap-1 align-items-center">
        <input type="text" id="miniIconSearch" class="form-control form-control-sm flex-grow-1" placeholder="Cari ikon…">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCloseMiniPicker">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div id="miniIconGrid" style="overflow-y:auto;flex:1;padding:6px;display:grid;grid-template-columns:repeat(6,1fr);gap:4px;"></div>
</div>
