<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * GET /api/v1/admin/roles
     * List all available user roles.
     */
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => User::getRoles(),
        ]);
    }
}
