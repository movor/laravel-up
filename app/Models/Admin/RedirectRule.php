<?php

namespace App\Models\Admin;

use Backpack\CRUD\CrudTrait;

class RedirectRule extends \Vkovic\LaravelDbRedirector\Models\RedirectRule
{
    protected $guarded = ['id'];

    use CrudTrait;
}