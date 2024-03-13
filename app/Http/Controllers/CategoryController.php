<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return response()->json([
           'status' => 'success',
           'data' => $categories
        ]);
    }


    public function show($slug)
    {
        $category = Category::where('slug', $slug)->first();

        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }

    public function add(Request $req)
    {
        $req->validate([
            'name' => 'required|unique:categories,name'
        ]);

        $category = new Category();
        $category->name = $req->name;
        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'SUCCESS ADD NEW CATEGORY',
            'data' => $category
        ]);
    }

    public function edit(Request $req, $slug)
    {
        try {
            $category = Category::where('slug', $slug)->first();

            $req->validate([
                'name' => 'required|unique:categories,name,'.$category->id,
            ]);

            $category->name = $req->name;
            $category->save();

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESS EDITED CATEGORY',
                'data' => $category
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 403);
        }
    }


    public function delete($slug)
    {
        try {
            $category = Category::where('slug', $slug)->first();

            // $category->books()->detach();
            DB::table('pivot_categories')->where('category_id', $category->id)->delete();
            $category->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESS DELETE CATEGORY',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
