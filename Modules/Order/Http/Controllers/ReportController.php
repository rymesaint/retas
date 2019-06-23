<?php

namespace Modules\Order\Http\Controllers;

use DataTables;
use Modules\Order\Entities\OrderTransaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Http\Controllers\ResponseFormat;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function orderHistory()
    {
        return view('order::order-history-list');
    }

    public function dataTableOrderHistory() {
        $orders = OrderTransaction::with('branch')
        ->select('id', 'order_code', 'branch_id', 'created_at')
        ->get();

        return DataTables::of($orders)
        ->addIndexColumn()
        ->make(true);
    }

    public function getOrderHistory(Request $request) {
        $order = OrderTransaction::with('branch', 'menus.branchMenu.menu')
        ->where('id', $request->id)
        ->first();

        if(is_null($order)) {
            return ResponseFormat::error('Order ID Not Found.', 404);
        }

        return ResponseFormat::success('Succeeded getting order profile.', $order);
    }
}
