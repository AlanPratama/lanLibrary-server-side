<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Type;
use App\Models\Writer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function getBooks(Request $req)
    {
        if ($req->paginate) {
            $books = Book::with('types', 'categories', 'writers')->paginate($req->paginate);

            return response()->json([
                'status' => 'success',
                'data' => $books
            ]);
        } else {
            $books = Book::all();

            return response()->json([
                'status' => 'success',
                'data' => $books
            ]);
        }
    }








    public function sideDishBook()
    {
        $types = Type::all();
        $writers = Writer::all();
        $categories = Category::all();

        return response()->json([
            'status' => 'success',
            'types' => $types,
            'writers' => $writers,
            'categories' => $categories
        ]);
    }

    public function index(Request $req)
    {
        if ($req->type) {
            $books = Book::with('categories', 'types', 'writers')
                ->orderBy('created_at', 'desc')
                ->where('title', 'LIKE', '%' . $req->title . '%')
                ->where('type_id', $req->type)
                ->paginate(15);
        } else {
            $books = Book::with('categories', 'types', 'writers')
                ->orderBy('created_at', 'desc')
                ->where('title', 'LIKE', '%' . $req->title . '%')
                ->paginate(15);
        }

        return response()->json([
            'status' => 'success',
            'data' => $books,
        ]);
    }


    public function detail($slug)
    {
        $book = Book::where('slug', $slug)->with('writers', 'types', 'categories')->first();

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
            'writer_id' => 'required',
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


            $type = Type::findOrFail($req->type_id);
            if ($type) {
                try {
                    $data = [
                        'type_id' => $req->type_id,
                        'writer_id' => $req->writer_id,
                        'total_book' => $req->total_book,
                        'title' => $req->title,
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
                    $type->total_book += 1;
                    $type->update();

                    if ($req->has('categories')) {
                        $book->categories()->sync($req->input('categories'));
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Success Create A New Book',
                        'data' => $book,
                    ]);
                } catch (Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ERROR! INVALID TYPE'
                ], 400);
            }
        }
    }


    public function edit(Request $req, $slug)
    {
        try {
            $book = Book::where('slug', $slug)->first();
            if ($book) {
                $validator = Validator::make($req->all(), [
                    'type_id' => 'required',
                    'total_book' => 'required',
                    'title' => 'required|unique:books,title,' . $book->id,
                    'writer_id' => 'required',
                    'publisher' => '',
                    'description' => 'required',
                    'year' => '',
                    'page' => 'required',
                    'cover' => '',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $validator->errors(),
                        'req' => $req->all()
                    ]);
                }

                $data = [
                    'type_id' => $req->type_id,
                    'writer_id' => $req->writer_id,
                    'total_book' => $req->total_book,
                    'title' => $req->title,
                    'publisher' => $req->publisher,
                    'description' => $req->description,
                    'year' => $req->year,
                    'page' => $req->page,
                    'cover' => $book->cover,
                ];

                if ($req->hasFile('cover')) {
                    if ($data['cover'] != '/assets/404-book-img.png') {
                        $img = str_replace('/storage', '', $data['cover']);
                        Storage::delete($img);
                    }
                    $fileName = Str::slug($req->title) . '.' . $req->file('cover')->getClientOriginalExtension();
                    $path = $req->file('cover')->storeAs('books', $fileName);
                    $data['cover'] = '/storage/' . $path;
                }

                if ($book->type_id) {
                    $oldType = $book->type_id;
                    if ($oldType == $req->type_id) {
                        $book->slug = null;
                        $book->update($data);
                    } else {
                        $oldType = Type::findOrFail($oldType);
                        $oldType->total_book -= 1;
                        $oldType->update();

                        $newType = Type::findOrFail($req->type_id);
                        $newType->total_book += 1;
                        $newType->update();

                        $book->slug = null;
                        $book->update($data);
                    }
                } else {
                    $newType = Type::findOrFail($req->type_id);
                    $newType->total_book += 1;
                    $newType->update();

                    $book->slug = null;
                    $book->update($data);
                }


                if ($req->has('categories')) {
                    $book->categories()->sync($req->input('categories'));
                } else {
                    $book->categories()->detach();
                }


                return response()->json([
                    'status' => 'success',
                    'message' => 'Success Updated Book Information',
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
            // RENTLOGS, REVIEW, CATEGORIES, FAVORITES
            $book = Book::where('slug', $slug)->first();

            if ($book) {
                $book->rentlogs()->delete();
                $book->reviews()->delete();
                $book->favorites()->delete();
                $book->categories()->detach();
                $book->types->total_book -= 1;
                $book->types->save();

                if ($book->cover != '/assets/404-book-img.png') {
                    $img = str_replace('/storage', '', $book->cover);
                    Storage::delete($img);
                }

                $book->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Success Deleted A Book',
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
                    'message' => 'SUCCESS FAVORITE THIS BOOK',
                ]);
            } else {
                $fav->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'SUCCESS UNFAVORITE THIS BOOK',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }




    // WRITER || WRITER || WRITER || WRITER || WRITER || WRITER || WRITER || WRITER || WRITER || WRITER || WRITER
    public function getAllWriter()
    {
        $writers = Writer::paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $writers
        ]);
    }

    public function getOneWriter($slug)
    {
        $writer = Writer::where('slug', $slug)->first();

        return response()->json([
            'status' => 'success',
            'data' => $writer
        ]);
    }

    public function addWriter(Request $req)
    {
        $req->validate([
            'name' => 'required',
        ]);

        $writer = Writer::create($req->all());

        return response()->json([
            'status' => 'success',
            'message' => 'SUCCESS CREATED A NEW WRITER',
            'data' => $writer
        ]);
    }

    public function editWriter(Request $req, $slug)
    {
        $req->validate([
            'name' => 'required',
        ]);

        $writer = Writer::where('slug', $slug)->first();

        if ($writer) {
            $writer->name = $req->name;
            $writer->save();

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESS CREATED A NEW WRITER',
                'data' => $writer
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'from' => 'notWriter',
                'message' => 'WRITER NOT FOUND',
            ]);
        }
    }

    public function delWriter($slug)
    {
        $writer = Writer::where('slug', $slug)->first();

        if ($writer) {
            $books = Book::where('writer_id')->get();
            if ($books->count() > 0) {
                foreach ($books as $book) {
                    $book->writer_id = null;
                    $book->save();
                }
            }

            $writer->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESS DELETE A WRITER'
            ]);
        }
    }
}
