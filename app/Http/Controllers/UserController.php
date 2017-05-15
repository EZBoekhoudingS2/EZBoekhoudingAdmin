<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Requests;

class UserController extends Controller
{
    // Weergeeft de algemene pagina
    public function index($user_id)
    {
        $show_user = DB::connection('mysql2')->table('users')->select('id', 'email', 'vnaam', 'anaam', 'straat', 'postcode', 'plaats', 'telefoon', 'bedrijfsnaam', 'iban', 'iban_naam', 'btw', 'kvk')->where('id', $user_id)->get();
        $current_sub = DB::connection('mysql2')->table('betaling_uniek')->where(['user_id' => $user_id, 'vld_time' => (DB::connection('mysql2')->table('betaling_uniek')->max('vld_time'))])->get();
        $facturen = DB::connection('mysql2')->table('facturen')->where('klant_id', $user_id)->orderBy('time', 'desc')->get();
        $kosten = DB::connection('mysql2')->table('kosten')->where('klant_id', $user_id)->orderBy('time', 'desc')->get();
        $uren = DB::connection('mysql2')->table('uren')->where('klant_id', $user_id)->orderBy('time', 'desc')->get();
        $betaling = DB::connection('mysql2')->table('betaling_generiek')->where('abbo', 1)->get();
        $incasso = DB::connection('mysql2')->table('incasso')->where('user_id', $user_id)->get();
        $current_user = DB::connection('mysql2')->table('users')->where('id', $user_id)->get();
        $kilometers = DB::connection('mysql2')->table('km')->where('klant', $user_id)->get();
        $korting = DB::connection('mysql2')->table('korting_generiek')->get();
        $kosten_cat = DB::connection('mysql2')->table('kosten_cat')->get();
        $abonnementen = [];
        $maandjaar = null;
        $trial_info = '';
        $max_rows = 20;
        $options = [];
        $rules = [];
        $trial = [];
        $abbo = '';
        $label = Array(
            'id' => 'Id',
            'email' => 'Emailadres',
            'vnaam' => 'Voornaam',
            'anaam' => 'Achternaam',
            'straat' => 'Adres',
            'postcode' => 'Postcode',
            'plaats' => 'Plaats',
            'telefoon' => 'Telefoonnummer',
            'bedrijfsnaam' => 'Bedrijfsnaam',
            'iban' => 'IBAN',
            'iban_naam' => 'IBAN naam',
            'btw' => 'Btw',
            'kvk' => 'KvK'
        );

        if (!empty($incasso[0]) && $incasso[0]->active == 1) {
            for ($i = 0; $i < count($betaling); $i++) {
                array_push($rules, [0 . '_' . $betaling[$i]->user_type . '_' . $betaling[$i]->id . '_' . $betaling[$i]->type => $betaling[$i]->titel]);
                if ($current_user[0]->type == 0 && $current_user[0]->user == $betaling[$i]->user_type && $betaling[$i]->type == $incasso[0]->maandjaar) {
                    // ALS DE KLANT GEEN GEBRUIKT MAAKT VAN KORTING MAAR WEL INCASSO
                    $abbo = $betaling[$i]->titel;
                    $maandjaar = $betaling[$i]->type;
                }
            }
            for ($i = 0; $i < count($korting); $i++) {
                array_push($rules, [$korting[$i]->spec_user . '_' . $korting[$i]->user_type . '_' . $korting[$i]->id . '_' . $korting[$i]->type => $korting[$i]->titel]);
                if ($current_user[0]->type > 0 && $korting[$i]->spec_user == $current_user[0]->type && $korting[$i]->user_type == $current_user[0]->user && $korting[$i]->type == $incasso[0]->maandjaar) {
                    // ALS DE KLANT WEL GEBRUIKT MAAKT VAN KORTING EN INCASSO
                    $abbo = $korting[$i]->titel;
                    $maandjaar = $korting[$i]->type;
                }
            }
        } else {
            if (!empty($current_sub[0])) {
                for ($i = 0; $i < count($betaling); $i++) {
                    array_push($rules, [0 . '_' . $betaling[$i]->user_type . '_' . $betaling[$i]->id . '_' . $betaling[$i]->type => $betaling[$i]->titel]);
                    if ($current_user[0]->type == 0 && $current_user[0]->user == $betaling[$i]->user_type && $current_sub[0]->abbo_id == $betaling[$i]->id) {
                        // ALS DE KLANT GEEN GEBRUIK MAAKT VAN KORTING EN INCASSO
                        $abbo = $betaling[$i]->titel;
                        $maandjaar = $betaling[$i]->type;
                    }
                }
                for ($i = 0; $i < count($korting); $i++) {
                    array_push($rules, [$korting[$i]->spec_user . '_' . $korting[$i]->user_type . '_' . $korting[$i]->id . '_' . $korting[$i]->type => $korting[$i]->titel]);
                    if ($current_user[0]->type > 0 && $current_user[0]->type == $korting[$i]->spec_user && $current_user[0]->user == $korting[$i]->user_type && $current_sub[0]->abbo_id == $korting[$i]->id) {
                        // ALS DE KLANT WEL GEBRUIK MAAKT VAN KORTING MAAR GEEN INCASSO
                        $abbo = $korting[$i]->titel;
                        $maandjaar = $korting[$i]->type;
                    }
                }
            } else {
                for ($i = 0; $i < count($betaling); $i++) {
                    array_push($rules, [0 . '_' . $betaling[$i]->user_type . '_' . $betaling[$i]->id . '_' . $betaling[$i]->type => $betaling[$i]->titel]);
                    if ($current_user[0]->type == 0 && $current_user[0]->user == $betaling[$i]->user_type) {
                        // ALS DE KLANT GEEN GEBRUIK MAAKT VAN KORTING EN INCASSO
                        $abbo = $betaling[$i]->titel;
                        $maandjaar = $betaling[$i]->type;
                    }
                }
                for ($i = 0; $i < count($korting); $i++) {
                    array_push($rules, [$korting[$i]->spec_user . '_' . $korting[$i]->user_type . '_' . $korting[$i]->id . '_' . $korting[$i]->type => $korting[$i]->titel]);
                    if ($current_user[0]->type > 0 && $current_user[0]->type == $korting[$i]->spec_user && $current_user[0]->user == $korting[$i]->user_type) {
                        // ALS DE KLANT WEL GEBRUIK MAAKT VAN KORTING MAAR GEEN INCASSO
                        $abbo = $korting[$i]->titel;
                        $maandjaar = $korting[$i]->type;
                    }
                }
            }
        }

        if ($current_user[0]->trial == 0) {
            array_push($trial, '<option value="0" selected="selected">Nee (momenteel)</option>');
            array_push($trial, '<option value="1">Ja</option>');
            if (!empty($current_sub[0])) {
                if (!empty($incasso[0])) {
                    if ($incasso[0]->active == 0) {
                        array_push($options, '<option value="0" selected="selected">Niet actief (momenteel)</option>');
                        array_push($options, '<option value="1">Actief</option>');
                    } else {
                        if ($current_sub[0]->time <= $incasso[0]->time) {
                            array_push($options, '<option value="0">Niet actief</option>');
                            array_push($options, '<option value="1" selected="selected">Actief (momenteel)</option>');
                        }
                    }
                } else {
                    array_push($options, '<option value="0" selected="selected">Niet actief (momenteel)</option>');
                    array_push($options, '<option value="1">Actief</option>');
                }
            } else {
                if (!empty($incasso[0]) && $incasso[0]->active == 1) {
                    array_push($options, '<option value="0">Niet actief</option>');
                    array_push($options, '<option value="1" selected="selected">Actief (momenteel)</option>');
                } else {
                    array_push($options, '<option value="0" selected="selected">Niet actief (momenteel)</option>');
                    array_push($options, '<option value="1">Actief</option>');
                }
            }
        } else {
            $days = abs(strtotime(date('d-m-Y')) - strtotime($current_user[0]->datum))/86400;
            $trial_info = '<span class="side-text">(deze klant heeft nog een trial lopen van <b>' . $days . '</b> dagen)</span>';
            array_push($options, '<option value="0" selected="selected">Niet actief (momenteel)</option>');
            array_push($options, '<option value="1">Actief</option>');
            array_push($trial, '<option value="0">Nee</option>');
            array_push($trial, '<option value="1" selected="selected">Ja (momenteel)</option>');
        }

        for($a = 0; $a < count($rules); $a++) {
            foreach($rules[$a] as $key => $value) {
                $key = explode('_', $key)[0] . '_' . explode('_', $key)[1] . '_' . explode('_', $key)[3];
                if (explode('_', $key)[2] == 2) {
                    if ($value == $abbo && $maandjaar == 2) {
                        array_push($abonnementen, '<option id="' . 'm' . explode('_', $key)[2] . '" value="' .$key . '" selected="selected">' . $value .' (per maand) (momenteel)</option>');
                    } else {
                        array_push($abonnementen, '<option id="' . 'm' . explode('_', $key)[2] . '" value="' .$key . '">' . $value .' (per maand)</option>');
                    }
                } else {
                    if ($value == $abbo && $maandjaar == 1) {
                        array_push($abonnementen, '<option id="' . 'j' . explode('_', $key)[2] . '" value="' .$key . '" selected="selected">' . $value .' (per jaar) (momenteel)</option>');
                    } else {
                        array_push($abonnementen, '<option id="' . 'j' . explode('_', $key)[2] . '" value="' .$key . '">' . $value .' (per jaar)</option>');
                    }
                }
            }
        }

        $fac_type = ['Factuur', 'Credit', 'Offerte'];
        $fac_voldaan = ['<a href="#"><img src="/images/busy.png"></a>', '<a href="#"><img src="/images/check.png"></a>'];
        $fac_verstuurd = ['Niet verstuurd', 'Verstuurd', 'Herinnering verstuurd', 'Laatste herinnering verstuurd'];

        if (!empty($facturen[0])) {
            for ($i = 0; $i < count($facturen); $i++) {
                $facturen[$i]->btw_bedrag = $this->punt_naar_komma(DB::connection('mysql2')
                    ->table('fac_kosten')
                    ->where('factuur_id', $facturen[$i]->factuur_id)
                    ->sum('btw_bedrag'));
                $facturen[$i]->type = $fac_type[$facturen[$i]->type];
                $facturen[$i]->adres = explode('^', $facturen[$i]->adres)[1];
                $facturen[$i]->voldaan = $fac_voldaan[$facturen[$i]->voldaan];
                $facturen[$i]->verstuurd = $fac_verstuurd[$facturen[$i]->verstuurd];
            }
        }

        foreach ($kosten as $kost) {
            $kost->btw_bedrag = $this->punt_naar_komma($kost->btw_bedrag);
            foreach ($kosten_cat as $cat) {
                if ($kost->cat == $cat->kosten_id) {
                    $kost->cat = $cat->naam;
                }
            }
        }

        foreach ($uren as $uur) {
            $uur->uren = $this->punt_naar_komma($uur->uren);
            if (empty($kilometers[0])) {
                $uur->km = 0;
            } else {
                foreach ($kilometers as $km) {
                    if ($km->uren_id == $uur->id) {
                        $uur->km = $km->km;
                        break;
                    } else {
                        $uur->km = 0;
                    }
                }
            }
            $uur->km = $this->punt_naar_komma($uur->km);
        }

        return view('user', compact('label', 'month', 'user_id', 'trial', 'show_user', 'current_user', 'abonnementen', 'options', 'trial_info', 'facturen', 'kosten', 'kosten_cat', 'uren', 'max_rows'));
    }

