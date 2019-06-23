<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;

use Modules\Branch\Entities\BranchMenu;
use Modules\Order\Entities\OrderTransaction;

class MenuOrderTransaction extends Model
{
    protected $fillable = [
        'order_transaction_id',
        'branch_menu_id',
        'quantity'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function order() {
        return $this->belongsTo(OrderTransaction::class, 'order_transaction_id');
    }

    public function branchMenu() {
        return $this->belongsTo(BranchMenu::class, 'branch_menu_id')->select('id', 'menu_id', 'price');
    }
}
