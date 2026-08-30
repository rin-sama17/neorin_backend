<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Role\PermissionCollection;
use App\Models\User\Permission;
use App\Traits\HttpResponses;

class PermissionController extends Controller
{
    use HttpResponses;

    public function index()
    {
        return new PermissionCollection(Permission::all());
    }
}
