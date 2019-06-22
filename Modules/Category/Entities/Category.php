<?php
namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Category\Entities\MenuCategory;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    protected $appends = [
        'menu_count'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function getMenuCountAttribute() {
        return $this->hasMany(MenuCategory::class)->count();
    }

    public function categoryMenus() {
        return $this->hasMany(MenuCategory::class, 'category_id');
    }
}
