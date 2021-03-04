<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class DashboardAdminController extends Controller
{
    public function dashboard_admin(Request $request) {

        Artisan::call('doc_management:check_emailed_documents');
        dd(Artisan::output());

        return view('/dashboard/admin/dashboard');
    }
}
