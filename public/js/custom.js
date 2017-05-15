function punt_naar_komma(bedrag) {
    var bedrag = String(bedrag);
    var los = bedrag.split('.');
    if(los.length == 1) {
        var terug = los.join('');
        if (terug.length == 0) {
            i_return = '0,00';
        } else {
            i_return = terug + ',00';
        }
        return i_return;
    } else {
        if (los.length[1] == 1) {
            return los.join(',') + '0';
        } else {
            return los.join(',');
        }
    }
}

function komma_naar_punt(bedrag) {
    var bedrag = String(bedrag);
    var los = bedrag.split(',');
    if(los.length == 1) {
        var terug = los.join('');
        if (terug.length == 0) {
            i_return = '0.00';
        } else {
            i_return = terug + '.00';
        }
        return i_return;
    } else {
        if (los.length[1] == 1) {
            return los.join('.') + '0';
        } else {
            return los.join('.');
        }
    }
}