    // Update de algemene pagina
    public function update(Request $request)
    {
        $id = $_POST['user_id'];
        $trial = $_POST['trial'];
        $incasso = DB::connection('mysql2')->table('incasso')->where('user_id', $id)->get();

        $this->validate($request, [
            'email' => 'required|email',
            'straat' => 'regex:/^([\p{L}a-zA-Z.? ]{3,30}\s[0-9]{1,5}([a-zA-Z]{1,3})?)?$/',
            'postcode' => 'regex:/^([1-9][0-9]{3} ?[a-zA-Z]{2})?$/',
            'telefoon' => 'regex:/^([\d\+\(\)\s]{10,15})?$/'
        ]);

        $_POST['straat'] = ucfirst($_POST['straat']);
        $_POST['postcode'] = str_replace(' ', '', strtoupper($_POST['postcode']));
        $_POST['plaats'] = ucfirst($_POST['plaats']);
        $_POST['email'] = strtolower($_POST['email']);

        if ($trial == 1) {
            $sub = $_POST['abbo-disabled'];
            $active = $_POST['active-disabled'];
        } else {
            $sub = $_POST['abbos'];
            $active = $_POST['active'];
        }

        if (!empty($incasso[0])) {
            DB::connection('mysql2')
                ->table('incasso')
                ->where('user_id', $id)
                ->update([
                    'active' => $active,
                    'maandjaar' => explode('_', $sub)[2]
                ]);
        } else {
            DB::connection('mysql2')
                ->table('incasso')
                ->insert([
                    'user_id' => $id,
                    'active' => 1,
                    'maandjaar' => explode('_', $sub)[2],
                    'date' => date('d-m-Y'),
                    'time' => time()
                ]);
        }

        DB::connection('mysql2')
            ->table('users')
            ->where('id', $id)
            ->update([
                'type' => explode('_', $sub)[0],
                'user' => explode('_', $sub)[1],
                'trial' => $trial
            ]);

        \App\Users::updateOrCreate(['id' => $id], $_POST);
        $success = '<div id="success-message"><div class="alert alert-success alert-dismissable">
        <a class="close" data-dismiss="alert" aria-label="close">&times;</a><p>Opgeslagen!</p>
        </div></div>';
        return $this->index($id) . $success;
    }

