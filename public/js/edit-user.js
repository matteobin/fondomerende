// @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later
function askEditUserConfirm(event) {
    event.preventDefault();
    if (confirm(translatedStrings[0]+' '+translatedStrings[1]+' '+event.target[2].value+'?')) {
        event.target.submit();
    }
}
document.getElementById('edit-user-form').addEventListener('submit', askEditUserConfirm);
// @license-end
