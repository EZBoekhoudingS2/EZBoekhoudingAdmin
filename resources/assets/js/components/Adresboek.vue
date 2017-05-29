<template>
    <div id="adresboek" class="col-xs-12 col-md-5 col-lg-4">
        <div class="element">
            <div class="element-header">
                <div class="inner-addon right-addon">
                    <span class="glyphicon glyphicon-search"></span>
                    <input id="search" type="text" name="s" value="" placeholder="Zoeken..." autocomplete="off">
                    <h2>Adresboek</h2>
                </div>
            </div>
            <div class="element-content">
                <ul class="list">
                    <li v-for="user in users">
                        <span class="EZ_adreslist contact_name" v-bind:id="user.valueForId" v-bind:value="user.id">
                            <a v-bind:href="'/user/' + user.id">{{ user.vnaam }} {{ user.anaam }}</a>
                            <span class="email">{{ user.email }}</span>
                        </span>
                    </li>
                </ul>
                <div class="contact_buttons btn-group btn-group-justified" role="group">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" id="add_contact">
                            <span class="glyphicon glyphicon-plus"></span>
                            <span class="hidden-xs"> Voeg een contact toe</span>
                        </button>
                    </div>
                </div>
                <div class="element-sidebar">
                    <a href="#index-#">#</a>
                    <a v-bind:href="'#index-' + letter" v-for="letter in letters">{{ letter }}</a>
                </div>
            </div>
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
                ]
            };
        },

        mounted() {
            this.fetchUsers();
        },

        methods: {
            fetchUsers: function(id) {
                $('#loading_screen').css({display: 'block'});
                $.getJSON('users/api', function(data) {
                    let letter = '';
                    let firstletter = '';
                    for (let i = 0; i < data.length; i++) {
                        firstletter = (data[i].vnaam).charAt(0);
                        if (!firstletter.match(/^[a-z]+$/i)) {
                            firstletter = '#';
                        } else {
                            firstletter = firstletter.toUpperCase();
                        }
                        if (firstletter !== letter) {
                            letter = firstletter;
                            data[i].valueForId = 'index-' + letter;
                        }
                    }
                    this.users = data;
                    setTimeout(function() {
                        let contacts = $('.EZ_adreslist');
                        if (typeof(id) !== 'undefined' && id !== null) {
                            for (let e = 0; e < contacts.length; e++) {
                                if (contacts[e].attributes.value.value === String(id)) {
                                    contacts.eq(e).css({'border-left': '5px solid #bbbbbb'});
                                    $('#delete_contact').prop('disabled', false);
                                }
                            }
                        } else {
                            contacts.css({border: '0px'});
                        }
                    }, 10);
                    $('#loading_screen').css({display: 'none'});
                }.bind(this));
            },
        }
    }
</script>