    // Weergeeft subpagina Factuuroverzicht
    public function fetch_facturen($klant_id, $factuur_id)
    {
        $current_user = DB::connection('mysql2')->table('users')->where('id', $klant_id)->get();
        $landen = DB::connection('mysql2')->table('landen')->orderBy('land', 'asc')->get();
        $facturen = DB::connection('mysql2')
            ->table('facturen')
            ->select('factuur_id', 'klant_id', 'factuur_nr', 'klant', 'adres', 'tav', 'email', 'voldaan',
                'datum', 'type', 'verlegd_btw', 'land_code', 'land_particulier',
                'verstuurd', 'verst_0', 'verst_1', 'verst_2', 'img', 'termijn')
            ->where(['factuur_id' => $factuur_id, 'klant_id' => $klant_id])
            ->get();
        $fac_kosten = DB::connection('mysql2')
            ->table('fac_kosten')
            ->select('kosten_id', 'factuur_id', 'klant_id', 'bedrag', 'btw_bedrag', 'btw_tarief', 'btw',
                'omschrijving', 'kwartaal', 'jaar', 'type')
            ->where(['factuur_id' => $factuur_id, 'klant_id' => $klant_id])
            ->get();

        for ($i = 0; $i < count($fac_kosten); $i++) {
            $fac_kosten[$i]->bedrag = $this->punt_naar_komma($fac_kosten[$i]->bedrag);
        }

        $fac_klant = explode('^', $facturen[0]->klant);
        $fac_adres = explode('^', $facturen[0]->adres);
        $fac_kosten_modal = [
            $fac_kosten[0]->omschrijving,
            $fac_kosten[0]->bedrag,
            $fac_kosten[0]->btw_bedrag,
            $fac_kosten[0]->btw_tarief
        ];

        $land = [];
        $labels = [
            'factuur_id' => 'Factuur id',
            'factuur_nr' => 'Factuurnummer',
            'klant' => 'Klantgegevens',
            'klant_id' => 'Klant id',
            'adres' => 'Adresgegevens',
            'tav' => 'T.a.v.',
            'email' => 'Email',
            'voldaan' => 'Voldaan',
            'datum' => 'Datum',
            'type' => 'Type',
            'verlegd_btw' => 'Verlegd btw',
            'land_code' => 'Land',
            'land_particulier' => 'Land particulier',
            'verstuurd' => 'Verstuurd',
            'verst_0' => 'Verstuurd 0',
            'verst_1' => 'Verstuurd 1',
            'verst_2' => 'Verstuurd 2',
            'img' => 'Foto',
            'termijn' => 'Termijn'
        ];
        $klant_labels = [
            'email' => ['Email'],
            'vnaam' => ['Voornaam'],
            'anaam' => ['Achternaam'],
            'adres' => ['Adres'],
            'postcode' => ['Postcode'],
            'plaats' => ['Plaats'],
            'bedrijfsnaam' => ['Bedrijfsnaam'],
            'iban' => ['IBAN'],
            'btw_nr' => ['Btw-nummer'],
            'kvk' => ['KvK-nummer'],
            'logo' => ['Logo'],
            'ibannaam' => ['IBAN naam'],
        ];
        $adres_labels = [
            'bedrijfsnaam' => ['Bedrijfsnaam'],
            'adres' => ['Adres'],
            'postcode' => ['Postcode'],
            'plaats' => ['Plaats'],
            'land' => ['Land'],
        ];
        $fackosten_labels = [
            'omschrijving' => ['Omschrijving'],
            'bedrag' => ['Bedrag excl.'],
            'btw_bedrag' => ['Bedrag incl.'],
            'btw_tarief' => ['Btw-tarief'],
        ];

        for ($j = 0; $j < count($landen); $j++) {
            $land[$landen[$j]->land_code . '_' . $landen[$j]->eu] = $landen[$j]->land . ' (' . $landen[$j]->land_code . ')';
        }

        $disabled = ['factuur_id', 'klant_id'];
        $buttons = ['klant', 'adres'];
        $dropdowns = [
            'voldaan' => [
                0 => 'Nee',
                1 => 'Ja'
            ],
            'type' => [
                0 => 'Factuur',
                1 => 'Credit',
                2 => 'Offerte'
            ],
            'land_code' => $land,
            'land_particulier' => [
                0 => 'Nee',
                1 => 'Ja'
            ],
            'verstuurd' => [
                0 => 'Niet verstuurd',
                1 => 'Verstuurd',
                2 => 'Herinnering verstuurd',
                3 => 'Laatste herinnering verstuurd'
            ],
        ];

        for ($i = 0; $i < count($klant_labels); $i++) {
            array_push($klant_labels[array_keys($klant_labels)[$i]], $fac_klant[$i]);
        }
        for ($i = 0; $i < count($adres_labels); $i++) {
            array_push($adres_labels[array_keys($adres_labels)[$i]], $fac_adres[$i]);
        }
        for ($i = 0; $i < count($fackosten_labels); $i++) {
            array_push($fackosten_labels[array_keys($fackosten_labels)[$i]], $fac_kosten_modal[$i]);
        }

        return view('factuur', compact('current_user', 'factuur_id', 'klant_id', 'facturen', 'fac_kosten', 'fac_klant', 'klant_labels', 'fac_adres', 'adres_labels', 'fackosten_labels', 'landen', 'disabled', 'buttons', 'dropdowns', 'labels'));
    }

