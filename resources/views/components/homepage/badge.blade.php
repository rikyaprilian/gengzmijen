@props([
    'text' => null,
])

@if(filled($text))

<span class="homepage-badge">

    {{ $text }}

</span>

@endif