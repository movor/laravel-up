<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomCasts\ImageCastBase;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class AbstractCrudController extends CrudController
{
    protected $dateTimeFields = [];
    protected $editedModel;

    public function __construct()
    {
        parent::__construct();

        $this->crud->allowAccess('show');

        // Try to set model (entity) name based on crud controller name.
        // "App\Http\Controllers\Admin\{modelNameFromHere}CrudController"
        $crudFullClassName = explode('\\', static::class);
        $crudClassName = end($crudFullClassName);

        if (strpos($crudClassName, 'CrudController') > 0) {
            $model = explode('CrudController', $crudClassName)[0];
            $this->crud->setEntityNameStrings($model, str_plural($model));
        }
    }

    public function getEditedModel()
    {
        if (!($this->crud->model instanceof Model)) {
            $message = 'Crud model not yet assigned';
            $message .= '. You should use `$this->crud->setModel($modelClass)`';
            $message .= ' before calling `getEditModel` method';

            throw new \Exception($message);
        }

        $modelClass = $this->crud->model;

        return $this->editedModel ?? ($this->editedModel = $modelClass::find(\Request::segment(3)));
    }

    public function storeCrud(Request $request = null)
    {
        if (!$request) {
            $request = request();
        }

        $this->handleDateTimeFields($request);

        return parent::storeCrud($request);
    }

    public function updateCrud(Request $request = null)
    {
        if (!$request) {
            $request = request();
        }

        $this->handleDateTimeFields($request);
        $this->handleImageFields($request);

        return parent::updateCrud($request);
    }

    protected function handleDateTimeFields(Request $request)
    {
        foreach ($this->dateTimeFields as $field) {
            try {
                $request->request->set($field, Carbon::parse(object_get($request, $field)));
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * When editing model and leave image as is, Backpack will leave image field as full url and
     * custom casts will remove it because it's not base 64 encoded image.
     *
     * @param Request $request
     *
     * @throws \Exception
     */
    protected function handleImageFields(Request $request)
    {
        // Get all model casts
        $casts = $this->getEditedModel()->getCustomCasts();

        // Filter only image casts
        foreach ($casts as $attribute => $castClass) {
            if (
                is_subclass_of($castClass, ImageCastBase::class)
                && !starts_with($this->request->get($attribute), 'data:image')
            ) {
                $request->request->remove($attribute);
            }
        }
    }

    public function isEditRequest()
    {
        return \Request::segment(4) == 'edit';
    }

    public function isCreateRequest()
    {
        return !$this->isEdit();
    }
}