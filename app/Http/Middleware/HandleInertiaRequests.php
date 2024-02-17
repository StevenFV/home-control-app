<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => fn() => $request->user()
                    ? array_merge($request->user()->only(['name', 'email']), [
                        'permissions' => $request->user()
                            ? $request->user()->getAllPermissions()->pluck('name')
                            : [],
                        'roles' => $request->user()
                            ? $request->user()->getRoleNames()
                            : []
                    ])
                    : null,
            ],
            'ziggy' => fn() => array_merge((new Ziggy())->toArray(), [
                'location' => $request->url(),
            ]),
            'locale' => fn() => collect(config('app.locale')),
        ]);
    }
}
