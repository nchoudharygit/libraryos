<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\DashboardController;

Route::get('/login', function () {
    return Socialite::driver('auth0')->redirect();
})->name('login');

use App\Models\User;

Route::get('/auth0/callback', function () {
    $auth0User = Socialite::driver('auth0')->user();

    $user = User::where('auth0_id', $auth0User->getId())->first();

    if (!$user) {
        $user = User::create([
            'name' => $auth0User->getName(),
            'email' => $auth0User->getEmail(),
            'auth0_id' => $auth0User->getId(),
            'password' => null,
        ]);
    }

    Auth::login($user);

    return redirect('/dashboard');
})->name('auth0.callback');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/', function () {
    return view('welcome');
});
