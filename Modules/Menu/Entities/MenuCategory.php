<?php
namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Menu\Entities\Menu;
use Modules\Category\Entities\Category;

class MenuCategory extends Model
{
    protected $fillable = [
        'id_menu',
        'id_category'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function menu() {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    public function category() {
        if(checkModule('Category')) {
            return $this->belongsTo(Category::class, 'id_category');
        }
        return null;
    }
}
