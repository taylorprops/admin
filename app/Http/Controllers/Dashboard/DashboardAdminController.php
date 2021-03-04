<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class DashboardAdminController extends Controller
{
    public function dashboard_admin(Request $request) {

        return view('/dashboard/admin/dashboard');
    }
}
