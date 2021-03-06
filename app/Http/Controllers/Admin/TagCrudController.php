<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Tag;
use Illuminate\Http\Request;

class TagCrudController extends AbstractCrudController
{
    public function setup()
    {
        $this->crud->setModel(Tag::class);
        $this->crud->orderBy('name', 'asc');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tag');

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
        $this->validateFields($request);

        return parent::storeCrud($request);
    }

    public function update(Request $request)
    {
        $this->validateFields($request);

        return parent::updateCrud();
    }

    protected function validateFields(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2|max:32|unique:tags,name',
        ]);
    }
}
