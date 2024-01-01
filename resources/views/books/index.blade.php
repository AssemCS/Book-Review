@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-2x1">Books</h1>

    <form method="GET" action="{{route('books.index')}}" class="mb-4 flex items-center space-x-2">
        <input type="text" name="title" placeholder="Search by title"
               value="{{ request('title') }}" class="input h-10">
        <input type="hidden" name="filter" value="{{request('filter')}}">
        <button type="submit" class="btn h-10">Search</button>
        <a href="{{ route('books.index') }}" class="btn h-10">Clear</a>
    </form>

    <div class="filter-container mb-4 flex">
        @php
            $filters = [
                ''=>'Latest',
                'popular_last_month'=>'Popular Last Month',
                'popular_last_6months'=>'Popular Last 6 Months',
                'highest_rated_last_month'=>'Highest Rated Last Month',
                'highest_rated_last_6months'=>'Highest Rated Last 6 Months'
];
        @endphp

        @foreach($filters as $key=>$label)
            <a href="{{route('books.index', [...request()->query() ,'filter'=>$key])}}"
               class="{{request('filter') === $key || (request('filter')===null && $key==='') ? 'filter-item-active' : 'filter-item'}}">
                {{$label}}
            </a>
        @endforeach
    </div>

    <ul> @forelse($books as $book)
            <li class="mb-4">
                <div class="book-item">
                    <div
                        class="flex flex-wrap items-center justify-between">
                        <div class="w-full flex-grow sm:w-auto">
                            <a href="{{ route('books.show', ['book' => $book['id']])}}"
                               class="book-title">{{$book['volumeInfo']['title']}}</a>
                            <span class="book-author">{{$book['volumeInfo']['authors'][0]}}</span>
                        </div>
                        <div>
                            <div class="book-rating">
                                @if(isset($book['volumeInfo']['averageRating']))
                                    <x-star-rating :rating="$book['volumeInfo']['averageRating']"/>
                                @else
                                    <span class="text-red-500">Rating not available</span>
                                @endif
                            </div>
                            <div class="book-review-count">
                                @if(isset($book['volumeInfo']['ratingsCount']))
                                    out
                                    of {{ $book['volumeInfo']['ratingsCount'] }} {{ Str::plural('rating', $book['volumeInfo']['ratingsCount']) }}
                                @else
                                    Rating count not available
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="mb-4">
                <div class="empty-book-item">
                    <p class="empty-text">No books found</p>
                    <a href="{{route('books.index')}}" class="reset-link">Reset criteria</a>
                </div>
            </li>
        @endforelse
    </ul>
    @if($books->count())
        <nav class="mt-4">
            {{$books->links()}}
        </nav>
    @endif
@endsection
