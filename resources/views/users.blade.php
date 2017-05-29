@extends('layouts.app')
@section('title', 'Gebruikers')

@section('content')
    <adresboek></adresboek>
@endsection
@section('extra_js')
    <script>
        (function($) {
            $.fn.liveSearch = function(list, exclude) {
                var input = $(this),
                    regexp = {
                        provider: '/provider:([a-zA-Z0-9\.\-\_]+)/i',
                        email: '/\@([a-zA-z0-9\-\_\.]+)/i'
                    },
                    elements = list.children().not(exclude),
                    filter = function() {
                        var term = input.val().toLowerCase();
                        elements.show().filter(function() {
                            var text = $(this).text().toLowerCase(),
                                open = term.replace(regexp.provider, '').trim(),
                                found = term.match(regexp.provider);
                            if (found) {
                                if (text.indexOf('@' + found[1]) != -1 && !open) {
                                    return false;
                                } else {
                                    if (open && text.indexOf('@' + found[1]) != -1) return text.replace(regexp.email, '').toLowerCase().indexOf(open) == -1;
                                }
                            }
                            return text.replace(regexp.email, '').toLowerCase().indexOf(term) == -1;
                        }).hide();
                    };
                input.on('keyup select', filter);
                return this;
            };
            $('#search').liveSearch($('.list'));
        })(jQuery);
    </script>
@endsection
