<?php

namespace App\Http\Controllers;

use App\Models\Employees\Agents;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct() {
        $this -> middleware('auth');
    }

}
