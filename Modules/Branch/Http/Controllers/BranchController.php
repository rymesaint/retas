<?php

namespace Modules\Branch\Http\Controllers;

use DataTables;
use App\Models\User;
use Modules\Branch\Entities\Branch;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Branch\Http\Requests\CreateBranch;
use Modules\Branch\Http\Requests\UpdateBranch;

use App\Http\Controllers\ResponseFormat;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $staffs = User::where('role', '!=', 'owner')->get();
        return view('branch::index', compact('staffs'));
    }

    public function dataTable()
    {
        $branches = Branch::orderBy('created_at', 'DESC')
        ->get();

        return DataTables::of($branches)
        ->addIndexColumn()
        ->make(true);
    }

    public function get(Request $request)
    {
        $branch = Branch::find($request->id);

        if($branch) {
            return ResponseFormat::success('Succeeded to get branch profile.', $branch);
        } else {
            return ResponseFormat::error('Failed to get branch profile.', 500);
        }
    }

    public function create(CreateBranch $request)
    {
        $checkDuplicateName = Branch::where('branchName', $request->branchName)->first();
        if($checkDuplicateName) {
            return ResponseFormat::error('This branch name has already been used.');
        }

        $checkDuplicateMainBranch = Branch::where('isMainBranch', 1)->first();
        if($checkDuplicateMainBranch) {
            return ResponseFormat::error('Main branch is already active, cannot more than 1 main branch.');
        }

        $isMainBranch = false;
        if($request->has('isMainBranch')) {
            if($request->isMainBranch == 'on') {
                $isMainBranch = true;
            }
        }

        $create = Branch::create([
            'branchName' => $request->branchName,
            'location' => $request->location,
            'manager' => $request->manager,
            'percentagePrice' => $request->percentagePrice,
            'status' => 1, // Active branch
            'isMainBranch' => $isMainBranch
        ]);

        if($create) {
            return ResponseFormat::success('Succeeded to create a new branch.');
        }

        return ResponseFormat::error('Creating branch failed.', 500);
    }

    public function edit(UpdateBranch $request)
    {
        $branch = Branch::find($request->id);

        if(is_null($branch)) {
            return ResponseFormat::error('Failed getting branch profile.', 404);
        }

        if($branch->branchName != $request->branchName) {
            $checkDuplicateName = Branch::where('branchName', $request->branchName)->first();
            if($checkDuplicateName) {
                return ResponseFormat::error('This branch name has already been used.');
            }
        }

        $isMainBranch = false;
        if($request->has('isMainBranch')) {
            if($request->isMainBranch == 'on' || is_null($request->isMainBranch)) {
                if($branch->isMainBranch != 1 && is_null($branch->isMainBranch)) {
                    $checkDuplicateMainBranch = Branch::where('isMainBranch', 1)->first();
                    if($checkDuplicateMainBranch) {
                        return ResponseFormat::error('Main branch is already active, cannot more than 1 main branch.');
                    }

                    $isMainBranch = true;
                }
            }
        }

        $branch->branchName = $request->branchName;
        $branch->location = $request->location;
        $branch->manager = $request->manager;
        $branch->percentagePrice = $request->percentagePrice;
        $branch->status = $request->status;
        $branch->annotation = $request->annotation;
        $branch->isMainBranch = $isMainBranch;
        $update = $branch->update();

        if($update) {
            return ResponseFormat::success('Succeeded to update selected branch.');
        }

        return ResponseFormat::error('Updating branch failed.', 500);
    }
}
