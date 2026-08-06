@props([
    'categories',
])

<div class="category-filter">

    <button
        class="category-chip active"
        data-category="all">

        Semua

    </button>

    @foreach($categories as $category)

        <button
            class="category-chip"
            data-category="{{ $category->slug }}">

            {{ $category->name }}

        </button>

    @endforeach

</div>