@props([
'link',
])

<a
    href="{{ $link->url }}"
    target="_blank"
    class="app-card"
    data-card
    data-type="single"
    data-category="{{ $link->card->categories->pluck('slug')->implode(',') }}"
    data-search="{{ $link->search_text }}">

    <div class="app-left">

        <div class="app-icon">

            <i class="bi bi-{{ $link->icon }}"></i>

        </div>

        <div>

            <div class="app-title">

                {{ $link->title }}

            </div>

            <div class="app-subtitle">

                {{ $link->subtitle }}

            </div>

        </div>

    </div>

    <button
        type="button"
        class="copy-btn"
        data-url="{{ $link->url }}">

        <i class="bi bi-copy"></i>

    </button>

</a>