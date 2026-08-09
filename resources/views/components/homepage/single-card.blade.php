@props([
'link',
])

<div
    class="app-card {{ $link->color ? 'card-theme-' . $link->color : ($link->card->color ? 'card-theme-' . $link->card->color : '') }}"
    data-card
    data-uuid="{{ $link->card->uuid }}"
    data-type="single"
    data-category="{{ $link->card->categories->pluck('slug')->implode(',') }}"
    data-search="{{ $link->search_text }}">

    <div class="card-drag-handle me-2 edit-mode-only" title="Geser untuk mengubah urutan">
        <i class="bi bi-grip-vertical text-muted fs-5"></i>
    </div>

    <a href="{{ $link->url }}" target="_blank" class="app-left text-decoration-none text-reset flex-grow-1">

        <div class="app-icon {{ $link->color ? 'icon-badge-' . $link->color : ($link->card->color ? 'icon-badge-' . $link->card->color : '') }}">
            @if(\Illuminate\Support\Str::startsWith($link->icon, ['http://', 'https://', 'data:']) || \Illuminate\Support\Str::contains($link->icon, ['.png', '.svg', '.jpg', '.webp', '/']))
                <img src="{{ $link->icon }}" alt="{{ $link->title }}" class="app-icon-custom-img">
            @elseif(\Illuminate\Support\Str::startsWith($link->icon, ['fa-', 'fa ', 'fas ', 'far ', 'fab ', 'ri-']))
                <i class="{{ $link->icon }}"></i>
            @else
                <i class="bi bi-{{ $link->icon }}"></i>
            @endif
        </div>

        <div>

            <div class="app-title d-flex align-items-center gap-2">

                <span>{{ $link->title }}</span>

                @if($link->card->badge)
                    <span class="badge bg-primary rounded-pill badge-animated">{{ $link->card->badge }}</span>
                @endif

            </div>

            <div class="app-subtitle">

                {{ $link->subtitle }}

            </div>

        </div>

    </a>

    <div class="d-flex align-items-center gap-2">
        <button
            type="button"
            class="copy-btn me-1"
            data-url="{{ $link->url }}"
            title="Salin Link">

            <i class="bi bi-copy"></i>

        </button>

        <div class="edit-mode-actions edit-mode-only d-flex align-items-center gap-1">
            {{-- Tambah Tautan ke card ini (single → multi) --}}
            <button type="button"
                    class="btn btn-sm btn-outline-success btn-add-link-to-card"
                    data-card-uuid="{{ $link->card->uuid }}"
                    title="Tambah Tautan ke Kartu Ini">
                <i class="bi bi-plus-lg"></i>
            </button>

            {{-- Edit Kartu (judul, badge, kategori, expired) --}}
            <button type="button"
                    class="btn btn-sm btn-outline-primary btn-edit-card"
                    data-uuid="{{ $link->card->uuid }}"
                    data-title="{{ $link->card->title }}"
                    data-description="{{ $link->card->description }}"
                    data-badge="{{ $link->card->badge }}"
                    data-color="{{ $link->card->color }}"
                    data-expired-at="{{ $link->card->expired_at ? $link->card->expired_at->format('Y-m-d\TH:i') : '' }}"
                    data-categories="{{ json_encode($link->card->categories->pluck('id')) }}"
                    title="Edit Kartu">
                <i class="bi bi-pencil-square"></i>
            </button>

            {{-- Edit Link (url, icon, subtitle, expired) --}}
            <button type="button"
                    class="btn btn-sm btn-outline-secondary btn-edit-link"
                    data-uuid="{{ $link->uuid }}"
                    data-title="{{ $link->title }}"
                    data-subtitle="{{ $link->subtitle }}"
                    data-url="{{ $link->url }}"
                    data-icon="{{ $link->icon }}"
                    data-color="{{ $link->color }}"
                    data-expired-at="{{ $link->expired_at ? $link->expired_at->format('Y-m-d\TH:i') : '' }}"
                    title="Edit Tautan">
                <i class="bi bi-link-45deg"></i>
            </button>

            {{-- Hapus Kartu --}}
            <button type="button"
                    class="btn btn-sm btn-outline-danger btn-delete-card"
                    data-uuid="{{ $link->card->uuid }}"
                    data-title="{{ $link->card->title }}"
                    title="Hapus Kartu Ini">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>

</div>