    // Update subpagina Factuuroverzicht
    public function update_facturen(Request $request)
    {
        $this->validate($request, [
            'klant_email' => 'required|email',
            'klant_vnaam' => 'required',
            'klant_anaam' => 'required',
            'klant_adres' => 'required',
            'klant_postcode' => 'required|regex:/^[0-9]{4} ?[a-zA-Z]{2}$/',
            'klant_plaats' => 'required',
            'klant_bedrijfsnaam' => 'required',
            'klant_iban' => 'required',
            'klant_btw_nr' => 'required',
            'klant_kvk' => 'required',
            'klant_ibannaam' => 'required',

            'adres_bedrijfsnaam' => 'required',
            'adres_adres' => 'required',
            'adres_postcode' => 'required|regex:/^[0-9]{4} ?[a-zA-Z]{2}$/',
            'adres_plaats' => 'required',
            'adres_land' => 'required',

            'tav' => 'required',
            'email' => 'required|email',
            'voldaan' => 'required|numeric',
            'datum' => 'required|date_format:d-m-Y',
            'type' => 'required|numeric',
            'verlegd_btw' => 'required',
            'land_code' => 'required|regex:/^[A-Z]{2}\_[0-9]{1}$/',
            'verstuurd' => 'required|numeric',
            'termijn' => 'required|numeric',
        ]);

        $klant = implode('^', [
            $_POST['klant_email'],
            $_POST['klant_vnaam'],
            $_POST['klant_anaam'],
            $_POST['klant_adres'],
            $_POST['klant_postcode'],
            $_POST['klant_plaats'],
            $_POST['klant_bedrijfsnaam'],
            $_POST['klant_iban'],
            $_POST['klant_btw_nr'],
            $_POST['klant_kvk'],
            $_POST['klant_logo'],
            $_POST['klant_ibannaam']
        ]);
        $adres = implode('^', [
            $_POST['adres_bedrijfsnaam'],
            $_POST['adres_adres'],
            $_POST['adres_postcode'],
            $_POST['adres_plaats'],
            $_POST['adres_land']
        ]);

        $_POST['land_particulier'] = (isset($_POST['land_particulier'])) ? $_POST['land_particulier'] : 0;

        DB::connection('mysql2')->table('facturen')
            ->where(['factuur_id' => $_POST['factuur_id'], 'klant_id' => $_POST['klant_id']])
            ->update([
                'klant' => $klant,
                'adres' => $adres,
                'tav' => $_POST['tav'],
                'email' => $_POST['email'],
                'voldaan' => $_POST['voldaan'],
                'datum' => $_POST['datum'],
                'dag' => $this->getdag($_POST['datum']),
                'maand' => $this->getmaand($_POST['datum']),
                'jaar' => $this->getjaar($_POST['datum']),
                'kwartaal' => $this->kwartaal($_POST['datum']),
                'type' => $_POST['type'],
                'verlegd' => explode('_', $_POST['land_code'])[1],
                'verlegd_btw' => $_POST['verlegd_btw'],
                'land_code' => explode('_', $_POST['land_code'])[0],
                'land_particulier' => $_POST['land_particulier'],
                'verstuurd' => $_POST['verstuurd'],
                'verst_0' => $_POST['verst_0'],
                'verst_1' => $_POST['verst_1'],
                'verst_2' => $_POST['verst_2'],
                'img' => $_POST['img'],
                'termijn' => $_POST['termijn']
            ]);

        $fac_kosten = DB::connection('mysql2')->table('fac_kosten')
            ->where([
                'factuur_id' => $_POST['factuur_id'],
                'klant_id' => $_POST['klant_id']
            ])
            ->get();

        for ($i = 0; $i < count($fac_kosten); $i++) {
            DB::connection('mysql2')->table('fac_kosten')
                ->where(['factuur_id' => $_POST['factuur_id'], 'klant_id' => $_POST['klant_id']])
                ->update([
                    'jaar' => $this->getjaar($_POST['datum']),
                    'kwartaal' => $this->kwartaal($_POST['datum']),
                ]);
        }

        $success = '<div id="success-message"><div class="alert alert-success alert-dismissable">
        <a class="close" data-dismiss="alert" aria-label="close">&times;</a><p>Opgeslagen!</p>
        </div></div>';
        return $this->fetch_facturen($_POST['klant_id'], $_POST['factuur_id']) . $success;
    }

