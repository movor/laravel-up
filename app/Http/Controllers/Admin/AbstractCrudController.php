<?php

namespace App\Http\Controllers\Admin;

use App\CustomCasts\ImageCastBase;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Carbon\Carbon;
use Illuminate\Http\Request;

abstract class AbstractCrudController extends CrudController
{
    protected $dateTimeFields = [];

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

    public function storeCrud(Request $request = null)
    {
        if (!$request) {
            $request = request();
        }

        if ($this->dateTimeFields) {
            $this->handleDateTimeFields($request);
        }

        return parent::storeCrud($request);
    }

    public function updateCrud(Request $request = null)
    {
        if (!$request) {
            $request = request();
        }

        // Handle datetime fields
        // TODO.FOR:vkovic
        // Date fields should be automatically retrieved like fields from image logic below
        if ($this->dateTimeFields) {
            $this->handleDateTimeFields($request);
        }

        // Handle image fields
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

    protected function handleImageFields(Request $request)
    {
        foreach ($this->getImageFields()as $field) {
            if (strpos($request->$field, 'data:image') !== 0 && $request->$field !== null) {
                $request->request->remove($field);
            }
        }
    }

    protected function getImageFields()
    {
        // TODO.FOR:vkovic.SEE:https://trello.com/c/pTvO28N1/1-cast-fileds-shoud-be-available
        $reflection = new \ReflectionClass($this->crud->model);
        $property = $reflection->getProperty('casts');
        $property->setAccessible(true);
        $modelCasts = $property->getValue($this->crud->model);

        $imageCastFields = [];
        foreach ($modelCasts as $attribute => $castClass) {
            if (is_subclass_of($castClass, ImageCastBase::class)) {
                $imageCastFields[] = $attribute;
            }
        }

        return $imageCastFields;
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

