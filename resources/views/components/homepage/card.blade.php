@props([
    'card',
])

<section class="links">

    @if($card->links->count() === 1)

        @php
            $link = $card->links->first();
        @endphp

        <x-homepage.child-card
            :link="$link" />

    @else

        <div class="app-parent-card parent-toggle">

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

            <i class="bi bi-chevron-down parent-arrow"></i>

        </div>

        <div class="child-links d-none">

            @foreach($card->links as $link)

                <x-homepage.child-card
                    :link="$link" />

            @endforeach

        </div>

    @endif

</section>