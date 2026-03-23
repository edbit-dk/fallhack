<?php

namespace Lib;

use Lib\Session;

class Dump 
{
    public static $reset = false;
    public static $words = [];
    public static $correct = [];
    public static $dump = 'MEMORY_DUMP';
    public static $input = 'MEMORY_INPUT';
    public static $removed = 'MEMORY_REMOVED';
    public static $used_brackets = 'MEMORY_USED_BRACKETS';

    public static function reset()
    {
        self::$reset = true;
        Session::remove(self::$input);
        Session::remove(self::$dump);
        Session::remove(self::$removed);
        Session::remove(self::$used_brackets);
    }

    public static function words($words = [])
    {
        self::$words = array_map('strtoupper', $words);
    }

    public static function correct($words = [])
    {
        self::$correct = array_map('strtoupper', $words);
    }

    public static function remove($word) 
    {
        $removed = Session::get(self::$removed) ?: [];
        $word = strtoupper(trim($word));
        if (!in_array($word, $removed) && $word !== '') {
            $removed[] = $word;
            Session::set(self::$removed, $removed);
        }
    }

    public static function bracket($bracket_string) 
    {
        $used = Session::get(self::$used_brackets) ?: [];
        // Brug trim for at sikre, at ingen usynlige newline-tegn ødelægger matchet
        $bracket_string = trim($bracket_string);
        
        if (!in_array($bracket_string, $used) && $bracket_string !== '') {
            $used[] = $bracket_string;
            Session::set(self::$used_brackets, $used);
        }
    }

    public static function match($guess, $correct) 
    {
        $score = 0;
        $guess = strtoupper(trim($guess));
        $correct = strtoupper(trim($correct));
        $len = min(strlen($guess), strlen($correct));
        
        for ($i = 0; $i < $len; $i++) {
            if (isset($guess[$i]) && isset($correct[$i]) && $guess[$i] === $correct[$i]) {
                $score++;
            }
        }
        return $score;
    }

    public static function memory($rows = 16, $cols = 12, $header = "") 
    {
        $hexBase = 0xF964; // DEFINE BASE ADDRESS EARLY

        if(empty(self::$words)) {
            self::$words = ["HACK", "PASSWORD", "SECURITY", "VAULT", "ACCESS", "DENIED", "TERMINAL", "ADMIN", "PASS"];
        }
        
        $words = array_map('strtoupper', array_merge(self::$words, self::$correct));
        
        if (self::$reset || !Session::has(self::$dump)) {
            Session::remove(self::$input);
            $totalChars = $rows * $cols * 2;
            $symbols = ['<', '>', '[', ']', '{', '}', '(', ')', '/', '\\', '|', '?', '!', '@', '#', '$', '%', '^', '&', '*', '-', '_', '+', '=', '.', ',', ':', ';'];
            
            $data = [];
            for ($i = 0; $i < $totalChars; $i++) {
                $data[$i] = $symbols[array_rand($symbols)];
            }

            $usedPositions = [];
            foreach ($words as $word) {
                $wordLen = strlen($word);
                $attempts = 0;
                do {
                    $attempts++;
                    $pos = rand(0, $totalChars - $wordLen);
                    $startRow = floor($pos / $cols);
                    $endRow = floor(($pos + $wordLen - 1) / $cols);
                    $collision = ($startRow !== $endRow);
                    if (!$collision) {
                        for ($j = $pos; $j < $pos + $wordLen; $j++) {
                            if (isset($usedPositions[$j])) {
                                $collision = true;
                                break;
                            }
                        }
                    }
                    if ($attempts > 1000) break; 
                } while ($collision);

                for ($j = 0; $j < $wordLen; $j++) {
                    $data[$pos + $j] = $word[$j];
                    $usedPositions[$pos + $j] = true;
                }
            }
            Session::set(self::$dump, $data);
            self::$reset = false;
        } else {
            $data = Session::get(self::$dump);
        }

        $dataString = implode('', $data);
        
        // --- 1. ERSTAT BRUGTE BRACKETS FØRST ---
        // Det er vigtigt at gøre dette først, da de indeholder specialtegn
        $usedBrackets = Session::get(self::$used_brackets) ?: [];
        foreach ($usedBrackets as $bracket) {
            if (!empty($bracket)) {
                // Vi bruger str_replace for et præcist match på specialtegn
                $replacement = str_repeat('.', strlen($bracket));
                $dataString = str_replace((string)$bracket, $replacement, $dataString);
            }
        }

        // --- 2. ERSTAT BRUGTE ORD (DUDS FRA BRACKETS) ---
        $removedWords = Session::get(self::$removed) ?: [];
        foreach ($removedWords as $word) {
            if (!empty($word)) {
                $replacement = str_repeat('.', strlen($word));
                $dataString = str_ireplace($word, $replacement, $dataString);
            }
        }

        // --- 3. ERSTAT FORKERTE GÆT (INPUTS FRA BRUGEREN) ---
        $wrongGuesses = self::data();
        foreach ($wrongGuesses as $wrong) {
            if (!empty($wrong)) {
                $replacement = str_repeat('.', strlen($wrong));
                $dataString = str_ireplace($wrong, $replacement, $dataString);
            }
        }


        $displayData = str_split($dataString);

        $output = strtoupper($header);
        for ($i = 0; $i < $rows; $i++) {
            $addrL = sprintf("0x%04X", $hexBase + ($i * $cols));
            $addrR = sprintf("0x%04X", $hexBase + (($rows + $i) * $cols));
            
            $charsLeft = implode('', array_slice($displayData, $i * $cols, $cols));
            $charsRight = implode('', array_slice($displayData, ($rows + $i) * $cols, $cols));
            
            $output .= "$addrL $charsLeft   $addrR $charsRight\n";
        }
        echo $output;
    }

    public static function data()
    {
       $val = Session::get(self::$input);
        // Hvis $val er null eller ikke et array, returner et tomt array []
        return is_array($val) ? $val : [];
    }

    public static function input($word) 
    {
        $input = strtoupper(trim($word));
        $correctOnes = array_map('strtoupper', self::$correct);
        if (in_array($input, $correctOnes)) {
            return true;
        } else {
            $wrongGuesses = self::data();
            if ($input !== '' && !in_array($input, $wrongGuesses)) {
                $wrongGuesses[] = $input;
                Session::set(self::$input, $wrongGuesses);
            }
            return false;
        }
    }
}