<?php

namespace Modules\Branch\Http\Controllers;

use DataTables;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\ResponseFormat;

use Modules\Menu\Entities\Menu;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchMenu;

use Modules\Branch\Http\Requests\BranchMenuCreate;
use Modules\Branch\Http\Requests\BranchMenuUpdate;
use Modules\Branch\Http\Requests\BranchMenuAvailability;

class BranchMenuController extends Controller
{
    public function index()
    {
        $branches = Branch::where('status', 1)->get();
        $menus = requireModule('Menu') ? Menu::all() : '';
        return view('branch::branch-menu-list', compact('branches', 'menus'));
    }

    public function dataTable()
    {
        $branchMenus = BranchMenu::with('menu', 'branch')
        ->orderBy('created_at', 'DESC')
        ->get();

        return DataTables::of($branchMenus)
        ->addIndexColumn()
        ->make(true);
    }

    public function get(Request $request)
    {
        requireModule('Menu');
        $branchMenus = BranchMenu::with('menu', 'branch');

        if($request->has('branch_id')) {
            $branchMenus->where('branch_id', $request->branch_id);
            $branchMenus = $branchMenus->get();

            $branch = Branch::find($request->branch_id);
            $data = [
                'branch_menus' => $branchMenus,
                'branch' => $branch
            ];
            return ResponseFormat::success('Succeeded getting data menus at branch.', $data);
        }

        if($request->has('id')) {
            $branchMenus->where('id', $request->id);
            $branchMenu = $branchMenus->first();
            return ResponseFormat::success('Succeeded getting menu profile.', $branchMenu);
        }

        return ResponseFormat::error('Not found any parameter search.', 400);
    }

    public function create(BranchMenuCreate $request)
    {
        requireModule('Menu');
        $branchMenus = BranchMenu::where('branch_id', $request->branch_id)
        ->get();

        if($branchMenus->isEmpty()) {
            if(!empty($request->menu_id)) {
                $menus = [];
                foreach($request->menu_id as $menuId) {
                    $price = 0;
                    $useMasterPrice = true;
                    if($request->usePercentagePrice == 'on' && $request->usePercentagePrice != '') {
                        $branch = Branch::find($request->branch_id);
                        $menu = Menu::find($menuId);

                        if(is_null($menu)) {
                            return ResponseFormat::error('Menu not found.', 404);
                        }

                        if($menu->price >= 1) {
                            $price = calculatePrice($menu->price,  $branch->percentagePrice);
                        }
                        $useMasterPrice = false;
                    }
                    array_push($menus, [
                        'branch_id' => $request->branch_id,
                        'menu_id' => $menuId,
                        'price' => $price,
                        'useMasterPrice' => $useMasterPrice,
                        'availability' => true,
                        'created_at' => Carbon::now()
                    ]);
                }

                if(empty($menus)) {
                    return ResponseFormat::error('Menus cannot empty.', 400);
                }

                $create = BranchMenu::insert($menus);

                if($create) {
                    return ResponseFormat::success('Succeeded to add menus to branch.');
                } else {
                    return ResponseFormat::error('Failed to add menus to branch.', 500);
                }
            } else {
                return ResponseFormat::error('Menus cannot empty.', 400);
            }
        } else {
            $existsMenus = [];
            foreach($branchMenus as $branchMenu) {
                array_push($existsMenus, $branchMenu->menu_id);
            }

            $diff = array_diff($request->menu_id, $existsMenus);

            $newMenus = [];
            foreach($diff as $menuId) {
                $checkDuplicateMenu = BranchMenu::where('menu_id', $menuId)
                ->where('branch_id', $request->branch_id)
                ->first();

                if(is_null($checkDuplicateMenu)) {
                    $price = 0;
                    $useMasterPrice = true;
                    if($request->usePercentagePrice == 'on' || $request->usePercentagePrice != '') {
                        $branch = Branch::find($request->branch_id);
                        $menu = Menu::find($menuId);

                        if(is_null($menu)) {
                            return ResponseFormat::error('Menu not found.', 404);
                        }

                        if($menu->price >= 1) {
                            // Formula to multiplied the price based on percentage
                            $price = calculatePrice($menu->price, $branch->percentagePrice);
                        }
                        $useMasterPrice = false;
                    }

                    array_push($newMenus, [
                        'branch_id' => $request->branch_id,
                        'menu_id' => $menuId,
                        'price' => $price,
                        'useMasterPrice' => $useMasterPrice,
                        'availability' => true,
                        'created_at' => Carbon::now()
                    ]);
                }
            }

            if(!empty($newMenus)) {
                $update = BranchMenu::insert($newMenus);

                if($update) {
                    return ResponseFormat::success('Succeeded to update menus to branch.');
                } else {
                    return ResponseFormat::error('Failed to update menus to branch.', 500);
                }
            }

            return ResponseFormat::success('No update menus.');
        }
    }

    public function edit(BranchMenuUpdate $request)
    {
        requireModule('Menu');
        $branchMenu = BranchMenu::with('menu', 'branch')
        ->where('id', $request->id)
        ->first();

        if(is_null($branchMenu)) {
            return ResponseFormat::error('Not found menu.', 404);
        }

        $price = $request->price;
        $useMasterPrice = false;

        if($request->has('usePercentagePrice')) {
            if($request->usePercentagePrice == 'on') {
                $price = calculatePrice($branchMenu->menu->price, $branchMenu->branch->percentagePrice);
            }
        }

        if($request->has('useMasterPrice')) {
            if($request->useMasterPrice == 'on') {
                $price = $branchMenu->menu->price;
                $useMasterPrice = true;
            }
        }

        $branchMenu->price = $price;
        $branchMenu->useMasterPrice = $useMasterPrice;
        $update = $branchMenu->update();

        if($update) {
            return ResponseFormat::success('Succeeded to update selected menu.');
        } else {
            return ResponseFormat::error('Failed to update selected menu.', 500);
        }
    }

    public function delete(Request $request)
    {
        $branchMenu = BranchMenu::find($request->id);

        if(is_null($branchMenu)) {
            return ResponseFormat::error('Not found menu.', 404);
        }

        $delete = $branchMenu->delete();

        if($delete) {
            return ResponseFormat::success('Succeeded to delete selected menu.');
        } else {
            return ResponseFormat::error('Failed to delete selected menu.', 500);
        }
    }

    public function setAvailability(BranchMenuAvailability $request)
    {
        $branchMenu = BranchMenu::find($request->id);

        $branchMenu->availability = $request->availability;
        $update = $branchMenu->update();

        if($update) {
            return ResponseFormat::success('Succeeded to update availability.');
        } else {
            return ResponseFormat::success('Failed to update availability.', 500);
        }
    }
}
