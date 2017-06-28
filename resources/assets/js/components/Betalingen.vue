<template>
    <div id="betalingen_i">
        <div class="row">
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-12 col-sm-9 col-md-6 col-lg-5">
                        <div class="col-xs-5" style="min-width: 140px">
                            <select class="selectpicker form-control" v-model="currentQuarter">
                                <option v-for="quarter in quarters" v-bind:value="quarter">Kwartaal {{ quarter }}</option>
                            </select>
                        </div>
                        <div class="row col-xs-3" style="min-width: 100px">
                            <select class="selectpicker form-control" v-model="currentYear">
                                <option v-for="year in years" v-bind:value="year">{{ year }}</option>
                            </select>
                        </div>
                        <div class="col-xs-4">
                            <input class="btn btn-primary form-control" value="Bekijken" type="button" v-on:click="fetchBetalingen">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="payments-no"><strong>Geen</strong> betalingen uit kwartaal {{ showQuarter }} van {{ showYear }}</h3>
        <div class="table">
            <div class="row table_header">
                <div class="col-xs-1 text-left">Id</div>
                <div class="col-xs-1 text-left">Datum</div>
                <div class="col-xs-4">Gebruiker</div>
                <div class="col-xs-2">Titel</div>
                <div class="col-xs-3">Aanbieding</div>
                <div class="col-xs-1 text-right">Bedrag incl.</div>
            </div>
            <div id="betalingen_layers">
                <div v-bind:id="'betaling_' + betaling.id" class="row table_layer" v-for="betaling in betalingen">
                    <div class="table_col col-xs-1 text-left">{{ betaling.id }}</div>
                    <div class="table_col col-xs-1 text-left">{{ betaling.datum }}</div>
                    <div class="table_col col-xs-4">{{ betaling.bedrijfsnaam }} (<a v-bind:href="'/user/' + betaling.user_id" target="_blank">{{ betaling.email }}</a>)</div>
                    <div class="table_col col-xs-2">{{ betaling.titel }}</div>
                    <div class="table_col col-xs-3">{{ betaling.aanb }}</div>
                    <div class="table_col col-xs-1 text-right">&euro; {{ betaling.bedrag_in }}</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                years:          [],
                quarters:       [],
                betalingen:     [],
                showYear:       '',
                showQuarter:    '',
                currentYear:    new Date().getFullYear(),
                currentQuarter: getQuarterByMonth(new Date().getMonth()+1),
            }
        },

        mounted() {
            for (let kwartaal = 1; kwartaal < 5; kwartaal++) {
                this.quarters.push(kwartaal);
            }
            for (let jaar = this.currentYear; jaar > (this.currentYear - 8); jaar--) {
                this.years.push(jaar);
            }
            this.fetchBetalingen();
        },

        methods: {
            fetchBetalingen: function() {
                $('#loading_screen').show();
                $.getJSON('betalingen/api', {kwartaal:this.currentQuarter, jaar:this.currentYear}, function(betalingen) {
                    for(let i = 0; i < betalingen.length; i++) {
                        betalingen[i]['bedrag_in'] = punt_naar_komma(betalingen[i]['bedrag_in']);
                        betalingen[i]['aanb'] = (betalingen[i]['aanb'] !== '') ? betalingen[i]['aanb'] : 'Geen aanbieding';
                    }

                    this.betalingen = betalingen;
                    if (this.betalingen.length === 0) {
                        $('.table').hide();
                        $('.payments-no').show();
                    } else {
                        $('.table').show();
                        $('.payments-no').hide();
                    }
                    $('#loading_screen').hide();
                    this.showYear = this.currentYear;
                    this.showQuarter = this.currentQuarter;
                }.bind(this));
            }
        }
    }
</script>