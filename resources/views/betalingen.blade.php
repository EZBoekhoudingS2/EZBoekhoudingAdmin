@extends('layouts.app')
@section('title', 'iDeal Betalingen')
@section('content')
    <div id="betalingen">
        {{--<div class="row">--}}
            {{--<div class="form-group">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-xs-12 col-sm-9 col-md-6 col-lg-5">--}}
                        {{--<div class="col-xs-5" style="min-width: 140px">--}}
                            {{--<select class="selectpicker form-control" v-model="kwartaal">--}}
                                {{--@for ($kwartaal = 1; $kwartaal < 5; $kwartaal++)--}}
                                    {{--<option value="{{ $kwartaal }}">Kwartaal {{ $kwartaal }}</option>--}}
                                {{--@endfor--}}
                            {{--</select>--}}
                        {{--</div>--}}
                        {{--<div class="row col-xs-3" style="min-width: 100px">--}}
                            {{--<select class="selectpicker form-control" v-model="jaartal">--}}
                                {{--@for ($jaar = date('Y'); $jaar > date('Y') - 8; $jaar--)--}}
                                    {{--<option value="{{ $jaar }}">{{ $jaar }}</option>--}}
                                {{--@endfor--}}
                            {{--</select>--}}
                        {{--</div>--}}
                        {{--<div class="col-xs-4">--}}
                            {{--<input class="btn btn-primary form-control" value="Bekijken" type="button" @click="fetchBetalingen">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="table">
            <div class="row table_header">
                <div class="col-xs-1 text-left">Id</div>
                <div class="col-xs-1 text-left">Datum</div>
                <div class="col-xs-4">Gebruiker</div>
                <div class="col-xs-2">Titel</div>
                <div class="col-xs-2">Aanbieding</div>
                <div class="col-xs-1">Bedrag incl.</div>
                <div class="col-xs-1 text-right">Opties</div>
            </div>
            <div id="betalingen_layers">
                @foreach($betalingen as $betaling)
                    <div id="betaling_{{ $betaling->id }}" class="row table_layer">
                        <div class="table_col col-xs-1 text-left">{{ $betaling->id }}</div>
                        <div class="table_col col-xs-1 text-left">{{ $betaling->datum }}</div>
                        <div class="table_col col-xs-4">{{ $betaling->bedrijfsnaam }} (<a href="{{ url('/user/' . $betaling->user_id) }}" target="_blank">{{ $betaling->email }}</a>)</div>
                        <div class="table_col col-xs-2">{{ $betaling->titel }}</div>
                        <div class="table_col col-xs-2">{!! $betaling->aanb !!}</div>
                        <div class="table_col col-xs-1">&euro; {{ $betaling->bedrag_in }}</div>
                        <div class="table_col col-xs-1 text-right">
                            <a><img src="{{ url('images/bewerk.png') }}" title="Bewerken"></a>
                            <a><img src="{{ url('images/busy.png') }}" title="Verwijderen"></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('extra_js')
    <script>

    </script>
@endsection