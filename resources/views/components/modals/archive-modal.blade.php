{{-- ============================================================
     ARCHIVE MODAL: Arsip Card & Tautan (Soft Delete + Expired)
     ============================================================ --}}
<div class="modal fade" id="archiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-archive me-2"></i>Arsip — Kartu & Tautan Terhapus / Kadaluarsa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">

                {{-- Loading State --}}
                <div id="archiveLoading" class="text-center py-5">
                    <div class="spinner-border text-secondary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat arsip…</p>
                </div>

                {{-- Content (hidden until loaded) --}}
                <div id="archiveContent" class="d-none">
                    <ul class="nav nav-tabs px-3 pt-3" id="archiveTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-archived-cards" data-bs-toggle="tab"
                                    data-bs-target="#pane-archived-cards" type="button" role="tab">
                                <i class="bi bi-card-heading me-1"></i>Kartu Terhapus
                                <span class="badge bg-danger ms-1" id="archiveCardCount">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-archived-links" data-bs-toggle="tab"
                                    data-bs-target="#pane-archived-links" type="button" role="tab">
                                <i class="bi bi-link-45deg me-1"></i>Tautan Kadaluarsa / Terhapus
                                <span class="badge bg-danger ms-1" id="archiveLinkCount">0</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-3">
                        {{-- Tab: Archived Cards --}}
                        <div class="tab-pane fade show active" id="pane-archived-cards" role="tabpanel">
                            <div id="archiveCardsList">
                                <p class="text-muted text-center py-4">Tidak ada kartu yang dihapus.</p>
                            </div>
                        </div>

                        {{-- Tab: Archived Links --}}
                        <div class="tab-pane fade" id="pane-archived-links" role="tabpanel">
                            <div id="archiveLinksList">
                                <p class="text-muted text-center py-4">Tidak ada tautan kadaluarsa atau terhapus.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <small class="text-muted me-auto">
                    <i class="bi bi-info-circle me-1"></i>
                    Item yang dipulihkan akan kembali aktif. Hapus Permanen tidak dapat dibatalkan.
                </small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

