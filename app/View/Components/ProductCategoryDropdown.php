<?php

namespace App\View\Components;

use App\Models\ProductCategory;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCategoryDropdown extends Component
{
    public $id;
    public $selected;
    public function __construct($selected = null,$id)
    {
        $this->selected = $selected;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $categories = ProductCategory::all(); // Fetch categories
        return view('components.product-category-dropdown', [
            'categories' => $categories
        ]);
    }
}
