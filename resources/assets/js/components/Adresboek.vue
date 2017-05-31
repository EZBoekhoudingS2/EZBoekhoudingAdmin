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
                            <!--<a v-bind:href="'/user/' + user.id">-->
                                <span class="EZ_adreslist user_name" v-bind:id="user.valueForId" v-bind:value="user.id" v-on:click="showUser(user.id)">
                                    {{ user.vnaam }} {{ user.anaam }}
                                    <span class="user_email">{{ user.email }}</span>
                                </span>
                            <!--</a>-->
                        </li>
                    </ul>
                    <div class="element-sidebar">
                        <a v-on:click.stop.prevent="letterNavigation('#')" href="#index-#">#</a>
                        <a v-on:click.stop.prevent="letterNavigation(letter)" v-bind:href="'#index-' + letter" v-for="letter in letters">{{ letter }}</a>
                    </div>
                </div>
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
                letters:    [
                    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
                    'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
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
            this.fetchUsers();
        },

        methods: {
            fetchUsers: function(id) {
                $.getJSON('users/api', function(users) {
                    let letter = '';
                    let firstLetter = '';
                    $.each(users, function (key, user) {
                        firstLetter = (!(user.vnaam).charAt(0).match(/^[a-z]+$/i))
                            ? '#' : (user.vnaam).charAt(0).toUpperCase();
                        if (firstLetter !== letter) {
                            user.valueForId = 'index-' + firstLetter;
                            letter = firstLetter;
                        }
                    });
                    this.users = users;
                    setTimeout(function() {
                        let userFields = $('.EZ_adreslist');
                        if (typeof(id) !== 'undefined' && id !== null) {
                            userFields.each(function(key, userField) {
                                if (userField.attributes.value.value === String(id))
                                    $(userField).addClass('selected');
                            });
                        } else {
                            userFields.removeClass('selected');
                        }
                    }, 10);
                }.bind(this));
            },

            showUser: function(user_id) {
                $('#card-text, .edit-user').hide();
                $('#card, .loading-text').show();
                $.getJSON('users/api', {user_id: user_id}, function(data) {
                    const user = data[0];
                    this.id         = user.id;
                    this.vnaam      = user.vnaam;
                    this.anaam      = user.anaam;
                    this.straat     = user.straat;
                    this.postcode   = user.postcode;
                    this.plaats     = user.plaats;
                    this.email      = user.email;
                    this.telefoon   = user.telefoon;
                    $('.loading-text').hide();
                    $('#card-text, .edit-user').show();
                }.bind(this));
            },

            searchEngine: function() {
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