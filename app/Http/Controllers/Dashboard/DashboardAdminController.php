<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DashboardAdminController extends Controller
{
    public function dashboard_admin(Request $request)
    {
        return view('/dashboard/admin/dashboard');
    }
}
