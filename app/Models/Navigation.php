<?php
namespace App\Models;

class Navigation
{
    public $titleName;
    public $icon;
    public $route;
    public $hasChild;
    public $childs;
    public $isActiveRoute;

    public function __construct($titleName, $icon = null, $route = null, $isActiveRoute = null, $hasChild = false, $childs = []) {
        $this->titleName = $titleName;
        $this->icon = $icon;
        $this->route = $route;
        $this->hasChild = $hasChild;
        $this->childs = $childs;
        $this->isActiveRoute = $isActiveRoute;
    }
}
