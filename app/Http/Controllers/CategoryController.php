<?php
namespace App\Http\Controllers;

use DataTables;
use App\Models\Category;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Http\Requests\CreateCategory;
use App\Http\Requests\UpdateCategory;
use App\Http\Controllers\ResponseFormat;

class CategoryController extends Controller
{
    public function index()
    {
        return view('category-list');
    }

    public function dataTable()
    {
        $categories = Category::all();

        return DataTables::of($categories)
        ->addIndexColumn()
        ->make(true);
    }

    public function get(Request $request)
    {
        $category = Category::find($request->id);

        if($category) {
            return ResponseFormat::success('Succeeded to get category profile.', $category);
        } else {
            return ResponseFormat::error('Category not found', 404);
        }
    }

    public function create(CreateCategory $request)
    {
        $slug = Str::slug($request->name);
        $checkDuplicateSlug = Category::where('slug', $request->slug)->first();
        if($checkDuplicateSlug) {
            return ResponseFormat::error('This category slug has been used.', 400);
        }

        $create = Category::create([
            'name' => $request->name,
            'slug' => $slug
        ]);

        if($create) {
            return ResponseFormat::success('Succeeded to create new category.');
        } else {
            return ResponseFormat::error('Failed to create new category.', 500);
        }
    }

    public function edit(UpdateCategory $request)
    {
        $category = Category::find($request->id);

        if(is_null($category)) {
            return ResponseFormat::error('Category not found', 404);
        }

        $newSlug = Str::slug($request->name);
        if($category->slug != $newSlug) {
            $checkDuplicateSlug = Category::where('slug', $newSlug)->first();
            if($checkDuplicateSlug) {
                return ResponseFormat::error('This category slug has been used.', 400);
            }
        }

        $category->name = $request->name;
        $category->slug = $newSlug;
        $update = $category->update();

        if($update) {
            return ResponseFormat::success('Succeeded to update selected category.');
        } else {
            return ResponseFormat::error('Failed to update selected category.', 500);
        }
    }

    public function delete(Request $request)
    {
        $category = Category::find($request->id);

        if(is_null($category)) {
            return ResponseFormat::error('Category not found', 404);
        }

        if(!$category->categoryMenus->isEmpty()) {
            foreach($category->categoryMenus as $menu) {
                $menu->delete();
            }
        }

        $delete = $category->delete();

        if($delete) {
            return ResponseFormat::success('Succeeded to delete selected category.');
        }

        return ResponseFormat::error('Failed to delete selected category.', 404);
    }
}
