// @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later
function enableOrDisableBuyOptions() {
    var form = document.getElementById('buy-form');
    if (form[3].checked) {
        document.getElementById('buy-options').style.opacity = 1;
        form[4].disabled = false;
        form[5].disabled = false;
        form[6].disabled = false;
    } else {
        document.getElementById('buy-options').style.opacity = 0.5;
        form[4].disabled = true;
        form[5].disabled = true;
        form[6].disabled = true;
    }
}
function updateBuyOptions() {
    var form = document.getElementById('buy-form');
    var snackIndex = form[1].selectedIndex;
    form[4].value = snacks[snackIndex]['price'];
    form[5].value = snacks[snackIndex]['snacks-per-box'];
    form[6].value = snacks[snackIndex]['expiration'];
}
function askBuyConfirm(event) {
    event.preventDefault();
    var cratesNumber = event.target[2].value;
    var cratesString = ' '+translatedStrings[2];
    if (cratesNumber=='1') {
        cratesString = ' '+translatedStrings[3];
    }
    var confirmString = translatedStrings[4]+' '+cratesNumber+cratesString+' '+translatedStrings[5]+' '+event.target[1][event.target[1].selectedIndex].innerText+'?';
    if (event.target[3].checked) {
        confirmString += '\n\n'+translatedStrings[6]+': '+formatNumberString(event.target[4].value)+' €.\n'+translatedStrings[7]+': '+event.target[5].value+'. \n'+translatedStrings[8]+': '+event.target[6].value+'.';
    }
    if (confirm(confirmString)) {
        event.target.submit();
    }
}
enableOrDisableBuyOptions();
document.getElementById('customise-buy-options-input').addEventListener('change', enableOrDisableBuyOptions);
updateBuyOptions();
document.getElementById('snacks-select').addEventListener('change', updateBuyOptions);
document.getElementById('buy-form').addEventListener('submit', askBuyConfirm);
// @license-end
