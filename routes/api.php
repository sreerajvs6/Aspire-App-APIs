<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [App\Http\Controllers\App\AspireAppController::class, 'createCustomer'])->name('app.createCustomer');
Route::post('login', [App\Http\Controllers\App\AspireAppController::class, 'login'])->name('app.login');

//Customer
Route::post('applyLoan', [App\Http\Controllers\App\Customer\CustomerController::class, 'applyLoan'])->name('app.applyLoan');
Route::post('listCstLoans', [App\Http\Controllers\App\Customer\CustomerController::class, 'listCstLoans'])->name('app.listCstLoans');
Route::post('paySchedules', [App\Http\Controllers\App\Customer\CustomerController::class, 'paySchedules'])->name('app.paySchedules');

//Admin
Route::post('listLoans', [App\Http\Controllers\App\Admin\AdminController::class, 'listLoans'])->name('app.listLoans');
Route::post('statusChange', [App\Http\Controllers\App\Admin\AdminController::class, 'statusChange'])->name('app.statusChange');
Route::post('viewLoan', [App\Http\Controllers\App\Admin\AdminController::class, 'viewLoan'])->name('app.viewLoan');