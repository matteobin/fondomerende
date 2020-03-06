function formatNumberString(number) {
    number = number.split('');
    var passedDecimalPointSeparator = false;
    for (var index=number.length-1; index>=0; index--) {
        if (number[index]!='-' && number[index]!='+') {
            if (isNaN(number[index])) {
                if (passedDecimalPointSeparator) {
                    number[index] = thousandsSeparator;
                } else {
                    number[index] = decimalPointSeparator;
                    passedDecimalPointSeparator = true;
                }
            }
        }
    }
    return number.join('');
}
