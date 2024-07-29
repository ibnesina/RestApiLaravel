<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Route::get('/user', function() {
//     return "Hello";
// });
// Route::post('/user', function() {
//     return response()->json("Post API Hit Successfully");
// });
// Route::delete('/user/{id}', function($id) {
//     return response("Del ".$id, 200);
// });
// Route::put('/user/{id}', function($id) {
//     return response("Put ".$id, 200);
// });

Route::get('/test', function() {
    p("working");
});

Route::post('user/store', [UserController::class, 'store']);

Route::get('users/get/{flag}', [UserController::class, 'index']);
Route::get('user/{id}', [UserController::class, 'show']);

Route::delete('user/delete/{id}', [UserController::class, 'destroy']);

// put -> all columns update
// patch -> single column update
Route::put('user/update/{id}', [UserController::class, 'update']);
Route::patch('user/change_password/{id}', [UserController::class, 'changePassword']);

