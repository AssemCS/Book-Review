<?php

namespace App\Http\Controllers;

use App\Repositories\BookRepository;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BookController extends Controller
{
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        $books = $this->bookRepository->findByTitle($title);
        $books = $this->bookRepository->applyFilter($books, $filter);

        $query = $request->filled('query') ? $request->input('query') : 'laravel';
        $books = $this->bookRepository->getGoogleBooks($query);

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedData = $books->forPage($currentPage, $perPage);

        $books = new LengthAwarePaginator($pagedData, $books->count(), $perPage);

        return view('books.index', compact('books'));
    }

    public function show(Book $book)
    {
        $book = $this->bookRepository->getCachedBook($book->id);

        return view('books.show', ['book' => $book]);
    }
}
