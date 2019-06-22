<?php

namespace Modules\Menu\Http\Controllers;

use DataTables;
use Carbon\Carbon;

use Modules\Menu\Entities\Menu;
use Modules\Menu\Entities\MenuCategory;
use Modules\Category\Entities\Category;

use Illuminate\Http\Request;
use App\Http\Requests\CreateMenu;
use App\Http\Requests\UpdateMenu;

use Illuminate\Routing\Controller;
use App\Http\Controllers\ResponseFormat;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $categories = [];
        $categories = checkModule('Category') ? Category::all() : null;
        return view('menu::index', compact('categories'));
    }

    public function dataTable()
    {
        $menus = Menu::orderBy('created_at', 'DESC')
        ->get();

        return DataTables::of($menus)
        ->addIndexColumn()
        ->make(true);
    }

    public function get(Request $request)
    {
        $menu = Menu::with('menuCategories')
        ->where('id', $request->id)
        ->first();

        if($menu) {
            return ResponseFormat::success('Succeeded to get menu profile.', $menu);
        } else {
            return ResponseFormat::error('Failed to get menu profile.', 500);
        }
    }

    public function create(CreateMenu $request)
    {
        $create = Menu::create([
            'name' => $request->name,
            'price' => $request->price
        ]);

        if(!empty($request->menu_categories)) {
            $menuCategory = [];
            foreach($request->menu_categories as $category) {
                array_push($menuCategory, [
                    'menu_id' => $create->id,
                    'category_id' => $category,
                    'created_at' => Carbon::now()
                ]);
            }
            if(!empty($menuCategory)) {
                MenuCategory::insert($menuCategory);
            }
        }

        if($create) {
            return ResponseFormat::success('Succeeded to create a new menu.');
        } else {
            return ResponseFormat::error('Failed to create a new menu.', 500);
        }
    }

    public function edit(UpdateMenu $request)
    {
        $menu = Menu::with('menuCategories')
        ->where('id', $request->id)
        ->first();

        if(is_null($menu)) {
            return ResponseFormat::error('Menu not found.', 404);
        }

        if(!$menu->menuCategories->isEmpty()) {
            $existsCategories = [];

            foreach($menu->menuCategories as $category) {
                array_push($existsCategories, $category->category_id);
            }

            $tempCategories = [];
            if(!empty($request->menu_categories)) {
                $tempCategories = $request->menu_categories;
            }

            $diff = array_diff($existsCategories, $tempCategories);

            if(!empty($diff)) {
                $newMenuCategory = [];
                for($i = 0; $i < count($diff); $i++) {
                    if(!array_search($diff[$i], $tempCategories)) {
                        $getCategory = MenuCategory::where('category_id', $diff[$i])
                        ->where('menu_id', $menu->id)
                        ->first();

                        if(!is_null($getCategory)) {
                            $getCategory->delete();
                        }
                    } else {
                        array_push($newMenuCategory, [
                            'menu_id' => $create->id,
                            'category_id' => $category,
                            'created_at' => Carbon::now()
                        ]);
                    }
                }
                if(!empty($menuCategory)) {
                    MenuCategory::insert($menuCategory);
                }
            }
        } else {
            if(!empty($request->menu_categories)) {
                $menuCategory = [];
                foreach($request->menu_categories as $category) {
                    array_push($menuCategory, [
                        'menu_id' => $menu->id,
                        'category_id' => $category,
                        'created_at' => Carbon::now()
                    ]);
                }
                if(!empty($menuCategory)) {
                    MenuCategory::insert($menuCategory);
                }
            }
        }

        $menu->name = $request->name;
        $menu->price = $request->price;
        $update = $menu->update();

        if($update) {
            return ResponseFormat::success('Succeeded to update selected menu.');
        } else {
            return ResponseFormat::error('Failed to update selected menu.', 500);
        }
    }

    public function delete(Request $request)
    {
        $menu = Menu::find($request->id);

        if(is_null($menu)) {
            return ResponseFormat::error('Menu not found', 404);
        }

        if(!$menu->menuCategories->isEmpty()) {
            foreach($menu->menuCategories as $category) {
                $category->delete();
            }
        }

        if(!$menu->branchMenus->isEmpty()) {
            foreach($menu->branchMenus as $menuBranch) {
                $menuBranch->delete();
            }
        }

        $delete = $menu->delete();

        if($delete) {
            return ResponseFormat::success('Succeeded to delete selected category.');
        }

        return ResponseFormat::error('Failed to delete selected category.', 404);
    }
}
