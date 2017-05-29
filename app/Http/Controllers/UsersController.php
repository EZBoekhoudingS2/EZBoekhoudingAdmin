<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class UsersController extends Controller
{
    public function index()
    {
        $users = DB::connection('mysql2')->table('users')->orderBy('vnaam', 'asc')->get();
        return view('users', compact('users'));
    }

    public function api()
    {
//        return DB::table('users')
//            ->select('id', 'bedrijf', 'straat', 'postcode', 'plaats', 'land', 'email', 'tav', 'telefoon', 'notitie')
//            ->orderBy('vnaam', 'asc')
//            ->get();
        return DB::connection('mysql2')->table('users')->orderBy('vnaam', 'asc')->get();

    }
}