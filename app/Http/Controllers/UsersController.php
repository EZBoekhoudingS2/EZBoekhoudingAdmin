<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use stdClass;

class UsersController extends Controller
{
    public function index()
    {
        return view('users');
    }

    public function fetchAll()
    {
        $users          = DB::connection('mysql2')->table('users')->select('id', 'vnaam', 'anaam', 'email', 'trial', 'datum', 'type', 'user')->orderBy('vnaam', 'asc')->get();
        $korting        = DB::connection('mysql2')->table('korting_generiek')->get();
        $subs           = $this->subs();
        $returnUsers    = [];
        foreach($users as $user) {
            $returnUser                 = new stdClass();
            $returnUser->id             = $user->id;
            $returnUser->vnaam          = $user->vnaam;
            $returnUser->anaam          = $user->anaam;
            $returnUser->email          = $user->email;
            $isActiveUser               = ($user->trial === '0' && strtotime(date('d-m-Y')) <= strtotime($user->datum));
            $returnUser->isActiveUser   = $isActiveUser ? true : false;

            switch($user->type) {
                case '0':
                    for ($i = 0; $i < count($subs['no_discount_sub']); $i++) {
                        if ($user->user == $i) {
                            $returnUser->subscription = $subs['no_discount_sub'][$i]['title'];
                            break;
                        }
                    }
                    break;
                case '1':
                    for ($i = 0; $i < count($subs['discount_sub']); $i++) {
                        if ($korting[$i]->user_type == $user->user) {
                            $returnUser->subscription = $subs['discount_sub'][$i]['title'];
                            break;
                        }
                    }
                    break;
            }
            $returnUsers[] = $returnUser;
        }
        return $returnUsers;
    }
    
    public function fetchUser()
    {
        $users = DB::connection('mysql2')->table('users')
            ->select('id', 'vnaam', 'anaam', 'straat', 'postcode', 'plaats', 'email', 'telefoon')
            ->where(['id' => $_GET['id']])->orderBy('vnaam', 'asc')->get();
        return $users;
    }
    
    public function subs()
    {
        $users      = DB::connection('mysql2')->table('users')->get();
        $korting    = DB::connection('mysql2')->table('korting_generiek')->get();
        $subs       = [
            'no_discount_sub' => [
                ['users' => [], 'name' => 'purple_heart', 'title' => 'Purple Heart', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'name' => 'purple_haze', 'title' => 'Purple Haze', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'name' => 'purple_rain', 'title' => 'Purple Rain', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'name' => 'deep_purple', 'title' => 'Deep Purple', 'sub_count' => 0, 'active' => 0, 'non_active' => 0]
            ],
            'discount_sub' => [
                ['users' => [], 'name' => 'ez_dupho_light', 'title' => 'EZ + DUPHO Light', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'name' => 'ez_dupho_medium', 'title' => 'EZ + DUPHO Medium', 'sub_count' => 0, 'active' => 0, 'non_active' => 0],
                ['users' => [], 'name' => 'ez_dupho_full', 'title' => 'EZ + DUPHO Full', 'sub_count' => 0, 'active' => 0, 'non_active' => 0]
            ]
        ];

        foreach ($users as $user) {
            $isActiveUser = ($user->trial === '0' && strtotime(date('d-m-Y')) <= strtotime($user->datum));

            switch($user->type) {
                case '0':
                    for ($i = 0; $i < count($subs['no_discount_sub']); $i++) {
                        if ($user->user == $i) {
                            $subs['no_discount_sub'][$i]['sub_count']++;
                            $subs['no_discount_sub'][$i][($isActiveUser) ? 'active' : 'non_active']++;
                            array_push($subs['no_discount_sub'][$i]['users'], ['id' => $user->id, 'email' => $user->email, 'actief' => $isActiveUser]);
                        }
                    }
                    break;
                case '1':
                    for ($i = 0; $i < count($subs['discount_sub']); $i++) {
                        if ($korting[$i]->user_type == $user->user) {
                            $subs['discount_sub'][$i]['sub_count']++;
                            $subs['discount_sub'][$i][($isActiveUser) ? 'active' : 'non_active']++;
                            array_push($subs['discount_sub'][$i]['users'], ['id' => $user->id, 'email' => $user->email, 'actief' => $isActiveUser]);
                        }
                    }
                    break;
            }
        }
        return $subs;
    }
}