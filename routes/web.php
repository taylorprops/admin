<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('/auth/login');
});

Route::get('/dashboard', 'UserController@dashboard');


Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');




// Route::get('/test', 'Testcontroller@test');
Route::get('/testing', function() {
    return view('/tests/test');
});
Route::get('/form_elements', function() {
    return view('/tests/form_elements');
});
