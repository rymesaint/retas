<?php
namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Menu\Entities\MenuCategory;
use Modules\Branch\Entities\BranchMenu;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'price',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function menuCategories()
    {
        return $this->hasMany(MenuCategory::class, 'menu_id');
    }

    public function branchMenus()
    {
        if(checkModule('Branch')) {
            return $this->hasMany(BranchMenu::class, 'menu_id');
        }
        return null;
    }
}
