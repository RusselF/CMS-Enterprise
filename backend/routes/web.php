<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Health Check
|--------------------------------------------------------------------------
| Versioned API health endpoint. Module routes are loaded via
| ModuleServiceProvider from each module's routes.php file.
*/

Route::prefix('api/v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => 'v1',
        ]);
    })->name('api.health');
});
