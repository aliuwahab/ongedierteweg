<?php

use App\Http\Controllers\ProvinceController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', [ProvinceController::class, 'index'])->name('home');
Route::get('/3d', function () {
    $provinces = \App\Models\Province::with('activePestControlServices')
        ->get()
        ->mapWithKeys(function ($province) {
            return [$province->name => [
                'name' => $province->name,
                'population' => $province->population,
                'pestControlServices' => $province->activePestControlServices->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'address' => $service->address,
                        'phone' => $service->phone,
                        'email' => $service->email,
                        'specialty' => $service->specialty,
                        'rating' => (float) $service->rating,
                        'review_count' => $service->review_count,
                    ];
                })
            ]];
        });
    return view('welcome-threejs', compact('provinces'));
})->name('3d-map');

Route::get('/geo', function () {
    $provinces = \App\Models\Province::with('activePestControlServices')
        ->get()
        ->mapWithKeys(function ($province) {
            return [$province->name => [
                'name' => $province->name,
                'population' => $province->population,
                'pestControlServices' => $province->activePestControlServices->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'address' => $service->address,
                        'phone' => $service->phone,
                        'email' => $service->email,
                        'specialty' => $service->specialty,
                        'rating' => (float) $service->rating,
                        'review_count' => $service->review_count,
                    ];
                })
            ]];
        });
    return view('welcome-geographic', compact('provinces'));
})->name('geo-map');

Route::get('/simple3d', function () {
    $provinces = \App\Models\Province::with('activePestControlServices')
        ->get()
        ->mapWithKeys(function ($province) {
            return [$province->name => [
                'name' => $province->name,
                'population' => $province->population,
                'pestControlServices' => $province->activePestControlServices->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'address' => $service->address,
                        'phone' => $service->phone,
                        'email' => $service->email,
                        'specialty' => $service->specialty,
                        'rating' => (float) $service->rating,
                        'review_count' => $service->review_count,
                    ];
                })
            ]];
        });
    return view('welcome-simple3d', compact('provinces'));
})->name('simple3d-map');

// API routes for the map
Route::prefix('api')->group(function () {
    Route::get('/provinces', [ProvinceController::class, 'getProvincesData']);
    Route::get('/provinces/{provinceName}/services', [ProvinceController::class, 'getProvinceServices']);
    Route::get('/search/services', [ProvinceController::class, 'searchServices']);
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__.'/auth.php';
