@props([
'card',
])

@if($card->links->count() === 1)

<x-homepage.single-card
    :link="$card->links->first()" />

@else

<section
    class="portal-group {{ $card->color ? 'card-theme-' . $card->color : '' }}"
    data-card
    data-uuid="{{ $card->uuid }}"
    data-type="group"
    data-category="{{ $card->categories->pluck('slug')->implode(',') }}"
    data-search="{{ $card->search_text }}">

    <div class="portal-group-header" data-accordion>

        <div class="app-left">
            <div class="card-drag-handle me-2 edit-mode-only" title="Geser untuk mengubah urutan">
                <i class="bi bi-grip-vertical text-muted fs-5"></i>
            </div>

            <div class="app-icon {{ $card->color ? 'icon-badge-' . $card->color : '' }}">

                <i class="bi bi-grid-fill"></i>

            </div>

            <div>

                <div class="app-title d-flex align-items-center gap-2">

                    <span>{{ $card->title }}</span>

                    @if($card->badge)
                        <span class="badge bg-primary rounded-pill badge-animated">{{ $card->badge }}</span>
                    @endif

                </div>

                <div class="app-subtitle">

                    {{ $card->description }}

                </div>

            </div>

        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="edit-mode-actions edit-mode-only">
                <button type="button" class="btn btn-sm btn-outline-success btn-add-link-to-card me-1" data-card-uuid="{{ $card->uuid }}" title="Tambah Tautan ke Kartu Ini">
                    <i class="bi bi-plus-lg"></i> Tautan
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-card me-1" 
                        data-uuid="{{ $card->uuid }}"
                        data-title="{{ $card->title }}"
                        data-description="{{ $card->description }}"
                        data-badge="{{ $card->badge }}"
                        data-color="{{ $card->color }}"
                        data-expired-at="{{ $card->expired_at ? $card->expired_at->format('Y-m-d') : '' }}"
                        data-categories="{{ json_encode($card->categories->pluck('id')) }}"
                        title="Edit Kartu">
                    <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-card me-2" 
                        data-uuid="{{ $card->uuid }}"
                        data-title="{{ $card->title }}"
                        title="Hapus Kartu">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <i class="bi bi-chevron-down portal-arrow" data-arrow></i>
        </div>

    </div>

    <div class="portal-group-body links-sortable-container" data-body data-card-uuid="{{ $card->uuid }}">

        @foreach($card->links as $link)

        <x-homepage.child-card
            :link="$link" />

        @endforeach

    </div>

</section>

@endif