@extends('layouts.app')
@section('title', $current_user[0]->vnaam . ' ' . $current_user[0]->anaam)
@section('content')
    @if (count($errors))
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (!empty($success))
        {{ $success }}
    @endif
    <div id="user">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a data-toggle="tab" href="#algemeen">Algemeen</a></li>
            <li role="presentation"><a data-toggle="tab" href="#facturen">Facturen</a></li>
            <li role="presentation"><a data-toggle="tab" href="#kosten">Kosten</a></li>
            <li role="presentation"><a data-toggle="tab" href="#urenkm">Uren & Km's</a></li>
        </ul>
        <div class="tab-content">
            {{--ALGEMENE INFORMATIE PAGINA--}}
            <div role="tabpanel" class="tab-pane fade in active" id="algemeen">
                <h3>Klantgegevens van <b>{{ $current_user[0]->vnaam . ' ' . $current_user[0]->anaam }}</b> {!! $trial_info !!}</h3>
                <form action="" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
                    <div class="row inputs">
                        <div class="form-group col-xs-12">
                            @foreach($show_user[0] as $key => $value)
                                <div class="col-xs-12 col-sm-6 field">
                                    <label for="{{ $key }}" class="col-xs-12 col-sm-3">{{ $label[$key] }}:</label>
                                    <div class="col-xs-12 col-sm-9">
                                            @if ($key == 'id')
                                                <input class="form-control" id="{{ $key }}" value="{{ $value }}" name="{{ $key }}" placeholder="{{ $key }}" disabled="disabled">
                                            @else
                                                <input class="form-control" id="{{ $key }}" value="{{ $value }}" name="{{ $key }}" placeholder="{{ $key }}">
                                            @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <h3>Incasso:</h3>
                    <div class="row dropdowns">
                        <div class="col-xs-12 col-sm-4" id="active-div">
                            <label for="active-dropdown">Actief/niet actief:</label>
                            <select class="selectpicker" id="active-dropdown" name="active" data-width="100%">
                                {!! $options[0] !!}
                                {!! $options[1] !!}
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-4" id="abbos-div">
                            <label for="abbos-dropdown">Abonnement:</label>
                            <select class="selectpicker" id="abbos-dropdown" name="abbos" data-width="100%">
                                @foreach ($abonnementen as $abonnement)
                                    {!! $abonnement !!}
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-4" id="active-disabled-div">
                            <label for="active-disabled-dropdown">Actief/niet actief:</label>
                            <select class="selectpicker" id="active-disabled-dropdown" name="active-disabled" data-width="100%" readonly="readonly">
                                <option value="0">Niet actief</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-4" id="abbo-disabled-div">
                            <label for="abbo-disabled-dropdown">Abonnement:</label>
                            <select class="selectpicker" id="abbo-disabled-dropdown" name="abbo-disabled" data-width="100%" readonly="readonly">
                                <option value="0_3_2" selected="selected">Deep Purple (volledig pakket voor 30 dagen)</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-4" id="trial-div">
                            <label for="trial-dropdown">Trial gebruiker:</label>
                            <select class="selectpicker" id="trial-dropdown" name="trial" data-width="100%">
                                {!! $trial[0] !!}
                                {!! $trial[1] !!}
                            </select>
                        </div>
                    </div>
                    <input class="btn btn-default" type="submit" value="Opslaan">
                </form>
            </div>

            {{--FACTUREN-OVERZICHT PAGINA--}}
            <div role="tabpanel" class="tab-pane fade" id="facturen">
                @if (empty($facturen[0]))
                    <p class="empty-alert">Er zijn nog geen facturen verstuurd door <b>{{ $current_user[0]->vnaam }} {{ $current_user[0]->anaam }}</b>!</p>
                @else
                    <h3>Verstuurde facturen van <b>{{ $current_user[0]->vnaam }} {{ $current_user[0]->anaam }}</b></h3>
                    <div class="table">
                        <div class="row table_header">
                            <div class="col-xs-9">
                                <div class="col-xs-1 text-left">ID</div>
                                <div class="col-xs-2">Datum</div>
                                <div class="col-xs-2">Factuurnummer</div>
                                <div class="col-xs-2">Type</div>
                                <div class="col-xs-2">Gefactureerde</div>
                                <div class="col-xs-2">Bedrag incl.</div>
                                <div class="col-xs-1">Voldaan</div>
                            </div>
                            <div class="col-xs-3">
                                <div class="col-xs-10">Verstuurd</div>
                                <div class="col-xs-2 text-right">Opties</div>
                            </div>
                        </div>
                        <div id="factuur_layers">
                            <div role="tabpanel" class="tab-pane fade in active" id="factuur_page1">
                                @for ($i = 1; $i <= count($facturen); $i++)
                                    <div id="facturen_{{ $facturen[$i - 1]->factuur_id }}" class="row table_layer">
                                        <div class="col-xs-9">
                                            <div class="table_col col-xs-1 text-left">{{ $facturen[$i - 1]->factuur_id }}</div>
                                            <div class="table_col col-xs-2">{{ $facturen[$i - 1]->datum }}</div>
                                            <div class="table_col col-xs-2">{{ $facturen[$i - 1]->factuur_nr }}</div>
                                            <div class="table_col col-xs-2">{{ $facturen[$i - 1]->type }}</div>
                                            <div class="table_col col-xs-2">{{ $facturen[$i - 1]->adres }}</div>
                                            <div class="table_col col-xs-2">&euro; {{ $facturen[$i - 1]->btw_bedrag }}</div>
                                            <div class="table_col col-xs-1">{!! $facturen[$i - 1]->voldaan !!}</div>
                                        </div>
                                        <div class="col-xs-3">
                                            <div class="table_col col-xs-10">{{ $facturen[$i - 1]->verstuurd }}</div>
                                            <div class="table_col col-xs-2 text-right">
                                                <a href="{{ url('user/' . $user_id . '/' . $facturen[$i - 1]->factuur_id ) }}" target="_black"><img src="{{ url('images/bewerk.png') }}" title="Bewerken"></a>
                                                <a onclick="removeRow({{ $facturen[$i - 1]->factuur_id }}, 'facturen')"><img src="{{ url('images/busy.png') }}" title="Verwijderen"></a>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($i % $max_rows == 0)
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="factuur_page{{ ceil($i / $max_rows) + 1 }}">
                                        @continue
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="page_list">
                        <ul id="fac_pagination" class="pagination">
                            <li class="first_page" role="presentation" aria-label="First"><span aria-hidden="true">&laquo;</span></li>
                            <li class="previous_page" role="presentation" aria-label="Previous"><span aria-hidden="true">&lt;</span></li>
                            <li class="more_left disabled" role="presentation"><a aria-expanded="true">...</a></li>
                            @for ($j = 1; $j <= ceil(count($facturen) / $max_rows); $j++)
                                <li class="fac_pages" role="presentation"><a data-toggle="tab" href="#factuur_page{{ $j }}">{{ $j }}</a></li>
                            @endfor
                            <li class="more_right disabled" role="presentation"><a aria-expanded="true">...</a></li>
                            <li class="next_page" role="presentation" aria-label="Next"><span aria-hidden="true">&gt;</span></li>
                            <li class="last_page" role="presentation" aria-label="Last"><span aria-hidden="true">&raquo;</span></li>
                        </ul>
                    </div>
                @endif
            </div>

            {{--KOSTEN-OVERZICHT PAGINA--}}
            <div class="modal fade" id="kostenModal" tabindex="-1" role="dialog" aria-labelledby="kostenModalTitle">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="kostenModalTitle">Kosten: <span></span></h4>
                        </div>
                        <div class="modal-body">
                            <div class="kosten-error alert alert-danger">
                                <ul></ul>
                            </div>
                            <p class="loading text-center">Aan het laden...</p>
                            <div class="modal-form form-horizontal">
                                <input type="hidden" id="kosten_id" value="">
                                <div class="form-group">
                                    <label for="kosten_datum" class="control-label col-lg-2">Datum:</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="kosten_datum" placeholder="Datum">
                                    </div>
                                    <label for="kosten_omschrijving" class="control-label col-lg-2">Omschrijving:</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="kosten_omschrijving" placeholder="Omschrijving">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="kosten_cat" class="control-label col-lg-2">Categorie:</label>
                                    <div class="col-lg-4">
                                        <select class="form-control selectpicker" id="kosten_cat">
                                            @foreach ($kosten_cat as $cat)
                                                <option value="{{ $cat->kosten_id }}">{{ $cat->naam }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="kosten_prive" class="control-label col-lg-2">Privégebruik:</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <select class="form-control selectpicker" id="kosten_prive">
                                                @for ($i = 0; $i <= 100; $i += 5)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <span class="input-group-addon">&#37;</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="kosten_btw_bedrag" class="control-label col-lg-2">Bedrag excl.:</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input oninput="btwCheck('input')" type="text" class="form-control" id="kosten_btw_bedrag" placeholder="Bedrag excl.">
                                        </div>
                                    </div>
                                    <label for="kosten_btw_tarief" class="control-label col-lg-2">Btw-tarief:</label>
                                    <div class="col-lg-4">
                                        <select onchange="btwCheck('select')" class="form-control selectpicker" id="kosten_btw_tarief">
                                            <option value="vrij">Vrijgesteld</option>
                                            <option value="0">0&#37;</option>
                                            <option value="6">6&#37;</option>
                                            <option value="21">21&#37;</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="kosten_bedrag" class="control-label col-lg-2">Bedrag incl.:</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input type="text" class="form-control" id="kosten_bedrag" placeholder="Bedrag incl." disabled="disabled">
                                        </div>
                                    </div>
                                    <label for="kosten_buitenland" class="control-label col-lg-2">Buitenland:</label>
                                    <div class="col-lg-4">
                                        <select class="form-control selectpicker" id="kosten_buitenland">
                                            <option value="0">Nederland</option>
                                            <option value="1">Land binnen de EU</option>
                                            <option value="2">Land buiten de EU</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <span id="kostenSaved">Opgeslagen!</span>
                            <button onclick="updateRow('kosten')" class="btn btn-primary disabled" id="kostenEdit">Bewerken <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span></button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="kosten">
                @if (empty($kosten[0]))
                    <p class="empty-alert">Er zijn nog geen kosten opgevoerd door <b>{{ $current_user[0]->vnaam }} {{ $current_user[0]->anaam }}</b>!</p>
                @else
                    <h3>Opgevoerde kosten van <b>{{ $current_user[0]->vnaam }} {{ $current_user[0]->anaam }}</b></h3>
                    <div class="table">
                        <div class="row table_header">
                            <div class="col-xs-1 text-left">ID</div>
                            <div class="col-xs-1">Datum</div>
                            <div class="col-xs-2">Omschrijving</div>
                            <div class="col-xs-3">Categorie</div>
                            <div class="col-xs-2">Bedrag excl.</div>
                            <div class="col-xs-1">Privégebruik</div>
                            <div class="col-xs-1">Kwartaal</div>
                            <div class="col-xs-1 text-right">Opties</div>
                        </div>
                        <div id="kosten_layers">
                            <div role="tabpanel" class="tab-pane fade in active" id="kost_page1">
                                @for ($i = 1; $i <= count($kosten); $i++)
                                    <div id="kosten_{{ $kosten[$i - 1]->id }}" class="row table_layer">
                                        <div class="table_col col-xs-1 text-left id">{{ $kosten[$i - 1]->id }}</div>
                                        <div class="table_col col-xs-1 datum">{{ $kosten[$i - 1]->datum }}</div>
                                        <div class="table_col col-xs-2 omschrijving">{{ $kosten[$i - 1]->omschrijving }}</div>
                                        <div class="table_col col-xs-3 cat">{{ $kosten[$i - 1]->cat }}</div>
                                        <div class="table_col col-xs-2 btw_bedrag">&euro; {{ $kosten[$i - 1]->btw_bedrag }}</div>
                                        <div class="table_col col-xs-1 prive">{{ $kosten[$i - 1]->prive }}&#37;</div>
                                        <div class="table_col col-xs-1 kwartaal">Kwartaal {{ $kosten[$i - 1]->kwartaal }}</div>
                                        <div class="table_col col-xs-1 text-right">
                                            <a onclick="fetchRow({{ $kosten[$i - 1]->id }}, 'kosten')" data-toggle="modal" data-target="#kostenModal"><img src="{{ url('images/bewerk.png') }}" title="Bewerken"></a>
                                            <a onclick="removeRow({{ $kosten[$i - 1]->id }}, 'kosten')"><img src="{{ url('images/busy.png') }}" title="Verwijderen"></a>
                                        </div>
                                    </div>
                                    @if ($i % $max_rows == 0)
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="kost_page{{ ceil($i / $max_rows) + 1 }}">
                                        @continue
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="page_list">
                        <ul id="kost_pagination" class="pagination">
                            <li class="first_page" role="presentation" aria-label="First"><span aria-hidden="true">&laquo;</span></li>
                            <li class="previous_page" role="presentation" aria-label="Previous"><span aria-hidden="true">&lt;</span></li>
                            <li class="more_left disabled" role="presentation"><a aria-expanded="true">...</a></li>
                            @for ($j = 1; $j <= ceil(count($kosten) / $max_rows); $j++)
                                <li class="kost_pages" role="presentation"><a data-toggle="tab" href="#kost_page{{ $j }}">{{ $j }}</a></li>
                            @endfor
                            <li class="more_right disabled" role="presentation"><a aria-expanded="true">...</a></li>
                            <li class="next_page" role="presentation" aria-label="Next"><span aria-hidden="true">&gt;</span></li>
                            <li class="last_page" role="presentation" aria-label="Last"><span aria-hidden="true">&raquo;</span></li>
                        </ul>
                    </div>
                @endif
            </div>

            {{--UREN EN KILOMETERS PAGINA--}}
            <div class="modal fade" id="urenkmModal" tabindex="-1" role="dialog" aria-labelledby="urenkmModalTitle">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="urenkmModalTitle">Uren & kilometers: <span></span></h4>
                        </div>
                        <div class="modal-body">
                            <div class="urenkm-error alert alert-danger">
                                <ul></ul>
                            </div>
                            <p class="loading text-center">Aan het laden...</p>
                            <div class="modal-form form-horizontal">
                                <input type="hidden" id="urenkm_id" value="">
                                <div class="form-group">
                                    <label for="urenkm_datum" class="control-label col-lg-2">Datum:</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="urenkm_datum" placeholder="Datum">
                                    </div>
                                    <label for="urenkm_omschrijving" class="control-label col-lg-2">Omschrijving:</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="urenkm_omschrijving" placeholder="Omschrijving">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="urenkm_aantaluren" class="control-label col-lg-2">Aantal uren:</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="urenkm_aantaluren" placeholder="Aantal uren">
                                    </div>
                                    <label for="urenkm_aantalkm" class="control-label col-lg-2">Aantal kilometers:</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="urenkm_aantalkm" placeholder="Aantal kilometers">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <span id="urenkmSaved">Opgeslagen!</span>
                            <button onclick="updateRow('urenkm')" class="btn btn-primary disabled" id="urenkmEdit">Bewerken <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span></button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="urenkm">
                @if (empty($uren[0]))
                    <p class="empty-alert">Er zijn nog geen uren en kilometers opgevoerd door <b>{{ $current_user[0]->vnaam }} {{ $current_user[0]->anaam }}</b>!</p>
                @else
                    <h3>Opgevoerde uren en kilometers van <b>{{ $current_user[0]->vnaam }} {{ $current_user[0]->anaam }}</b></h3>
                    <div class="table">
                        <div class="row table_header">
                            <div class="col-xs-1 text-left">ID</div>
                            <div class="col-xs-1">Datum</div>
                            <div class="col-xs-3">Omschrijving</div>
                            <div class="col-xs-2">Aantal uren</div>
                            <div class="col-xs-2">Aantal kilometers</div>
                            <div class="col-xs-2">Kwartaal</div>
                            <div class="col-xs-1 text-right">Opties</div>
                        </div>
                        <div id="urenkm_layers">
                            <div role="tabpanel" class="tab-pane fade in active" id="urenkm_page1">
                                @for ($i = 1; $i <= count($uren); $i++)
                                    <div id="urenkm_{{ $uren[$i - 1]->id }}" class="row table_layer">
                                        <div class="table_col col-xs-1 text-left id">{{ $uren[$i - 1]->id }}</div>
                                        <div class="table_col col-xs-1 datum">{{ $uren[$i - 1]->datum }}</div>
                                        <div class="table_col col-xs-3 omschrijving">{{ $uren[$i - 1]->omschrijving }}</div>
                                        <div class="table_col col-xs-2 uren">{{ $uren[$i - 1]->uren }} uren</div>
                                        <div class="table_col col-xs-2 kilometers">{{ $uren[$i - 1]->km }} km</div>
                                        <div class="table_col col-xs-2 kwartaal">Kwartaal {{ $uren[$i - 1]->kwartaal }}</div>
                                        <div class="table_col col-xs-1 text-right">
                                            <a onclick="fetchRow({{ $uren[$i - 1]->id }}, 'urenkm')" data-toggle="modal" data-target="#urenkmModal"><img src="{{ url('images/bewerk.png') }}" title="Bewerken"></a>
                                            <a onclick="removeRow({{ $uren[$i - 1]->id }}, 'urenkm')"><img src="{{ url('images/busy.png') }}" title="Verwijderen"></a>
                                        </div>
                                    </div>
                                    @if ($i % $max_rows == 0)
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="urenkm_page{{ ceil($i / $max_rows) + 1 }}">
                                        @continue
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="page_list">
                        <ul id="urenkm_pagination" class="pagination">
                            <li class="first_page" role="presentation" aria-label="First"><span aria-hidden="true">&laquo;</span></li>
                            <li class="previous_page" role="presentation" aria-label="Previous"><span aria-hidden="true">&lt;</span></li>
                            <li class="more_left disabled" role="presentation"><a aria-expanded="true">...</a></li>
                            @for ($j = 1; $j <= ceil(count($uren) / $max_rows); $j++)
                                <li class="urenkm_pages" role="presentation"><a data-toggle="tab" href="#urenkm{{ $j }}">{{ $j }}</a></li>
                            @endfor
                            <li class="more_right disabled" role="presentation"><a aria-expanded="true">...</a></li>
                            <li class="next_page" role="presentation" aria-label="Next"><span aria-hidden="true">&gt;</span></li>
                            <li class="last_page" role="presentation" aria-label="Last"><span aria-hidden="true">&raquo;</span></li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('extra_js')
    <script>
        // Globale variable van het maximaal aantal pagina's dat hij laat zien
        const max_pages = 10;

        //Veranderd de Bedrag incl. input zodra er iets veranderd
        function btwCheck(where) {
            var bedrag = $('#kosten_bedrag');
            var btw_bedrag = $('#kosten_btw_bedrag').val();
            var btw_tarief = $('#kosten_btw_tarief').val();
            btw_tarief === 'vrij' ? btw_tarief = 0 : btw_tarief;

            // Checkt of de dropdown of inputveld veranderd is
            if (where === 'select') {
                bedrag.val(punt_naar_komma((komma_naar_punt(btw_bedrag) * (btw_tarief / 100 + 1)).toFixed(2)));
            } else if (where === 'input') {
                btw_bedrag.val(punt_naar_komma((komma_naar_punt(btw_bedrag) / 100 * (100 + parseInt(btw_tarief))).toFixed(2)));
            } else {
                btw_bedrag.val('');
            }
        }

        //Laat rijen zien uit de database
        function fetchRow(row_id, page) {
            $('#' + page + 'Modal').find('.loading').css('display', 'block').end().find('.modal-form').css('display', 'none');
            $('#' + page + 'Edit').addClass('disabled');
            $('#' + page + 'ModalTitle').find('span').text('');
                $.ajax({
                    url: '/fetch_' + page,
                    data: {'id': row_id},
                    success: function(data) {
                        var row = data[0];
                        switch(page) {
                            case 'kosten':
                                $('#' + page + 'ModalTitle').find('span').text(row.id);
                                $('#' + page + '_id').val(row.id);
                                $('#' + page + '_datum').val(row.datum);
                                $('#' + page + '_omschrijving').val(row.omschrijving);
                                $('#' + page + '_cat').val(row.cat);
                                $('#' + page + '_prive').val(row.prive);
                                $('#' + page + '_btw_bedrag').val(row.btw_bedrag);
                                $('#' + page + '_btw_tarief').val(row.btw_tarief);
                                $('#' + page + '_bedrag').val(row.bedrag);
                                $('#' + page + '_buitenland').val(row.buitenland);
                                $('.selectpicker').selectpicker('refresh');
                                break;
                            case 'urenkm':
                                $('#' + page + 'ModalTitle').find('span').text(row.id);
                                $('#' + page + '_id').val(row.id);
                                $('#' + page + '_datum').val(row.datum);
                                $('#' + page + '_omschrijving').val(row.omschrijving);
                                $('#' + page + '_aantaluren').val(row.uren);
                                if (row.km === null) {
                                    $('#' + page + '_aantalkm').val('0.00');
                                } else {
                                    $('#' + page + '_aantalkm').val(row.km);
                                }
                                break;
                            default:
                                alert('An error has occured. Check your Console to view the error.');
                                console.log('Unknown page "' + page + '" or unknown row ID "' + row_id + '"');
                        }
                    },
                    complete: function() {
                        $('#' + page + 'Modal').find('.loading').css('display', 'none').end().find('.modal-form').css('display', 'block');
                        $('#' + page + 'Edit').removeClass('disabled');
                    }
                });
        }

        //Updatet rijen uit de database en op de pagina
        function updateRow(page) {
            $('#' + page + 'Edit').find('span').css('display', 'inline-block');
            switch(page) {
                case 'kosten':
                    $.ajax({
                        url: '/update_kosten',
                        data: {
                            'user_id': $('#user_id').val(),
                            'id': $('#' + page + '_id').val(),
                            'datum': $('#' + page + '_datum').val(),
                            'omschrijving': $('#' + page + '_omschrijving').val(),
                            'cat': $('#' + page + '_cat').val(),
                            'prive': $('#' + page + '_prive').val(),
                            'btw_bedrag': $('#' + page + '_btw_bedrag').val(),
                            'btw_tarief': $('#' + page + '_btw_tarief').val(),
                            'bedrag': $('#' + page + '_bedrag').val(),
                            'buitenland': $('#' + page + '_buitenland').val()
                        },
                        error: function (data) {
                            var errors = data.responseJSON;
                            $('.' + page + '-error')
                                .css('display', 'block').find('ul').empty().end()
                                .removeClass('alert-success').addClass('alert-danger');
                            $.each(errors, function(key, value) {
                                $('.' + page + '-error').find('ul').append('<li>' + value[0] + '</li>');
                            });
                        },
                        success: function () {
                            $('.' + page + '-error').css('display', 'block')
                                .find('ul').empty().append('<li>Opgeslagen!</li>')
                                .end().removeClass('alert-danger')
                                .addClass('alert-success').fadeOut(2500);
                        },
                        complete: function () {
                            $('#' + page + 'Edit').find('span').css('display', 'none');
                            $.getJSON('/fetch_kosten?id=' + $('#' + page + '_id').val(), function (data) {
                                var layer = $('#' + page + '_' + $('#' + page + '_id').val());
                                var row = data[0];
                                layer.find('.datum').html(row.datum);
                                layer.find('.omschrijving').html(row.omschrijving);
                                layer.find('.cat').html($('#' + page + '_cat').find(':selected').text());
                                layer.find('.btw_bedrag').html('&euro; ' + row.btw_bedrag);
                                layer.find('.prive').html(row.prive + '&#37;');
                                layer.find('.kwartaal').html('Kwartaal ' + row.kwartaal);
                            });
                        }
                    });
                    break;
                case 'urenkm':
                    $.ajax({
                        url: '/update_urenkm',
                        data: {
                            'user_id': $('#user_id').val(),
                            'id': $('#' + page + '_id').val(),
                            'datum': $('#' + page + '_datum').val(),
                            'omschrijving': $('#' + page + '_omschrijving').val(),
                            'aantaluren': $('#' + page + '_aantaluren').val(),
                            'aantalkm': $('#' + page + '_aantalkm').val()
                        },
                        error: function (data) {
                            var errors = data.responseJSON;
                            $('.' + page + '-error')
                                .css('display', 'block').find('ul').empty().end()
                                .removeClass('alert-success').addClass('alert-danger');
                            $.each(errors, function(key, value) {
                                $('.' + page + '-error').find('ul').append('<li>' + value[0] + '</li>');
                            });
                        },
                        success: function () {
                            $('.' + page + '-error').css('display', 'block')
                                .find('ul').empty().append('<li>Opgeslagen!</li>')
                                .end().removeClass('alert-danger')
                                .addClass('alert-success').fadeOut(2500);
                        },
                        complete: function () {
                            $('#' + page + 'Edit').find('span').css('display', 'none');
                            $.getJSON('/fetch_' + page + '?id=' + $('#' + page + '_id').val(), function (data) {
                                var layer = $('#' + page + '_' + $('#' + page + '_id').val());
                                var row = data[0];
                                layer.find('.datum').html(row.datum);
                                layer.find('.omschrijving').html(row.omschrijving);
                                layer.find('.uren').html(row.uren + ' uren');
                                layer.find('.kilometers').html(row.km + ' km');
                                layer.find('.kwartaal').html('Kwartaal ' + row.kwartaal);
                            });
                        }
                    });
                    break;
                default:
                    alert('An error has occured. Check your Console to view the error.');
                    console.log('Unknown page: ' + page);
            }
        }

        //Verwijderd rijen uit de database en op de pagina
        function removeRow(row_id, page) {
            if (confirm("Weet je zeker dat je rij " + row_id + " wilt verwijderen?")) {
                $('#loading_screen').show();
                $.ajax({
                    url: '/remove_row',
                    data: {
                        'page': page,
                        'id': row_id,
                        'user_id': $('#user_id').val()
                    },
                    complete: function () {
                        $('#' + page + '_' + row_id).remove();
                        $('#loading_screen').hide();
                    }
                });
            }
        }

        $(document).ready(function() {
            // Als er een succesbericht is, verdwijnt ie in 5 seconden
            $('#success-message').fadeOut(5000);

            // Verbergt het laad icoontje en de tekst 'Opgeslagen!' in de Modals
            $('#urenkmSaved').css('display', 'none');
            $('#kostenSaved').css('display', 'none');
            $('#facturenSaved').css('display', 'none');
            $('#urenkmEdit').find('span').css('display', 'none');
            $('#kostenEdit').find('span').css('display', 'none');
            $('#facturenEdit').find('span').css('display', 'none');

            //Laat de goede dropdowns zien op de Algemene pagina
            jQuery(function($) {
                $('#trial-dropdown').on('change' ,function() {
                    if ($('#trial-dropdown').val() === '1') {
                        $('#abbos-div, #active-div').hide();
                        $('#abbo-disabled-div, #active-disabled-div').show();
                    } else {
                        $('#abbos-div, #active-div').show();
                        $('#abbo-disabled-div, #active-disabled-div').hide();
                    }
                }).change();
            });

            // Facturenpagina
            var fac_pages = $('.fac_pages');
            var fac_pagination = $('#fac_pagination');
            var fac_layers = $('#factuur_layers').children();
            fac_pages.first().addClass('active');
            fac_layers.first().css('display', 'block');
            if (fac_layers.last().children().length === 0) {
                fac_layers.last().remove();
                fac_pages.last().remove();
            }
            $.each(fac_pages, function(page) {
                if (page < max_pages) {
                    $(this).css('display', 'inline');
                } else {
                    $(this).css('display', 'none');
                    fac_pagination.find('.more_right').css('display', 'inline');
                }
            });
            fac_pagination.find('.first_page, .previous_page').addClass('disabled');
            if (fac_pages.length === 1) {
                fac_pagination.find('.first_page, .previous_page, .next_page, .last_page').addClass('disabled');
            }

            // Kostenpagina
            var kost_pages = $('.kost_pages');
            var kost_pagination = $('#kost_pagination');
            var kost_layers = $('#kosten_layers').children();
            kost_pages.first().addClass('active');
            kost_layers.first().css('display', 'block');
            if (kost_layers.last().children().length === 0) {
                kost_layers.last().remove();
                kost_pages.last().remove();
            }
            $.each(kost_pages, function(page) {
                if (page < max_pages) {
                    $(this).css('display', 'inline');
                } else {
                    $(this).css('display', 'none');
                    kost_pagination.find('.more_right').css('display', 'inline');
                }
            });
            kost_pagination.find('.first_page, .previous_page').addClass('disabled');
            if (kost_pages.length === 1) {
                kost_pagination.find('.first_page, .previous_page, .next_page, .last_page').addClass('disabled');
            }

            // Uren en kilometers pagina
            var urenkm_pages = $('.urenkm_pages');
            var urenkm_pagination = $('#urenkm_pagination');
            var urenkm_layers = $('#urenkm_layers').children();
            urenkm_pages.first().addClass('active');
            urenkm_layers.first().css('display', 'block');
            if (urenkm_layers.last().children().length === 0) {
                urenkm_layers.last().remove();
                urenkm_pages.last().remove();
            }
            $.each(urenkm_pages, function(page) {
                if (page < max_pages) {
                    $(this).css('display', 'inline');
                } else {
                    $(this).css('display', 'none');
                    urenkm_pagination.find('.more_right').css('display', 'inline');
                }
            });
            urenkm_pagination.find('.first_page, .previous_page').addClass('disabled');
            if (urenkm_pages.length === 1) {
                urenkm_pagination.find('.first_page, .previous_page, .next_page, .last_page').addClass('disabled');
            }
        });

        // Pagination functie
        $('.pagination li').click(function(a) {
            var pages = '';
            var layers = '';
            var first_page = '1';
            var visible_page = '';
            var page = a.target.innerText;
            var more_left = $(this).parent().find('.more_left');
            var more_right = $(this).parent().find('.more_right');

            // Zet de juiste waarden in de variablen
            switch(true) {
                case $(this).parent().is('#fac_pagination'):
                    pages = $('.fac_pages');
                    visible_page = $('.fac_pages:visible');
                    layers = $('#factuur_layers').children();
                    break;
                case $(this).parent().is('#kost_pagination'):
                    pages = $('.kost_pages');
                    visible_page = $('.kost_pages:visible');
                    layers = $('#kosten_layers').children();
                    break;
                case $(this).parent().is('#urenkm_pagination'):
                    pages = $('.urenkm_pages');
                    visible_page = $('.urenkm_pages:visible');
                    layers = $('#urenkm_layers').children();
                    break;
            }

            // Kijkt of er maar 1 pagina is
            if (pages.length === 1) {
                $(this).parent().find('.first_page, .previous_page, .next_page, .last_page').addClass('disabled');
            } else {
                // Checkt of de knop waar je op drukt niet disabled is
                if (!$(this).hasClass('disabled')) {
                    // Als je op het eerste paginanummer klikt
                    if (page === first_page) {
                        $(this).parent().find('.first_page, .previous_page').addClass('disabled');
                    } else {
                        $(this).parent().find('.first_page, .previous_page').removeClass('disabled');
                    }
                    // Als je op het laatste paginanummer drukt
                    if (page === String(pages.length)) {
                        $(this).parent().find('.next_page, .last_page').addClass('disabled');
                    } else {
                        $(this).parent().find('.next_page, .last_page').removeClass('disabled');
                    }

                    // Loopt door elke rij op de pagina
                    for (var i = 0; i < layers.length; i++) {
                        // Kijkt op welke knop gedrukt is
                        switch (page) {
                            case String(i + 1):
                                layers.eq(i).css('display', 'block');
                                break;
                            case '<':
                                for (var j = 0; j < pages.length; j++) {
                                    if (pages.eq(j).hasClass('active')) {
                                        layers.eq(j).css('display', 'none');
                                        layers.eq(j).removeClass('active in');
                                        layers.eq(j - 1).css('display', 'block');
                                        layers.eq(j - 1).addClass('active in');
                                        pages.eq(j).removeClass('active');
                                        pages.eq(j - 1).addClass('active');
                                        if (pages.eq(j - 1).text() === first_page) {
                                            $(this).parent().find('.first_page, .previous_page').addClass('disabled');
                                        }
                                        if (more_left.css('display') === 'inline' && visible_page.first().prev().hasClass('active')) {
                                            visible_page.last().css('display', 'none');
                                            visible_page.first().prev().css('display', 'inline');
                                            if (more_right.css('display') === 'none') {
                                                more_right.css('display', 'inline');
                                            }
                                            if (pages.first().hasClass('active')) {
                                                more_left.css('display', 'none');
                                            }
                                        }
                                        break;
                                    }
                                }
                                return;
                                break;
                            case '>':
                                for (var k = 0; k < pages.length; k++) {
                                    if (pages.eq(k).hasClass('active')) {
                                        layers.eq(k).css('display', 'none');
                                        layers.eq(k).removeClass('active in');
                                        layers.eq(k + 1).css('display', 'block');
                                        layers.eq(k + 1).addClass('active in');
                                        pages.eq(k).removeClass('active');
                                        pages.eq(k + 1).addClass('active');
                                        if (pages.eq(k + 1).text() === String(pages.length)) {
                                            $(this).parent().find('.next_page, .last_page').addClass('disabled');
                                        }
                                        if (more_right.css('display') === 'inline' && visible_page.last().next().hasClass('active')) {
                                            visible_page.first().css('display', 'none');
                                            visible_page.last().next().css('display', 'inline');
                                            if (more_left.css('display') === 'none') {
                                                more_left.css('display', 'inline');
                                            }
                                            if (pages.last().hasClass('active')) {
                                                more_right.css('display', 'none');
                                            }
                                        }
                                        break;
                                    }
                                }
                                return;
                                break;
                            case '«':
                                layers.eq(i).css('display', 'none');
                                layers.eq(i).removeClass('active in');
                                layers.first().css('display', 'block');
                                layers.first().addClass('active in');
                                pages.eq(i + 1).removeClass('active');
                                pages.first().addClass('active');
                                $(this).parent().find('.first_page, .previous_page').addClass('disabled');
                                if (more_left.css('display') === 'inline') {
                                    pages.css('display', 'inline');
                                    pages.slice(max_pages).css('display', 'none');
                                    more_left.css('display', 'none');
                                    more_right.css('display', 'inline');

                                }
                                break;
                            case '»':
                                layers.eq(i).css('display', 'none');
                                layers.eq(i).removeClass('active in');
                                layers.last().css('display', 'block');
                                layers.last().addClass('active in');
                                pages.eq(i).removeClass('active');
                                pages.last().addClass('active');
                                $(this).parent().find('.next_page, .last_page').addClass('disabled');
                                if (more_right.css('display') === 'inline') {
                                    pages.css('display', 'none');
                                    pages.slice(-max_pages).css('display', 'inline');
                                    more_left.css('display', 'inline');
                                    more_right.css('display', 'none');
                                }
                                break;
                            default:
                                layers.eq(i).css('display', 'none');
                        }
                    }
                }
            }
        });
    </script>
@endsection