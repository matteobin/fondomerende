function askEditUserConfirm(event) {
    event.preventDefault();
    if (confirm(translatedStrings[0]+' '+translatedStrings[1]+' '+event.target[2].value+'?')) {
        event.target.submit();
    }
}
document.getElementById('edit-user-form').addEventListener('submit', askEditUserConfirm);