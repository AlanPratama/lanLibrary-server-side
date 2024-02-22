<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Type;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('categories', 'types')->get();

        return response()->json([
            'status' => 'success',
            'data' => $books,
        ]);
    }


    public function detail($slug)
    {
        $book = Book::findOrFail($slug);

        if ($book) {
            return response()->json([
                'status' => 'success',
                'data' => $book
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'BOOK NOT FOUND'
            ]);
        }
    }

    public function add(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'type_id' => 'required',
            'total_book' => 'required',
            'title' => 'required|unique:books,title',
            'writer' => 'required',
            'publisher' => '',
            'description' => 'required',
            'year' => '',
            'page' => 'required',
            'cover' => '',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ]);
        } else {


            try {
                $data = [
                    'type_id' => $req->type_id,
                    'total_book' => $req->total_book,
                    'title' => $req->title,
                    'writer' => $req->writer,
                    'publisher' => $req->publisher,
                    'description' => $req->description,
                    'year' => $req->year,
                    'page' => $req->page,
                    'cover' => '/assets/404-book-img.png',
                ];

                if ($req->hasFile('cover')) {
                    $file = $req->file('cover');
                    $fileName = Str::slug($req->title) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('books', $fileName);
                    $data['cover'] =  '/storage/' . $path;
                }

                $book = Book::create($data);

                if ($req->has('categories')) {
                    $book->categories()->sync($req->input('categories'));
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully Create A New Book',
                    'data' => $book,
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }


    public function edit(Request $req, $slug)
    {
        try {
            $book = Book::where('slug' . $slug)->first();
            if ($book) {
                $validator = Validator::make($req->all(), [
                    'type_id' => 'required',
                    'total_book' => 'required',
                    'title' => 'required|unique:books,title,' . $book->id,
                    'writer' => 'required',
                    'publisher' => '',
                    'description' => 'required',
                    'year' => '',
                    'page' => 'required',
                    'cover' => '',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $validator->errors()
                    ]);
                }

                $data = [
                    'type_id' => $req->type_id,
                    'total_book' => $req->total_book,
                    'title' => $req->title,
                    'writer' => $req->writer,
                    'publisher' => $req->publisher,
                    'description' => $req->description,
                    'year' => $req->year,
                    'page' => $req->page,
                    'cover' => $book->cover,
                ];

                if ($req->hasFile('cover')) {
                    if ($data['cover']) {
                        Storage::delete($data['cover']);
                    }
                } else {
                    $fileName = Str::slug($req->title) . '.' . $req->file('cover')->getClientOriginalExtension();
                    $path = $req->file('cover')->storeAs('books', $fileName);
                    $data['cover'] = '/storage/' . $path;
                }

                $book->slug = null;
                $book->save($data);

                if ($req->has('categories')) {
                    $book->categories()->sync($req->input('categories'));
                } else {
                    $book->categories()->detach();
                }


                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully Updated Book Information',
                    'data' => $book,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'BOOK NOT FOUND'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function delete($slug)
    {
        try {
            // RENTLOGS, REVIEW, TYPE, CATEGORIES, FAVORITES
            $book = Book::where('slug', $slug)->first();

            if ($book) {
                $book->types->delete();
                $book->rentlogs()->delete();
                $book->reviews()->delete();
                $book->favorites()->delete();
                $book->categories()->detach();

                Storage::delete($book->cover);
                $book->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully Deleted A Book',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'BOOK NOT FOUND'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }




    // END CRUD || END CRUD || END CRUD || END CRUD || END CRUD || END CRUD || END CRUD || END CRUD || END CRUD ||


    public function fav(Request $req, $code)
    {
        $fav = Favorite::where('book_id', $req->book_id)->where('user_id', Auth::user()->id)->first();

        try {
            if ($fav) {
                $data = [
                    'user_id' => Auth::user(),
                    'code' => Str::random(4) . '-' . Auth::user()->username,
                    'book_id' => $req->book_id
                ];

                Favorite::create($data);

                return response()->json([
                    'status' => 'success',
                    'message' => 'SUCCESSFULLY FAVORITE THIS BOOK',
                ]);
            } else {
                $fav->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'SUCCESSFULLY UNFAVORITE THIS BOOK',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
