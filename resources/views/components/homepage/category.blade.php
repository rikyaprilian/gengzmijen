@props([
    'category',
])

<section class="homepage-category">

    <div class="homepage-category-title">

        {{ $category->name }}

    </div>

    {{ $slot }}

</section>