<?php

namespace Modules\Branch\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Menu\Entities\Menu;
use Modules\Branch\Entities\Branch;

class BranchMenu extends Model
{
    protected $fillable = [
        'menu_id',
        'branch_id',
        'price',
        'useMasterPrice',
        'availability'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function menu()
    {
        if(checkModule('Menu')) {
            return $this->belongsTo(Menu::class, 'menu_id');
        }
        return null;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
