// @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later
function askAddSnackConfirm(event) {
    event.preventDefault();
    var confirmString = translatedStrings[2]+' '+translatedStrings[3]+' '+event.target[1].value+'?\n\n'+translatedStrings[4]+': '+formatNumberString(event.target[2].value)+' €.\n'+translatedStrings[5]+': '+event.target[3].value+'.\n'+translatedStrings[6]+' '+translatedStrings[7]+': '+event.target[4].value+'.\n';
    if (event.target[5].checked) {
        confirmString += translatedStrings[8];
    } else {
        confirmString += translatedStrings[9];
    }
    confirmString += '.';
    if (confirm(confirmString)) {
        event.target.submit();
    }
}
document.getElementById('add-snack-form').addEventListener('submit', askAddSnackConfirm);
// @license-end
