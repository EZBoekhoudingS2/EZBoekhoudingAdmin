<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class UsersController extends Controller
{
    public function index()
    {
        return view('users');
    }

    public function api()
    {
        if (isset($_GET['user_id'])) {
            return DB::connection('mysql2')->table('users')
                ->select('id', 'vnaam', 'anaam', 'straat', 'postcode', 'plaats', 'email', 'telefoon')
                ->where(['id' => $_GET['user_id']])
                ->orderBy('vnaam', 'asc')->get();
        } else {
            return DB::connection('mysql2')->table('users')
                ->select('id', 'vnaam', 'anaam', 'straat', 'postcode', 'plaats', 'email', 'telefoon')
                ->orderBy('vnaam', 'asc')->get();
        }
    }
}