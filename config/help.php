<?php

return [
    [
        'cmd' => 'HELP', 
        'input' => '[CMD|PAGE]', 
        'info' => 'SHOWS INFO ABOUT COMMAND',
        'is_user' => 1,
        'is_host' => 1,
        'is_visitor' => 1,
        'is_guest' => 1
    ],
    [
        'cmd' => 'UPLINK', 
        'input' => '[ACCESS CODE]', 
        'info' => 'CONNECT TO GLOBAL NETWORK',
        'is_user' => 0,
        'is_host' => 0,
        'is_visitor' => 1,
        'is_guest' => 0
    ],
    [
        'cmd' => 'REGISTER', 
        'input' => '[USERNAME]', 
        'info' => 'NEW ACCOUNT',
        'is_user' => 0,
        'is_host' => 0,
        'is_visitor' => 1,
        'is_guest' => 0
    ],
    [
        'cmd' => 'LOGON', 
        'input' => '[USERNAME]', 
        'info' => 'LOGON ACCOUNT',
        'is_user' => 0,
        'is_host' => 0,
        'is_visitor' => 1,
        'is_guest' => 1
    ],
    [
        'cmd' => 'LOGOUT', 
        'input' => NULL, 
        'info' => 'DISCONNECT FROM MAINFRAME (ALIAS: EXIT, DC, QUIT, CLOSE)',
        'is_user' => 1,
        'is_host' => 1,
        'is_visitor' => 0,
        'is_guest' => 1
    ],
    [
        'cmd' => 'VERSION', 
        'input' => NULL, 
        'info' => 'SysCorp/OS V.1.9.84',
        'is_user' => 1,
        'is_host' => 1,
        'is_visitor' => 1,
        'is_guest' => 1
    ],
    [
        'cmd' => 'MUSIC', 
        'input' => '[START|STOP|NEXT]', 
        'info' => 'PLAY 80S MUSIC',
        'is_user' => 1,
        'is_host' => 1,
        'is_visitor' => 1,
        'is_guest' => 1
    ],
    [
        'cmd' => 'COLOR', 
        'input' => '[GREEN|WHITE|YELLOW|BLUE]', 
        'info' => 'TERMINAL COLOR MODE',
        'is_user' => 1,
        'is_host' => 1,
        'is_visitor' => 1,
        'is_guest' => 1
    ],
    [
        'cmd' => 'TERM', 
        'input' => '[DEC-VT100|IBM-3270]', 
        'info' => 'CHANGE TERMINAL MODE',
        'is_user' => 1,
        'is_host' => 1,
        'is_visitor' => 1,
        'is_guest' => 1
    ],
    [
        'cmd' => 'SCAN', 
        'input' => NULL, 
        'info' => 'LIST CONNECTED MAINFRAMES',
        'is_user' => 1,
        'is_host' => 1,
        'is_visitor' => 0,
        'is_guest' => 0
    ],
    [
        'cmd' => 'CONNECT', 
        'input' => '[MAINFRAME]', 
        'info' => 'CONNECT TO MAINFRAME',
        'is_user' => 1,
        'is_host' => 1,
        'is_visitor' => 0,
        'is_guest' => 0
    ],
    [
        'cmd' => 'MAIL', 
        'input' => '[SEND|READ|LIST|DELETE]', 
        'info' => "EMAIL ACCOUNT: -S <SUBJECT> <ACCOUNT>[@MAINFRAME] < <MESSAGE> \n
        LIST EMAILS: [-L] \n
        READ EMAIL: [-R] <ID> \n
        SENT EMAILS: -S \n
        SENT EMAIL: -S <ID> \n
        DELETE EMAIL: -D <ID>",
        'is_user' => 1,
        'is_host' => 1,
        'is_visitor' => 0,
        'is_guest' => 0
    ],
    [
        'cmd' => 'DIR', 
        'input' => NULL, 
        'info' => 'LIST FILES ON MAINFRAME',
        'is_user' => 0,
        'is_host' => 1,
        'is_visitor' => 0,
        'is_guest' => 1
    ],
    [
        'cmd' => 'TYPE', 
        'input' => '[FILENAME]', 
        'info' => 'PRINT CONTENTS OF FILE',
        'is_user' => 0,
        'is_host' => 1,
        'is_visitor' => 0,
        'is_guest' => 1
    ],
    [
        'cmd' => 'DEBUG', 
        'input' => '[DUMP]', 
        'info' => 'RUN MEMORY DUMP (ADMIN ONLY)',
        'is_user' => 1,
        'is_host' => 0,
        'is_visitor' => 0,
        'is_guest' => 1
    ],
];