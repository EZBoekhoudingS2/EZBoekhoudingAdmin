<?php

namespace App\Http\Controllers;

use Faker\Provider\cs_CZ\DateTime;
use Illuminate\Http\Request;
use DB;
use Illuminate\Pagination\Paginator;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users              = DB::connection('mysql2')->table('users')->get();
        $korting            = DB::connection('mysql2')->table('korting_generiek')->get();
        $userTrial          = 0;
        $userActive         = 0;
        $userJustExpired    = 0;
        $userExpired        = 0;

        $subs = [
            'no_discount_sub' => [
                ['users' => [], 'title' => 'Purple Heart', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'title' => 'Purple Haze', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'title' => 'Purple Rain', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'title' => 'Deep Purple', 'sub_count' => 0, 'active' => 0, 'non_active' => 0]
            ],
            'discount_sub' => [
                ['users' => [], 'title' => 'EZ + DUPHO Light', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'title' => 'EZ + DUPHO Medium', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'title' => 'EZ + DUPHO Full', 'sub_count' => 0, 'active' => 0, 'non_active' => 0]
            ]
        ];

        foreach ($users as $user) {
            $trial              = $user->trial;
            $userExpiryDate     = $user->datum;
            $today              = date('d-m-Y');
            $expiredDate        = date('d-m-Y', strtotime('-2 months'));

            // Booleans
            $isActiveUser       = $trial === '0' && strtotime($today) <= strtotime($userExpiryDate);
            $isUserTrial        = $trial === '1' && strtotime($today) <= strtotime($userExpiryDate);
            $isJustExpiredUser  = strtotime($userExpiryDate) < strtotime($today) && strtotime($userExpiryDate) >= strtotime($expiredDate);
            $isExpiredUser      = strtotime($userExpiryDate) < strtotime($today) && strtotime($userExpiryDate) < strtotime($expiredDate);

            switch(true) {
                case $isUserTrial:
                    $userTrial++;
                    break;
                case $isActiveUser:
                    $userActive++;
                    break;
                case $isJustExpiredUser:
                    $userJustExpired++;
                    break;
                case $isExpiredUser:
                    $userExpired++;
                    break;
            }

            switch($user->type) {
                case '0':
                    for ($i = 0; $i < count($subs['no_discount_sub']); $i++) {
                        if ($user->user == $i) {
                            $subs['no_discount_sub'][$i]['sub_count']++;
                            if ($isActiveUser) {
                                $subs['no_discount_sub'][$i]['active']++;
                                $active = 'Actief';
                            } else {
                                $subs['no_discount_sub'][$i]['non_active']++;
                                $active = 'Niet actief';
                            }
                            array_push($subs['no_discount_sub'][$i]['users'], ['id' => $user->id, 'email' => $user->email, 'actief' => $active]);
                        }
                    }
                    break;

                case '1':
                    for ($i = 0; $i < count($subs['discount_sub']); $i++) {
                        if ($korting[$i]->user_type == $user->user) {
                            $subs['discount_sub'][$i]['sub_count']++;
                            if ($isActiveUser) {
                                $subs['discount_sub'][$i]['active']++;
                                $active = 'Actief';
                            } else {
                                $subs['discount_sub'][$i]['non_active']++;
                                $active = 'Niet actief';
                            }
                            array_push($subs['discount_sub'][$i]['users'], ['id' => $user->id, 'email' => $user->email, 'actief' => $active]);
                        }
                    }
                    break;
            }
        }

        $user_count = Array(
            'Trial'         => $userTrial,
            'Actieve'       => $userActive,
            'Pas verlopen'  => $userJustExpired,
            'Verlopen'      => $userExpired
        );

        return view('dashboard', compact('users', 'user_count', 'subs'));
    }
}