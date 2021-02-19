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
}) -> name('login');


Route::get('/dashboard_admin', 'Dashboard\DashboardAdminController@dashboard_admin');
Route::get('/dashboard_agent', 'Dashboard\DashboardAgentController@dashboard_agent');
Route::get('/dashboard_agent_referral', 'Dashboard\DashboardAgentReferralController@dashboard_agent_referral');


Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout') -> name('logout');


// Route::get('/test', 'Testcontroller@test');
/* Route::get('/testing', function() {
    return view('/tests/test');
});
Route::get('/form_elements', function() {
    return view('/tests/form_elements');
}); */
