<?php
namespace App\Repositories;

use App\Models\Book;
use Illuminate\Support\Facades\Http;


class BookRepository
{
    public function findByTitle($title)
    {
        return Book::when($title, fn($query) => $query->title($title));
    }

    public function applyFilter($books, $filter)
    {
        switch ($filter) {
            case 'popular_last_month':
                return $books->popularLastMonth();
            case 'popular_last_6months':
                return $books->popularLast6Months();
            case 'highest_rated_last_month':
                return $books->highestRatedLastMonth();
            case 'highest_rated_last_6months':
                return $books->highestRatedLast6Months();
            default:
                return $books->latest()->withAvgRating()->withReviewsCount();
        }
    }

    public function getGoogleBooks($query)
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
        ]);

        return collect($response->json()['items'] ?? []);
    }

    public function getCachedBook($bookId)
    {
        $cacheKey = 'book:' . $bookId;
        return cache()->remember(
            $cacheKey,
            3600,
            fn() => Book::with([
                'reviews' => fn($query) => $query->latest()
            ])->withAvgRating()->withReviewsCount()->findOrFail($bookId)
        );
    }
}
