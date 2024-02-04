<?php

namespace App\Http\Controllers;

use App\Abstracts\AbstractDeviceMessenger;
use App\Enums\PermissionName;
use App\Enums\PermissionRole;
use Inertia\Inertia;
use Inertia\Response;

class LightingController extends AbstractDeviceMessenger
{
    public function __construct()
    {
        parent::__construct();

        // todosfv use Model Permission instead Enum for PHP and Vue.js - Pass permission data to Vue.js inside index
        $this->middleware(function ($request, $next) {
            if (
                $request->user()->can(PermissionRole::ADMIN->value) ||
                $request->user()->can(PermissionName::CONTROL_LIGHTING->value)
            ) {
                return $next($request);
            }

            if (
                $request->user()->can(PermissionName::VIEW_LIGHTING->value) && optional(
                    $request->route()
                )->getActionMethod() !== 'index'
            ) {
                abort(403, 'Unauthorized action.');
            }

            return $next($request);
        });
    }

    public function index(): Response
    {
        return Inertia::render('Lighting/Index', [
            'subscribeTopicMessage' => $this->fetchDeviceTopicMessage()
        ]);
    }
}
