// Function to handle redirect
function handleResponse(response, timeout = 2500) {

    // Rens responsen for eventuelle skjulte tegn
    const cleanResponse = response.trim();

    if (cleanResponse.startsWith('ACCESSING')) {
        setTimeout(function() { redirectTo('') }, timeout);
    }

    if (cleanResponse.includes('LOGGING OUT...')) {
        setTimeout(function() { redirectTo('') }, timeout);
    }

    if (cleanResponse.includes('SECURITY ACCESS CODE SEQUENCE ACCEPTED')) {
        setTimeout(function() { redirectTo('') }, timeout);
    }

    if (cleanResponse.includes('VERIFYING CREDENTIALS')) {
        sessionStorage.setItem('host', true);
        setTimeout(function() { redirectTo('') }, timeout);
    }

}

function refreshConnection() {
    $('#connection').load('connection', function() {
        themeConnection();
        scrollToBottom();
        console.log("Connection UI updated with theme.");
    });
}

// Function to redirect to a specific query string
function redirectTo(url, reload = false, timeout = 2500) {
    if(reload) {
        return window.location.href = url;
    }
    //clearTerminal();
    setTimeout(function() { 
        refreshConnection();
        sendCommand('main', ''); 
    }, timeout);}

// Function to validate the string pattern
function isUplinkCode(input) {
    // 1. Fjern alle mellemrum og bindestreger for at få den rene kode
    const cleanCode = input.replace(/[\s\-]/g, '');

    // 2. Tjek om den rene kode består af præcis 24 alfanumeriske tegn
    // (Da din oprindelige kode var 27 tegn inkl. 3 bindestreger, er selve koden 24 tegn)
    const pattern = /^[A-Za-z0-9]{24}$/;
    
    return pattern.test(cleanCode);
}

// Utility function to find the common prefix of an array of strings
function findCommonPrefix(strings) {
    if (!strings.length) return '';
    let prefix = strings[0];
    for (let i = 1; i < strings.length; i++) {
        while (!strings[i].startsWith(prefix)) {
            prefix = prefix.slice(0, -1);
            if (!prefix) break;
        }
    }
    return prefix;
}
