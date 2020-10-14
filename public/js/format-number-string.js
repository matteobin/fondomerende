// @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later
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
// @license-end
