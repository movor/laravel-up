<?php namespace App\Http\Controllers\Admin;

use App\Models\BlogTag;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Request;

class BlogTagCrudController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel(BlogTag::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/blog-tag');

        // Columns
        $this->crud
            ->addColumn([
                'label' => 'Name',
                'name' => 'name',
            ]);

        // Fields
        $this->crud
            ->addField([
                'label' => 'Name',
                'name' => 'name'
            ]);
    }

    public function store(Request $request)
    {
        return parent::storeCrud();
    }

    public function update(Request $request)
    {
        return parent::updateCrud();
    }
}