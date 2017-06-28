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

function getQuarterByMonth(maand) {
    var kwartaal = null;
    maand = String(maand);
    switch(true) {
        case maand >= '1' && maand <= '3':
            kwartaal = 1;
            break;
        case maand >= '4' && maand <= '6':
            kwartaal = 2;
            break;
        case maand >= '7' && maand <= '9':
            kwartaal = 3;
            break;
        case maand >= '10' && maand <= '12':
            kwartaal = 4;
            break;
        default:
            kwartaal = false;
    }
    return kwartaal;
}

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}