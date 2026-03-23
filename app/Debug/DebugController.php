<?php

namespace App\Debug;

use App\AppController;
use Lib\Dump;
use Lib\Session;
use App\Host\HostService as Host;

class DebugController extends AppController
{
    public function dump()
    {
        $host_password = strtoupper(Host::password());
        $input = strtoupper(trim($this->data));
        // Tilføj denne linje for at være sikker på specialtegn:
        $input = urldecode($input);
        $feedback = "";

        // 1. INITIALISER ORDLISTE
        Dump::words(wordlist(strlen($host_password), Host::level(), 'word_list.txt'));
        Dump::correct([strtoupper(Host::admin()), $host_password]);

        // 2. HÅNDTER INPUT
        if ($input) {
            if ($input == 'RESET') {
                Dump::reset();
            } 
            // TJEK FOR BRACKETS (F.EKS. <!!!>, [###], OVS.)
            elseif (preg_match('/^[\[\{\(\<].*[\]\}\)\>]$/', $input)) {
                Dump::bracket($input); // GEM SOM BRUGT

                // 25% CHANCE FOR NULSTILLING AF FORSØG, 75% FOR FJERNE DUD
                if (rand(1, 4) === 1) {
                    Session::remove(Dump::$input);
                    $feedback = "SUCCESS: TRIES RESET.";
                } else {
                    $allWords = Dump::$words;
                    $removedAlready = Session::get(Dump::$removed) ?: [];
                    $availableDuds = array_diff($allWords, [$host_password], $removedAlready);

                    if (!empty($availableDuds)) {
                        $dudToRemove = $availableDuds[array_rand($availableDuds)];
                        Dump::remove($dudToRemove);
                        $feedback = "SUCCESS: DUD REMOVED.";
                    }
                }
            } 
            // ALMINDELIGT ORD-GÆTs
            else {
                $isCorrect = Dump::input($input);
                if (!$isCorrect) {
                    $score = Dump::match($input, $host_password);
                    $feedback = "ERROR: ENTRY DENIED. MATCH=$score";
                } else {
                    $feedback = "SUCCESS: ACCESS GRANTED. WELCOME, AUTHORIZED USER.";
                }
            }
        }

        // 3. GENERER HEADER
        $attemptsLeft = 4 - count(Dump::data());
        $header = "SYSCORP (TM) TERMLINK PROTOCOL\n";
        $header .= "UNIFIED SYSTEMS OF CORPORATIONS - LEVEL " . Host::level() . "\n";
        $header .= "ATTEMPT(S) LEFT: " . ($attemptsLeft < 0 ? 0 : $attemptsLeft) . "\n";
        
        if ($feedback) {
            $header .= "> " . strtoupper($feedback) . "\n";
        }

        // 4. VIS TERMINALEN
        Dump::memory(16, 12, $header . "\n");
    }
}