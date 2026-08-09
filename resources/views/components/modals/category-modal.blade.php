<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-tags me-2"></i>Kelola Kategori Portal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Form Tambah Kategori -->
                <form id="categoryForm" class="mb-4 p-3 border rounded bg-light">
                    <h6 class="fw-bold mb-3" id="categoryFormTitle">Tambah Kategori Baru</h6>
                    <input type="hidden" id="catUuid" name="uuid">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control" id="catName" name="name" required placeholder="Nama Kategori (contoh: Harian)">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="catIcon" name="icon">
                                <option value="house">House (Beranda)</option>
                                <option value="video">Video (Meeting)</option>
                                <option value="clipboard-list">Clipboard (Pelaporan)</option>
                                <option value="globe">Globe (Website)</option>
                                <option value="star">Star (Favorit)</option>
                                <option value="briefcase">Briefcase (Kerja)</option>
                                <option value="tag">Tag (Lainnya)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="catColor" name="color">
                                <option value="primary">Biru (Primary)</option>
                                <option value="success">Hijau (Success)</option>
                                <option value="warning">Kuning (Warning)</option>
                                <option value="danger">Merah (Danger)</option>
                                <option value="info">Cyan (Info)</option>
                                <option value="dark">Hitam (Dark)</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary" id="btnSaveCategory">
                                <i class="bi bi-plus-lg me-1"></i>Simpan
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Daftar Kategori Existing -->
                <h6 class="fw-bold mb-2">Daftar Kategori Saat Ini</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Icon</th>
                                <th>Nama Kategori</th>
                                <th>Slug</th>
                                <th>Warna</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="categoryTableBody">
                            @foreach($categories as $cat)
                                <tr data-uuid="{{ $cat->uuid }}">
                                    <td><i class="bi bi-{{ $cat->icon }} text-{{ $cat->color }} fs-5"></i></td>
                                    <td class="fw-semibold">{{ $cat->name }}</td>
                                    <td><code>{{ $cat->slug }}</code></td>
                                    <td><span class="badge bg-{{ $cat->color }}">{{ $cat->color }}</span></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-warning me-1 btn-edit-cat"
                                                data-uuid="{{ $cat->uuid }}"
                                                data-name="{{ $cat->name }}"
                                                data-icon="{{ $cat->icon }}"
                                                data-color="{{ $cat->color }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-cat"
                                                data-uuid="{{ $cat->uuid }}"
                                                data-name="{{ $cat->name }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
