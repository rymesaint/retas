<?php

namespace Modules\Branch\Entities;

use Illuminate\Database\Eloquent\Model;

use Modules\Branch\Entities\BranchMenu;

class Branch extends Model
{
    protected $fillable = [
        'branchName',
        'branchCode',
        'location',
        'manager',
        'percentagePrice',
        'status',
        'annotation',
        'isMainBranch'
    ];

    protected $appends = [
        'total_menu'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function getTotalMenuAttribute() {
        return $this->hasMany(BranchMenu::class, 'branch_id')->count();
    }
}
