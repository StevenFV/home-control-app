<?php

use App\Console\Commands\Devices\PublishMessage;
use App\Enums\PermissionName;
use App\Enums\PermissionRole;
use App\Http\Controllers\Devices\LightingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Define a route to handle requests for the favicon.ico file.
// This route prevents a 404 error when the browser requests the favicon.ico.
Route::get('/favicon', function () {
    $path = public_path('favicon.ico');
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
});

// Switch language
Route::get('locale/{locale}', function (string $locale) {
    $locale === 'en' ? $locale = 'fr' : $locale = 'en';
    Session::Put('locale', $locale);

    return Inertia::location(url()->previous());
})->name('locale');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::group(
        ['middleware' => ['role_or_permission:' .
            PermissionRole::ADMIN->value . '|' .
            PermissionName::VIEW_LIGHTING->value . '|' .
            PermissionName::CONTROL_LIGHTING->value]],
        function () {
            Route::get('devices/lighting', [LightingController::class, 'index'])->name('lighting.index');
            Route::post('devices/lighting/set', [PublishMessage::class, 'handle'])->name('lighting.set');
            Route::get('devices/lighting/get', [LightingController::class, 'fetchDataForFrontend'])
                ->name('lighting.get');
        }
    );
});

require __DIR__ . '/auth.php';
