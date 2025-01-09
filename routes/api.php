<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 假的API
Route::get('/fake-data', function () {
    return response()->json([
        'success' => true,
        'data' => [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'created_at' => now()->subDays(3)->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
            [
                'id' => 2,
                'name' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
                'created_at' => now()->subDays(5)->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ],
    ]);
});