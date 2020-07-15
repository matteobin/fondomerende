function formatNumberString(number) {
    number = number.split('');
    var passedDecimalPointSeparator = false;
    for (var index=number.length-1; index>=0; index--) {
        if (number[index]!='-' && number[index]!='+') {
            if (isNaN(number[index])) {
                if (passedDecimalPointSeparator) {
                    number[index] = translatedStrings[1];
                } else {
                    number[index] = translatedStrings[0];
                    passedDecimalPointSeparator = true;
                }
            }
        }
    }
    return number.join('');
}
