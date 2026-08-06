@props([
'card',
])

@if($card->links->count() === 1)

<x-homepage.single-card
    :link="$card->links->first()" />

@else

<section
    class="portal-group"
    data-card
    data-type="group"
    data-category="{{ $card->categories->pluck('slug')->implode(',') }}"
    data-search="{{ $card->search_text }}">

    <div class="portal-group-header" data-accordion>

        <div class="app-left">

            <div class="app-icon">

                <i class="bi bi-grid"></i>

            </div>

            <div>

                <div class="app-title">

                    {{ $card->title }}

                </div>

                <div class="app-subtitle">

                    {{ $card->description }}

                </div>

            </div>

        </div>

        <i class="bi bi-chevron-down portal-arrow" data-arrow></i>

    </div>

    <div class="portal-group-body" data-body>

        @foreach($card->links as $link)

        <x-homepage.child-card
            :link="$link" />

        @endforeach

    </div>

</section>

@endif