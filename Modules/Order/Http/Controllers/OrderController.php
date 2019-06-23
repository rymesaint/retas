<?php

namespace Modules\Order\Http\Controllers;

use DataTables;
use Modules\Order\Entities\OrderTransaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('order::order-list');
    }

    public function dataTable() {
        $orders = OrderTransaction::with('branch')
        ->select('id', 'order_code', 'branch_id', 'created_at')
        ->get();

        return DataTables::of($orders)
        ->addIndexColumn()
        ->make(true);
    }

    public function delete() {

    }
}
