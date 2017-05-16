@extends('layouts.app')
@section('title', 'Factuur ' . $factuur_id)
@section('content')
    @if (!empty($success))
        {{ $success }}
    @endif
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div id="factuur">
        <form action="" method="post" class="form-group col-xs-12 col-sm-6">
            {{ csrf_field() }}
            <input type="hidden" name="factuur_id" id="factuur_id" value="{{ $factuur_id }}">
            <input type="hidden" name="klant_id" id="klant_id" value="{{ $klant_id }}">
            <div class="modal fade" id="klantModal" tabindex="-1" role="dialog" aria-labelledby="klantModalTitle">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="klantModalTitle">Klantgegevens</h4>
                        </div>
                        <div class="modal-body">
                            @foreach($klant_labels as $key => $label)
                                @if ($key === 'logo')
                                    <div class="col-xs-12 col-sm-6 factuur_layer form-group">
                                        <label for="fac_klant_{{ $key }}" class="control-label col-xs-12 col-sm-4">{{ $label[0] }}:</label>
                                        <div class="col-xs-12 col-sm-8">
                                            <input type="text" class="form-control" name="klant_{{ $key }}" id="fac_klant_{{ $key }}" placeholder="{{ $key }}" value="{{ $label[1] }}" readonly="readonly">
                                        </div>
                                    </div>
                                @else
                                    <div class="col-xs-12 col-sm-6 factuur_layer form-group">
                                        <label for="fac_klant_{{ $key }}" class="control-label col-xs-12 col-sm-4">{{ $label[0] }}:</label>
                                        <div class="col-xs-12 col-sm-8">
                                            <input type="text" class="form-control" name="klant_{{ $key }}" id="fac_klant_{{ $key }}" placeholder="{{ $key }}" value="{{ $label[1] }}">
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="adresModal" tabindex="-1" role="dialog" aria-labelledby="adresModalTitle">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="adresModalTitle">Adresgegevens</h4>
                        </div>
                        <div class="modal-body">
                            @foreach($adres_labels as $key => $label)
                                @if ($key === 'land')
                                    <div class="col-xs-12 col-sm-6 factuur_layer form-group">
                                        <label for="fac_adres_{{ $key }}" class="control-label col-xs-12 col-sm-4">{{ $label[0] }}:</label>
                                        <div class="col-xs-12 col-sm-8">
                                            <select class="form-control selectpicker" name="adres_{{ $key }}" id="fac_adres_{{ $key }}">
                                                @for ($j = 0; $j < count($landen); $j++)
                                                    @if ($landen[$j]->land === $label[1])
                                                        <option value="{{ $landen[$j]->land }}" selected="selected">{{ $landen[$j]->land }}</option>
                                                    @else
                                                        <option value="{{ $landen[$j]->land }}">{{ $landen[$j]->land }}</option>
                                                    @endif
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-xs-12 col-sm-6 factuur_layer form-group">
                                        <label for="fac_adres_{{ $key }}" class="control-label col-xs-12 col-sm-4">{{ $label[0] }}:</label>
                                        <div class="col-xs-12 col-sm-8">
                                            <input type="text" class="form-control" name="adres_{{ $key }}" id="fac_adres_{{ $key }}" placeholder="{{ $key }}" value="{{ $label[1] }}">
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="fackostenModal" tabindex="-1" role="dialog" aria-labelledby="fackostenModalTitle">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="fackostenModalTitle">Kostenpost: <span></span></h4>
                        </div>
                        <div class="modal-body">
                            <p class="loading text-center">Aan het laden...</p>
                            <div class="modal-form form-horizontal">
                                <input type="hidden" id="fackosten_id" value="">
                                <div class="form-group">
                                    <label for="fackosten_omschrijving" class="control-label col-lg-2">Omschrijving:</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="fackosten_omschrijving" placeholder="Omschrijving">
                                    </div>
                                    <label for="fackosten_bedrag" class="control-label col-lg-2">Bedrag excl.:</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input oninput="btwCheck('input')" type="text" class="form-control" id="fackosten_bedrag" placeholder="Bedrag excl.">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="fackosten_btw_bedrag" class="control-label col-lg-2">Bedrag incl.:</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input type="text" class="form-control" id="fackosten_btw_bedrag" placeholder="Bedrag incl." disabled="disabled">
                                        </div>
                                    </div>
                                    <label for="fackosten_btw_tarief" class="control-label col-lg-2">Btw-tarief:</label>
                                    <div class="col-lg-4">
                                        <select onchange="btwCheck('select')" class="form-control selectpicker" id="fackosten_btw_tarief">
                                            <option value="vrij">Vrijgesteld</option>
                                            <option value="0">0&#37;</option>
                                            <option value="6">6&#37;</option>
                                            <option value="21">21&#37;</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="fackosten_type" class="control-label col-lg-2">Type:</label>
                                    <div class="col-lg-4">
                                        <select class="form-control selectpicker" id="fackosten_type">
                                            <option value="0">Geen type</option>
                                            <option value="1">Product</option>
                                            <option value="2">Dienst</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <span id="fackostenSaved"></span>
                            <button type="button" class="btn btn-primary disabled" id="fackostenEdit">Bewerken <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span></button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="changeTypeModal" tabindex="-1" role="dialog" aria-labelledby="changeTypeModalTitle">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="changeTypeModalTitle">Type veranderen</h4>
                        </div>
                        <div class="modal-body type-not-zero">
                            <p class="loading text-center">
                                Als het land aanpast naar een land dat niet Nederland is,
                                dan moet bij elke kostenpost het type veranderd worden naar een product of dienst!
                                Ga met je muis over de ID om de omschrijving te zien van de kostenpost.
                            </p>
                            <div class="modal-form form-horizontal">
                                <input type="hidden" value="{{ count($fac_kosten) }}" class="fackosten-count">
                                @foreach ($fac_kosten as $fac_kost)
                                    <div class="form-group">
                                        <input type="hidden" value="{{ $fac_kost->type }}" class="fackosten-type">
                                        <input type="hidden" value="{{ $fac_kost->kosten_id }}" class="fackosten-id">
                                        <label for="kostenid_{{ $fac_kost->kosten_id }}" class="control-label col-lg-2" title="{{ $fac_kost->omschrijving }}">{{ $fac_kost->kosten_id }}:</label>
                                        <div class="col-lg-10">
                                            <select id="kostenid_{{ $fac_kost->kosten_id }}" class="form-control selectpicker changeType" title="Kies voor:">
                                                <option value="1">Product</option>
                                                <option value="2">Dienst</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-body type-zero">
                            <p class="loading text-center">
                                Als het land aanpast naar Nederland, dan moet bij elke kostenpost het type veranderd worden naar 0!
                                Ga met je muis over de ID om de omschrijving te zien van de kostenpost.
                            </p>
                            <div class="modal-form form-horizontal">
                                <input type="hidden" value="{{ count($fac_kosten) }}" class="fackosten-count">
                                @foreach ($fac_kosten as $fac_kost)
                                    <div class="form-group">
                                        <input type="hidden" value="{{ $fac_kost->type }}" class="fackosten-type">
                                        <input type="hidden" value="{{ $fac_kost->kosten_id }}" class="fackosten-id">
                                        <label for="kostenid_{{ $fac_kost->kosten_id }}" class="control-label col-lg-2" title="{{ $fac_kost->omschrijving }}">{{ $fac_kost->kosten_id }}:</label>
                                        <div class="col-lg-10">
                                            <select id="kostenid_{{ $fac_kost->kosten_id }}" class="form-control selectpicker changeType">
                                                <option value="0">Geen type</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <span id="changeTypeSaved"></span>
                            <button type="button" class="btn btn-primary" id="changeTypeEdit">Bewerken <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span></button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="facturen-inputs">
                <h2 class="fackosten-head">Factuur <b>{{ $factuur_id }}</b> van <b>{{ $current_user[0]->vnaam . ' ' . $current_user[0]->anaam }}</b></h2>
                <h5>Als een label <span style="color: rgb(255, 0, 0)">rood</span> kleurt, betekent dat dat er veranderingen zijn toegepast die nog niet zijn opgeslagen.</h5>
                @foreach ($facturen[0] as $key => $row)
                    <!-- Als de velden disabled moeten zijn -->
                    @if (in_array($key, $disabled))
                        <div class="col-xs-12 factuur_layer">
                            <label for="factuur_{{ $key }}" class="control-label col-xs-12 col-sm-5 col-md-3">{{ $labels[$key] }}:</label>
                            <div class="col-xs-12 col-sm-7 col-md-9">
                                <input type="text" class="form-control" name="{{ $key }}" id="factuur_{{ $key }}" placeholder="{{ $key }}" value="{{ $row }}" readonly="readonly">
                            </div>
                        </div>
                    <!-- Als het buttons moeten zijn -->
                    @elseif (in_array($key, $buttons))
                        @if ($key === 'klant')
                            <div class="col-xs-12 col-sm-7 col-sm-offset-5 col-md-9 col-md-offset-3 col-lg-4 col-lg-offset-3 factuur_layer">
                                <input type="button" class="btn btn-default {{ $key }}_btn col-xs-12" data-toggle="modal" data-target="#{{ $key }}Modal" value="Bekijk {{ $labels[$key] }}">
                            </div>
                        @endif
                        @if ($key === 'adres')
                            <div class="col-xs-12 col-sm-7 col-sm-offset-5 col-md-9 col-md-offset-3 col-lg-4 col-lg-offset-1 factuur_layer">
                                <input type="button" class="btn btn-default {{ $key }}_btn col-xs-12" data-toggle="modal" data-target="#{{ $key }}Modal" value="Bekijk {{ $labels[$key] }}">
                            </div>
                        @endif
                    <!-- Als het dropdowns moeten zijn -->
                    @elseif (array_key_exists($key, $dropdowns))
                        <div class="col-xs-12 factuur_layer">
                            <label for="factuur_{{ $key }}" class="control-label col-xs-12 col-sm-5 col-md-3">{{ $labels[$key] }}:</label>
                            <div class="col-xs-12 col-sm-7 col-md-9">
                                <select class="form-control selectpicker" name="{{ $key }}" id="factuur_{{ $key }}">
                                    @foreach ($dropdowns[$key] as $value => $text)
                                        @if ($key === 'land_code')
                                            @if ($row === explode('_', $value)[0])
                                                <option value="{{ $value }}" selected="selected">{{ $text }}</option>
                                            @else
                                                <option value="{{ $value }}">{{ $text }}</option>
                                            @endif
                                        @else
                                            @if ($row === $value)
                                                <option value="{{ $value }}" selected="selected">{{ $text }}</option>
                                            @else
                                                <option value="{{ $value }}">{{ $text }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @else
                        @if ($key === 'verlegd_btw')
                            <div class="col-xs-12 factuur_layer">
                                <label for="factuur_{{ $key }}" class="control-label col-xs-12 col-sm-5 col-md-3">{{ $labels[$key] }}:</label>
                                <div class="col-xs-12 col-sm-7 col-md-9 input-group">
                                    <input type="text" class="form-control" name="{{ $key }}" id="factuur_{{ $key }}" placeholder="{{ $key }}" value="{{ $row }}">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-info-sign" id="verlegd_btw_popover" data-toggle="popover" data-placement="left"></span>
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="col-xs-12 factuur_layer">
                                <label for="factuur_{{ $key }}" class="control-label col-xs-12 col-sm-5 col-md-3">{{ $labels[$key] }}:</label>
                                <div class="col-xs-12 col-sm-7 col-md-9">
                                    <input type="text" class="form-control" name="{{ $key }}" id="factuur_{{ $key }}" placeholder="{{ $key }}" value="{{ $row }}">
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
                <input type="submit" class="btn btn-default save_factuur" value="Opslaan">
            </div>
        </form>
        <div class="form-group col-xs-12 col-sm-6">
            <div class="kostenposten-temp"></div>
            <div class="fackosten-error alert alert-danger">
                <ul></ul>
            </div>
            <div id="add-new-row" class="form-group">
                <label for="new-omschrijving" class="control-label col-xs-6">Omschrijving.:</label>
                <div class="col-xs-6">
                    <input type="text" class="form-control" id="new-omschrijving" placeholder="Omschrijving">
                </div>
                <label for="new-bedrag" class="control-label col-xs-6">Bedrag excl.:</label>
                <div class="col-xs-6">
                    <div class="input-group">
                        <span class="input-group-addon">&euro;</span>
                        <input type="text" class="form-control" id="new-bedrag" placeholder="Bedrag excl.">
                    </div>
                </div>
                <label for="new-btw-tarief" class="control-label col-xs-6">Btw tarief:</label>
                <div class="col-xs-6">
                    <select class="form-control selectpicker" id="new-btw-tarief" title="Btw tarief:">
                        <option value="vrij">Vrijgesteld</option>
                        <option value="0">0&#37;</option>
                        <option value="6">6&#37;</option>
                        <option value="21">21&#37;</option>
                    </select>
                </div>
                <label for="new-type" class="control-label col-xs-6">Type:</label>
                <div class="col-xs-6 input-group">
                    <select class="form-control selectpicker" id="new-type">
                        <option value="0">Geen type</option>
                        <option value="1">Product</option>
                        <option value="2">Dienst</option>
                    </select>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-info-sign" id="new-type-popover" data-toggle="popover" data-placement="left"></span>
                    </span>
                </div>
                <button class="btn btn-default" onclick="beforeAddRow()">Annuleren</button>
                <button class="btn btn-default" onclick="addRow()">Opslaan</button>
            </div>
            <button class="btn btn-default fackosten-add" onclick="beforeAddRow()">Voeg een rij toe</button>
        </div>
    </div>
@endsection
@section('extra_js')
    <script>
        // Globale variablen
        var klant = [];
        var adres = [];
        var inputs = [];
        const alert_color = '#ff0000';
//        const verlegd = $('#factuur_land_code').val().split('_')[1];

        function showRow() {
            $.ajax({
                url: '/fetch_fackosten',
                data: {
                    'factuur_id': $('#factuur_id').val(),
                    'klant_id': $('#klant_id').val()
                },
                complete: function(data) {
                    var rows = data.responseJSON;
                    var fackosten = '';
                    if (rows.length === 0) {
                        fackosten = '<h2 class="fackosten-head">Er zijn geen kostenposten toegevoegd bij deze factuur.</h2>';
                    } else {
                        fackosten = '<div class="table">' +
                            '<div class="row table_header">' +
                            '<div class="col-xs-2 text-left">ID</div>' +
                            '<div class="col-xs-4 text-left">Omschrijving</div>' +
                            '<div class="col-xs-3">Bedrag excl.</div>' +
                            '<div class="col-xs-2">Btw tarief</div>' +
                            '<div class="col-xs-1 text-right">Opties</div>' +
                            '</div>' +
                            '<div id="fackosten_layers">';

                        $.each(rows, function(key, row) {
                            fackosten += '<div class="row table_layer" id="row_' + row.kosten_id + '">' +
                                '<div class="table_col id col-xs-2 text-left">' + row.kosten_id + '</div>' +
                                '<div class="table_col omschrijving col-xs-4 text-left">' + row.omschrijving + '</div>' +
                                '<div class="table_col bedrag col-xs-3">&euro;' + row.bedrag + '</div>';
                            if (row.btw_tarief === 'vrij') {
                                fackosten += '<div class="table_col btw_tarief col-xs-2">Vrijgesteld</div>';
                            } else {
                                fackosten += '<div class="table_col btw_tarief col-xs-2">' + row.btw_tarief + '&#37;</div>';
                            }
                            fackosten += '<div class="table_col col-xs-1 text-right">' +
                                '<a onclick="fetchRow(' + row.kosten_id + ')" data-toggle="modal" data-target="#fackostenModal"><img src="{{ url('images/bewerk.png') }}" title="Bewerken"></a>' +
                                '<a onclick="removeRow(' + row.kosten_id + ')"><img src="{{ url('images/busy.png') }}" title="Verwijderen"></a>' +
                                '</div>' +
                                '</div>';


                        });
                        fackosten += '</div>' +
                            '</div>';
                    }
                    $('.kostenposten-temp').html(fackosten);
                }
            });
        }

        // Deze functie wordt geactiveerd als de pagina geladen is
        $(document).ready(function() {
            showRow();

            // Als er een succesbericht is, verdwijnt ie in 5 seconden
            $('#success-message').fadeOut(5000);

            // Verbergt het laad icoontje en de tekst 'Opgeslagen!' in de Modals
            $('#fackostenEdit').find('span').hide();
            $('#changeTypeEdit').find('span').hide();
            $('#fackostenSaved').text('Opgeslagen!').hide();
            $('#changeTypeSaved').text('Opgeslagen!').hide();

            // Zet alle waarden van alle inputs en dropdowns in deze variabelen
            $('#klantModal').find('input, select').each(function() {klant.push($(this).val())});
            $('#adresModal').find('input, select').each(function() {adres.push($(this).val())});
            $('.facturen-inputs').find('input, select').each(function() {inputs.push($(this).val())});

            // Zet de waarde van verlegd btw in deze variabele
            var verlegd_btw = $('#factuur_verlegd_btw').val();


            jQuery(function() {
                // Deze functie wordt geactiveerd als de Land dropdown wordt veranderd
                $('#factuur_land_code').on('change', function() {
                    // Als het geselecteerde land Nederland is
                    if ($('#factuur_land_code').val().split('_')[1] === '0') {
                        $('#factuur_verlegd_btw').val('*').attr('readonly', true);
                        $('#factuur_land_particulier').val(0).attr('disabled', true);
                        $('#fackosten_type').attr('disabled', true).find('[value=0]').show();
                        $('.selectpicker').selectpicker('refresh');
                    } else {
                        $('#factuur_verlegd_btw').val(verlegd_btw).attr('readonly', false);
                        $('#factuur_land_particulier').attr('disabled', false);
                        // Als Verlegd btw geen sterretje is, dan Land particulier nee
                        (verlegd_btw !== '*') ? $('#factuur_land_particulier').val(0) : $('#factuur_land_particulier').val(1);
                        $('#fackosten_type').attr('disabled', false).find('[value=0]').hide();
                        $('.selectpicker').selectpicker('refresh');
                    }
                }).change();
                // Deze functie wordt geactiveerd als de Verlegd btw input wordt veranderd
                $('#factuur_verlegd_btw').on('keyup', function() {
                    // Als een sterretje is ingevuld bij Verlegd btw
                    if($(this).val() === '*') {
                        $('#factuur_land_particulier').val(1);
                        $('.selectpicker').selectpicker('refresh');
                    } else {
                        $('#factuur_land_particulier').val(0);
                        $('.selectpicker').selectpicker('refresh');
                    }
                }).change();
                // Deze functie wordt geactiveerd als de Land particulier dropdown wordt veranderd
                $('#factuur_land_particulier').on('change', function() {
                    // Als het geselecteerde land niet Nederland is
                    if ($('#factuur_land_code').val().split('_')[1] !== '0') {
                        if ($('#factuur_land_particulier').val() === '0') {
                            // Als de waarde al een sterretje is, maak het veld Verlegd btw dan leeg
                            (verlegd_btw !== '*') ? $('#factuur_verlegd_btw').val(verlegd_btw) : $('#factuur_verlegd_btw').val('');
                        } else {
                            $('#factuur_verlegd_btw').val('*');
                        }
                    }
                }).change();
            });

            // Als het geselecteerde land Nederland is
            if ($('#factuur_land_code').val().split('_')[1] === '0') {
                $('#new-type').attr('disabled', true).find('[value=0]').show();
                $('.fackosten-type').each(function() {
                    // Als een kostenpost geen Type 0 heeft
                    if($(this).val() !== '0') {
                        $('#changeTypeModal').modal('show');
                        $('.type-not-zero').hide();
                        $('.type-zero').show();
                        return false;
                    }
                });
            } else {
                $('#new-type').attr({'disabled': false, 'title': 'Kies voor:'}).find('[value=0]').hide();
                $('.fackosten-type').each(function() {
                    // Als een kostenpost Type 0 heeft
                    if($(this).val() === '0') {
                        $('#changeTypeModal').modal('show');
                        $('.type-not-zero').show();
                        $('.type-zero').hide();
                        return false;
                    }
                });
            }
        });

        // Deze functie wordt geactiveerd als er iets wordt aangepast in de pagina
        $('.facturen-inputs, #klantModal, #adresModal').on('change', function() {
            var i = 0;
            var a = 0;
            var btn = '';
            var info = '';

            // Zet het juiste DOM object in de variabelen
            switch(true) {
                case $(this).is($('#klantModal')):
                    info = klant;
                    btn = $('.klant_btn');
                    break;
                case $(this).is($('#adresModal')):
                    info = adres;
                    btn = $('.adres_btn');
                    break;
                case $(this).is($('.facturen-inputs')):
                    info = inputs;
                    break;
            }

            // Loopt door de inputs en dropdowns om te kijken of er verandering zijn opgetreden
            $(this).find('input, select').each(function() {
                var label_color = (info[i] !== $(this).val() ? alert_color : '');
                $(this).parents('.factuur_layer').find('label').css('color', label_color);
                i++;
            });

            // Veranderd de kleur van de knoppen als er verandering zijn opgetreden
            if (btn !== '') {
                $(this).find('input, select').each(function() {
                    var btn_color = (info[a] !== $(this).val() ? alert_color : '');
                    btn.css('color', btn_color);
                    a++;
                    if (btn_color === alert_color) return false;
                });
            }
        });

        // Deze functie laat de juiste kostenpost zien in de Modal
        function fetchRow(row_id) {
            $('#fackostenModal').find('.loading').show().end().find('.modal-form').hide();
            $('#fackostenEdit').addClass('disabled');
            $('#fackostenModalTitle').find('span').text('');
            $.ajax({
                url: '/fetch_fackosten',
                data: {
                    'kosten_id': row_id,
                    'klant_id': $('#klant_id').val()
                },
                success: function(data) {
                    var row = data[0];
                    $('#fackostenModalTitle').find('span').text(row.kosten_id);
                    $('#fackosten_id').val(row.kosten_id);
                    $('#fackosten_omschrijving').val(row.omschrijving);
                    $('#fackosten_btw_bedrag').val(row.btw_bedrag);
                    $('#fackosten_btw_tarief').val(row.btw_tarief);
                    $('#fackosten_bedrag').val(row.bedrag);
                    $('#fackosten_type').val(row.type);
                    $('.selectpicker').selectpicker('refresh');
                },
                complete: function() {
                    $('#fackostenModal').find('.loading').hide().end().find('.modal-form').show();
                    $('#fackostenEdit').removeClass('disabled');
                }
            });
        }

        function beforeAddRow() {
            // Als Nederland in de Database staat
            $('.selectpicker').selectpicker('refresh');
            $('#add-new-row').toggle();
            $('.fackosten-add').toggle();
            $('.fackosten-error').css('display', 'none');
        }

        // Deze functie voegt een kostenpost toe in de database
        function addRow() {
            var bedrag = ($('#new-bedrag').val() !== '') ? parseFloat(komma_naar_punt($('#new-bedrag').val())).toFixed(2) : '';
            $('#loading_screen').css('display', 'block');
            $.ajax({
                url: '/add_fackosten',
                data: {
                    'factuur_id': $('#factuur_id').val(),
                    'klant_id': $('#klant_id').val(),
                    'bedrag': bedrag,
                    'btw_tarief': $('#new-btw-tarief').val(),
                    'omschrijving': $('#new-omschrijving').val(),
                    'type': $('#new-type').val()
                },
                error: function(jqXhr) {
                    var errors = jqXhr.responseJSON;
                    $('.fackosten-error')
                        .css('display', 'block').find('ul').empty().end()
                        .removeClass('alert-success').addClass('alert-danger');
                    $.each(errors, function(key, value) {
                        $('.fackosten-error').find('ul').append('<li>' + value[0] + '</li>');
                    });

                },
                success: function() {
                    $('#add-new-row').find('input').val('').end()
                        .find('.selectpicker').selectpicker('val', '');
                    $('#add-new-row').toggle();
                    $('.fackosten-add').toggle();
                    $('.fackosten-error').css('display', 'block')
                        .find('ul').empty().append('<li>Opgeslagen!</li>')
                        .end().removeClass('alert-danger')
                        .addClass('alert-success').fadeOut(2500);
                    showRow();
                },
                complete: function() {
                    $('#loading_screen').css('display', 'none');
                }
            });
        }

        // De functie wordt geactiveerd als er op Bewerken wordt geklikt in de Kostenpost Modal
        $('#fackostenEdit').on('click', function() {
            $('#fackostenEdit').find('span').show();
            $('#fackostenSaved').text('Opgeslagen!').hide();
            var fackosten_id = $('#fackosten_id').val();
            $.ajax({
                url: '/update_fackosten',
                data: {
                    'klant_id': $('#klant_id').val(),
                    'kosten_id': fackosten_id,
                    'omschrijving': $('#fackosten_omschrijving').val(),
                    'btw_bedrag': $('#fackosten_btw_bedrag').val(),
                    'btw_tarief': $('#fackosten_btw_tarief').val(),
                    'bedrag': $('#fackosten_bedrag').val(),
                    'type': $('#fackosten_type').val()
                },
                complete: function() {
                    showRow();
                    $('#fackostenEdit').find('span').hide();
                    $('#fackostenSaved').text('Opgeslagen!').show().fadeOut(2000);
                }
            });
        });

        // Deze functie verwijdert een kostenpost uit de database
        function removeRow(row_id) {
            if (confirm('Weet je zeker dat je rij ' + row_id + ' wilt verwijderen?') === true) {
                $('#loading_screen').css('display', 'block');
                $.ajax({
                    url: '/remove_fackosten',
                    data: {
                        'kosten_id': row_id,
                        'klant_id': $('#klant_id').val()
                    },
                    complete: function() {
                        showRow();
                        $('#loading_screen').css('display', 'none');
                    }
                });
            }
        }

        // De functie wordt geactiveerd als er op Bewerken wordt geklikt in de Type veranderen Modal
        $('#changeTypeEdit').on('click', function() {
            var fackosten_id = [];
            var fackosten_type = [];
            var modal_body = ($('.type-not-zero').is(':visible') && $('.type-zero').is(':hidden')) ? $('.type-not-zero') : $('.type-zero');
            $('#changeTypeEdit').find('span').show();
            $('#changeTypeSaved').text('Opgeslagen!').hide();

            // Zet alle dropdown waarden in een array
            for (var a = 0; a < modal_body.find('.fackosten-count').val(); a++) {
                fackosten_id.push(modal_body.find('.fackosten-id').eq(a).val());
                fackosten_type.push(modal_body.find('select.changeType').eq(a).val());
            }

            // Checkt of alles is geselecteerd
            if (jQuery.inArray('', fackosten_type) !== -1) {
                $('#changeTypeSaved').text('Selecteer voor elke dropdown Product of Dienst!').show().fadeOut(5000);
                $('#changeTypeEdit').find('span').hide();
                return false;
            }

            $.ajax({
                url: '/update_fackosten',
                data: {
                    'request': 'type',
                    'count': modal_body.find('.fackosten-count').val(),
                    'klant_id': $('#klant_id').val(),
                    'kosten_id': fackosten_id,
                    'type': fackosten_type
                },
                error: function(data) {
                    $('#changeTypeEdit').find('span').hide();
                    $('#changeTypeSaved').text('Er is een fout opgetreden! Bekijk de Console voor de foutmelding.').show().fadeOut(5000);
                    console.log(data);
                },
                success: function() {
                    $('#changeTypeEdit').find('span').hide();
                    $('#changeTypeSaved').text('Opgeslagen!').show().fadeOut(500);
                    // Verbergt de Modal na 2 seconden
                    setTimeout(function() {
                        // Als de modal zichtbaar is, verberg hem
                        if ($('#changeTypeModal').is(':visible')) $('#changeTypeModal').modal('toggle');
                    },500);
                }
            });
        });

        // Veranderd de Btw Incl. input in de Kostenpost Modal zodra de Btw excl. input of de Btw-tarief dropdown wordt aangepast
        function btwCheck(where) {
            var bedrag = $('#fackosten_bedrag').val();
            var btw_bedrag = $('#fackosten_btw_bedrag');
            var btw_tarief = $('#fackosten_btw_tarief').val();
            btw_tarief === 'vrij' ? btw_tarief = 0 : btw_tarief;

            // Checkt of de dropdown of inputveld veranderd is
            if (where === 'select') {
                btw_bedrag.val(punt_naar_komma((komma_naar_punt(bedrag) * (btw_tarief / 100 + 1)).toFixed(2)));
            } else if (where === 'input') {
                btw_bedrag.val(punt_naar_komma((komma_naar_punt(bedrag) / 100 * (100 + parseInt(btw_tarief))).toFixed(2)));
            } else {
                btw_bedrag.val('');
            }
        }

        // Popover
        $('#verlegd_btw_popover').popover({
            container: "body",
            trigger: "hover",
            content: "<p>Als je een factuur stuurt naar een land dat niet Nederland is en het land waar je het naar stuurt is geen particulier, dan vul je hier het Btw-nummer in van de gefactureerde.<br>Zo niet, dan vul je hier gewoon een sterretje in.</p>",
            html: true
        });

        $('#new-type-popover').popover({
            container: "body",
            trigger: "hover",
            content: "<p>Als je een factuur stuurt naar een land dat niet Nederland is, dan moet het type een Product of Dienst zijn. Als het wel naar Nederland gaat, dan wordt het type standaard Geen type en dat kun je dan alleen aanpassen als je het land aanpast naar een ander land.</p>",
            html: true
        });
    </script>
@endsection