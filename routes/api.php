<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PermitController;
use App\Http\Controllers\PersonController;

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




Route::get('permits/last/{year}', [ PermitController::class, 'last' ]); // get last permit number
Route::get('permits/{id}', [ PermitController::class, 'show' ]); // get one permit by id
Route::post('permits', [ PermitController::class, 'store' ]); // store one permit
Route::put('permits/{id}', [ PermitController::class, 'update' ]); // update one permit by id
Route::delete('permits/expire/{id}', [ PermitController::class, 'expire' ]); // make the permit expired (by id)
Route::delete('permits/{id}', [ PermitController::class, 'delete' ]); // delete one permit by id
Route::post('permits/filter', [ PermitController::class, 'filter' ]); // get filtered permits
Route::post('permits/karma/{id}', [ PermitController::class, 'toggle' ]); // toggle person's karma


Route::post('person/autocomplete', [ PersonController::class, 'autocomplete' ]); // get filtered persons
Route::post('person/filter', [ PersonController::class, 'filter' ]); // get filtered permits
