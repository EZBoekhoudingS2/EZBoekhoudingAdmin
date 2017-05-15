<form id="temp-form" action="../temp/update" method="post">
    <h2 style="text-align: center">{{ $start_count }} t/m {{ $test_count }}</h2>
    <input type="hidden" name="start_count" value="{{ $start_count }}">
    <input type="hidden" name="test_count" value="{{ $test_count }}">
    {{ csrf_field() }}
    @for ($a = $start_count; $a < $test_count; $a++)
        {{--@if (preg_match('/[^a-zA-Z0-9áéíóúÁÉÍÓÚäëïöüÄËÏÖÜàèìòùÀÈÌÒÙ(!?\^@<>\s.,*\-\'\'\\\\\/&|)]/', $db[$a]->klant)--}}
          {{--|| preg_match('/[^a-zA-Z0-9áéíóúÁÉÍÓÚäëïöüÄËÏÖÜàèìòùÀÈÌÒÙ(!?\^@<>\s.,*\-\'\'\\\\\/&|)]/', $db[$a]->adres))--}}
            {{--<input type="hidden" name="id_{{ $a }}" value="{{ $db[$a]->factuur_id }}">--}}
            {{--<label>watch:<b>{{ $db[$a]->factuur_id }}</b></label>--}}
            {{--@if (preg_match('/[^a-zA-Z0-9áéíóúÁÉÍÓÚäëïöüÄËÏÖÜàèìòùÀÈÌÒÙ(!?\^@<>\s.,*\-\'\'\\\\\/&|)]/', $db[$a]->klant))--}}
                {{-->><input name="klant_{{ $a }}" value="{{ $db[$a]->klant }}" style="width:1150px">--}}
                {{--<input name="adres_{{ $a }}" value="{{ $db[$a]->adres }}" style="width:1220px">--}}
            {{--@elseif (preg_match('/[^a-zA-Z0-9áéíóúÁÉÍÓÚäëïöüÄËÏÖÜàèìòùÀÈÌÒÙ(!?\^@<>\s.,*\-\'\'\\\\\/&|)]/', $db[$a]->adres))--}}
                {{--<input name="klant_{{ $a }}" value="{{ $db[$a]->klant }}" style="width:1150px">--}}
                {{-->><input name="adres_{{ $a }}" value="{{ $db[$a]->adres }}" style="width:1220px">--}}
            {{--@else--}}
                {{-->><input name="klant_{{ $a }}" value="{{ $db[$a]->klant }}" style="width:1150px">--}}
                {{-->><input name="adres_{{ $a }}" value="{{ $db[$a]->adres }}" style="width:1150px">--}}
            {{--@endif--}}
        {{--@else--}}
            {{--<input type="hidden" name="id_{{ $a }}" value="{{ $db[$a]->factuur_id }}">--}}
            {{--<label><b>{{ $db[$a]->factuur_id }}</b></label>--}}
            {{--<input name="klant_{{ $a }}" value="{{ $db[$a]->klant }}" style="width:600px">--}}
            {{--<input name="adres_{{ $a }}" value="{{ $db[$a]->adres }}" style="width:600px">--}}
        {{--@endif--}}

        @if (preg_match('/[^a-zA-Z0-9áéíóúÁÉÍÓÚäëïöüÄËÏÖÜàèìòùÀÈÌÒÙ(!\?\|%\:\€%=\^@\<\>&\s\.,\-\*\+\(\)"\'\\\\\/)]/', $db[$a]->omschrijving))
            <input type="hidden" name="id_{{ $a }}" value="{{ $db[$a]->kosten_id }}">
            <label style="color: red">watch:<b>{{ $db[$a]->kosten_id }}</b></label>
            <input name="omschrijving_{{ $a }}" value="{{ $db[$a]->omschrijving }}" style="width:325px">
        @else
            <input type="hidden" name="id_{{ $a }}" value="{{ $db[$a]->kosten_id }}">
            <label><b>{{ $db[$a]->kosten_id }}</b></label>
            <input id="omschrijving_{{ $a }}" name="omschrijving_{{ $a }}" value="{{ $db[$a]->omschrijving }}" style="width:368px">
        @endif
    @endfor
    <input type="submit" value="Updaten">
</form>
{{ $count }} changes detected!

