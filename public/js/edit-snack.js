// @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later
function askEditSnackConfirm(event) {
    event.preventDefault();
    console.log(event);
    var confirmString = translatedStrings[2]+' '+translatedStrings[3]+' '+event.target[2].value+'?\n\n'+translatedStrings[4]+': '+formatNumberString(event.target[3].value)+' €.\n'+translatedStrings[5]+': '+event.target[4].value+'.\n'+translatedStrings[6]+' '+translatedStrings[7]+': '+event.target[5].value+'.';
    if (event.target[7].checked) {
        confirmString += '\n'+translatedStrings[8]+'.';
    } else {
        confirmString += '\n'+translatedStrings[9]+'.';
    }
    if (confirm(confirmString)) {
        event.target.submit();
    }
}
document.getElementById('edit-snack-form').addEventListener('submit', askEditSnackConfirm);
// @license-end
