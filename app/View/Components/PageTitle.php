<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageTitle extends Component
{
    public $title;
    public $breadcrumbs;

    public function __construct($title, $breadcrumbs = [])
    {
        $this->title = $title;
        $this->breadcrumbs = $breadcrumbs;
    }

    public function render()
    {
        return view('components.page-title');
    }
}
