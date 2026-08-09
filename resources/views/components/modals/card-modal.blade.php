<div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="cardModalTitle">
                    <i class="bi bi-card-heading me-2"></i>Tambah Kartu Portal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cardForm">
                <input type="hidden" id="cardUuid" name="uuid">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="cardTitle" class="form-label fw-bold">Judul Kartu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cardTitle" name="title" required placeholder="Contoh: SIPGN / Tauwas Care">
                    </div>

                    <div class="mb-3">
                        <label for="cardDescription" class="form-label fw-bold">Deskripsi / Sub-judul</label>
                        <textarea class="form-control" id="cardDescription" name="description" rows="2" placeholder="Penjelasan singkat mengenai grup atau tautan ini"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cardBadge" class="form-label fw-bold">Badge Teks (Opsional)</label>
                            <input type="text" class="form-control" id="cardBadge" name="badge" placeholder="Contoh: POPULER / BARU">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cardColor" class="form-label fw-bold">Warna Aksen Kartu</label>
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
                        <label for="cardExpiredAt" class="form-label fw-bold">Tanggal Kadaluarsa (Opsional)</label>
                        <input type="datetime-local" class="form-control" id="cardExpiredAt" name="expired_at">
                        <small class="text-muted">Kosongkan jika berlaku selamanya</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Kartu</label>
                        <div id="cardCategoriesContainer" class="d-flex flex-wrap gap-2 pt-1">
                            @foreach($categories as $cat)
                                <div class="form-check form-check-inline category-checkbox-item px-3 py-2 border rounded">
                                    <input class="form-check-input" type="checkbox" name="category_ids[]" value="{{ $cat->id }}" id="cat_check_{{ $cat->id }}">
                                    <label class="form-check-label ms-1" for="cat_check_{{ $cat->id }}">
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