    public function remove_facturen()
    {
        DB::connection('mysql2')->table('facturen')
            ->where('factuur_id', $_GET['id'])
            ->delete();
        DB::connection('mysql2')->table('fac_kosten')
            ->where('factuur_id', $_GET['id'])
            ->delete();
    }

    // Weergeeft kostenposten
    public function fetch_fackosten()
    {
        if (isset($_GET['kosten_id'])) {
            $fac_kosten = DB::connection('mysql2')->table('fac_kosten')
                ->where(['kosten_id' => $_GET['kosten_id'], 'klant_id' => $_GET['klant_id']])
                ->get();
            foreach ($fac_kosten as $fac_kost) {
                $fac_kost->bedrag = $this->punt_naar_komma($fac_kost->bedrag);
                $fac_kost->btw_bedrag = $this->punt_naar_komma($fac_kost->btw_bedrag);
            }
            return $fac_kosten;
        } else {
            $fac_kosten = DB::connection('mysql2')->table('fac_kosten')
                ->where(['factuur_id' => $_GET['factuur_id'], 'klant_id' => $_GET['klant_id']])
                ->get();
            foreach ($fac_kosten as $fac_kost) {
                $fac_kost->bedrag = $this->punt_naar_komma($fac_kost->bedrag);
                $fac_kost->btw_bedrag = $this->punt_naar_komma($fac_kost->btw_bedrag);
            }
            return $fac_kosten;
        }
    }

