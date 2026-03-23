// Array to store command history
let path_public = 'public/';
let stylesheets = path_public + 'css/';
let commandHistory = [];
let historyIndex = -1;
let currentDirectory = ''; // Variable to store the current directory
let isPasswordPrompt = false; // Flag to track if password prompt is active
let userPassword = ''; // Variable to store the password
let usernameForLogon = ''; // Variable to store the username for logon
let usernameForNewUser = ''; // Variable to store the username for new user
let isUsernamePrompt = false;
let currentCommand = '';
let commands = [];
let files = [];
let folders = [];
let cmd = '';
let currentSongIndex = 0;
let audio;

// Event listener for when the DOM content is loaded
$(document).ready(function() {
    // Load the saved theme when the document is ready
    loadSavedTheme();

    // Load the saved term when the document is ready
    loadSavedTermMode();

    //Check commands available
    autoHelp();

    // Vi lytter nu på både .terminal-word og .terminal-bracket
    $('#terminal').on('click', '.terminal-word, .terminal-bracket', function() {
        const $this = $(this);
        const clickedText = $this.text();
        const $input = $('#command-input');
        
        // Tjek om det er en bracket-sekvens eller et ord
        if ($this.hasClass('terminal-bracket')) {
            // Fallout-logik: Brackets sendes ofte direkte til systemet 
            // for at fjerne "duds" eller nulstille forsøg med det samme.
            $input.val(clickedText);
            
            // Valgfrit: Trigger submit automatisk for brackets, 
            // da de i Fallout kører med det samme uden Enter.
            $('#terminal-form').submit(); 

            // Gør den ubrugelig efter klik (ligesom i Fallout)
        $this.removeClass('terminal-bracket clickable').css('opacity', '0.5');
        } else {
            // Din eksisterende logik for ord
            const currentVal = $input.val().trim();
            if (currentVal === "") {
                $input.val(clickedText);
            } else {
                $input.val(currentVal + " " + clickedText);
            }
        }

        $input.focus();
        
        // Din fede flash-effekt
        $this.css('background-color', '#00ff00').delay(100).queue(function(next){
            $this.css('background-color', '');
            next();
        });
    });

    // Check if 'boot' command has been sent during the current session
    if (!localStorage.getItem('boot')) {

        setTimeout(function() {
            sendCommand('boot', ''); // Send 'boot' command
        }, 500);
        
        setTimeout(function() {
            localStorage.setItem('boot', true); // Set 'boot' flag in sessionStorage
            clearTerminal();
            sendCommand('main', '');
        }, 30000);
    } else {

        setTimeout(function() {
            refreshConnection();
            themeConnection();
            sendCommand('main', ''); // Send 'welcome' command if boot has been set
            scrollToBottom();
        }, 500);
    }
});
