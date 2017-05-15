<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class TempController extends Controller
{
    public function index()
    {
        $db = DB::connection('eztemp')->table('fac_kosten')->get();
        $start_count = 0;
        $test_count = $start_count + 450;
//        $test_count = count($db);
        $chars = Array(
            '%AMP%;#223;' => 'ß',
            '%AMP%;#231;' => 'ç',
            '%AMP%;#232;' => 'è',
            '%AMP%;#233;' => 'é',
            '%AMP%;#234;' => 'ê',
            '%AMP%;#235;' => 'ë',
            '%AMP%;#8217;' => '\'',
            '%AMP%;#246;' => 'ö',
            '%AMP%;ouml;' => 'ö',
            '%AMP%;#252;' => 'ü',
            '%AMP%;#8211;' => '-',
            '%AMP%;#8232;' => '-',
            '%AMP%;#60;' => '<',
            '%AMP%;#62;' => '>',
            '%AMP%;#38;amp;#039;' => '\'',
            '%AMP%;#38;amp;eacute;' => 'é',
            '&;#38;amp;eacute;' => 'é',
            '%AMP%;#38;amp;amp;' => '&',
            'e%AMP%;#769;' => 'é',
            'a%AMP%;#769;' => 'á',
            '&nbsp;' => ' ',
            'â€‹' => '',
            '%AMP%;#8203;' => '',
            '&amp;' => '&',
            '&;#60;' => '<',
            '&;#62;' => '>',
            '%AMP%;#38;' => '&',
            '%AMP%' => '&',
            '%amp%' => '&',
            '&;#38;' => '&',
            '&;#39;' => '\'',
            '&#39;' => '\'',
            'â‚¬' => '€',
            '&;#233;' => 'é',
            'Ã©' => 'é',
            'ï¿½' => 'é',
            'eÌˆ' => 'ë',
            'Ã«' => 'ë',
            'eÌ' => 'é',
            '&;#223;' => 'ß',
            '&;#231;' => 'ç',
            '&;#232;' => 'è',
            '&;#235;' => 'ë',
            '&;#246;' => 'ö',
            '&ouml;' => 'ö',
            '&#252;' => 'ü',
            '_' => ' ',
        );

        $db_return = $this->clean($db, $start_count, $test_count, $chars);
        $db = $db_return['db'];
        $count = $db_return['count'];
        return view('temp', compact('db', 'count', 'start_count', 'test_count'));
    }

    public function clean($db, $start_count, $test_count, $chars)
    {
        $count = 0;
        for($a = $start_count; $a < $test_count; $a++) {
            $db[$a]->omschrijving = trim($db[$a]->omschrijving);
            foreach ($chars as $key => $value) {
                if (strpos($db[$a]->omschrijving, $key)) {
                    $count += substr_count($db[$a]->omschrijving, $key);
                    $db[$a]->omschrijving = str_replace($key, $value, $db[$a]->omschrijving);
                }
            }
        }
        return ['db' => $db, 'count' => $count];
    }

    public function update()
    {
        for ($a = $_POST['start_count']; $a < $_POST['test_count']; $a++) {
            DB::connection('eztemp')
                ->table('fac_kosten')
                ->where('kosten_id', $_POST['id_' . $a])
                ->update(
                    ['omschrijving' => $_POST['omschrijving_' . $a]]
                );
        }
        return redirect('/temp');
    }
}
