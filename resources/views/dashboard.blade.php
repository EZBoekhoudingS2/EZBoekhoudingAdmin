@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div id="dashboard" class="row">
        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="user_subs">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="user_subs">Gebruikerssoorten</h4>
                    </div>
                    <div class="modal-body">
                        @foreach ($subs as $subs_type)
                            @foreach($subs_type as $sub_name)
                                @if (!empty($sub_name['users']))
                                    <div class="abbo-box col-xs-6">
                                        <table class="col-xs-12">
                                            <tr>
                                                <td class="abbo-header" colspan="2">{{ $sub_name['title'] }}</td>
                                            </tr>
                                            @foreach ($sub_name['users'] as $sub)
                                                <tr>
                                                    <td class="user-id">{{ $sub['id'] }}</td>
                                                    <td class="user-email">
                                                        <a href="{{ url('/user/' . $sub['id']) }}">{{ $sub['email'] }}</a> ({{ $sub['actief'] }})
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-div col-xs-12 col-sm-6 col-md-4">
            <div class="dashboard-user-types">
                <h3 class="dashboard-header">Gebruikerssoorten</h3>
                <canvas id="userTypes" width="200" height="100"></canvas>
            </div>
        </div>
        <div class="dashboard-div col-xs-12 col-sm-6 col-md-4">
            <div class="dashboard-sub-count">
                <h3 class="dashboard-header">Abonnementen</h3>
                <canvas id="userCount" width="200" height="100"></canvas>
            </div>
        </div>
        <div class="dashboard-div col-xs-12 col-sm-6 col-md-4">
            <div class="dashboard-user-count">
                <h3 class="dashboard-header">Abonnement aantallen</h3>
                @foreach ($subs as $subs_type)
                    @foreach ($subs_type as $sub)
                        <div class="user-count sub-count row">
                            <div class="col-xs-10">{{ $sub['title'] }}:</div>
                            <div class="sub user-count-num col-xs-2" data-toggle="modal" data-target=".modal">
                                <input class="active-count" type="hidden" value="{{ $sub[1] }}">
                                <input class="non-active-count" type="hidden" value="{{ $sub[2] }}">
                                <a class="user-count-active" data-toggle="popover" data-placement="left">{{ $sub[0] }}</a>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
        <div class="dashboard-div col-xs-12 col-sm-6 col-md-4">
            <div class="dashboard-users">
                <h3 class="dashboard-header">Alle gebruikers</h3>
                <div class="form-group">
                    <select class="selectpicker" id="user-dropdown" name="user" title="Selecteer een gebruiker..." data-width="100%">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" name="{{ $user->id }}">{{ '(' . $user->id . ') ' . $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                <a id="btn-id">
                    <button class="btn btn-default" type="button">Bekijk</button>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('extra_js')
    <script>
        // Verwijst je naar de juiste pagina
        $('#btn-id').on('click', function() {
            var id = $('#user-dropdown').val();
            if(id !== '') {
                $(this).attr('href', '/user/' + id);
            }
        });

        // Laat zien hoeveel actieve en non-actieve gebruikers er zijn in het vakje Gebruikerssoorten in een popover
        $('.user-count-active').each(function(){
            $(this).popover( {
                container: "body",
                trigger: "hover",
                title: "Gebruikers:",
                content: "Actief: " + $(this).parent().find('.active-count').val() + " Non-actief: " + $(this).parent().find('.non-active-count').val(),
                html: true
            });
        });

        var userTypesLabels = [];
        var userTypesValues = [];
        @foreach($user_count as $key => $value)
            userTypesLabels.push('{{ $key }}');
            userTypesValues.push('{{ $value }}');
        @endforeach

        var subCountLabels = [];
        var subCountValues = [];
        @foreach ($subs as $subs_type)
            @foreach ($subs_type as $sub)
                subCountLabels.push('{{ $sub['title'] }}');
                subCountValues.push('{{ $sub[0] }}');
            @endforeach
        @endforeach

        var userTypes = new Chart($('#userTypes'), {
            type: 'pie',
            data: {
                labels: userTypesLabels,
                datasets: [{
                    backgroundColor: [
                        '#00008b',
                        '#f1c40f',
                        '#e67e22',
                        '#16a085',
                        '#2980b9',
                        '#ff2828',
                        '#ff1493',
                        '#a0522d'
                    ],
                    data: userTypesValues
                }]
            },
            options: {
                cutoutPercentage: 60,
                animation: {
                    animateScale: true
//                },
//                legend: {
//                    display: false
                }
            }
        });


        var userCount = new Chart($('#userCount'), {
            type: 'pie',
            data: {
                labels: subCountLabels,
                datasets: [{
                    backgroundColor: [
                        '#00008b',
                        '#f1c40f',
                        '#e67e22',
                        '#16a085',
                        '#2980b9',
                        '#ff2828',
                        '#ff1493',
                        '#a0522d'
                    ],
                    data: subCountValues
                }]
            },
            options: {
                cutoutPercentage: 60,
                animation: {
                    animateScale: true
//                },
//                legend: {
//                    display: false
                }
            }
        });
    </script>
@endsection