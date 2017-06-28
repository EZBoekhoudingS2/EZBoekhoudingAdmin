<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function punt_naar_komma($bedrag) {
        $los = explode('.', $bedrag);
        if (count($los) == 1) {
            $terug = implode('', $los);
            if(strlen($terug) == 0) {
                $return = '0,00';
            } else {
                $return = $terug . ',00';
            }
            return $return;
        } else {
            if(strlen($los[1]) == 1) {
                return implode(',', $los) . '0';
            } else {
                return implode(',', $los);
            }

        }
    }

    function komma_naar_punt($bedrag) {
        $los = explode(',', $bedrag);
        if (count($los) == 1) {
            $terug = implode('', $los);
            if(strlen($terug) == 0) {
                return '0.00';
            } else {
                $check = explode('.', $bedrag);
                if (count($check) == 1) {
                    return $terug . '.00';
                } else {
                    return $terug;
                }
            }
        } else {
            if(strlen($los[1]) == 1) {
                return implode('.', $los) . '0';
            } else {
                return implode('.', $los);
            }

        }
    }

    function calcDates($kwartaal, $jaar)
    {
        // Als kwartaal en jaar goed omgezet zijn naar Integers
        if (($kwartaal = intval($kwartaal)) && ($jaar = intval($jaar))) {
            switch ($kwartaal) {
                case 1:
                    $firstMonth = '01';
                    $lastMonth = '03';
                    break;
                case 2:
                    $firstMonth = '04';
                    $lastMonth = '06';
                    break;
                case 3:
                    $firstMonth = '07';
                    $lastMonth = '09';
                    break;
                case 4:
                    $firstMonth = '10';
                    $lastMonth = '12';
                    break;
                default:
                    die('Quarter is not valid');
            }

            $lastDay = cal_days_in_month(CAL_GREGORIAN, $lastMonth, $jaar);
            return [
                'firstDate' => '01' . '-' . $firstMonth . '-' . $jaar,
                'lastDate' => $lastDay . '-' . $lastMonth . '-' . $jaar
            ];
        }
        die('Month or year is not a number.');
    }

    function kwartaal($date) {
        $date = explode('-', $date);
        if ($date[1] > 0 && $date[1] < 4) {
            return '1';
        } elseif ($date[1] > 3 && $date[1] < 7) {
            return '2';
        } elseif ($date[1] > 6 && $date[1] < 10) {
            return '3';
        }elseif ($date[1] > 9 && $date[1] < 13) {
            return '4';
        } else {
            return false;
        }
    }

    function getdag($jaar) {
        $jaar = explode('-', $jaar);
        return $jaar[0];
    }

    function getmaand($jaar) {
        $jaar = explode('-', $jaar);
        return $jaar[1];
    }

    function getjaar($jaar) {
        $jaar = explode('-', $jaar);
        return $jaar[2];
    }
}
