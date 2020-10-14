// @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later
function askEatConfirm(event) {
    event.preventDefault();
    if (confirm(translatedStrings[0]+' '+event.target.childNodes[3].innerText+'?')) {
        event.target.submit();
    }
}
var submits = document.getElementsByTagName('form');
var submitsNumber = submits.length;
for (var index=0; index<submitsNumber; index++) {
    submits[index].addEventListener('submit', askEatConfirm);
}
// @license-end
