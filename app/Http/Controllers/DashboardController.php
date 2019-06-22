<?php

namespace App\Http\Controllers;

use Modules\Menu\Entities\Menu;
use Modules\Branch\Entities\Branch;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $menuCount = checkModule('Menu') ? Menu::all()->count() : 0;
        $branchCount = checkModule('Branch') ? Branch::all()->count() : 0;
        $salesCount = format_number_in_k_notation(5000);
        return view('dashboard', compact('menuCount', 'branchCount', 'salesCount'));
    }
}
