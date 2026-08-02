@props([
    'card',
])

@if($card->links->count() === 1)

    <x-homepage.single-card
        :link="$card->links->first()" />

@else

<section class="portal-group">

    <div class="portal-group-header parent-toggle">

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

        <i class="bi bi-chevron-down portal-arrow"></i>

    </div>

    <div class="portal-group-body d-none">

        @foreach($card->links as $link)

            <x-homepage.child-card
                :link="$link"/>

        @endforeach

    </div>

</section>

@endif