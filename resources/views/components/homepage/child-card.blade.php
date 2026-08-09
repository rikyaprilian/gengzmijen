@props([
    'link',
])

<div
    class="portal-item {{ $link->color ? 'link-theme-' . $link->color : '' }}"
    data-link
    data-uuid="{{ $link->uuid }}"
    data-search="{{ $link->search_text }}">

    <div class="link-drag-handle me-2 edit-mode-only" title="Geser untuk mengubah urutan">
        <i class="bi bi-grip-vertical text-muted fs-6"></i>
    </div>

    <a href="{{ $link->url }}" target="_blank" class="app-left text-decoration-none text-reset flex-grow-1">

        <div class="app-icon {{ $link->color ? 'icon-badge-' . $link->color : '' }}">
            @if(\Illuminate\Support\Str::startsWith($link->icon, ['http://', 'https://', 'data:']) || \Illuminate\Support\Str::contains($link->icon, ['.png', '.svg', '.jpg', '.webp', '/']))
                <img src="{{ $link->icon }}" alt="{{ $link->title }}" class="app-icon-custom-img">
            @elseif(\Illuminate\Support\Str::startsWith($link->icon, ['fa-', 'fa ', 'fas ', 'far ', 'fab ', 'ri-']))
                <i class="{{ $link->icon }}"></i>
            @else
                <i class="bi bi-{{ $link->icon }}"></i>
            @endif
        </div>

        <div>

            <div class="app-title">

                {{ $link->title }}

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

        <div class="edit-mode-actions edit-mode-only">
            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-link me-1"
                    data-uuid="{{ $link->uuid }}"
                    data-title="{{ $link->title }}"
                    data-subtitle="{{ $link->subtitle }}"
                    data-url="{{ $link->url }}"
                    data-icon="{{ $link->icon }}"
                    data-color="{{ $link->color }}"
                    data-expired-at="{{ $link->expired_at ? $link->expired_at->format('Y-m-d\TH:i') : '' }}"
                    title="Edit Tautan">
                <i class="bi bi-pencil"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-link"
                    data-uuid="{{ $link->uuid }}"
                    data-title="{{ $link->title }}"
                    title="Hapus Tautan">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>

</div>