<template>
    <div id="users">
        <div id="adresboek" class="col-xs-12 col-md-5 col-lg-4">
            <div class="element">
                <div class="element-header">
                    <div class="inner-addon right-addon">
                        <span class="glyphicon glyphicon-search"></span>
                        <input id="search" type="text" name="s" value="" placeholder="Zoeken..." autocomplete="off" v-on:keydown="searchEngine()">
                        <h2>Alle gebruikers</h2>
                    </div>
                </div>
                <div class="element-content">
                    <ul class="list">
                        <li v-for="user in users">
                            <span v-bind:id="user.valueForId" class="user_info" v-on:click="showUser(user.id)">
                                <span class="user_id" style="display: none">{{ user.id }}</span>
                                <span class="user_name">{{ user.vnaam }} {{ user.anaam }}</span>
                                <span class="user_email">{{ user.email }}</span>
                            </span>
                            <span class="user_sub">{{ user.subscription }}</span>
                            <span class="user_active"></span>
                        </li>
                    </ul>
                    <div class="element-sidebar">
                        <a v-on:click.stop.prevent="letterNavigation(letter)" v-bind:href="'#index-' + letter" v-for="letter in letters">{{ letter }}</a>
                    </div>
                </div>
            </div>
            <div id="sublegend" class="col-xs-12">
                <div class="legend-head col-xs-12">Selecteer hier welke gebruikers je wilt zien:</div>
                <label class="col-xs-6" for="show_all"><input type="checkbox" id="show_all" value="show_all" v-on:click="showSub('show_all')">Alle gebruikers</label>
                <label class="col-xs-6" v-for="sub in subs" v-bind:for="sub.name"><input type="checkbox" v-bind:id="sub.name" v-bind:value="sub.name" v-on:click="showSub(sub.name)">{{ sub.title }}</label>
                <label class="col-xs-6" for="active"><input type="checkbox" id="active" value="active" v-on:click="showSub('active')">Actieve gebruikers</label>
                <label class="col-xs-6" for="non_active"><input type="checkbox" id="non_active" value="non_active" v-on:click="showSub('non_active')">Niet actieve gebruikers</label>
            </div>
        </div>
        <div id="card" class="col-xs-12 col-md-7 col-lg-6">
            <input type="hidden" id="user_id" name="id" v-bind:value="id">
            <span class="card-head visible-xs">Gebruikersgegevens</span>
            <span class="col-sm-4 glyphicon glyphicon-user hidden-xs"></span>
            <p class="loading-text col-xs-12 col-sm-8">Aan het laden...</p>
            <div id="card-text" class="col-xs-12 col-sm-8">
                <div class="row">
                    <ul class="col-xs-5 hidden-xs">
                        <li><label>Id:</label></li>
                        <li><span class="id">{{ id }}</span></li>
                        <li><label>Voornaam:</label></li>
                        <li><span class="vnaam">{{ vnaam }}</span></li>
                        <li><label>Achternaam:</label></li>
                        <li><span class="anaam">{{ anaam }}</span></li>
                        <li><label>Adres:</label></li>
                        <li><span class="straat">{{ straat }}</span></li>
                    </ul>
                    <ul class="col-xs-7 hidden-xs">
                        <li><label>Postcode:</label></li>
                        <li><span class="postcode">{{ postcode }}</span></li>
                        <li><label>Plaats:</label></li>
                        <li><span class="plaats">{{ plaats }}</span></li>
                        <li><label>Emailadres:</label></li>
                        <li><span class="email"><a v-bind:href="'mailto:' + email">{{ email }}</a></span></li>
                        <li><label>Telefoonnummer:</label></li>
                        <li><span class="telefoon"><a v-bind:href="'tel:' + telefoon">{{ telefoon }}</a></span></li>
                    </ul>
                    <div class="small-info visible-xs">
                        <div class="row">
                            <p class="id">
                                <label>Id:</label><span>{{ id }}</span>
                            </p>
                            <p class="vnaam">
                                <label>Voornaam:</label><span>{{ vnaam }}</span>
                            </p>
                            <p class="anaam">
                                <label>Achternaam:</label><span>{{ anaam }}</span>
                            </p>
                            <p class="straat">
                                <label>Adres:</label><span>{{ straat }}</span>
                            </p>
                            <p class="postcode">
                                <label>Postcode:</label><span>{{ postcode }}</span>
                            </p>
                            <p class="plaats">
                                <label>Plaats:</label><span>{{ plaats }}</span>
                            </p>
                            <p class="email">
                                <label>Emailadres:</label><span><a v-bind:href="'mailto:' + email">{{ email }}</a></span>
                            </p>
                            <p class="telefoon">
                                <label>Telefoonnummer:</label><span><a v-bind:href="'tel:' + telefoon">{{ telefoon }}</a></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <a v-bind:href="'/user/' + id"><button class="btn btn-default edit-user">Bewerk deze gebruiker</button></a>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                users:      [],
                subs:       [],
                letters:    [
                    '#', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
                    'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',
                    'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
                ],
                id:         '',
                vnaam:      '',
                anaam:      '',
                straat:     '',
                postcode:   '',
                plaats:     '',
                email:      '',
                telefoon:   '',
            };
        },

        mounted() {
            $('#show_all').prop('checked', true).parent().css({'text-decoration': 'underline', 'font-weight' : 'bold'});
            this.fetchUsers();
            this.$http.get().then(response => {
                $('.user_id').each(function(key, id) {
                    $.each(this.users, function(key, user) {
                        let color = (user.isActiveUser) ? '#0d0' : '#f00';
                        let title = (user.isActiveUser) ? 'Actief' : 'Niet actief';
                        if (user.id === parseInt($(id).text())) {
                            $(id).parent().siblings('.user_active').css('color', color).attr('title', title);
                            return false;
                        }
                    }.bind(this));
                }.bind(this));
                if (this.getScrollbarWidth() === 0) {
                    $('.list').find('li').each(function (key, userField) {
//                        alert($(userField).width());
                        let newWidth = $(userField).width() - 2.5;
                        $('.list').find('li').css('width', newWidth + 'px');
                    });
                }
            });
        },

        methods: {
            getScrollbarWidth: function() {
                let outer = document.createElement("div");
                outer.style.visibility = "hidden";
                outer.style.width = "100px";
                document.body.appendChild(outer);

                let widthNoScroll = outer.offsetWidth;
                // force scrollbars
                outer.style.overflow = "scroll";

                // add innerdiv
                let inner = document.createElement("div");
                inner.style.width = "100%";
                outer.appendChild(inner);

                let widthWithScroll = inner.offsetWidth;

                // remove divs
                outer.parentNode.removeChild(outer);

                return widthNoScroll - widthWithScroll;
            },

            isChecked: function() {
                $('#sublegend').find('label').each(function() {
                    const decoration = ($(this).children().is(':checked')) ? 'underline' : 'none';
                    const fontWeight = ($(this).children().is(':checked')) ? 'bold' : 'normal';
                    $(this).css({
                        'text-decoration': decoration,
                        'font-weight': fontWeight
                    });
                });
            },

            fetchUsers: function() {
                $.getJSON('users/fetch_all', function(users) {
                    this.users = users;
                    this.setIndex();
                }.bind(this));
            },

            setIndex: function() {
                let letter = '';
                let firstLetter = '';
                $.each(this.users, function (key, user) {
                    firstLetter = (!(user.vnaam).charAt(0).match(/^[a-z]+$/i)) ? '#' : (user.vnaam).charAt(0).toUpperCase();
                    if (firstLetter !== letter) {
                        user.valueForId = 'index-' + firstLetter;
                        letter = firstLetter;
                    }
                });
                this.fetchSubs();
            },

            showUser: function(user_id) {
                const userFields = $('.user_info');
                userFields.removeClass('selected');
                $('#card-text, .edit-user').hide();
                $('#card, .loading-text').show();
                $.getJSON('users/fetch_user', {id: user_id}, function(data) {
                    const user = data[0];
                    this.id         = user.id;
                    this.vnaam      = user.vnaam;
                    this.anaam      = user.anaam;
                    this.straat     = user.straat;
                    this.postcode   = user.postcode;
                    this.plaats     = user.plaats;
                    this.email      = user.email;
                    this.telefoon   = user.telefoon;
                    userFields.each(function(key, userField) {
                        if ($(userField).find('.user_id').text() === String(user_id)) {
                            $(userField).addClass('selected');
                            return false;
                        }
                    });
                    $('.loading-text').hide();
                    $('#card-text, .edit-user').show();
                }.bind(this));
            },

            fetchSubs: function() {
                $.getJSON('users/subs', function(subs) {
                    $.each(subs, function (key, subTypes) {
                        $.each(subTypes, function (key, sub) {
                            this.subs.push(sub);
                        }.bind(this));
                    }.bind(this));
                }.bind(this));
            },

            showSub: function(clicked) {
                this.isChecked();
                const active            = (clicked === 'active'); // Boolean
                const adresboek         = $('#adresboek');
                const checkboxes        = adresboek.find(':checkbox');
                const clickedObject     = adresboek.find('#' + clicked);
                adresboek.find('li').hide();
                switch(clicked) {
                    case 'show_all':
                        if (clickedObject.is(':checked')) {
                            checkboxes.prop('checked', false);
                            clickedObject.prop('checked', true);
                            this.isChecked();
                            adresboek.find('li').show();
                        }
                        break;
                    case 'active':
                    case 'non_active':
                        if (clickedObject.is(':checked')) {
                            checkboxes.prop('checked', false);
                            clickedObject.prop('checked', true);
                            this.isChecked();
                            $.each(this.subs, function (key, sub) {
                                $.each(sub.users, function (key, user) {
                                    adresboek.find('.user_info').each(function (key, userField) {
                                        if (user.actief === active && $(userField).find('.user_id').text() === String(user.id)) {
                                            $(userField).parent().show();
                                            return false;
                                        }
                                    });
                                }.bind(this));
                            }.bind(this));
                        }
                        break;
                    default:
                        $('#show_all, #active, #non_active').prop('checked', false);
                        this.isChecked();
                        $.each(adresboek.find(':checkbox:checked'), function (key, checkbox) {
                            $.each(this.subs, function (key, sub) {
                                if ($(checkbox).val() === sub.name) {
                                    $.each(sub.users, function (key, user) {
                                        adresboek.find('.user_info').each(function (key, userField) {
                                            if ($(userField).find('.user_id').text() === String(user.id)) {
                                                $(userField).parent().show();
                                                return false;
                                            }
                                        });
                                    }.bind(this));
                                    return false;
                                }
                            }.bind(this));
                        }.bind(this));
                }


//                if (clicked === 'show_all') {
//                    if (clickedObject.is(':checked')) {
//                        checkboxes.prop('checked', false);
//                        clickedObject.prop('checked', true);
//                        this.isChecked();
//                        adresboek.find('li').show();
//                    }
//                } else if (clicked === 'active' || clicked === 'non_active') {
//
//                    if (clickedObject.is(':checked')) {
//                        checkboxes.prop('checked', false);
//                        clickedObject.prop('checked', true);
//                        this.isChecked();
//                        $.each(this.subs, function (key, sub) {
//                            $.each(sub.users, function (key, user) {
//                                adresboek.find('.user_info').each(function (key, userField) {
//                                    if (user.actief === active && $(userField).find('.user_id').text() === String(user.id)) {
//                                        $(userField).parent().show();
//                                        return false;
//                                    }
//                                });
//                            }.bind(this));
//                        }.bind(this));
//                    }
//                } else {
//                    $('#show_all, #active, #non_active').prop('checked', false);
////                    $('#show_all').prop('checked', false);
//                    this.isChecked();
//                    $.each(adresboek.find(':checkbox:checked'), function (key, checkbox) {
//                        $.each(this.subs, function (key, sub) {
//                            if ($(checkbox).val() === sub.name) {
//                                $.each(sub.users, function (key, user) {
//                                    adresboek.find('.user_info').each(function (key, userField) {
//                                        if ($(userField).find('.user_id').text() === String(user.id)) {
//                                            $(userField).parent().show();
//                                            return false;
//                                        }
//                                    });
//                                }.bind(this));
//                                return false;
//                            }
//                        }.bind(this));
//                    }.bind(this));
//                }
            },

            searchEngine: function() {
                $(':checkbox').prop('checked', false);
                $('#show_all').prop('checked', true);
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
            },
            
            letterNavigation: function (letter) {
                location.hash = 'index-' + letter;
                var scrollV, scrollH, loc = window.location;
                if ('pushState' in history) {
                    history.pushState('', document.title, loc.pathname +loc.search);
                } else {
                    scrollV = document.body.scrollTop;
                    scrollH = document.body.scrollLeft;

                    loc.hash = '';

                    document.body.scrollTop = scrollV;
                    document.body.scrollLeft = scrollH;
                }
            }
        }
    }
</script>