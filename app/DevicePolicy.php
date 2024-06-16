<?php

namespace App;

use App\Enums\PermissionName;
use App\Enums\PermissionRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DevicePolicy extends controller
{
    public function check(): void
    {
        $this->middleware(function ($request, $next) {
            if (
                $this->isAdmin($request->user()) ||
                $this->canControlDevice($request->user())
            ) {
                return $next($request);
            }

            if (
                $this->canViewDevice($request->user()) && $this->isIndex($request)
            ) {
                $this->redirectUnauthorisedAction();
            }

            return $next($request);
        });
    }


    private function isAdmin(User $user): bool
    {
        return $user->can(PermissionRole::ADMIN->value);
    }

    private function canControlDevice(User $user): bool
    {
        return $user->can(PermissionName::CONTROL_LIGHTING->value);
    }

    private function canViewDevice(User $user): bool
    {
        return $user->can(PermissionName::VIEW_LIGHTING->value);
    }

    private function isIndex(Request $request): bool
    {
        return optional($request->route())->getActionMethod() !== 'index';
    }

    private function redirectUnauthorisedAction(): void
    {
        abort(403, 'Unauthorized action.');
    }
}
