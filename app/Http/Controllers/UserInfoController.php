<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class UserInfoController extends Controller
{

    public $komma_getallen_regex = '/^\d{1,}(,?\d{1,2})?$/'; // Regex accepteert komma getallen

    public function __construct()
    {
        $this->middleware('auth');
    }

    // Weergeeft de algemene pagina
    public function index($user_id)
    {
        $sepa_incasso   = DB::table('Incasso')->leftJoin('Sepa_incasso', 'Incasso.id', '=', 'Sepa_incasso.incasso_id')->select('Incasso.beschrijving AS beschrijving_incasso', 'Sepa_incasso.*')->where(['Sepa_incasso.user_id' => $user_id])->get();
        $betalingen     = DB::connection('mysql2')->table('betaling_uniek')->leftJoin('users', 'betaling_uniek.user_id', '=', 'users.id')->select('users.id AS user_id', 'users.bedrijfsnaam', 'users.email', 'betaling_uniek.id', 'betaling_uniek.titel', 'betaling_uniek.omschr', 'betaling_uniek.aanb', 'betaling_uniek.datum', 'betaling_uniek.bedrag_in')->where(['betaling_uniek.user_id' => $user_id])->get();
        $show_user      = DB::connection('mysql2')->table('users')->select('id', 'email', 'vnaam', 'anaam', 'straat', 'postcode', 'plaats', 'telefoon', 'bedrijfsnaam', 'iban', 'iban_naam', 'btw', 'kvk')->where(['id' => $user_id])->get();
        $current_sub    = DB::connection('mysql2')->table('betaling_uniek')->where(['user_id' => $user_id, 'vld_time' => (DB::connection('mysql2')->table('betaling_uniek')->max('vld_time'))])->get();
        $facturen       = DB::connection('mysql2')->table('facturen')->where(['klant_id' => $user_id])->orderBy('time', 'desc')->get();
        $kosten         = DB::connection('mysql2')->table('kosten')->where(['klant_id' => $user_id])->orderBy('time', 'desc')->get();
        $uren           = DB::connection('mysql2')->table('uren')->where(['klant_id' => $user_id])->orderBy('time', 'desc')->get();
        $betaling       = DB::connection('mysql2')->table('betaling_generiek')->where(['abbo' => 1])->get();
        $incasso        = DB::connection('mysql2')->table('incasso')->where('user_id', $user_id)->get();
        $current_user   = DB::connection('mysql2')->table('users')->where(['id' => $user_id])->get();
        $kilometers     = DB::connection('mysql2')->table('km')->where(['klant' => $user_id])->get();
        $korting        = DB::connection('mysql2')->table('korting_generiek')->get();
        $kosten_cat     = DB::connection('mysql2')->table('kosten_cat')->get();
        $currently      = '(huidig)';
        $abonnementen   = array();
        $options        = array();
        $rules          = array();
        $trial          = array();
        $maandjaar      = null;
        $trial_info     = '';
        $abbo           = '';
        $maxRows        = 20;
        $maxRowsB       = 5;
        $label          = array(
            'id'            => 'Id',
            'email'         => 'Emailadres',
            'vnaam'         => 'Voornaam',
            'anaam'         => 'Achternaam',
            'straat'        => 'Adres',
            'postcode'      => 'Postcode',
            'plaats'        => 'Plaats',
            'telefoon'      => 'Telefoonnummer',
            'bedrijfsnaam'  => 'Bedrijfsnaam',
            'iban'          => 'IBAN',
            'iban_naam'     => 'IBAN naam',
            'btw'           => 'Btw',
            'kvk'           => 'KvK'
        );

        foreach ($sepa_incasso as $item) {
            $item->bedrag   = $this->punt_naar_komma($item->bedrag);
            $item->succes   = ($item->succes == false)
                ? '<a href="#"><input type="hidden" value="' . $item->succes . '"><img src="/images/busy.png"></a>'
                : '<a href="#"><input type="hidden" value="' . $item->succes . '"><img src="/images/check.png"></a>';

            $item->verlengt = ($item->verlengt == false)
                ? '<a href="#"><input type="hidden" value="' . $item->verlengt . '"><img src="/images/busy.png"></a>'
                : '<a href="#"><input type="hidden" value="' . $item->verlengt . '"><img src="/images/check.png"></a>';
        }

//        echo '<pre>';
//        print_r($sepa_incasso);
//        echo '</pre>';
//        exit;

        if (!empty($incasso[0]) && $incasso[0]->active == 1) {
            for ($i = 0; $i < count($betaling); $i++) {
                array_push($rules, [0 . '_' . $betaling[$i]->user_type . '_' . $betaling[$i]->id . '_' . $betaling[$i]->type => $betaling[$i]->titel]);
                if ($current_user[0]->type == 0 && $current_user[0]->user == $betaling[$i]->user_type && $betaling[$i]->type == $incasso[0]->maandjaar) {
                    // ALS DE KLANT GEEN GEBRUIKT MAAKT VAN KORTING MAAR WEL INCASSO
                    $abbo       = $betaling[$i]->titel;
                    $maandjaar  = $betaling[$i]->type;
                }
            }
            for ($i = 0; $i < count($korting); $i++) {
                array_push($rules, [$korting[$i]->spec_user . '_' . $korting[$i]->user_type . '_' . $korting[$i]->id . '_' . $korting[$i]->type => $korting[$i]->titel]);
                if ($current_user[0]->type > 0 && $korting[$i]->spec_user == $current_user[0]->type && $korting[$i]->user_type == $current_user[0]->user && $korting[$i]->type == $incasso[0]->maandjaar) {
                    // ALS DE KLANT WEL GEBRUIKT MAAKT VAN KORTING EN INCASSO
                    $abbo       = $korting[$i]->titel;
                    $maandjaar  = $korting[$i]->type;
                }
            }
        } else {
            if (!empty($current_sub[0])) {
                for ($i = 0; $i < count($betaling); $i++) {
                    array_push($rules, [0 . '_' . $betaling[$i]->user_type . '_' . $betaling[$i]->id . '_' . $betaling[$i]->type => $betaling[$i]->titel]);
                    if ($current_user[0]->type == 0 && $current_user[0]->user == $betaling[$i]->user_type && $current_sub[0]->abbo_id == $betaling[$i]->id) {
                        // ALS DE KLANT GEEN GEBRUIK MAAKT VAN KORTING EN INCASSO
                        $abbo       = $betaling[$i]->titel;
                        $maandjaar  = $betaling[$i]->type;
                    }
                }
                for ($i = 0; $i < count($korting); $i++) {
                    array_push($rules, [$korting[$i]->spec_user . '_' . $korting[$i]->user_type . '_' . $korting[$i]->id . '_' . $korting[$i]->type => $korting[$i]->titel]);
                    if ($current_user[0]->type > 0 && $current_user[0]->type == $korting[$i]->spec_user && $current_user[0]->user == $korting[$i]->user_type && $current_sub[0]->abbo_id == $korting[$i]->id) {
                        // ALS DE KLANT WEL GEBRUIK MAAKT VAN KORTING MAAR GEEN INCASSO
                        $abbo       = $korting[$i]->titel;
                        $maandjaar  = $korting[$i]->type;
                    }
                }
            } else {
                for ($i = 0; $i < count($betaling); $i++) {
                    array_push($rules, [0 . '_' . $betaling[$i]->user_type . '_' . $betaling[$i]->id . '_' . $betaling[$i]->type => $betaling[$i]->titel]);
                    if ($current_user[0]->type == 0 && $current_user[0]->user == $betaling[$i]->user_type) {
                        // ALS DE KLANT GEEN GEBRUIK MAAKT VAN KORTING EN INCASSO
                        $abbo       = $betaling[$i]->titel;
                        $maandjaar  = $betaling[$i]->type;
                    }
                }
                for ($i = 0; $i < count($korting); $i++) {
                    array_push($rules, [$korting[$i]->spec_user . '_' . $korting[$i]->user_type . '_' . $korting[$i]->id . '_' . $korting[$i]->type => $korting[$i]->titel]);
                    if ($current_user[0]->type > 0 && $current_user[0]->type == $korting[$i]->spec_user && $current_user[0]->user == $korting[$i]->user_type) {
                        // ALS DE KLANT WEL GEBRUIK MAAKT VAN KORTING MAAR GEEN INCASSO
                        $abbo       = $korting[$i]->titel;
                        $maandjaar  = $korting[$i]->type;
                    }
                }
            }
        }

        if ($current_user[0]->trial == 0) {
            array_push($trial, '<option value="0" selected="selected">Nee ' . $currently . '</option>');
            array_push($trial, '<option value="1">Ja</option>');
            if (!empty($current_sub[0])) {
                if (!empty($incasso[0])) {
                    if ($incasso[0]->active == 0) {
                        array_push($options, '<option value="0" selected="selected">Uit ' . $currently . '</option>');
                        array_push($options, '<option value="1">Aan</option>');
                    } else {
                        if ($current_sub[0]->time <= $incasso[0]->time) {
                            array_push($options, '<option value="0">Uit</option>');
                            array_push($options, '<option value="1" selected="selected">Aan ' . $currently . '</option>');
                        }
                    }
                } else {
                    array_push($options, '<option value="0" selected="selected">Uit ' . $currently . '</option>');
                    array_push($options, '<option value="1">Aan</option>');
                }
            } else {
                if (!empty($incasso[0]) && $incasso[0]->active == 1) {
                    array_push($options, '<option value="0">Uit</option>');
                    array_push($options, '<option value="1" selected="selected">Aan ' . $currently . '</option>');
                } else {
                    array_push($options, '<option value="0" selected="selected">Uit ' . $currently . '</option>');
                    array_push($options, '<option value="1">Aan</option>');
                }
            }
        } else {
            $days = abs(strtotime(date('d-m-Y')) - strtotime($current_user[0]->datum)) / 86400;
            $trial_info = '<span class="side-text">(deze klant heeft nog een trial lopen van <b>' . $days . '</b> dagen)</span>';
            array_push($options, '<option value="0" selected="selected">Aan ' . $currently . '</option>');
            array_push($options, '<option value="1">Uit</option>');
            array_push($trial, '<option value="0">Nee</option>');
            array_push($trial, '<option value="1" selected="selected">Ja ' . $currently . '</option>');
        }

        for($a = 0; $a < count($rules); $a++) {
            foreach($rules[$a] as $key => $value) {
                $key = explode('_', $key)[0] . '_' . explode('_', $key)[1] . '_' . explode('_', $key)[3];
                if (explode('_', $key)[2] == 2) {
                    if ($value == $abbo && $maandjaar == 2) {
                        array_push($abonnementen, '<option value="' .$key . '" selected="selected">' . $value .' (per maand) ' . $currently . '</option>');
                    } else {
                        array_push($abonnementen, '<option value="' .$key . '">' . $value .' (per maand)</option>');
                    }
                } else {
                    if ($value == $abbo && $maandjaar == 1) {
                        array_push($abonnementen, '<option value="' .$key . '" selected="selected">' . $value .' (per jaar) ' . $currently . '</option>');
                    } else {
                        array_push($abonnementen, '<option value="' .$key . '">' . $value .' (per jaar)</option>');
                    }
                }
            }
        }

        $fac_type       = array('Factuur', 'Credit', 'Offerte');
        $fac_voldaan    = array('<a href="#"><img src="/images/busy.png"></a>', '<a href="#"><img src="/images/check.png"></a>');
        $fac_verstuurd  = array('Niet verstuurd', 'Verstuurd', 'Herinnering verstuurd', 'Laatste herinnering verstuurd');

        if (!empty($facturen[0])) {
            for ($i = 0; $i < count($facturen); $i++) {
                $facturen[$i]->btw_bedrag   = $this->punt_naar_komma(
                    DB::connection('mysql2')->table('fac_kosten')
                    ->where(['factuur_id' => $facturen[$i]->factuur_id])
                    ->sum('btw_bedrag')
                );
                $facturen[$i]->type         = $fac_type[$facturen[$i]->type];
                $facturen[$i]->adres        = explode('^', $facturen[$i]->adres)[1];
                $facturen[$i]->voldaan      = $fac_voldaan[$facturen[$i]->voldaan];
                $facturen[$i]->verstuurd    = $fac_verstuurd[$facturen[$i]->verstuurd];
            }
        }

        foreach ($betalingen as $betaling) {
            $betaling->bedrag_in = $this->punt_naar_komma($betaling->bedrag_in);
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

        return view('userinfo', compact('label', 'month', 'user_id', 'trial', 'sepa_incasso', 'betalingen', 'show_user', 'current_user', 'abonnementen', 'options', 'trial_info', 'facturen', 'kosten', 'kosten_cat', 'uren', 'maxRows', 'maxRowsB'));
    }

    // Update de algemene pagina
    public function update(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email',
            'straat'    => 'regex:/^([\p{L}a-zA-Z.? ]{3,30}\s[0-9]{1,5}([a-zA-Z]{1,3})?)?$/',
            'postcode'  => 'regex:/^([1-9][0-9]{3} ?[a-zA-Z]{2})?$/',
            'telefoon'  => 'regex:/^([\d\+\(\)\s]{10,15})?$/'
        ]);

        $trial              = $_POST['trial'];
        $sub                = $_POST['abbos'];
        $active             = $_POST['active'];
        $user_id            = $_POST['user_id'];
        $_POST['email']     = strtolower($_POST['email']);
        $_POST['straat']    = ucfirst($_POST['straat']);
        $_POST['plaats']    = ucfirst($_POST['plaats']);
        $_POST['postcode']  = str_replace(' ', '', strtoupper($_POST['postcode']));
        $incasso_count      = DB::connection('mysql2')->table('incasso')->where(['user_id' => $user_id])->count();

        $type               = explode('_', $sub)[0];
        $user               = explode('_', $sub)[1];
        $maandjaar          = explode('_', $sub)[2];

        if ($incasso_count !== 0) {
            DB::connection('mysql2')->table('incasso')
                ->where(['user_id' => $user_id])
                ->update(['active' => $active, 'maandjaar' => $maandjaar]);
        } else {
            DB::connection('mysql2')->table('incasso')
                ->insert([
                    'user_id'   => $user_id,
                    'active'    => $active,
                    'maandjaar' => $maandjaar,
                    'date'      => date('d-m-Y'),
                    'time'      => time()
                ]);
        }

        DB::connection('mysql2')->table('users')
            ->where(['id' => $user_id])
            ->update([
                'type'          => $type,
                'user'          => $user,
                'trial'         => $trial
            ]);

        \App\Users::updateOrCreate(['id' => $user_id], $_POST);
        $success =
            '<div id="success-message">
                <div class="alert alert-success alert-dismissable">
                    <a class="close" data-dismiss="alert" aria-label="close">&times;</a><p>Opgeslagen!</p>
                </div>
            </div>';

        return $this->index($user_id) . $success;
    }

    // Weergeeft subpagina Factuuroverzicht
    public function fetchFacturen($klant_id, $factuur_id)
    {
        $current_user   = DB::connection('mysql2')->table('users')->where(['id' => $klant_id])->get();
        $landen         = DB::connection('mysql2')->table('landen')->orderBy('land', 'asc')->get();
        $facturen       = DB::connection('mysql2')->table('facturen')
            ->select(
                'factuur_id', 'klant_id', 'factuur_nr', 'klant', 'adres', 'tav', 'email', 'voldaan',
                'datum', 'type', 'verlegd_btw', 'land_code', 'land_particulier',
                'verstuurd', 'verst_0', 'verst_1', 'verst_2', 'img', 'termijn'
            )
            ->where(['factuur_id' => $factuur_id, 'klant_id' => $klant_id])->get();

        $fackosten = DB::connection('mysql2')->table('fac_kosten')
            ->select(
                'kosten_id', 'factuur_id', 'klant_id', 'bedrag', 'btw_bedrag',
                'btw_tarief', 'btw', 'omschrijving', 'kwartaal', 'jaar', 'type'
            )
            ->where(['factuur_id' => $factuur_id, 'klant_id' => $klant_id])->get();

        $land = array();
        $labels = array(
            'factuur_id'        => 'Factuur id',
            'factuur_nr'        => 'Factuurnummer',
            'klant'             => 'Klantgegevens',
            'klant_id'          => 'Klant id',
            'adres'             => 'Adresgegevens',
            'tav'               => 'T.a.v.',
            'email'             => 'Email',
            'voldaan'           => 'Voldaan',
            'datum'             => 'Datum',
            'type'              => 'Type',
            'verlegd_btw'       => 'Verlegd btw',
            'land_code'         => 'Land',
            'land_particulier'  => 'Land particulier',
            'verstuurd'         => 'Verstuurd',
            'verst_0'           => 'Verstuurd 0',
            'verst_1'           => 'Verstuurd 1',
            'verst_2'           => 'Verstuurd 2',
            'img'               => 'Foto',
            'termijn'           => 'Termijn'
        );
        $klant_labels = array(
            'email'             => ['Email'],
            'vnaam'             => ['Voornaam'],
            'anaam'             => ['Achternaam'],
            'adres'             => ['Adres'],
            'postcode'          => ['Postcode'],
            'plaats'            => ['Plaats'],
            'bedrijfsnaam'      => ['Bedrijfsnaam'],
            'iban'              => ['IBAN'],
            'btw_nr'            => ['Btw-nummer'],
            'kvk'               => ['KvK-nummer'],
            'logo'              => ['Logo'],
            'ibannaam'          => ['IBAN naam'],
        );

        $adres_labels = array(
            'bedrijfsnaam'      => ['Bedrijfsnaam'],
            'adres'             => ['Adres'],
            'postcode'          => ['Postcode'],
            'plaats'            => ['Plaats'],
            'land'              => ['Land'],
        );

        foreach ($fackosten as $fackost) {
            $fackost->bedrag = $this->punt_naar_komma($fackost->bedrag);
        }

        // Als Klant leeg is, zet de delimiters (^) er wel in
        if (empty($facturen[0]->klant)) {
            DB::connection('mysql2')->table('facturen')
                ->where(['factuur_id' => $factuur_id, 'klant_id' => $klant_id])
                ->update(['klant' => '^^^^^^^^^^^']);
        }

        // Als Adres leeg is, zet de delimiters (^) er wel in
        if (empty($facturen[0]->adres)) {
            DB::connection('mysql2')->table('facturen')
                ->where(['factuur_id' => $factuur_id, 'klant_id' => $klant_id])
                ->update(['adres' => '^^^^']);
        }

        $facklant = explode('^', $facturen[0]->klant);
        $facadres = explode('^', $facturen[0]->adres);

        // OUTPUT EXAMPLE: $klant_labels['email'] = ['email', 'test@test.com'];
        for ($i = 0; $i < count($klant_labels); $i++) {
            array_push($klant_labels[array_keys($klant_labels)[$i]], $facklant[$i]);
        }

        // OUTPUT EXAMPLE: $klant_labels['bedrijfsnaam'] = ['bedrijfsnaam', 'Trump Organization'];
        for ($i = 0; $i < count($adres_labels); $i++) {
            array_push($adres_labels[array_keys($adres_labels)[$i]], $facadres[$i]);
        }

        // OUTPUT EXAMPLE: $land[NL_0] = Nederland (NL);
        for ($j = 0; $j < count($landen); $j++) {
            $land[$landen[$j]->land_code . '_' . $landen[$j]->eu] = $landen[$j]->land . ' (' . $landen[$j]->land_code . ')';
        }

        $disabled = array('factuur_id', 'klant_id');
        $buttons = array('klant', 'adres');
        $dropdowns = array(
            'voldaan' => array(
                0 => 'Nee',
                1 => 'Ja'
            ),
            'type' => array(
                0 => 'Factuur',
                1 => 'Credit',
                2 => 'Offerte'
            ),
            'land_code' => $land,
            'land_particulier' => array(
                0 => 'Nee',
                1 => 'Ja'
            ),
            'verstuurd' => array(
                0 => 'Niet verstuurd',
                1 => 'Verstuurd',
                2 => 'Herinnering verstuurd',
                3 => 'Laatste herinnering verstuurd'
            ),
        );

        return view('factuur', compact('current_user', 'factuur_id', 'klant_id', 'facturen', 'fackosten', 'facklant', 'facadres', 'klant_labels', 'adres_labels', 'landen', 'disabled', 'buttons', 'dropdowns', 'labels'));
    }

    // Update subpagina Factuuroverzicht
    public function updateFacturen(Request $request)
    {
        $this->validate($request, [
            'klant_email'           => 'required|email',
            'klant_vnaam'           => 'required',
            'klant_anaam'           => 'required',
            'klant_adres'           => 'required',
            'klant_postcode'        => 'required|regex:/^[0-9]{4} ?[a-zA-Z]{2}$/',
            'klant_plaats'          => 'required',
            'klant_bedrijfsnaam'    => 'required',
            'klant_iban'            => 'required',
            'klant_btw_nr'          => 'required',
            'klant_kvk'             => 'required',
            'klant_ibannaam'        => 'required',

            'adres_bedrijfsnaam'    => 'required',
            'adres_adres'           => 'required',
            'adres_postcode'        => 'required|regex:/^[0-9]{4} ?[a-zA-Z]{2}$/',
            'adres_plaats'          => 'required',
            'adres_land'            => 'required',

            'tav'                   => 'required',
            'email'                 => 'required|email',
            'voldaan'               => 'required|numeric',
            'datum'                 => 'required|date_format:d-m-Y',
            'type'                  => 'required|numeric',
            'verlegd_btw'           => 'required',
            'land_code'             => 'required|regex:/^[A-Z]{2}\_[0-9]{1}$/',
            'verstuurd'             => 'required|numeric',
            'termijn'               => 'required|numeric',
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
            ->where([
                'factuur_id'        => $_POST['factuur_id'],
                'klant_id'          => $_POST['klant_id']
            ])
            ->update([
                'klant'             => $klant,
                'adres'             => $adres,
                'tav'               => $_POST['tav'],
                'email'             => $_POST['email'],
                'voldaan'           => $_POST['voldaan'],
                'datum'             => $_POST['datum'],
                'dag'               => $this->getdag($_POST['datum']),
                'maand'             => $this->getmaand($_POST['datum']),
                'jaar'              => $this->getjaar($_POST['datum']),
                'kwartaal'          => $this->kwartaal($_POST['datum']),
                'type'              => $_POST['type'],
                'verlegd'           => explode('_', $_POST['land_code'])[1],
                'verlegd_btw'       => $_POST['verlegd_btw'],
                'land_code'         => explode('_', $_POST['land_code'])[0],
                'land_particulier'  => $_POST['land_particulier'],
                'verstuurd'         => $_POST['verstuurd'],
                'verst_0'           => $_POST['verst_0'],
                'verst_1'           => $_POST['verst_1'],
                'verst_2'           => $_POST['verst_2'],
                'img'               => $_POST['img'],
                'termijn'           => $_POST['termijn']
            ]);

        $fackosten_count = DB::connection('mysql2')->table('fac_kosten')->where(['factuur_id' => $_POST['factuur_id'], 'klant_id' => $_POST['klant_id']])->count();
        for ($i = 0; $i < $fackosten_count; $i++) {
            DB::connection('mysql2')->table('fac_kosten')
                ->where(['factuur_id' => $_POST['factuur_id'], 'klant_id' => $_POST['klant_id']])
                ->update(['jaar' => $this->getjaar($_POST['datum']), 'kwartaal' => $this->kwartaal($_POST['datum'])]);
        }

        $success =
            '<div id="success-message">
                <div class="alert alert-success alert-dismissable">
                    <a class="close" data-dismiss="alert" aria-label="close">&times;</a><p>Opgeslagen!</p>
                </div>
            </div>';
        return $this->fetchFacturen($_POST['klant_id'], $_POST['factuur_id']) . $success;
    }

    // Weergeeft kostenposten
    public function fetchFackosten()
    {
        $fac_kosten = (isset($_GET['kosten_id']))
            ? DB::connection('mysql2')->table('fac_kosten')
                ->where(['kosten_id' => $_GET['kosten_id'], 'klant_id' => $_GET['klant_id']])->get()
            : DB::connection('mysql2')->table('fac_kosten')
                ->where(['factuur_id' => $_GET['factuur_id'], 'klant_id' => $_GET['klant_id']])->get();

        foreach ($fac_kosten as $fac_kost) {
            $fac_kost->bedrag = $this->punt_naar_komma($fac_kost->bedrag);
            $fac_kost->btw_bedrag = $this->punt_naar_komma($fac_kost->btw_bedrag);
        }
        return $fac_kosten;
    }

    // Voegt een kostenpost toe
    public function addFackosten(Request $request)
    {
        $this->validate($request, [
            'omschrijving'  => 'required',
            'bedrag'        => 'required',
            'btw_tarief'    => 'required',
            'type'          => 'required|numeric',
        ]);

        $btw_bedrag = ($_GET['btw_tarief'] === 'vrij') ? $_GET['bedrag'] : number_format($_GET['bedrag'] / 100 * (100 + $_GET['btw_tarief']), 2, '.', '');

        $facturen = DB::connection('mysql2')->table('facturen')
            ->select('kwartaal', 'jaar')->where(['factuur_id' => $_GET['factuur_id']])->get();

        DB::connection('mysql2')->table('fac_kosten')
            ->insert([
                'factuur_id'    => $_GET['factuur_id'],
                'klant_id'      => $_GET['klant_id'],
                'bedrag'        => $_GET['bedrag'],
                'btw_bedrag'    => $btw_bedrag,
                'btw_tarief'    => $_GET['btw_tarief'],
                'btw'           => number_format($btw_bedrag - $_GET['bedrag'], 2, '.', ''),
                'omschrijving'  => $_GET['omschrijving'],
                'kwartaal'      => $facturen[0]->kwartaal,
                'jaar'          => $facturen[0]->jaar,
                'type'          => $_GET['type']
            ]);
    }

    // Update kostenposten
    public function updateFackosten()
    {
        if (isset($_GET['request']) && $_GET['request'] === 'type') {
            // Update types van alle kostenposten
            for ($i = 0; $i < $_GET['count']; $i++) {
                DB::connection('mysql2')->table('fac_kosten')
                    ->where(['kosten_id' => $_GET['kosten_id'][$i], 'klant_id' => $_GET['klant_id']])
                    ->update(['type' => $_GET['type'][$i]]);
            }
        } else {
            // Update alles van een kostenpost
            DB::connection('mysql2')->table('fac_kosten')
                ->where(['kosten_id' => $_GET['kosten_id'], 'klant_id' => $_GET['klant_id']])
                ->update([
                    'bedrag'        => $this->komma_naar_punt($_GET['bedrag']),
                    'btw_bedrag'    => $this->komma_naar_punt($_GET['btw_bedrag']),
                    'btw_tarief'    => $_GET['btw_tarief'],
                    'btw'           => $this->komma_naar_punt($this->komma_naar_punt($_GET['btw_bedrag']) - $this->komma_naar_punt($_GET['bedrag'])),
                    'omschrijving'  => $_GET['omschrijving'],
                    'type'          => $_GET['type']
                ]);
        }
    }

    // Weergeeft subpagina kostenoverzicht
    public function fetchKosten()
    {
        $kosten = DB::connection('mysql2')->table('kosten')->where(['id' => $_GET['id']])->get();
        $kosten[0]->btw_bedrag = $this->punt_naar_komma($kosten[0]->btw_bedrag);
        $kosten[0]->bedrag = $this->punt_naar_komma($kosten[0]->bedrag);
        return $kosten;
    }

    // Update subpagina kostenoverzicht
    public function updateKosten(Request $request)
    {
        $this->validate($request, [
            'datum'         => 'required|date_format:d-m-Y',
            'omschrijving'  => 'required',
            'cat'           => 'required|numeric',
            'prive'         => 'required|numeric',
            'btw_bedrag'    => 'required|regex:' . $this->komma_getallen_regex,
            'btw_tarief'    => 'required',
            'bedrag'        => 'required|regex:' . $this->komma_getallen_regex,
            'buitenland'    => 'required|numeric',
        ]);

        $bedrag         = $this->komma_naar_punt($_GET['bedrag']);
        $btw_bedrag     = $this->komma_naar_punt($_GET['btw_bedrag']);
        $btw            = $this->komma_naar_punt($bedrag - $btw_bedrag);
        $jaar           = $this->getjaar($_GET['datum']);
        $kwartaal       = $this->kwartaal($_GET['datum']);
        $time           = strtotime($_GET['datum']);

        DB::connection('mysql2')->table('kosten')
            ->where([
                'id'            => $_GET['id'],
                'klant_id'      => $_GET['user_id']
            ])
            ->update([
                'cat'           => $_GET['cat'],
                'omschrijving'  => $_GET['omschrijving'],
                'bedrag'        => $bedrag,
                'btw_tarief'    => $_GET['btw_tarief'],
                'btw_bedrag'    => $btw_bedrag,
                'btw'           => $btw,
                'datum'         => $_GET['datum'],
                'jaar'          => $jaar,
                'kwartaal'      => $kwartaal,
                'buitenland'    => $_GET['buitenland'],
                'prive'         => $_GET['prive'],
                'time'          => $time
            ]);
    }

    // Weergeeft subpagina Uren & Kms
    public function fetchUrenkm()
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
    public function updateUrenkm(Request $request)
    {
        $this->validate($request, [
            'datum'         => 'required|date_format:d-m-Y',
            'omschrijving'  => 'required',
            'aantaluren'    => 'required|regex:' . $this->komma_getallen_regex,
            'aantalkm'      => 'required|regex:' . $this->komma_getallen_regex,
        ]);

        $uren_id        = $_GET['id'];
        $datum          = $_GET['datum'];
        $user_id        = $_GET['user_id'];
        $time           = strtotime($datum);
        $omschrijving   = $_GET['omschrijving'];
        $jaar           = $this->getjaar($datum);
        $kwartaal       = $this->kwartaal($datum);
        $km             = $this->komma_naar_punt($_GET['aantalkm']);
        $uren           = $this->komma_naar_punt($_GET['aantaluren']);
        $km_count       = DB::connection('mysql2')->table('km')->where(['uren_id' => $uren_id])->count();

        DB::connection('mysql2')->table('uren')
            ->where(['id' => $uren_id, 'klant_id' => $user_id])
            ->update([
                'omschrijving'  => $omschrijving,
                'uren'          => $uren,
                'datum'         => $datum,
                'kwartaal'      => $kwartaal,
                'jaar'          => $jaar,
                'time'          => $time
            ]);

        // Als er nog geen rij in de km tabel staat, voeg er dan 1 toe
        if ($km_count === 0 && $km !== '0.00') {
            DB::connection('mysql2')->table('km')
                ->insert([
                    'klant'     => $user_id,
                    'uren_id'   => $uren_id,
                    'km'        => $km,
                    'van'       => '',
                    'naar'      => '',
                    'datum'     => $datum,
                    'jaar'      => $jaar,
                    'time'      => $time
                ]);
        } else {
            DB::connection('mysql2')->table('km')
                ->where(['klant' => $user_id, 'uren_id' => $uren_id])
                ->update([
                    'km'        => $km,
                    'datum'     => $datum,
                    'jaar'      => $jaar,
                    'time'      => $time
                ]);
        }
        return $this->fetchUrenkm();
    }

    // Verwijdert rijen uit de database
    public function removeRow()
    {
        switch($_GET['page']) {
            case 'facturen':
                DB::connection('mysql2')->table('facturen')->where(['factuur_id' => $_GET['id'], 'klant' => $_GET['user_id']])->delete();
                DB::connection('mysql2')->table('fac_kosten')->where(['factuur_id' => $_GET['id'], 'klant_id' => $_GET['user_id']])->delete();
                break;
            case 'fackosten':
                DB::connection('mysql2')->table('fac_kosten')->where(['kosten_id' => $_GET['id'], 'klant_id' => $_GET['user_id']])->delete();
                break;
            case 'kosten':
                DB::connection('mysql2')->table('kosten')->where(['id' => $_GET['id'], 'klant_id' => $_GET['user_id']])->delete();
                break;
            case 'urenkm':
                DB::connection('mysql2')->table('uren')->where(['id' => $_GET['id'], 'klant_id' => $_GET['user_id']])->delete();
                DB::connection('mysql2')->table('km')->where(['uren_id' => $_GET['id'], 'klant' => $_GET['user_id']])->delete();
                break;
        }
    }

    public function toggle()
    {
        DB::table('Sepa_incasso')->where(['id' => $_GET['id']], ['user_id' => $_GET['user_id']])
            ->update(['succes'  => $_GET['voltooid']]);
    }
}