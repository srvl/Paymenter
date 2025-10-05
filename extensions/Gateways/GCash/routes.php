<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

/*
|--------------------------------------------------------------------------
| Extension Routes
|--------------------------------------------------------------------------
*/

// Route for the front-end to check the user's live credit balance every 10 seconds.
Route::middleware('api')->prefix('v1/user')->group(function () {
    Route::get('/credits/balance', function () {
        if (!Auth::check()) {
            return response()->json(['balance' => 0.00], 401); 
        }

        // CRITICAL FIX: Log out the user model instance from the session
        // to force the system to pull a fresh model from the database immediately.
        Auth::guard()->forgetUser(); 

        // Fetch the user model fresh from the database by ID.
        // The total_credits attribute on the User model will now compute the latest sum.
        $user = User::find(Auth::id()); 
        
        if (!$user) {
             return response()->json(['balance' => 0.00], 404); 
        }

        // Access the computed attribute, which runs the sum query
        $totalCredits = $user->total_credits; 
        
        // Add headers to prevent caching by browser or proxy 
        return response()->json(['balance' => $totalCredits])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

    })->name('extensions.gcash.user.credits.balance');
});