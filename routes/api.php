<?php

use App\Http\Controllers\Api\Controller;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/test', function(Request $request) {
    return response()->json([
        'message' => 'hello'
    ]);
});

Route::post('/user/register', [Controller::class, 'userRegister']);
Route::post('/user/login', [Controller::class, 'userLogin']);
Route::post('/blog/create', [Controller::class, 'createBlog']);
Route::get('/blog/get', [Controller::class, 'getAllBlog']);
Route::post('/comment/create', [Controller::class, 'addCommentToBlog']);
Route::get('/comment/getCommentByBlogId', [Controller::class, 'getCommentByBlogId']);
Route::post('/comment/delete', [Controller::class, 'deleteComments']);