    // Voegt een kostenpost toe
    public function add_fackosten(Request $request)
    {
        $this->validate($request, [
            'omschrijving' => 'required',
            'bedrag' => 'required',
            'btw_tarief' => 'required',
            'type' => 'required|numeric',
        ]);

        $btw_bedrag = ($_GET['btw_tarief'] === 'vrij') ? $_GET['bedrag'] : number_format($_GET['bedrag'] / 100 * (100 + $_GET['btw_tarief']), 2, '.', '');
        $facturen = DB::connection('mysql2')->table('facturen')
            ->select('kwartaal', 'jaar')
            ->where('factuur_id', '=', $_GET['factuur_id'])
            ->get();

        DB::connection('mysql2')->table('fac_kosten')
            ->insert([
                'factuur_id' => $_GET['factuur_id'],
                'klant_id' => $_GET['klant_id'],
                'bedrag' => $_GET['bedrag'],
                'btw_bedrag' => $btw_bedrag,
                'btw_tarief' => $_GET['btw_tarief'],
                'btw' => number_format($btw_bedrag - $_GET['bedrag'], 2, '.', ''),
                'omschrijving' => $_GET['omschrijving'],
                'kwartaal' => $facturen[0]->kwartaal,
                'jaar' => $facturen[0]->jaar,
                'type' => $_GET['type']
            ]);
    }

