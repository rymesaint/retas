<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;

use Modules\Branch\Entities\Branch;
use Modules\Order\Entities\MenuOrderTransaction;

class OrderTransaction extends Model
{
    protected $fillable = [
        'order_code',
        'branch_id',
        'total'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id')->select('id', 'branchName');
    }

    public function menus() {
        return $this->hasMany(MenuOrderTransaction::class, 'order_transaction_id', 'id')->select('id', 'order_transaction_id', 'branch_menu_id', 'quantity');
    }
}
