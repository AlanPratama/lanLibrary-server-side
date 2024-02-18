<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
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

        return response()->json([
            'status' => 'success',
            'data' => $book
        ]);
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
            'cover' => 'required',
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
                    'cover' => null,
                ];

                if ($req->hasFile('cover')) {
                    $file = $req->file('cover');
                    $fileName = Str::slug($req->title) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('books', $fileName);
                    $data['cover'] = $path;
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
            $validator = Validator::make($req->all(), [
                'type_id' => 'required',
                'total_book' => 'required',
                'title' => 'required|unique:books,title,'.$book->id,
                'writer' => 'required',
                'publisher' => '',
                'description' => 'required',
                'year' => '',
                'page' => 'required',
                'cover' => 'required',
            ]);

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
                $data['cover'] = $path;
            }

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
            $book = Book::where('slug', $slug)->first();
            $book->categories()->detach();
            $book->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Deleted A Book',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


}
