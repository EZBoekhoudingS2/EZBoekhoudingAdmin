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
                        @foreach ($subs as $subType)
                            @foreach($subType as $subName)
                                @if (!empty($subName['users']))
                                    <div class="abbo-box col-xs-6">
                                        <table class="col-xs-12">
                                            <tr>
                                                <td class="abbo-header" colspan="2">{{ $subName['title'] }}</td>
                                            </tr>
                                            @foreach ($subName['users'] as $sub)
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
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-panel">
                <div class="panel-header">Gebruikerssoorten</div>
                <div class="panel-content">
                    <canvas id="userTypes" height="88"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-panel">
                <div class="panel-header">Abonnement aantallen</div>
                <div class="panel-content">
                    @foreach ($subs as $subs_type)
                        @foreach ($subs_type as $sub)
                            <div class="user-count sub-count row">
                                <div class="col-xs-10">{{ $sub['title'] }}:</div>
                                <div class="sub user-count-num col-xs-2" data-toggle="modal" data-target=".modal">
                                    <input class="active-count" type="hidden" value="{{ $sub['active'] }}">
                                    <input class="non-active-count" type="hidden" value="{{ $sub['non_active'] }}">
                                    <a class="user-count-active" data-toggle="popover" data-placement="left">{{ $sub['sub_count'] }}</a>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-4">
            <div class="dashboard-panel">
                <div class="panel-header">Totaal aantal gebruikers</div>
                <div class="panel-content">
                    <div class="panel-number">{{ count($users) }}</div>
                    <div class="text-center">gebruikers</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-4">
            <div class="dashboard-panel">
                <div class="panel-header">Actieve gebruikers</div>
                <div class="panel-content">
                    <div class="panel-number">{{ $user_count['Actieve'] }}</div>
                    <div class="text-center">gebruikers</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-4">
            <div class="dashboard-panel">
                <div class="panel-header">Niet-actieve gebruikers</div>
                <div class="panel-content">
                    <div class="panel-number">{{ count($users) - $user_count['Actieve'] }}</div>
                    <div class="text-center">gebruikers</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="dashboard-panel">
                <div class="panel-header">Abonnementen</div>
                <div class="panel-content">
                    <canvas id="userCount" height="75"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra_js')
    <script>
//        $(window).resize(function() {
//            console.log($(this).width());
//            if ($(this).width() < 960) {
//                $("#userCount").removeAttr('height');
//                userTypes.update();
//            } else {
//                $("#userCount").attr('height', '75');
//                userTypes.update();
//            }
//        });

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

        var subCountLabels = [];
        var subCountValues = [];
        @foreach ($subs as $subs_type)
            @foreach ($subs_type as $sub)
                subCountLabels.push('{{ $sub['title'] }}');
                subCountValues.push('{{ $sub['sub_count'] }}');
            @endforeach
        @endforeach

        var userTypesLabels = [];
        var userTypesValues = [];
        @foreach($user_count as $key => $value)
            userTypesLabels.push('{{ $key }}');
            userTypesValues.push('{{ $value }}');
        @endforeach

        var userCount = new Chart($('#userCount'), {
            type: 'bar',
            data: {
                labels: subCountLabels,
                datasets: [{
                    backgroundColor: [
                        '#f06292',
                        '#ffb74d',
                        '#fff176',
                        '#aed581',
                        '#7986cb',
                        '#ba68c8',
                        '#a1887f'
                    ],
                    data: subCountValues
                }]
            },
            options: {
                animation: {
                    animateScale: true
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            userCallback: function(label, index, labels) {
                                if (Math.floor(label) === label) {
                                    return label;
                                }
                            }
                        }
                    }]
                }
            }
        });

        var userTypes = new Chart($('#userTypes'), {
            type: 'pie',
            data: {
                labels: userTypesLabels,
                datasets: [{
                    backgroundColor: [
                        '#f06292',
                        '#ffb74d',
                        '#fff176',
                        '#aed581',
                        '#7986cb',
                        '#ba68c8',
                        '#a1887f'
                    ],
                    data: userTypesValues
                }]
            },
            options: {
                animation: {
                    animateScale: true,
                    duration: 0
                }
            }
        });
    </script>
@endsection