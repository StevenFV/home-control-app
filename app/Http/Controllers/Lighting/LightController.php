<?php

namespace App\Http\Controllers\Lighting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LightController extends Controller
{
    public function __construct()
    {
        // todosfv controllers __construct to test
        $this->middleware('auth');
        $this->middleware('permission:read_permission',
            ['only' => ['data']]
        );
        $this->middleware('permission:edit_permission',
            ['only' => ['index']]
        );
    }

    public function index()
    {
        return Inertia::render('Lighting/index');
    }
}
