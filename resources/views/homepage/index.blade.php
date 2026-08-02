@extends('layouts.app')

@section('content')

<div class="container py-5">

    <h2 class="mb-4">
        Homepage
    </h2>

    @foreach($categories as $category)

        <h4 class="mt-4">
            {{ $category->name }}
        </h4>

        @foreach($category->cards as $card)

            <div class="border rounded p-3 mb-3">

                <strong>{{ $card->title }}</strong>

                <br>

                <small>{{ $card->description }}</small>

                <ul class="mt-3">

                    @foreach($card->links as $link)

                        <li>

                            {{ $link->title }}

                        </li>

                    @endforeach

                </ul>

            </div>

        @endforeach

    @endforeach

</div>

@endsection