    // Update kostenposten
    public function update_fackosten()
    {
        if (isset($_GET['request']) && $_GET['request'] === 'type') {
            for ($i = 0; $i < $_GET['count']; $i++) {
                DB::connection('mysql2')->table('fac_kosten')
                    ->where([
                        'kosten_id' => $_GET['kosten_id'][$i],
                        'klant_id' => $_GET['klant_id']
                    ])
                    ->update([
                        'type' => $_GET['type'][$i]
                    ]);
            }
        } else {
            DB::connection('mysql2')->table('fac_kosten')
                ->where(['kosten_id' => $_GET['kosten_id'], 'klant_id' => $_GET['klant_id']])
                ->update([
                    'bedrag' => $this->komma_naar_punt($_GET['bedrag']),
                    'btw_bedrag' => $this->komma_naar_punt($_GET['btw_bedrag']),
                    'btw_tarief' => $_GET['btw_tarief'],
                    'btw' => $this->komma_naar_punt($this->komma_naar_punt($_GET['btw_bedrag']) - $this->komma_naar_punt($_GET['bedrag'])),
                    'omschrijving' => $_GET['omschrijving'],
                    'type' => $_GET['type']
                ]);
        }
    }

    public function remove_fackosten()
    {
        DB::connection('mysql2')->table('fac_kosten')
            ->where([
                'kosten_id' => $_GET['kosten_id'],
                'klant_id' => $_GET['klant_id']
            ])
            ->delete();
    }

    // Weergeeft subpagina kostenoverzicht
    public function fetch_kosten()
    {
        $kosten = DB::connection('mysql2')->table('kosten')
            ->where('id', $_GET['id'])
            ->get();
        $kosten[0]->btw_bedrag = $this->punt_naar_komma($kosten[0]->btw_bedrag);
        $kosten[0]->bedrag = $this->punt_naar_komma($kosten[0]->bedrag);
        return $kosten;
    }

