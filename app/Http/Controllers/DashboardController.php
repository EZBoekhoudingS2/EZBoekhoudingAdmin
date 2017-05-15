<?php

namespace App\Http\Controllers;

use Faker\Provider\cs_CZ\DateTime;
use Illuminate\Http\Request;
use DB;
use Illuminate\Pagination\Paginator;

class DashboardController extends Controller
{
    public function index() {
        $users = DB::connection('mysql2')->table('users')->get();
        $korting = DB::connection('mysql2')->table('korting_generiek')->get();
        $user_trial = $user_active = $user_just_expired = $user_expired = 0;
        $subs = [
            $normal_sub = [
                $purple_heart = ['users' => [], 'title' => 'Purple Heart', 0 => 0, 1 => 0, 2 => 0],
                $purple_haze = ['users' => [], 'title' => 'Purple Haze', 0 => 0, 1 => 0, 2 => 0],
                $purple_rain = ['users' => [], 'title' => 'Purple Rain', 0 => 0, 1 => 0, 2 => 0],
                $deep_purple = ['users' => [], 'title' => 'Deep Purple', 0 => 0, 1 => 0, 2 => 0]
            ],
            $korting_sub = [
                $dupho_light = ['users' => [], 'title' => 'EZ + DUPHO Light', 0 => 0, 1 => 0, 2 => 0],
                $dupho_medium = ['users' => [], 'title' => 'EZ + DUPHO Medium', 0 => 0, 1 => 0, 2 => 0],
                $dupho_full = ['users' => [], 'title' => 'EZ + DUPHO Full', 0 => 0, 1 => 0, 2 => 0]
            ]
        ];

        foreach ($users as $user) {
            $today = date('d-m-Y');
            $expired_date = date('d-m-Y', strtotime('-2 months'));
            $db_date = $user->datum;
            $trial = $user->trial;
            $active_user = $trial == 0 && strtotime($today) < strtotime($db_date);

            if ($trial == 1 && strtotime($today) < strtotime($db_date)) {
                $user_trial++;
            } elseif ($trial == 0 && strtotime($today) < strtotime($db_date)) {
                $user_active++;
            } elseif (strtotime($db_date) < strtotime($today) && strtotime($db_date) > strtotime($expired_date)) {
                $user_just_expired++;
            } else {
                $user_expired++;
            }

            if ($user->type == 0) {
                for ($i = 0; $i < count($subs[0]); $i++) {
                    if ($user->user == $i) {
                        $subs[0][$i][0]++;
                        if ($active_user) {
                            $subs[0][$i][1]++;
                            $active = 'Actief';
                        } else {
                            $subs[0][$i][2]++;
                            $active = 'Niet actief';
                        }
                        array_push($subs[0][$i]['users'], ['id' => $user->id, 'email' => $user->email, 'actief' => $active]);
                    }
                }
            }

            if ($user->type == 1) {
                for ($i = 0; $i < count($subs[1]); $i++) {
                    if ($korting[$i]->user_type == $user->user) {
                        $subs[1][$i][0]++;
                        if ($active_user) {
                            $subs[1][$i][1]++;
                            $active = 'Actief';
                        } else {
                            $subs[1][$i][2]++;
                            $active = 'Niet actief';
                        }
                        array_push($subs[1][$i]['users'], ['id' => $user->id, 'email' => $user->email, 'actief' => $active]);
                    }
                }
            }
        }

        $user_count = Array(
            'Trial' => $user_trial,
            'Actieve' => $user_active,
            'Pas verlopen' => $user_just_expired,
            'Verlopen' => $user_expired
        );

        return view('dashboard', compact('users', 'user_count', 'subs'));
    }
}
