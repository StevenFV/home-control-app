<?php

use App\Http\Controllers\LightingController;
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

// Switch language TODOSFV Check for console errors when language switch
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
// todosfv modify permission for this section (actually not work for new User registration)
// todosfv and update ProfileTest.php Pest test
//    Route::group(['middleware' => ['permission:read']], function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
//    });

    Route::group(['middleware' => ['permission:control lighting']], function () {
        Route::get('/lighting', [LightingController::class, 'index'])->name('lighting.index');
        Route::post('/lighting/set', [LightingController::class, 'publishLightingToggle'])->name('lighting.set');
    });
// todosfv add permission for this section and update ProfileTest.php Pest test
//    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//    });
});

require __DIR__ . '/auth.php';
