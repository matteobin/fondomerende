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