    // Update subpagina kostenoverzicht
    public function update_kosten()
    {
        $user_id = $_GET['user_id'];
        $id = $_GET['id'];
        $cat = $_GET['cat'];
        $omschrijving = $_GET['omschrijving'];
        $bedrag = $_GET['bedrag'];
        $btw_tarief = $_GET['btw_tarief'];
        $btw_bedrag = $_GET['btw_bedrag'];
        $btw = $this->komma_naar_punt($bedrag) - $this->komma_naar_punt($btw_bedrag);
        $datum = $_GET['datum'];
        $buitenland = $_GET['buitenland'];
        $prive = $_GET['prive'];

        DB::connection('mysql2')->table('kosten')
            ->where(['id' => $id, 'klant_id' => $user_id])
            ->update([
                'cat' => $cat,
                'omschrijving' => $omschrijving,
                'bedrag' => $this->komma_naar_punt($bedrag),
                'btw_tarief' => $btw_tarief,
                'btw_bedrag' => $this->komma_naar_punt($btw_bedrag),
                'btw' => $this->komma_naar_punt($btw),
                'datum' => $datum,
                'jaar' => $this->getjaar($datum),
                'kwartaal' => $this->kwartaal($datum),
                'buitenland' => $buitenland,
                'prive' => $prive,
                'time' => strtotime($datum)
            ]);
    }

    public function remove_kosten()
    {
        DB::connection('mysql2')->table('kosten')
            ->where('id', $_GET['id'])
            ->delete();
    }

    // Weergeeft subpagina Uren & Kms
    public function fetch_urenkm()
    {
        $urenkm = DB::connection('mysql2')->table('uren')
            ->leftJoin('km', 'uren.id', '=', 'km.uren_id')
            ->where('uren.id', $_GET['id'])
            ->select('uren.*', 'km.km')
            ->get();

        $urenkm[0]->uren = $this->punt_naar_komma($urenkm[0]->uren);
        $urenkm[0]->km = $this->punt_naar_komma($urenkm[0]->km);
        return $urenkm;
    }

    // Update subpagina Uren & Kms
    public function update_urenkm()
    {
        $user_id = $_GET['user_id'];
        $id = $_GET['id'];
        $datum = $_GET['datum'];
        $omschrijving = $_GET['omschrijving'];
        $aantaluren = $_GET['aantaluren'];
        $aantalkm = $_GET['aantalkm'];

        DB::connection('mysql2')->table('uren')
            ->where('id', $id)
            ->update([
                'omschrijving' => $omschrijving,
                'uren' => $this->komma_naar_punt($aantaluren),
                'datum' => $datum,
                'kwartaal' => $this->kwartaal($datum),
                'jaar' => $this->getjaar($datum),
                'time' => strtotime($datum)
            ]);

        if (DB::connection('mysql2')->table('km')->where('uren_id', $id)->count() == 0 && $aantalkm !== '0,00') {
            DB::connection('mysql2')->table('km')
                ->insert([
                    'klant' => $user_id,
                    'uren_id' => $id,
                    'km' => $this->komma_naar_punt($aantalkm),
                    'van' => '',
                    'naar' => '',
                    'datum' => $datum,
                    'jaar' => $this->getjaar($datum),
                    'time' => strtotime($datum)
                ]);
        } else {
            DB::connection('mysql2')->table('km')
                ->where('uren_id', $id)
                ->update([
                    'km' => $this->komma_naar_punt($aantalkm),
                    'datum' => $datum,
                    'jaar' => $this->getjaar($datum),
                    'time' => strtotime($datum)
                ]);
        }

        return DB::connection('mysql2')->table('uren')
            ->leftJoin('km', 'uren.id', '=', 'km.uren_id')
            ->where('uren.id', $id)
            ->select('uren.*', 'km.km')
            ->get();
    }


    public function remove_urenkm()
    {
        DB::connection('mysql2')->table('uren')
            ->where('id', $_GET['id'])
            ->delete();
        DB::connection('mysql2')->table('km')
            ->where('uren_id', $_GET['id'])
            ->delete();
    }
}