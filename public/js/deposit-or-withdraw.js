function askDepositOrWithdrawConfirm(event) {
    event.preventDefault();
    if (confirm(translatedStrings[2]+' '+formatNumberString(event.target[2].value)+' €?')) {
        event.target.submit();
    }
}
document.getElementById('deposit-or-withdraw-form').addEventListener('submit', askDepositOrWithdrawConfirm);
