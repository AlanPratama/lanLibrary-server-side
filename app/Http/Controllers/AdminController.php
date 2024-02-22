<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rentlogs;
use App\Models\User;

class AdminController extends Controller
{
    public function test()
    {
        $rent = Rentlogs::with('users', 'books')->get();
        $book = Book::with('types', 'categories')->paginate(1);
        $user = User::all();

        return response()->json([
            'status' => 'success',
            'data' => [
                'rent' => $rent,
                'book' => $book,
                'user' => $user,
            ],
        ]);
    }



}
