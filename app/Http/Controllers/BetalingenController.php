<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class BetalingenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('betalingen');
    }

    public function api()
    {
        $betalingenFinal = [];
        $dates = $this->calcDates($_GET['kwartaal'], $_GET['jaar']);
        $betalingen = DB::connection('mysql2')->table('betaling_uniek')
            ->leftJoin('users', 'betaling_uniek.user_id', '=', 'users.id')
            ->select(
                'users.id AS user_id',
                'users.bedrijfsnaam',
                'users.email',
                'betaling_uniek.id',
                'betaling_uniek.titel',
                'betaling_uniek.omschr',
                'betaling_uniek.aanb',
                'betaling_uniek.datum',
                'betaling_uniek.bedrag_in'
            )
            ->get();

        for ($i = 0; $i < count($betalingen); $i++) {
            if (strtotime($betalingen[$i]->datum) >= strtotime($dates['firstDate']) &&
                strtotime($betalingen[$i]->datum) <= strtotime($dates['lastDate'])) {
                array_push($betalingenFinal, $betalingen[$i]);
            }
        }
        return $betalingenFinal;
    }
}