<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PermitController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });




Route::get('permits', [ PermitController::class, 'index' ]); // get all permits
Route::get('permits/{id}', [ PermitController::class, 'show' ]); // get one permit by id
Route::post('permits', [ PermitController::class, 'store' ]); // store one permit
Route::put('permits/{id}', [ PermitController::class, 'update' ]); // update one permit by id
Route::delete('permits/{id}', [ PermitController::class, 'delete' ]); // delete one permit by id
