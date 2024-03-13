<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Expr;

class TypeController extends Controller
{
    public function index()
    {
        $type = Type::with('books')->get();

        return response()->json([
            'status' => 'success',
            'data' => $type,
        ], 200);
    }

    public function show($slug)
    {
        $type = Type::where('slug', $slug)->first();
        return response()->json([
            'status' => 'success',
            'data' => $type
        ]);
    }


    public function add(Request $req)
    {
        try {
            $req->validate([
                'name' => 'required|unique:types,name',
            ]);

            $type = new Type();
            $type->name = $req->name;
            $type->save();

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESS ADD NEW TYPE',
                'data' => $type
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function edit(Request $req, $slug)
    {
        try {
            $type = Type::where('slug', $slug)->first();

            $req->validate([
                'name' => 'required|unique:types,name,' . $type->id,
            ]);

            $type->name = $req->name;
            $type->slug = null;
            $type->save();

            return response()->json([
               'status' => 'success',
               'message' => 'SUCCESS EDITED TYPE',
               'data' => Type::with('books')->get(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($slug)
    {
        try {
            $type = Type::where('slug', $slug)->first();

            foreach ($type->books as $book) {
                $book->type_id = null;
                $book->save();
            }

            $type->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESS DELETED TYPE',
                'data' => $type
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
